const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const getPool = require('../../config/database');

module.exports = async (req, res) => {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Credentials', 'true');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,PATCH,DELETE,POST,PUT');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Authorization'
  );

  // Handle preflight
  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

  try {
    const { username, password } = req.body;

    // Validate input
    if (!username || !password) {
      return res.status(400).json({ error: 'Username dan password harus diisi' });
    }

    const pool = getPool();

    // Get user dari database
    const userResult = await pool.query(
      'SELECT id, username, password, nama_lengkap, role FROM users WHERE username = $1',
      [username]
    );

    if (userResult.rows.length === 0) {
      return res.status(401).json({ error: 'Username atau password salah' });
    }

    const user = userResult.rows[0];

    // Verify password
    let storedHash = user.password || '';
    if (storedHash.startsWith('$2y$')) {
      storedHash = '$2b$' + storedHash.slice(4);
    }
    const passwordMatch = await bcrypt.compare(password, storedHash);
    if (!passwordMatch) {
      return res.status(401).json({ error: 'Username atau password salah' });
    }

    // Generate JWT token
    const token = jwt.sign(
      { id: user.id, username: user.username, role: user.role },
      process.env.JWT_SECRET || 'your-secret-key',
      { expiresIn: '24h' }
    );

    // Return user data & token
    res.json({
      success: true,
      user: {
        id: user.id,
        username: user.username,
        name: user.nama_lengkap,
        role: user.role
      },
      token: token
    });

  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({ error: 'Login gagal', details: error.message });
  }
};
