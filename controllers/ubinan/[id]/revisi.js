const getPool = require('../../../config/database');

module.exports = async (req, res) => {
  // Basic CORS headers
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
    res.status(405).json({ error: 'Method not allowed' });
    return;
  }

  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'Token diperlukan' });
    }

    const jwt = require('jsonwebtoken');
    const user = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');
    const { role } = user;

    // Hanya supervisor atau pml yang boleh kirim revisi
    if (role !== 'supervisor' && role !== 'pml') {
      return res.status(403).json({ error: 'Tidak diizinkan mengirim revisi' });
    }

    const { note_revisi } = req.body || {};
    if (!note_revisi || !note_revisi.trim()) {
      return res.status(400).json({ error: 'Catatan revisi wajib diisi' });
    }

    const pool = getPool();
    const id = req.query.id || req.params.id;

    const updateResult = await pool.query(
      `UPDATE monitoring_data_panen
         SET note_revisi = $1,
             status = 'revisi',
             revised_at = NOW(),
             updated_at = NOW()
       WHERE id = $2
       RETURNING id`,
      [note_revisi.trim(), id]
    );

    if (updateResult.rows.length === 0) {
      return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
    }

    return res.json({
      success: true,
      message: 'Revisi berhasil dikirim',
      data: { id: updateResult.rows[0].id }
    });
  } catch (error) {
    console.error('Error submit revisi:', error);
    return res.status(500).json({ error: 'Gagal mengirim revisi', details: error.message });
  }
};
