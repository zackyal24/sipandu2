const { Pool } = require('pg');

let pool;

// Gunakan singleton pattern untuk serverless
function getPool() {
  if (!pool) {
    // Parse password dari .env (remove quotes jika ada)
    let password = process.env.DB_PASSWORD || '';
    if (password.startsWith('"') && password.endsWith('"')) {
      password = password.slice(1, -1);
    }

    const poolConfig = {
      user: process.env.DB_USER || 'postgres',
      password: password,
      host: process.env.DB_HOST || 'localhost',
      port: process.env.DB_PORT || 5432,
      database: process.env.DB_NAME || 'sipandu',
      // Optimal settings untuk serverless
      max: 1, // Batasi koneksi per instance
      idleTimeoutMillis: 30000,
      connectionTimeoutMillis: 10000,
    };

    // Aktifkan SSL jika DB_SSL=true (untuk Supabase/public cloud)
    if (process.env.DB_SSL === 'true') {
      poolConfig.ssl = { rejectUnauthorized: false };
    }

    pool = new Pool(poolConfig);

    pool.on('error', (err) => {
      console.error('Unexpected error on idle client', err);
      pool = null; // Reset pool jika error
    });
  }

  return pool;
}

module.exports = getPool;
