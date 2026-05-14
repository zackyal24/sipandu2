const getPool = require('../../config/database');
const { verifyToken } = require('../../middleware/auth');
const { getFilePathFromUrl, getSignedUrl } = require('../../config/gcs');

module.exports = async (req, res) => {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,POST');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  if (req.method !== 'GET' && req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  // Verify token
  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'Token diperlukan' });
    }

    const jwt = require('jsonwebtoken');
    const user = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');

    const { role, id } = user;
    const pool = getPool();

    // POST - Tambah data ubinan baru (tanpa file)
    if (req.method === 'POST') {
      try {
        const { 
          nama_petani, 
          desa, 
          kecamatan, 
          tanggal_panen, 
          subround, 
          nomor_segmen, 
          nomor_sub_segmen, 
          status 
        } = req.body;

        // Validasi required fields
        if (!nama_petani || !desa || !kecamatan || !tanggal_panen || !subround || !nomor_segmen || !nomor_sub_segmen) {
          return res.status(400).json({ error: 'Semua field wajib diisi' });
        }

        // Kalkulasi GKP, GKG, KU dari berat_plot
        let gkp = 0, gkg = 0, ku = 0;
        if (req.body.berat_plot) {
          const berat_plot = parseFloat(req.body.berat_plot);
          gkp = (berat_plot / 100) / (6.25 / 10000);
          gkg = gkp * 0.8602;
          ku = gkg * 0.6274;
        }

        // Insert with status default 'sedang diperiksa'
        const insertResult = await pool.query(
          `INSERT INTO monitoring_data_panen 
           (nama_petani, desa, kecamatan, tanggal_panen, subround, nomor_segmen, nomor_sub_segmen, status, user_id, 
            berat_plot, gkp, gkg, ku, foto_penyampaian_uang, foto_ktp_petani, foto_timbangan_ubinan, foto_proses_ubinan, foto_plot_setelah_panen, created_at)
           VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, '', '', '', '', '', NOW())
           RETURNING id`,
          [nama_petani, desa, kecamatan, tanggal_panen, subround, nomor_segmen, nomor_sub_segmen, status || 'sedang diperiksa', user.id, req.body.berat_plot || 0, gkp, gkg, ku]
        );

        return res.json({
          success: true,
          message: 'Data ubinan berhasil ditambahkan',
          data: { id: insertResult.rows[0].id }
        });
      } catch (dbErr) {
        console.error('Database error:', dbErr);
        return res.status(500).json({ error: 'Database error', details: dbErr.message });
      }
    }

    // GET - Ambil data ubinan (dengan opsi filter tanggal/subround)
    const isPcl = role === 'pcl';
    const isPml = role === 'pml';

    let baseQuery = `
      SELECT 
        m.id, 
        m.nama_petani, 
        m.desa, 
        m.kecamatan, 
        m.tanggal_panen, 
        m.subround,
        m.berat_plot,
        m.gkp,
        m.gkg,
        m.ku,
        m.status,
        m.nomor_segmen,
        m.nomor_sub_segmen,
        m.note_revisi,
        m.revised_at,
        m.created_at,
        m.user_id,
        m.foto_penyampaian_uang,
        m.foto_ktp_petani,
        m.foto_timbangan_ubinan,
        m.foto_proses_ubinan,
        m.foto_plot_setelah_panen,
        u.nama_lengkap as pcl_name,
        u.no_hp
      FROM monitoring_data_panen m
      LEFT JOIN users u ON m.user_id = u.id
    `;

    const whereClauses = [];
    const params = [];

    if (isPcl) {
      whereClauses.push(`m.user_id = $${params.length + 1}`);
      params.push(user.id);
    } else if (isPml) {
      whereClauses.push(`u.pml_id = $${params.length + 1}`);
      params.push(user.id);
    }

    // Apply date/subround filters
    const { filterType } = req.query || {};
    if (filterType === 'monthly') {
      const month = parseInt(req.query.month, 10);
      const year = parseInt(req.query.year, 10);
      if (month && year) {
        whereClauses.push(`EXTRACT(MONTH FROM m.tanggal_panen) = $${params.length + 1}`);
        params.push(month);
        whereClauses.push(`EXTRACT(YEAR FROM m.tanggal_panen) = $${params.length + 1}`);
        params.push(year);
      }
    } else if (filterType === 'yearly') {
      const year = parseInt(req.query.year, 10);
      if (year) {
        whereClauses.push(`EXTRACT(YEAR FROM m.tanggal_panen) = $${params.length + 1}`);
        params.push(year);
      }
    } else if (filterType === 'subround') {
      const sr = parseInt(req.query.subround, 10);
      if (sr) {
        whereClauses.push(`m.subround = $${params.length + 1}`);
        params.push(sr);
        const year = parseInt(req.query.year, 10);
        if (year) {
          whereClauses.push(`EXTRACT(YEAR FROM m.tanggal_panen) = $${params.length + 1}`);
          params.push(year);
        }
      }
    } else if (filterType === 'daterange') {
      const startMonth = req.query.startMonth;
      const endMonth = req.query.endMonth;
      if (startMonth && endMonth) {
        const [startYear, startMo] = startMonth.split('-');
        const [endYear, endMo] = endMonth.split('-');
        const startDate = `${startYear}-${String(startMo).padStart(2, '0')}-01`;
        const endLastDay = new Date(parseInt(endYear), parseInt(endMo), 0).getDate();
        const endDate = `${endYear}-${String(endMo).padStart(2, '0')}-${String(endLastDay).padStart(2, '0')}`;
        whereClauses.push(`m.tanggal_panen >= $${params.length + 1}`);
        params.push(startDate);
        whereClauses.push(`m.tanggal_panen <= $${params.length + 1}`);
        params.push(endDate);
      }
    }

    let finalQuery = baseQuery;
    if (whereClauses.length) {
      finalQuery += ' WHERE ' + whereClauses.join(' AND ');
    }
    finalQuery += ' ORDER BY m.tanggal_panen DESC NULLS LAST, m.created_at DESC LIMIT 200';

    const result = await pool.query(finalQuery, params);
    
    // Skip signed URL generation untuk list view (hanya untuk detail view)
    // Signed URLs hanya di-generate di endpoint [id].js
    
    res.json({
      success: true,
      data: result.rows,
      count: result.rows.length
    });
  } catch (error) {
    console.error('Error fetching ubinan:', error);
    res.status(500).json({ error: 'Gagal mengambil data ubinan', details: error.message });
  }
};
