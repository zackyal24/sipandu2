const express = require('express');
const router = express.Router();

// Import all route modules
const authRoutes = require('./auth');
const usersRoutes = require('./users');
const ubinanRoutes = require('./ubinan');
const desaRoutes = require('./desa');
const kecamatanRoutes = require('./kecamatan');
const segmenRoutes = require('./segmen');

// Mount routes
router.use('/auth', authRoutes);
router.use('/users', usersRoutes);
router.use('/ubinan', ubinanRoutes);
router.use('/desa', desaRoutes);
router.use('/kecamatan', kecamatanRoutes);
router.use('/segmen', segmenRoutes);

module.exports = router;
