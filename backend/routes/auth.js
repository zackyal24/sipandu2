const express = require('express');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const pool = require('../config/database');

const router = express.Router();

// Login endpoint
router.post('/login', async (req, res) => {
  try {
    const { username, password } = req.body;

    // Validate input
    if (!username || !password) {
      return res.status(400).json({ error: 'Username dan password harus diisi' });
    }

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
    // Some existing hashes may use PHP's $2y$ prefix — normalize to $2b$ for bcrypt compatibility
    let storedHash = user.password || '';
    if (storedHash.startsWith('$2y$')) {
      storedHash = '$2b$' + storedHash.slice(4);
    }
    const passwordMatch = await bcrypt.compare(password, storedHash);
    if (!passwordMatch) {
      return res.status(401).json({ error: 'Username atau password salah' });
    }

    // Generate JWT token (optional, untuk keamanan lebih)
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
});

// Optional: Logout endpoint (mainly client-side)
router.post('/logout', (req, res) => {
  res.json({ success: true, message: 'Logout berhasil' });
});

module.exports = router;
