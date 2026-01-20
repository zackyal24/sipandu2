const getPool = require('../../config/database');

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,PUT,DELETE');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'Token diperlukan' });
    }

    const jwt = require('jsonwebtoken');
    const user = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');
    
    if (user.role !== 'pml' && user.role !== 'supervisor') {
      return res.status(403).json({ error: 'Akses ditolak' });
    }

    const pool = getPool();
    const id = req.query.id || req.params.id;

    // GET specific user
    if (req.method === 'GET') {
      const result = await pool.query(`
        SELECT 
          id, 
          username, 
          nama_lengkap, 
          no_hp,
          email,
          role,
          pml_id,
          created_at
        FROM users
        WHERE id = $1
      `, [id]);

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'User tidak ditemukan' });
      }

      return res.json({
        success: true,
        data: result.rows[0]
      });
    }

    // ...existing code...

    // DELETE user
    if (req.method === 'DELETE') {
      if (user.role !== 'supervisor') {
        return res.status(403).json({ error: 'Hanya supervisor yang dapat menghapus user' });
      }

      if (parseInt(id) === user.id) {
        return res.status(400).json({ error: 'Tidak bisa menghapus akun sendiri' });
      }

      const result = await pool.query('DELETE FROM users WHERE id = $1 RETURNING id', [id]);

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'User tidak ditemukan' });
      }

      return res.json({
        success: true,
        message: 'User berhasil dihapus'
      });
    }

    return res.status(405).json({ error: 'Method not allowed' });

  } catch (error) {
    console.error('Error:', error);
    res.status(500).json({ error: 'Server error', details: error.message });
  }
};
