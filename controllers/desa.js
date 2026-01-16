const getPool = require('../config/database');

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const { kecamatan_id } = req.query;
    const pool = getPool();

    const result = await pool.query(`
      SELECT id, nama_desa FROM desa WHERE kecamatan_id = $1 ORDER BY nama_desa ASC
    `, [kecamatan_id]);
    
    res.json({
      success: true,
      data: result.rows
    });
  } catch (error) {
    console.error('Error fetching desa:', error);
    res.status(500).json({ error: 'Gagal mengambil data desa' });
  }
};
