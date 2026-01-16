const getPool = require('../config/database');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcrypt');

// Login
exports.login = async (req, res) => {
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST,OPTIONS');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version'
  );

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  const { username, password } = req.body;

  if (!username || !password) {
    return res.status(400).json({ error: 'Username dan password diperlukan' });
  }

  try {
    const pool = getPool();
    const result = await pool.query(
      'SELECT * FROM users WHERE username = $1',
      [username]
    );

    if (result.rows.length === 0) {
      return res.status(401).json({ error: 'Username atau password salah' });
    }

    const user = result.rows[0];
    const validPassword = await bcrypt.compare(password, user.password);

    if (!validPassword) {
      return res.status(401).json({ error: 'Username atau password salah' });
    }

    const token = jwt.sign(
      { 
        id: user.id, 
        username: user.username, 
        role: user.role,
        pml_id: user.pml_id
      },
      process.env.JWT_SECRET || 'your-secret-key',
      { expiresIn: '24h' }
    );

    res.json({
      success: true,
      token,
      user: {
        id: user.id,
        username: user.username,
        nama_lengkap: user.nama_lengkap,
        role: user.role,
        pml_id: user.pml_id
      }
    });
  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({ error: 'Terjadi kesalahan server', details: error.message });
  }
};

// Logout
exports.logout = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.json({ success: true, message: 'Logout berhasil' });
};

// Change Password
exports.changePassword = async (req, res) => {
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST,OPTIONS');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'Token diperlukan' });
    }

    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');
    const { password_lama, password_baru } = req.body;

    if (!password_lama || !password_baru) {
      return res.status(400).json({ error: 'Password lama dan baru diperlukan' });
    }

    if (password_baru.length < 6) {
      return res.status(400).json({ error: 'Password baru minimal 6 karakter' });
    }

    const pool = getPool();
    const result = await pool.query('SELECT * FROM users WHERE id = $1', [decoded.id]);

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'User tidak ditemukan' });
    }

    const user = result.rows[0];
    let storedHash = user.password || '';
    if (storedHash.startsWith('$2y$')) {
      storedHash = '$2b$' + storedHash.slice(4);
    }
    const validPassword = await bcrypt.compare(password_lama, storedHash);

    if (!validPassword) {
      return res.status(401).json({ error: 'Password lama salah' });
    }

    const hashedPassword = await bcrypt.hash(password_baru, 10);
    await pool.query('UPDATE users SET password = $1 WHERE id = $2', [hashedPassword, decoded.id]);

    res.json({ success: true, message: 'Password berhasil diubah' });
  } catch (error) {
    console.error('Change password error:', error);
    res.status(500).json({ error: 'Terjadi kesalahan server' });
  }
};
