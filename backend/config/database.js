const { Pool } = require('pg');

// Konfigurasi pool pg dengan opsi SSL untuk Supabase
const poolConfig = {
  user: process.env.DB_USER || 'postgres',
  password: process.env.DB_PASSWORD || '',
  host: process.env.DB_HOST || 'localhost',
  port: process.env.DB_PORT || 5432,
  database: process.env.DB_NAME || 'sipandu'
};

// Aktifkan SSL jika DB_SSL=true (untuk Supabase/public cloud)
if (process.env.DB_SSL === 'true') {
  poolConfig.ssl = { rejectUnauthorized: false };
}

const pool = new Pool(poolConfig);

pool.on('error', (err) => {
  console.error('Unexpected error on idle client', err);
});

// Test connection (log host dan timestamp)
pool.query('SELECT NOW()', (err, result) => {
  if (err) {
    console.error('Database connection error:', err);
  } else {
    console.log('Database connected at:', result.rows[0].now);
    console.log('DB host:', poolConfig.host);
  }
});

module.exports = pool;
