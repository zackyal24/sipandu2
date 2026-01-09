const express = require('express');
const pool = require('../config/database');
const { verifyToken } = require('../middleware/auth');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

const router = express.Router();

// Configure multer for file uploads
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    let uploadPath = 'uploads/';
    if (file.fieldname === 'foto_serah_terima') uploadPath += 'serah_terima/';
    else if (file.fieldname === 'foto_bukti_plot_ubinan') uploadPath += 'bukti_plot_ubinan/';
    else if (file.fieldname === 'foto_berat_timbangan') uploadPath += 'berat_timbangan/';
    
    // Create directory if doesn't exist
    if (!fs.existsSync(uploadPath)) {
      fs.mkdirSync(uploadPath, { recursive: true });
    }
    cb(null, uploadPath);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, file.fieldname + '-' + uniqueSuffix + path.extname(file.originalname));
  }
});

const upload = multer({
  storage: storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB limit
  fileFilter: function (req, file, cb) {
    const allowedTypes = /jpeg|jpg|png/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);
    if (mimetype && extname) {
      return cb(null, true);
    } else {
      cb(new Error('Hanya file gambar (JPG, JPEG, PNG) yang diperbolehkan'));
    }
  }
});

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
    const isPml = role === 'pml';

    let query = `
      SELECT 
        m.id, 
        m.nama_petani, 
        m.desa, 
        m.kecamatan, 
        m.tanggal_panen, 
        m.subround,
        m.berat_plot,
        m.gkp,
        m.gkg,
        m.ku,
        m.status,
        m.nomor_segmen,
        m.nomor_sub_segmen,
        m.note_revisi,
        m.revised_at,
        m.created_at,
        m.user_id,
        u.nama_lengkap as pcl_name
      FROM monitoring_data_panen m
      LEFT JOIN users u ON m.user_id = u.id
    `;
    let params = [];

    if (isPcl) {
      query += ' WHERE m.user_id = $1';
      params.push(id);
    } else if (isPml) {
      // PML hanya melihat data dari PCL yang diawasi (pml_id = id PML)
      query += ' WHERE u.pml_id = $1';
      params.push(id);
    }

    query += ' ORDER BY m.tanggal_panen DESC NULLS LAST, m.created_at DESC LIMIT 200';

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

