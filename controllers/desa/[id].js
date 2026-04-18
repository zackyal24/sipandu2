const getPool = require('../../config/database');

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    // Support both req.params (Express route) dan req.query (query string) untuk fleksibilitas
    const id = req.query.id || req.params.id;
    const pool = getPool();

    const result = await pool.query(`
      SELECT id, nama_desa FROM desa WHERE id_kecamatan = $1 ORDER BY nama_desa ASC
    `, [id]);
    
    res.json({
      success: true,
      data: result.rows
    });
  } catch (error) {
    console.error('Error fetching desa:', error);
    res.status(500).json({ error: 'Gagal mengambil data desa', details: error.message });
  }
};
