const express = require('express');
const router = express.Router();

// Import handler (file-based untuk sementara)
const kecamatanHandler = require('../controllers/kecamatan');

// Kecamatan routes
router.get('/', kecamatanHandler);

module.exports = router;
