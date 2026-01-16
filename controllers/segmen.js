const getPool = require('../config/database');
const fs = require('fs');
const path = require('path');
const { IncomingForm } = require('formidable');
const XLSX = require('xlsx');

module.exports = async (req, res) => {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,POST,DELETE');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  try {
    const pool = getPool();

    // GET - Ambil semua segmen
    if (req.method === 'GET') {
      const result = await pool.query('SELECT id, nomor_segmen FROM segmen ORDER BY nomor_segmen');
      return res.json({
        success: true,
        data: result.rows,
        count: result.rows.length
      });
    }

    // POST - Tambah segmen atau Import file
    if (req.method === 'POST') {
      // CEK IMPORT DULU - jika ada file upload (multipart/form-data)
      if (req.headers['content-type']?.includes('multipart/form-data')) {
        const form = new IncomingForm();
        
        form.parse(req, async (err, fields, files) => {
          if (err) {
            return res.status(400).json({ error: 'File parsing error', details: err.message });
          }

          const file = files.file?.[0] || files.file;
          if (!file) {
            return res.status(400).json({ error: 'No file uploaded' });
          }

          try {
            const filePath = file.filepath;
            const fileName = file.originalFilename || file.filename || '';
            let segments = [];

            // Parse CSV
            if (fileName.endsWith('.csv')) {
              const content = fs.readFileSync(filePath, 'utf-8');
              const lines = content.split('\n').filter(line => line.trim());
              
              for (let i = 0; i < lines.length; i++) {
                const nomor = lines[i].trim();
                // Skip header row
                if (i === 0 && (nomor.toLowerCase() === 'nomor_segmen' || nomor.toLowerCase() === 'nomor')) {
                  continue;
                }
                if (nomor && nomor !== 'nomor_segmen') {
                  segments.push(nomor);
                }
              }
            }
            // Parse Excel
            else if (fileName.endsWith('.xlsx') || fileName.endsWith('.xls')) {
              const workbook = XLSX.readFile(filePath);
              const sheet = workbook.Sheets[workbook.SheetNames[0]];
              const data = XLSX.utils.sheet_to_json(sheet, { header: 1 });
              
              for (let i = 0; i < data.length; i++) {
                const row = data[i];
                const nomor = row?.[0];
                // Skip header row
                if (i === 0 && (nomor?.toLowerCase?.() === 'nomor_segmen' || nomor?.toLowerCase?.() === 'nomor')) {
                  continue;
                }
                if (nomor && nomor.toString().trim() && nomor.toString().toLowerCase() !== 'nomor_segmen') {
                  segments.push(nomor.toString().trim());
                }
              }
            } else {
              return res.status(400).json({ error: 'Format file harus CSV atau Excel (.xlsx, .xls)' });
            }

            // Clean up temp file
            fs.unlinkSync(filePath);

            if (segments.length === 0) {
              return res.status(400).json({ error: 'Tidak ada data segmen ditemukan di file' });
            }

            // Insert segments
            let inserted = 0;
            let skipped = 0;

            for (const seg of segments) {
              try {
                const result = await pool.query(
                  'INSERT INTO segmen (nomor_segmen) VALUES ($1) ON CONFLICT (nomor_segmen) DO NOTHING RETURNING id',
                  [String(seg)]
                );
                
                if (result.rows.length > 0) {
                  inserted++;
                } else {
                  skipped++;
                }
              } catch (rowErr) {
                console.error(`Error inserting segment ${seg}:`, rowErr.message);
              }
            }

            return res.json({
              success: true,
              message: `Import berhasil (${inserted} ditambahkan, ${skipped} duplikat)`,
              inserted,
              skipped,
              total: segments.length
            });

          } catch (importErr) {
            console.error('Import error:', importErr);
            return res.status(500).json({ error: 'Import gagal', details: importErr.message });
          }
        });
        return;
      }

      // TAMBAH SEGMEN SINGLE - JSON body
      const { nomor_segmen, segments } = req.body;
      
      let segmentsToAdd = [];
      
      // Support single nomor_segmen (dari frontend form)
      if (nomor_segmen) {
        if (typeof nomor_segmen !== 'string' || nomor_segmen.trim() === '') {
          return res.status(400).json({ error: 'Nomor segmen harus berupa string non-kosong' });
        }
        segmentsToAdd = [nomor_segmen.trim()];
      }
      // Support bulk segments array (dari import)
      else if (segments && Array.isArray(segments) && segments.length > 0) {
        segmentsToAdd = segments;
      }
      else {
        return res.status(400).json({ error: 'Nomor segmen atau segments array diperlukan' });
      }

      try {
        let inserted = 0;
        let skipped = 0;
        
        // Insert satu per satu
        for (const seg of segmentsToAdd) {
          try {
            const result = await pool.query(
              'INSERT INTO segmen (nomor_segmen) VALUES ($1) ON CONFLICT (nomor_segmen) DO NOTHING RETURNING id',
              [String(seg)]
            );
            
            if (result.rows.length > 0) {
              inserted++;
            } else {
              skipped++;
            }
          } catch (rowErr) {
            console.error(`Error inserting segment ${seg}:`, rowErr.message);
          }
        }
        
        return res.json({
          success: true,
          message: `Segmen berhasil diproses (${inserted} ditambahkan, ${skipped} duplikat)`,
          inserted,
          skipped
        });
      } catch (dbErr) {
        console.error('[POST /segmen ERROR]', dbErr);
        return res.status(500).json({ error: 'Database error', details: dbErr.message });
      }
    }

    // DELETE - Hapus segmen tertentu atau semua
    if (req.method === 'DELETE') {
      const id = req.params?.id || req.query?.id;
      
      if (id) {
        // DELETE /segmen/:id atau /segmen?id=xxx
        await pool.query('DELETE FROM segmen WHERE id = $1', [id]);
        return res.json({ success: true, message: 'Segmen berhasil dihapus' });
      } else {
        // DELETE /segmen atau /segmen/all/delete (hapus semua)
        const result = await pool.query('DELETE FROM segmen');
        return res.json({ 
          success: true, 
          message: 'Semua segmen berhasil dihapus',
          deletedCount: result.rowCount
        });
      }
    }

    // IMPORT - Import dari CSV/Excel file (SUDAH DIHANDLE DI ATAS PADA if POST multipart check)
    
    return res.status(405).json({ error: 'Method not allowed' });

  } catch (error) {
    console.error('Segmen error:', error);
    return res.status(500).json({ error: 'Server error', details: error.message });
  }
};
