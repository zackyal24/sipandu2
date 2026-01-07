require('dotenv').config();
const { Pool } = require('pg');

const pool = new Pool({
  user: process.env.DB_USER || 'postgres',
  password: process.env.DB_PASSWORD || '',
  host: process.env.DB_HOST || 'localhost',
  port: process.env.DB_PORT || 5432,
  database: process.env.DB_NAME || 'monitoring_panen'
});

console.log('Testing database connection...');
console.log(`Config: ${process.env.DB_USER}@${process.env.DB_HOST}:${process.env.DB_PORT}/${process.env.DB_NAME}`);

pool.query('SELECT NOW()', (err, result) => {
  if (err) {
    console.error('❌ Connection FAILED:');
    console.error('Error:', err.message);
    console.error('\nChecklist:');
    console.error('1. PostgreSQL server running?');
    console.error('2. Username correct? (currently: ' + process.env.DB_USER + ')');
    console.error('3. Password correct? (currently: ' + (process.env.DB_PASSWORD ? '***' : 'empty') + ')');
    console.error('4. Database exists? (currently: ' + process.env.DB_NAME + ')');
    process.exit(1);
  }

  console.log('✓ Connection SUCCESS!');
  console.log('Server time:', result.rows[0].now);

  // Check users table
  pool.query('SELECT COUNT(*) as count FROM users', (err2, result2) => {
    if (err2) {
      console.error('❌ Users table error:', err2.message);
      process.exit(1);
    }

    console.log('✓ Users table exists');
    console.log('Current users in database:', result2.rows[0].count);

    pool.end();
  });
});
