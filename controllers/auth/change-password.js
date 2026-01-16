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

  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ error: 'Method not allowed' });
  }

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

    const pool = getPool();

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
};
