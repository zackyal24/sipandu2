const express = require('express');
const pool = require('../config/database');
const { verifyToken } = require('../middleware/auth');

const router = express.Router();

// Middleware untuk verify JWT (optional, untuk keamanan)
const optionalAuth = async (req, res, next) => {
  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (token) {
      const decoded = require('jsonwebtoken').verify(token, process.env.JWT_SECRET || 'your-secret-key');
      req.user = decoded;
    }
  } catch (error) {
    // Token invalid, lanjut tanpa user
  }
  next();
};

// Middleware untuk require auth
const requireAuth = (req, res, next) => {
  try {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) {
      return res.status(401).json({ error: 'Token diperlukan' });
    }
    const decoded = require('jsonwebtoken').verify(token, process.env.JWT_SECRET || 'your-secret-key');
    req.user = decoded;
    next();
  } catch (error) {
    return res.status(401).json({ error: 'Token invalid atau expired' });
  }
};

// GET Data Ubinan (monitoring_data_panen)
router.get('/ubinan', requireAuth, async (req, res) => {
  try {
    const { role, id } = req.user;
    const isPcl = role === 'pcl';

    let query = `
      SELECT 
        id, 
        nama_petani, 
        desa, 
        kecamatan, 
        tanggal_panen, 
        subround,
        berat_plot,
        status,
        nomor_segmen,
        nomor_sub_segmen,
        note_revisi,
        revised_at,
        created_at
      FROM monitoring_data_panen
    `;
    let params = [];

    if (isPcl) {
      query += ' WHERE user_id = $1';
      params.push(id);
    }

    query += ' ORDER BY tanggal_panen DESC NULLS LAST, created_at DESC LIMIT 200';

    const result = await pool.query(query, params);
    
    res.json({
      success: true,
      data: result.rows,
      count: result.rows.length
    });
  } catch (error) {
    console.error('Error fetching ubinan:', error);
    res.status(500).json({ error: 'Gagal mengambil data ubinan' });
  }
});

// GET Users (pml/supervisor only)
router.get('/users', requireAuth, async (req, res) => {
  try {
    // Check role
    if (req.user.role !== 'pml' && req.user.role !== 'supervisor') {
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
        created_at
      FROM users
      ORDER BY created_at DESC
    `);

    res.json({
      success: true,
      data: result.rows,
      count: result.rows.length
    });
  } catch (error) {
    console.error('Error fetching users:', error);
    res.status(500).json({ error: 'Gagal mengambil data users' });
  }
});

// GET Dashboard stats
router.get('/dashboard/stats', optionalAuth, async (req, res) => {
  try {
    const ubinanResult = await pool.query(`
      SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as completed,
        AVG(berat_plot) as average_weight
      FROM monitoring_data_panen
    `);

    const usersResult = await pool.query(`
      SELECT COUNT(*) as total FROM users
    `);

    res.json({
      success: true,
      data: {
        ubinan: ubinanResult.rows[0],
        users: usersResult.rows[0]
      }
    });
  } catch (error) {
    console.error('Error fetching stats:', error);
    res.status(500).json({ error: 'Gagal mengambil statistik' });
  }
});

module.exports = router;
