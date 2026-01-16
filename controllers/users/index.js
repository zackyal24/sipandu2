const getPool = require('../../config/database');

module.exports = async (req, res) => {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,POST,PUT,DELETE');
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

    const pool = getPool();

    // GET - List users
    if (req.method === 'GET') {
      if (user.role !== 'pml' && user.role !== 'supervisor') {
        return res.status(403).json({ error: 'Akses ditolak' });
      }

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
        ORDER BY created_at DESC
      `);

      return res.json({
        success: true,
        data: result.rows,
        count: result.rows.length
      });
    }

    // POST - Create user
    if (req.method === 'POST') {
      if (user.role !== 'supervisor') {
        return res.status(403).json({ error: 'Hanya supervisor yang dapat menambah user' });
      }

      const { username, nama_lengkap, email, no_hp, password, role, pml_id } = req.body;

      if (!username || !password || !nama_lengkap || !email || !no_hp || !role) {
        return res.status(400).json({ error: 'Semua field harus diisi' });
      }

      if (role.toLowerCase() === 'pcl' && !pml_id) {
        return res.status(400).json({ error: 'PCL harus memiliki PML pengawas' });
      }

      if (password.length < 6) {
        return res.status(400).json({ error: 'Password minimal 6 karakter' });
      }

      const checkUser = await pool.query('SELECT id FROM users WHERE username = $1', [username]);
      if (checkUser.rows.length > 0) {
        return res.status(400).json({ error: 'Username sudah digunakan' });
      }

      const bcrypt = require('bcrypt');
      const hashedPassword = await bcrypt.hash(password, 10);

      let query, params;
      if (role.toLowerCase() === 'pcl' && pml_id) {
        query = `INSERT INTO users (username, password, nama_lengkap, no_hp, email, role, pml_id, created_at)
                 VALUES ($1, $2, $3, $4, $5, $6, $7, NOW())
                 RETURNING id, username, nama_lengkap, no_hp, email, role, pml_id, created_at`;
        params = [username, hashedPassword, nama_lengkap, no_hp, email, role.toLowerCase(), pml_id];
      } else {
        query = `INSERT INTO users (username, password, nama_lengkap, no_hp, email, role, created_at)
                 VALUES ($1, $2, $3, $4, $5, $6, NOW())
                 RETURNING id, username, nama_lengkap, no_hp, email, role, created_at`;
        params = [username, hashedPassword, nama_lengkap, no_hp, email, role.toLowerCase()];
      }

      const result = await pool.query(query, params);

      return res.json({
        success: true,
        message: 'User berhasil ditambahkan',
        data: result.rows[0]
      });
    }

    return res.status(405).json({ error: 'Method not allowed' });

  } catch (error) {
    console.error('Error:', error);
    res.status(500).json({ error: 'Server error', details: error.message });
  }
};
