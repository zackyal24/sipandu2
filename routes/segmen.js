const express = require('express');
const router = express.Router();

// Import handler (file-based untuk sementara)
const segmenHandler = require('../controllers/segmen');

// Segmen routes
router.get('/', segmenHandler);
router.post('/', segmenHandler);
router.post('/add', segmenHandler);  // POST /segmen/add untuk tambah single
router.delete('/', segmenHandler);
router.delete('/:id', segmenHandler);  // DELETE /segmen/:id untuk hapus single
router.delete('/all/delete', segmenHandler);  // DELETE /segmen/all/delete untuk hapus semua
router.post('/import', segmenHandler);  // POST /segmen/import untuk import file

module.exports = router;
