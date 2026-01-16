const getPool = require('../config/database');

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const pool = getPool();

    const result = await pool.query(`
      SELECT id, nama_kecamatan FROM kecamatan ORDER BY nama_kecamatan ASC
    `);
    
    res.json({
      success: true,
      data: result.rows
    });
  } catch (error) {
    console.error('Error fetching kecamatan:', error);
    res.status(500).json({ error: 'Gagal mengambil data kecamatan' });
  }
};
