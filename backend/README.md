# Backend SIPANDU

Backend API untuk sistem monitoring panen dengan Node.js + Express + PostgreSQL.

## Setup

1. Install dependencies:
```bash
npm install
```

2. Buat file `.env` di folder backend:
```
DB_USER=postgres
DB_PASSWORD=your_password
DB_HOST=localhost
DB_PORT=5432
DB_NAME=monitoring_panen
PORT=5000
NODE_ENV=development
```

3. Pastikan database sudah dibuat dengan schema dari `schema_postgres.sql` dan data dari `data_postgres.sql`

4. Jalankan server:
```bash
npm run dev
```

Server akan jalan di `http://localhost:5000`

## API Endpoints

### Auth
- `POST /api/auth/login` - Login user

### Data
- `GET /api/ubinan` - Get data ubinan (memerlukan auth)
- `GET /api/users` - Get list users (admin/supervisor only)

## Database Connection

Database sudah dibuat sesuai schema di folder root. Backend akan connect otomatis dengan konfigurasi `.env`.