// GET Kecamatan
router.get('/kecamatan', optionalAuth, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT id, nama_kecamatan FROM kecamatan ORDER BY nama_kecamatan ASC
    `);
    res.json({
      success: true,
      data: result.rows
    });
  } catch (error) {
    console.error('Error fetching kecamatan:', error);
    res.status(500).json({ error: 'Gagal mengambil data kecamatan' });
  }
});

// GET Desa by Kecamatan
router.get('/desa/:kecamatan_id', optionalAuth, async (req, res) => {
  try {
    const { kecamatan_id } = req.params;
    const result = await pool.query(`
      SELECT id, nama_desa FROM desa WHERE id_kecamatan = $1 ORDER BY nama_desa ASC
    `, [kecamatan_id]);
    res.json({
      success: true,
      data: result.rows
    });
  } catch (error) {
    console.error('Error fetching desa:', error);
    res.status(500).json({ error: 'Gagal mengambil data desa' });
  }
});

// GET Segmen
router.get('/segmen', optionalAuth, async (req, res) => {
  try {
    const result = await pool.query(`
      SELECT id, nomor_segmen FROM segmen ORDER BY nomor_segmen ASC
    `);
    res.json({
      success: true,
      data: result.rows
    });
  } catch (error) {
    console.error('Error fetching segmen:', error);
    res.status(500).json({ error: 'Gagal mengambil data segmen' });
  }
});

// POST Tambah Data Ubinan
router.post('/ubinan', requireAuth, async (req, res) => {
  try {
    const {
      nama_petani,
      desa,
      kecamatan,
      tanggal_panen,
      subround,
      nomor_segmen,
      nomor_sub_segmen,
      status
    } = req.body;

    if (!nama_petani || !desa || !kecamatan || !tanggal_panen || !subround || !nomor_segmen || !nomor_sub_segmen) {
      return res.status(400).json({ error: 'Data tidak lengkap' });
    }

    const result = await pool.query(`
      INSERT INTO monitoring_data_panen 
      (nama_petani, desa, kecamatan, tanggal_panen, subround, nomor_segmen, nomor_sub_segmen, user_id, status, foto_serah_terima, foto_bukti_plot_ubinan, foto_berat_timbangan, created_at)
      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, '', '', '', CURRENT_TIMESTAMP)
      RETURNING id, nama_petani, desa, kecamatan, tanggal_panen, subround, nomor_segmen, nomor_sub_segmen, status, created_at
    `, [nama_petani, desa, kecamatan, tanggal_panen, subround, nomor_segmen, nomor_sub_segmen, req.user.id, status || 'belum selesai']);

    res.status(201).json({
      success: true,
      message: 'Data ubinan berhasil ditambahkan',
      data: result.rows[0]
    });
  } catch (error) {
    console.error('Error creating ubinan:', error);
    res.status(500).json({ error: 'Gagal menambahkan data ubinan' });
  }
});

// GET Single Ubinan by ID
router.get('/ubinan/:id', requireAuth, async (req, res) => {
  try {
    const { id } = req.params;
    const { role, id: userId } = req.user;
    
    const result = await pool.query(`
      SELECT 
        id, nama_petani, desa, kecamatan, tanggal_panen, subround,
        berat_plot, gkp, gkg, ku, status,
        nomor_segmen, nomor_sub_segmen,
        foto_serah_terima, foto_bukti_plot_ubinan, foto_berat_timbangan,
        note_revisi, revised_at, created_at, user_id
      FROM monitoring_data_panen
      WHERE id = $1
    `, [id]);
    
    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
    }
    
    const ubinan = result.rows[0];
    
    if (role === 'pcl' && ubinan.user_id !== userId) {
      return res.status(403).json({ error: 'Anda tidak memiliki akses ke data ini' });
    }
    
    res.json({
      success: true,
      data: ubinan
    });
  } catch (error) {
    console.error('Error fetching ubinan by ID:', error);
    res.status(500).json({ error: 'Gagal mengambil data ubinan' });
  }
});

// PUT Update Ubinan dengan file upload
router.put('/ubinan/:id', requireAuth, upload.fields([
  { name: 'foto_serah_terima', maxCount: 1 },
  { name: 'foto_bukti_plot_ubinan', maxCount: 1 },
  { name: 'foto_berat_timbangan', maxCount: 1 }
]), async (req, res) => {
  try {
    const { id } = req.params;
    const { role, id: userId } = req.user;
    const { berat_plot, status: requestedStatus } = req.body;
    
    // Check if record exists and user has access
    const checkResult = await pool.query('SELECT user_id, status FROM monitoring_data_panen WHERE id = $1', [id]);
    
    if (checkResult.rows.length === 0) {
      return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
    }
    
    if (role === 'pcl' && checkResult.rows[0].user_id !== userId) {
      return res.status(403).json({ error: 'Anda tidak memiliki akses untuk mengubah data ini' });
    }

    // FSM status validation (with 'revisi' support)
    const currentStatus = (checkResult.rows[0].status || '').toLowerCase();
    const newStatus = (requestedStatus || 'selesai').toLowerCase();

    let allowedTargets = [];
    switch (currentStatus) {
      case 'belum':
        allowedTargets = ['belum', 'sudah', 'selesai', 'tidak bisa'];
        break;
      case 'sudah':
        allowedTargets = ['sudah', 'selesai'];
        break;
      case 'selesai':
        // Only PML/Supervisor can move 'selesai' -> 'revisi'
        if (req.user.role === 'pml' || req.user.role === 'supervisor') {
          allowedTargets = ['selesai', 'revisi'];
        } else {
          allowedTargets = ['selesai'];
        }
        break;
      case 'revisi':
        // PCL should be able to fix and submit back to 'selesai'
        allowedTargets = ['revisi', 'selesai'];
        break;
      case 'tidak bisa':
        allowedTargets = ['tidak bisa'];
        break;
      default:
        allowedTargets = ['belum', 'sudah', 'selesai', 'tidak bisa'];
    }
    if (!allowedTargets.includes(newStatus)) {
      return res.status(400).json({ error: `Transisi status tidak diizinkan dari '${currentStatus || '-'}' ke '${newStatus}'.` });
    }
    
    // Calculate gkp, gkg, ku
    const beratPlot = parseFloat(berat_plot);
    const gkp = (beratPlot / 100) / (6.25 / 10000);
    const gkg = gkp * 0.8602;
    const ku = gkg * 0.6274;
    
    // Build update query
    let updateFields = ['berat_plot = $1', 'gkp = $2', 'gkg = $3', 'ku = $4', 'status = $5', 'updated_at = CURRENT_TIMESTAMP'];
    let values = [beratPlot, gkp, gkg, ku, newStatus];
    let paramCounter = 6;
    
    // If resolving a revision (revisi -> selesai), clear revision note and timestamp
    if (currentStatus === 'revisi' && newStatus === 'selesai') {
      updateFields.push('note_revisi = NULL', 'revised_at = NULL');
    }

    // Handle file uploads
    if (req.files) {
      if (req.files.foto_serah_terima) {
        updateFields.push(`foto_serah_terima = $${paramCounter++}`);
        values.push(req.files.foto_serah_terima[0].path.replace(/\\/g, '/'));
      }
      if (req.files.foto_bukti_plot_ubinan) {
        updateFields.push(`foto_bukti_plot_ubinan = $${paramCounter++}`);
        values.push(req.files.foto_bukti_plot_ubinan[0].path.replace(/\\/g, '/'));
      }
      if (req.files.foto_berat_timbangan) {
        updateFields.push(`foto_berat_timbangan = $${paramCounter++}`);
        values.push(req.files.foto_berat_timbangan[0].path.replace(/\\/g, '/'));
      }
    }
    
    values.push(id);
    
    const query = `
      UPDATE monitoring_data_panen 
      SET ${updateFields.join(', ')}
      WHERE id = $${paramCounter}
      RETURNING id, nama_petani, desa, kecamatan, tanggal_panen, subround, berat_plot, gkp, gkg, ku, status, nomor_segmen, nomor_sub_segmen
    `;
    
    const result = await pool.query(query, values);
    
    res.json({
      success: true,
      message: 'Data monitoring berhasil diupdate',
      data: result.rows[0]
    });
  } catch (error) {
    console.error('Error updating ubinan:', error);
    res.status(500).json({ error: 'Gagal mengupdate data ubinan: ' + error.message });
  }
});

// POST Revisi data ubinan (PML/Supervisor only)
router.post('/ubinan/:id/revisi', requireAuth, async (req, res) => {
  try {
    const { id } = req.params;
    const { note_revisi } = req.body;
    const { role } = req.user;

    // Only PML and Supervisor can create revisions
    if (role !== 'pml' && role !== 'supervisor') {
      return res.status(403).json({ error: 'Hanya PML dan Supervisor yang dapat memberi revisi' });
    }

    if (!note_revisi || note_revisi.trim() === '') {
      return res.status(400).json({ error: 'Catatan revisi harus diisi' });
    }

    // Check if record exists and status is 'selesai'
    const checkResult = await pool.query('SELECT status FROM monitoring_data_panen WHERE id = $1', [id]);
    
    if (checkResult.rows.length === 0) {
      return res.status(404).json({ error: 'Data ubinan tidak ditemukan' });
    }

    const currentStatus = (checkResult.rows[0].status || '').toLowerCase();
    if (currentStatus !== 'selesai') {
      return res.status(400).json({ error: 'Hanya data dengan status "selesai" yang dapat direvisi' });
    }

    // Update status to 'revisi' and add note
    const result = await pool.query(`
      UPDATE monitoring_data_panen 
      SET status = 'revisi', note_revisi = $1, revised_at = CURRENT_TIMESTAMP
      WHERE id = $2
      RETURNING id, nama_petani, status, note_revisi, revised_at
    `, [note_revisi, id]);

    res.json({
      success: true,
      message: 'Revisi berhasil dikirim ke PCL',
      data: result.rows[0]
    });
  } catch (error) {
    console.error('Error creating revision:', error);
    res.status(500).json({ error: 'Gagal membuat revisi: ' + error.message });
  }
});

module.exports = router;
