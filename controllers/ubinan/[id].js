const getPool = require('../../config/database');
const { deleteFileFromGCS, getFilePathFromUrl, getSignedUrl } = require('../../config/gcs');

module.exports = async (req, res) => {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'DELETE,OPTIONS,GET,PUT');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  if (req.method !== 'DELETE' && req.method !== 'GET' && req.method !== 'PUT') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'Token diperlukan' });
    }

    const jwt = require('jsonwebtoken');
    const user = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');
    const { role } = user;

    const pool = getPool();
    const id = req.query.id || req.params.id;

    // GET - Ambil detail ubinan
    if (req.method === 'GET') {
      const result = await pool.query(
        'SELECT * FROM monitoring_data_panen WHERE id = $1',
        [id]
      );
      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
      }

      const data = result.rows[0];
      const bucket = process.env.GCS_BUCKET;

      // Generate signed URLs for photos (valid 1 hour)
      if (bucket) {
        const photoFields = ['foto_serah_terima', 'foto_bukti_plot_ubinan', 'foto_berat_timbangan'];
        for (const field of photoFields) {
          if (data[field]) {
            const gcsPath = getFilePathFromUrl(data[field]);
            if (gcsPath) {
              try {
                data[`${field}_url`] = await getSignedUrl(bucket, gcsPath, 3600);
              } catch (err) {
                console.error(`Error generating signed URL for ${field}:`, err.message);
              }
            }
          }
        }
      }

      return res.json({
        success: true,
        data
      });
    }

    // PUT - Update data ubinan
    if (req.method === 'PUT') {
      try {
        const data = req.body;
        const { berat_plot, status } = data;

        // Ambil status lama dari database
        const oldStatusResult = await pool.query(
          'SELECT status FROM monitoring_data_panen WHERE id = $1',
          [id]
        );
        if (oldStatusResult.rows.length === 0) {
          return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
        }
        const oldStatus = (oldStatusResult.rows[0].status || '').toLowerCase();
        const newStatus = (status || 'selesai').toLowerCase();

        // Validasi berat_plot hanya wajib jika status baru 'selesai' atau 'sudah'
        if ((newStatus === 'selesai' || newStatus === 'sudah') && !berat_plot) {
          return res.status(400).json({ error: 'berat_plot harus diisi jika status selesai/sudah' });
        }


        // Definisi transisi FSM
        const validTransitions = {
          'belum': ['selesai', 'tidak bisa'],
          'sudah': ['selesai'],
          'selesai': ['revisi'],
          'revisi': ['selesai'],
          'tidak bisa': []
        };

        // Validasi transisi
        if (oldStatus !== newStatus) {
          const allowed = validTransitions[oldStatus] || [];
          if (!allowed.includes(newStatus)) {
            return res.status(400).json({
              error: `Transisi status dari '${oldStatus}' ke '${newStatus}' tidak diizinkan.`
            });
          }
        }


        // Kalkulasi GKP, GKG, KU dari berat_plot jika ada
        let berat_plot_num = null, gkp = null, gkg = null, ku = null;
        if (berat_plot) {
          berat_plot_num = parseFloat(berat_plot);
          gkp = (berat_plot_num / 100) / (6.25 / 10000);
          gkg = gkp * 0.8602;
          ku = gkg * 0.6274;
        }

        const shouldClearRevisi = newStatus === 'selesai';

        const updateResult = await pool.query(
          `UPDATE monitoring_data_panen 
           SET berat_plot = $1, gkp = $2, gkg = $3, ku = $4, status = $5,
               note_revisi = CASE WHEN $6 THEN NULL ELSE note_revisi END,
               revised_at = CASE WHEN $6 THEN NULL ELSE revised_at END,
               updated_at = NOW()
           WHERE id = $7
           RETURNING id`,
          [berat_plot_num, gkp, gkg, ku, newStatus, shouldClearRevisi, id]
        );

        if (updateResult.rows.length === 0) {
          return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
        }

        return res.json({
          success: true,
          message: 'Data ubinan berhasil diperbarui',
          data: { id: updateResult.rows[0].id }
        });
      } catch (dbErr) {
        console.error('Database error:', dbErr);
        return res.status(500).json({ error: 'Database error', details: dbErr.message });
      }
    }

    // DELETE - Hanya Supervisor dan PML yang bisa delete
    if (role !== 'supervisor' && role !== 'pml') {
      return res.status(403).json({ error: 'Hanya Supervisor dan PML yang dapat menghapus data' });
    }

    // Get the record with file names
    const checkResult = await pool.query(
      'SELECT foto_serah_terima, foto_bukti_plot_ubinan, foto_berat_timbangan FROM monitoring_data_panen WHERE id = $1',
      [id]
    );

    if (checkResult.rows.length === 0) {
      return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
    }

    const record = checkResult.rows[0];
    const bucket = process.env.GCS_BUCKET;

    // Delete associated image files from GCS
    const fileFields = [
      record.foto_serah_terima,
      record.foto_bukti_plot_ubinan,
      record.foto_berat_timbangan
    ];

    if (bucket) {
      for (const fileUrl of fileFields) {
        if (fileUrl) {
          const gcsPath = getFilePathFromUrl(fileUrl);
          if (gcsPath) {
            try {
              await deleteFileFromGCS(bucket, gcsPath);
              console.log(`Deleted from GCS: ${gcsPath}`);
            } catch (fileError) {
              console.error(`Error deleting file from GCS ${gcsPath}:`, fileError.message);
            }
          }
        }
      }
    }

    // Delete the record from database
    await pool.query('DELETE FROM monitoring_data_panen WHERE id = $1', [id]);

    return res.json({
      success: true,
      message: 'Data ubinan dan file gambar berhasil dihapus'
    });
  } catch (error) {
    console.error('Error deleting ubinan:', error);
    return res.status(500).json({ error: 'Gagal menghapus data ubinan', details: error.message });
  }
};
