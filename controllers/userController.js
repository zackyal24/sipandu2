const getPool = require('../config/database');

// Get all users or create new user
exports.listOrCreate = async (req, res) => {
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,POST');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
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
          id, username, nama_lengkap, no_hp, email, role, pml_id, created_at
        FROM users
        ORDER BY created_at DESC
      `);

      return res.json({ success: true, data: result.rows });
    }

    // POST - Create user
    if (req.method === 'POST') {
      if (user.role !== 'pml' && user.role !== 'supervisor') {
        return res.status(403).json({ error: 'Akses ditolak' });
      }

      const { username, password, nama_lengkap, no_hp, email, role, pml_id } = req.body;

      if (!username || !password || !nama_lengkap || !role) {
        return res.status(400).json({ error: 'Field yang diperlukan tidak lengkap' });
      }

      const bcrypt = require('bcrypt');
      const hashedPassword = await bcrypt.hash(password, 10);

      const result = await pool.query(
        `INSERT INTO users (username, password, nama_lengkap, no_hp, email, role, pml_id) 
         VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING id, username, nama_lengkap, role`,
        [username, hashedPassword, nama_lengkap, no_hp, email, role, pml_id]
      );

      return res.status(201).json({ success: true, data: result.rows[0] });
    }
  } catch (error) {
    console.error('Users error:', error);
    res.status(500).json({ error: 'Terjadi kesalahan server', details: error.message });
  }
};

// Get, update, or delete specific user
exports.getOrUpdateOrDelete = async (req, res) => {
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,PUT,DELETE');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
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
    const id = req.params.id;

    // GET specific user
    if (req.method === 'GET') {
      const result = await pool.query(
        `SELECT id, username, nama_lengkap, no_hp, email, role, pml_id, created_at
         FROM users WHERE id = $1`,
        [id]
      );

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'User tidak ditemukan' });
      }

      return res.json({ success: true, data: result.rows[0] });
    }

    // PUT - Update user
    if (req.method === 'PUT') {
      const { nama_lengkap, no_hp, email, role, pml_id } = req.body;

      const result = await pool.query(
        `UPDATE users 
         SET nama_lengkap = COALESCE($1, nama_lengkap),
             no_hp = COALESCE($2, no_hp),
             email = COALESCE($3, email),
             role = COALESCE($4, role),
             pml_id = COALESCE($5, pml_id)
         WHERE id = $6
         RETURNING id, username, nama_lengkap, no_hp, email, role, pml_id`,
        [nama_lengkap, no_hp, email, role, pml_id, id]
      );

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'User tidak ditemukan' });
      }

      return res.json({ success: true, data: result.rows[0] });
    }

    // DELETE user
    if (req.method === 'DELETE') {
      const result = await pool.query('DELETE FROM users WHERE id = $1 RETURNING id', [id]);

      if (result.rows.length === 0) {
        return res.status(404).json({ error: 'User tidak ditemukan' });
      }

      return res.json({ success: true, message: 'User berhasil dihapus' });
    }
  } catch (error) {
    console.error('User operation error:', error);
    res.status(500).json({ error: 'Terjadi kesalahan server', details: error.message });
  }
};
