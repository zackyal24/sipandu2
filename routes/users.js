const express = require('express');
const router = express.Router();
const userController = require('../controllers/userController');

// User routes
router.get('/', userController.listOrCreate);
router.post('/', userController.listOrCreate);

router.get('/:id', userController.getOrUpdateOrDelete);
router.put('/:id', userController.getOrUpdateOrDelete);
router.delete('/:id', userController.getOrUpdateOrDelete);

module.exports = router;
