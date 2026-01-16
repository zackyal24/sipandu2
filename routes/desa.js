const express = require('express');
const router = express.Router();

// Import handlers (file-based untuk sementara)
const desaHandler = require('../controllers/desa');
const desaIdHandler = require('../controllers/desa/[id]');

// Desa routes
router.get('/', desaHandler);
router.get('/:id', desaIdHandler);

module.exports = router;
