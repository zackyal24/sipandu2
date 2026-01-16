# SIPANDU - Sistem Informasi Padi Ubinan

API Backend menggunakan Express.js yang compatible untuk deployment ke Vercel.

## 🚀 Quick Start (Development Lokal)

### 1. Install Dependencies
```bash
npm install
```

### 2. Setup Environment Variables
Buat file `.env` di root folder:
```env
# Database Configuration
DB_HOST=localhost
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=your_password
DB_NAME=sipandu

# JWT Secret
JWT_SECRET=your-secret-key-here

# Server Configuration
PORT=3000
NODE_ENV=development
```

### 3. Setup Database
Import schema dan data ke PostgreSQL:
```bash
psql -U postgres -d sipandu < schema_postgres.sql
psql -U postgres -d sipandu < data_postgres.sql
```

### 4. Jalankan Server
```bash
npm start
```

Server akan berjalan di: **http://localhost:3000**

## 📁 Struktur Proyek (MVC Pattern)

```
sipandu/
├── index.js                # Main Express server
├── package.json            # Dependencies & scripts
├── .env                    # Environment variables (jangan di-commit)
├── vercel.json            # Konfigurasi Vercel (untuk deployment)
│
├── routes/                 # Route definitions
│   ├── index.js           # Mount semua routes
│   ├── auth.js            # Auth routes
│   ├── users.js           # User management routes
│   ├── ubinan.js          # Ubinan routes
│   ├── desa.js            # Desa routes
│   ├── kecamatan.js       # Kecamatan routes
│   └── segmen.js          # Segmen routes
│
├── controllers/            # Business Logic (MVC Controllers)
│   ├── authController.js  # Auth controller (login, logout, change password)
│   ├── userController.js  # User management controller
│   ├── kecamatan.js       # Kecamatan handler
│   ├── segmen.js          # Segmen handler
│   ├── desa.js            # Desa list handler
│   ├── desa/              # Desa controllers
│   │   └── [id].js        # Desa by ID handler
│   └── ubinan/            # Ubinan controllers
│       ├── index.js       # Ubinan list/create
│       ├── [id].js        # Ubinan by ID
│       └── [id]/
│           └── upload.js  # Ubinan file upload
│
├── config/                 # Configuration
│   └── database.js        # PostgreSQL connection pool
│
├── middleware/             # Custom Middleware
│   └── auth.js            # JWT authentication middleware
│
├── models/                 # Database Models (optional)
│
├── public/                 # Frontend static files
│   ├── index.html
│   ├── auth/
│   ├── pml/
│   └── supervisor/
│
└── uploads/               # File uploads (untuk development lokal)
    ├── berat_timbangan/
    ├── bukti_plot_ubinan/
    └── serah_terima/
```

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user
- `POST /api/auth/change-password` - Ganti password

### Master Data
- `GET /api/kecamatan` - List kecamatan
- `GET /api/desa` - List desa
- `GET /api/desa/:id` - Get desa by ID
- `GET /api/segmen` - List segmen
- `POST /api/segmen` - Create segmen
- `DELETE /api/segmen` - Delete segmen

### Ubinan Management
- `GET /api/ubinan` - List semua ubinan
- `POST /api/ubinan` - Create ubinan baru
- `GET /api/ubinan/:id` - Get ubinan by ID
- `PUT /api/ubinan/:id` - Update ubinan
- `DELETE /api/ubinan/:id` - Delete ubinan
- `POST /api/ubinan/:id/upload` - Upload file untuk ubinan

### User Management
- `GET /api/users` - List users
- `POST /api/users` - Create user
- `GET /api/users/:id` - Get user by ID
- `PUT /api/users/:id` - Update user
- `DELETE /api/users/:id` - Delete user

### Health Check
- `GET /health` - Server status check

## 🧪 Testing API

Menggunakan curl:
```bash
# Health check
curl http://localhost:3000/health

# Login
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password123"}'

# Get kecamatan (dengan token)
curl http://localhost:3000/api/kecamatan \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

Atau gunakan tools seperti:
- **Postman**
- **Thunder Client** (VS Code Extension)
- **REST Client** (VS Code Extension)

## 🌐 Deploy ke Vercel

### Persiapan
1. Pastikan database PostgreSQL bisa diakses dari internet
   - Gunakan: Vercel Postgres, Neon, Supabase, Railway, dll
2. Install Vercel CLI: `npm install -g vercel`

### Deploy
```bash
# Login ke Vercel
vercel login

# Deploy (development)
vercel

# Deploy (production)
vercel --prod
```

### Set Environment Variables di Vercel
Setelah deploy, tambahkan environment variables di Vercel Dashboard:
1. Buka https://vercel.com/dashboard
2. Pilih project → Settings → Environment Variables
3. Tambahkan semua variable dari file `.env`

### ⚠️ Catatan Penting untuk Vercel
- **File Uploads**: Vercel menggunakan filesystem read-only untuk serverless functions
  - Untuk production di Vercel, migrate file uploads ke cloud storage:
    - Vercel Blob Storage
    - AWS S3
    - Cloudinary
    - Google Cloud Storage

## 🛠️ Development

### Watch Mode (Auto-reload)
Install nodemon untuk development:
```bash
npm install -g nodemon
nodemon index.js
```

Atau tambahkan ke package.json:
```json
{
  "scripts": {
    "dev": "nodemon index.js"
  }
}
```

### Environment
- **Development**: `NODE_ENV=development` (default)
- **Production**: `NODE_ENV=production`

### Logging
Logging otomatis aktif saat development mode (`NODE_ENV !== 'production'`)

## 📦 Dependencies

- **express** - Web framework
- **pg** - PostgreSQL client
- **jsonwebtoken** - JWT authentication
- **bcrypt** - Password hashing
- **cors** - CORS middleware
- **dotenv** - Environment variables
- **multer** - File upload handling
- **formidable** - Alternative file upload parser

## 🔒 Security

- JWT untuk authentication
- Password di-hash dengan bcrypt
- CORS enabled
- Environment variables untuk sensitive data
- SQL injection protection (parameterized queries)

## 📝 License

ISC

## 👥 Support

Untuk pertanyaan atau masalah, silakan buat issue di repository ini.
