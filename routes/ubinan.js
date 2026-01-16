const express = require('express');
const router = express.Router();

// Import handlers (file-based untuk sementara)
const ubinanHandler = require('../controllers/ubinan/index');
const ubinanIdHandler = require('../controllers/ubinan/[id]');
const ubinanIdUploadHandler = require('../controllers/ubinan/[id]/upload');
const ubinanRevisiHandler = require('../controllers/ubinan/[id]/revisi');
const ubinanExportHandler = require('../controllers/ubinan/export');

// Ubinan routes
router.get('/', ubinanHandler);
router.post('/', ubinanHandler);

router.get('/export', ubinanExportHandler);

router.get('/:id', ubinanIdHandler);
router.put('/:id', ubinanIdHandler);
router.delete('/:id', ubinanIdHandler);

router.post('/:id/upload', ubinanIdUploadHandler);
router.post('/:id/revisi', ubinanRevisiHandler);

module.exports = router;
