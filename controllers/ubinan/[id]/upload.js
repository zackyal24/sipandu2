const fs = require('fs');
const path = require('path');
const sharp = require('sharp');
const getPool = require('../../../config/database');
const { formidable } = require('formidable');
const { uploadFileToGCS, deleteFileFromGCS, getFilePathFromUrl } = require('../../../config/gcs');

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST,OPTIONS');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'Token diperlukan' });
    }

    const jwt = require('jsonwebtoken');
    jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');

    const id = req.query.id || req.params.id;
    const pool = getPool();
    const bucket = process.env.GCS_BUCKET;

    if (!bucket) {
      return res.status(500).json({ error: 'GCS_BUCKET not configured' });
    }

    const form = formidable({
      maxFileSize: 10 * 1024 * 1024,
      keepExtensions: true
    });

    form.parse(req, async (err, fields, files) => {
      if (err) {
        return res.status(400).json({ error: 'Form parse error', details: err.message });
      }

      const fileUpdates = {};

      try {
        // Validation: Check if any files uploaded
        if (!files || Object.keys(files).length === 0) {
          return res.status(400).json({ error: 'Tidak ada file yang diunggah' });
        }

        // Get desa, tanggal_panen, and existing file paths from database
        const dataResult = await pool.query(
          `SELECT desa, tanggal_panen,
            foto_penyampaian_uang, foto_ktp_petani, foto_timbangan_ubinan, foto_proses_ubinan, foto_plot_setelah_panen
          FROM monitoring_data_panen WHERE id = $1`,
          [id]
        );

        if (dataResult.rows.length === 0) {
          return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
        }

        const {
          desa, tanggal_panen,
          foto_penyampaian_uang, foto_ktp_petani, foto_timbangan_ubinan, foto_proses_ubinan, foto_plot_setelah_panen
        } = dataResult.rows[0];
        const oldFiles = {
          foto_penyampaian_uang,
          foto_ktp_petani,
          foto_timbangan_ubinan,
          foto_proses_ubinan,
          foto_plot_setelah_panen
        };
        const dateStr = tanggal_panen ? new Date(tanggal_panen).toISOString().split('T')[0] : new Date().toISOString().split('T')[0];

        // Process each uploaded file
        for (const [fieldname, fileArray] of Object.entries(files)) {
          const file = Array.isArray(fileArray) ? fileArray[0] : fileArray;
          if (!file) continue;

          // ===== FILE VALIDATION =====
          
          // 1. Check file size (max 10MB = 10485760 bytes)
          const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
          if (file.size > MAX_FILE_SIZE) {
            // Clean up temp file
            if (fs.existsSync(file.filepath)) {
              fs.unlinkSync(file.filepath);
            }
            return res.status(400).json({ 
              error: `File terlalu besar. Maksimal 10MB (${fieldname} = ${(file.size / 1024 / 1024).toFixed(2)}MB)`
            });
          }

          // 2. Check file type (only images)
          // Tambah variasi MIME yang kadang dikirim browser/ponsel (image/jpg, image/pjpeg) + HEIC/HEIF
          const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp', 'image/heic', 'image/heif'];
          const ALLOWED_EXTENSIONS = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.heic', '.heif'];
          
          let ext = path.extname(file.originalFilename).toLowerCase();
          let mimeType = file.mimetype || '';
          let uploadPath = file.filepath;

          if (!ALLOWED_EXTENSIONS.includes(ext) || !ALLOWED_MIME_TYPES.includes(mimeType)) {
            // Clean up temp file
            if (fs.existsSync(file.filepath)) {
              fs.unlinkSync(file.filepath);
            }
            return res.status(400).json({ 
              error: `Format file tidak didukung (${fieldname}). Hanya JPG, PNG, GIF, WebP, HEIC/HEIF (akan dikonversi) yang diizinkan`
            });
          }

          // 3. Convert HEIC/HEIF to JPEG
          const isHeic = ['.heic', '.heif'].includes(ext) || ['image/heic', 'image/heif'].includes(mimeType);
          if (isHeic) {
            try {
              const convertedPath = path.join(path.dirname(file.filepath), `${path.basename(file.filepath, ext)}.jpg`);
              await sharp(file.filepath).jpeg({ quality: 90 }).toFile(convertedPath);
              // remove original temp file
              if (fs.existsSync(file.filepath)) {
                fs.unlinkSync(file.filepath);
              }
              uploadPath = convertedPath;
              ext = '.jpg';
              mimeType = 'image/jpeg';
            } catch (convErr) {
              console.error('HEIC convert error:', convErr);
              if (fs.existsSync(file.filepath)) {
                fs.unlinkSync(file.filepath);
              }
              return res.status(400).json({ error: 'Gagal mengonversi foto HEIC ke JPEG, coba ulangi atau set kamera ke JPEG' });
            }
          }

          let gcsFolder = 'lainnya';
          let kategori = fieldname;
          if (fieldname === 'foto_penyampaian_uang') {
            gcsFolder = 'penyampaian_uang';
            kategori = 'penyampaian-uang';
          } else if (fieldname === 'foto_ktp_petani') {
            gcsFolder = 'ktp_petani';
            kategori = 'ktp-petani';
          } else if (fieldname === 'foto_timbangan_ubinan') {
            gcsFolder = 'timbangan_ubinan';
            kategori = 'timbangan-ubinan';
          } else if (fieldname === 'foto_proses_ubinan') {
            gcsFolder = 'proses_ubinan';
            kategori = 'proses-ubinan';
          } else if (fieldname === 'foto_plot_setelah_panen') {
            gcsFolder = 'plot_setelah_panen';
            kategori = 'plot-setelah-panen';
          }

          const timestamp = Date.now(); // Unix timestamp untuk unique filename
          const newFilename = `${desa.toLowerCase()}_${dateStr}_${kategori}_${timestamp}${ext}`;
          const gcsDestination = `${gcsFolder}/${newFilename}`;

          // Delete old file from GCS if exists in database
          if (oldFiles[fieldname]) {
            const oldGcsPath = getFilePathFromUrl(oldFiles[fieldname]);
            if (oldGcsPath) {
              try {
                await deleteFileFromGCS(bucket, oldGcsPath);
                console.log(`Deleted old file from GCS: ${oldGcsPath}`);
              } catch (deleteErr) {
                console.error(`Error deleting old file from GCS:`, deleteErr.message);
              }
            }
          }

          // Upload to GCS
          try {
            await uploadFileToGCS(bucket, gcsDestination, uploadPath, mimeType);
            
            // Clean up temporary file
            if (fs.existsSync(uploadPath)) {
              fs.unlinkSync(uploadPath);
            }

            // Store GCS path in database (bukan signed URL, generate on-demand saat dibaca)
            fileUpdates[fieldname] = `https://storage.googleapis.com/${bucket}/${gcsDestination}`;
          } catch (uploadErr) {
            console.error(`Error uploading ${fieldname} to GCS:`, uploadErr.message);
            // Clean up temp file on error
            if (fs.existsSync(file.filepath)) {
              fs.unlinkSync(file.filepath);
            }
            throw uploadErr;
          }
        }

        if (Object.keys(fileUpdates).length === 0) {
          return res.status(400).json({ error: 'Tidak ada file yang berhasil diunggah' });
        }

        // Update database
        const updates = [];
        const values = [];
        let paramCount = 1;

        Object.entries(fileUpdates).forEach(([fieldname, filepath]) => {
          updates.push(`${fieldname} = $${paramCount++}`);
          values.push(filepath);
        });

        values.push(id);
        const updateQuery = `UPDATE monitoring_data_panen SET ${updates.join(', ')}, updated_at = NOW() WHERE id = $${paramCount} RETURNING id`;

        const result = await pool.query(updateQuery, values);

        if (result.rows.length === 0) {
          return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
        }

        return res.json({
          success: true,
          message: 'File berhasil diunggah',
          files: fileUpdates
        });
      } catch (error) {
        return res.status(500).json({ error: 'Processing error', details: error.message });
      }
    });
  } catch (error) {
    res.status(500).json({ error: 'Server error', details: error.message });
  }
};
