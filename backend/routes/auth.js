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

// Change password endpoint
router.post('/change-password', async (req, res) => {
  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'Token diperlukan' });
    }

    let decoded;
    try {
      decoded = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');
    } catch (err) {
      return res.status(401).json({ error: 'Token invalid atau expired' });
    }

    const { password_lama, password_baru } = req.body;

    if (!password_lama || !password_baru) {
      return res.status(400).json({ error: 'Password lama dan password baru harus diisi' });
    }

    if (password_baru.length < 6) {
      return res.status(400).json({ error: 'Password baru minimal 6 karakter' });
    }

    // Get user dari database
    const userResult = await pool.query(
      'SELECT id, password FROM users WHERE id = $1',
      [decoded.id]
    );

    if (userResult.rows.length === 0) {
      return res.status(404).json({ error: 'User tidak ditemukan' });
    }

    const user = userResult.rows[0];

    // Verify old password
    let storedHash = user.password || '';
    if (storedHash.startsWith('$2y$')) {
      storedHash = '$2b$' + storedHash.slice(4);
    }
    const passwordMatch = await bcrypt.compare(password_lama, storedHash);
    if (!passwordMatch) {
      return res.status(401).json({ error: 'Password lama tidak cocok' });
    }

    // Hash new password
    const hashedPassword = await bcrypt.hash(password_baru, 10);

    // Update password di database
    await pool.query(
      'UPDATE users SET password = $1 WHERE id = $2',
      [hashedPassword, decoded.id]
    );

    res.json({
      success: true,
      message: 'Password berhasil diubah'
    });
  } catch (error) {
    console.error('Change password error:', error);
    res.status(500).json({ error: 'Gagal mengubah password', details: error.message });
  }
});

module.exports = router;
