# Setup SIPANDU - Frontend + Backend

## Persyaratan
- Node.js v14+ dan npm
- PostgreSQL sudah terinstall dan running
- Database `monitoring_panen` sudah dibuat dengan schema dari `schema_postgres.sql`

## Langkah Setup

### 1. Setup Database PostgreSQL

```bash
# Login ke PostgreSQL
psql -U postgres

# Buat database baru (jika belum ada)
CREATE DATABASE monitoring_panen;

# Keluar dari psql
\q
```

Jalankan schema:
```bash
psql -U postgres -d monitoring_panen -f schema_postgres.sql
```

Jalankan data (opsional, untuk contoh data):
```bash
psql -U postgres -d monitoring_panen -f data_postgres.sql
```

### 2. Setup Backend Node.js

```bash
# Masuk folder backend
cd backend

# Install dependencies
npm install

# Buat file .env dari template
cp .env.example .env

# Edit .env dengan konfigurasi database Anda
# Contoh:
# DB_USER=postgres
# DB_PASSWORD=your_password
# DB_HOST=localhost
# DB_PORT=5432
# DB_NAME=monitoring_panen
# PORT=5000
```

Edit file `.env` dengan text editor sesuai database Anda.

### 3. Setup Demo Users (untuk testing)

```bash
# Masih di folder backend
node scripts/setup-demo-users.js
```

Output akan menampilkan:
```
✓ User admin dibuat dengan password admin123
✓ User supervisor dibuat dengan password supervisor123
✓ User pcl dibuat dengan password pcl123
```

### 4. Jalankan Backend

```bash
# Development (auto-reload dengan nodemon)
npm run dev

# Production
npm start
```

Server akan jalan di `http://localhost:5000`

Anda akan melihat:
```
Backend SIPANDU running on port 5000
Environment: development
Database connected: [timestamp]
```

### 5. Akses Frontend

Buka browser dan buka file `static/index.html` atau serve dengan simple HTTP server:

```bash
# Dari folder root sipandu
npx http-server static -p 8080
```

Atau buka langsung: `file:///C:/Users/Zacky/Downloads/sipandu/static/index.html`

### 6. Login dengan Demo Credentials

Gunakan salah satu:
- **Admin**: `admin` / `admin123` → Dashboard PML (admin-biasa)
- **Supervisor**: `supervisor` / `supervisor123` → Dashboard Supervisor
- **PCL/User**: `pcl` / `pcl123` → Dashboard PCL (form-user)

## Struktur Folder

```
sipandu/
├── backend/                 # Node.js backend
│   ├── config/
│   │   └── database.js      # PostgreSQL connection
│   ├── routes/
│   │   ├── auth.js          # Login endpoint
│   │   └── data.js          # Data endpoints
│   ├── middleware/
│   │   └── auth.js          # JWT & role validation
│   ├── scripts/
│   │   └── setup-demo-users.js  # Setup demo users
│   ├── server.js            # Express server
│   ├── package.json
│   ├── .env                 # Environment variables (create from .env.example)
│   └── README.md
├── static/                  # Frontend (HTML/CSS/JS)
│   ├── index.html           # Login page
│   ├── js/
│   │   └── auth.js          # Client-side auth helper
│   ├── admin-biasa/         # Admin dashboard
│   ├── supervisor/          # Supervisor dashboard
│   ├── form-user/           # User dashboard (PCL)
│   └── auth/                # Password reset pages
├── schema_postgres.sql      # Database schema
├── data_postgres.sql        # Sample data (optional)
└── SETUP.md                 # File ini
```

## API Endpoints

### Auth
- `POST /api/auth/login`
  ```bash
  curl -X POST http://localhost:5000/api/auth/login \
    -H "Content-Type: application/json" \
    -d '{"username":"admin","password":"admin123"}'
  ```

### Data
- `GET /api/ubinan` - Get monitoring data
- `GET /api/users` - Get users (admin/supervisor only, requires token)
- `GET /api/dashboard/stats` - Get dashboard stats

## Troubleshooting

### Backend tidak connect ke database
1. Cek apakah PostgreSQL running: `psql -U postgres -d monitoring_panen -c "SELECT NOW();"`
2. Cek konfigurasi .env (DB_USER, DB_PASSWORD, dll)
3. Cek apakah database sudah dibuat: `psql -l | grep monitoring_panen`

### Frontend tidak bisa login
1. Cek apakah backend running: `http://localhost:5000/api/health` di browser
2. Cek console browser (F12) untuk error CORS atau network
3. Jika backend tidak running, frontend akan fallback ke demo mode

### Demo users tidak ada
1. Jalankan: `node scripts/setup-demo-users.js` dari folder backend
2. Cek password di database sudah di-hash

## Pengembangan Selanjutnya

1. **Frontend Protection**: Lindungi halaman lain yang belum dilindungi
2. **API Endpoints**: Tambah endpoint untuk CRUD data (tambah, edit, hapus ubinan)
3. **File Upload**: Implement upload foto bukti (serah terima, ubinan, timbangan)
4. **Reports**: Buat halaman laporan/dashboard dengan chart
5. **Real JWT**: Gunakan token JWT di setiap request untuk security

## Catatan Security

- Untuk production: 
  - Ganti `JWT_SECRET` di .env dengan string yang lebih aman
  - Gunakan environment variables untuk password DB
  - Setup HTTPS
  - Implement rate limiting untuk login
  - Add input validation dan sanitization

Selamat menggunakan SIPANDU! 🌾
