require('dotenv').config();
const express = require('express');
const cors = require('cors');
const authRoutes = require('./routes/auth');
const dataRoutes = require('./routes/data');

const app = express();

// Middleware
// Allow CORS from any origin in development so frontend served from file:// or localhost can connect
if (process.env.NODE_ENV === 'development') {
  app.use(cors());
} else {
  app.use(cors({ origin: ['http://localhost:3000', 'http://localhost:5173'], credentials: true }));
}
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Routes
app.use('/api/auth', authRoutes);
app.use('/api', dataRoutes);

// Health check
app.get('/api/health', (req, res) => {
  res.json({ status: 'OK', timestamp: new Date().toISOString() });
});

// Error handling
app.use((err, req, res, next) => {
  console.error('Error:', err);
  res.status(500).json({ error: 'Internal Server Error', message: err.message });
});

// 404
app.use((req, res) => {
  res.status(404).json({ error: 'Endpoint not found' });
});

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`Backend SIPANDU running on port ${PORT}`);
  console.log(`Environment: ${process.env.NODE_ENV}`);
});
