# Dokumentasi API SIPANDU

Dokumen ini dibuat berdasarkan implementasi route dan controller di backend Express saat ini.

## Base URL

- Development lokal: `http://localhost:3000`
- Prefix API: `/api`

Contoh endpoint penuh: `http://localhost:3000/api/auth/login`

## Format Authentication

Endpoint yang butuh login menggunakan header:

```http
Authorization: Bearer <JWT_TOKEN>
```

Token didapat dari endpoint login dan berlaku selama 24 jam.

## Ringkasan Endpoint

### Public

- `GET /health`
- `GET /`
- `POST /api/auth/login`
- `POST /api/auth/logout`

### Authenticated

- `POST /api/auth/change-password`
- `GET /api/users`
- `POST /api/users`
- `GET /api/users/:id`
- `PUT /api/users/:id`
- `DELETE /api/users/:id`
- `GET /api/ubinan`
- `POST /api/ubinan`
- `GET /api/ubinan/export`
- `GET /api/ubinan/:id`
- `PUT /api/ubinan/:id`
- `DELETE /api/ubinan/:id`
- `POST /api/ubinan/:id/upload`
- `POST /api/ubinan/:id/revisi`

### Master Data (tanpa auth di implementasi saat ini)

- `GET /api/kecamatan`
- `GET /api/desa`
- `GET /api/desa/:id`
- `GET /api/segmen`
- `POST /api/segmen`
- `POST /api/segmen/add`
- `POST /api/segmen/import`
- `DELETE /api/segmen`
- `DELETE /api/segmen/:id`
- `DELETE /api/segmen/all/delete`

## Detail Endpoint

### 1) Health & Root

#### GET /health
Cek status server.

Contoh response:
```json
{
  "status": "OK",
  "message": "Server is running"
}
```

#### GET /
Menyajikan file `public/index.html`.

---

### 2) Authentication

#### POST /api/auth/login
Login user.

Body JSON:
```json
{
  "username": "string",
  "password": "string"
}
```

Response sukses:
```json
{
  "success": true,
  "token": "<jwt>",
  "user": {
    "id": 1,
    "username": "demo",
    "nama_lengkap": "Nama User",
    "role": "pcl",
    "pml_id": 10
  }
}
```

Error umum:
- `400` username/password kosong
- `401` kredensial salah
- `500` error server

#### POST /api/auth/logout
Logout stateless (hanya response sukses, token tidak diblacklist).

Response:
```json
{
  "success": true,
  "message": "Logout berhasil"
}
```

#### POST /api/auth/change-password
Ganti password user yang sedang login.

Butuh Authorization header.

Body JSON:
```json
{
  "password_lama": "string",
  "password_baru": "string"
}
```

Aturan:
- `password_baru` minimal 6 karakter

Response sukses:
```json
{
  "success": true,
  "message": "Password berhasil diubah"
}
```

Error umum:
- `401` token tidak ada/tidak valid atau password lama salah
- `404` user tidak ditemukan
- `500` error server

---

### 3) Users

Semua endpoint users memerlukan token dan role `pml` atau `supervisor`.

#### GET /api/users
Ambil daftar user.

Response sukses:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "username": "user1",
      "nama_lengkap": "User Satu",
      "no_hp": "08...",
      "email": "u1@mail.com",
      "role": "pcl",
      "pml_id": 2,
      "created_at": "2026-04-27T10:00:00.000Z"
    }
  ]
}
```

#### POST /api/users
Buat user baru.

Body JSON:
```json
{
  "username": "string",
  "password": "string",
  "nama_lengkap": "string",
  "no_hp": "string",
  "email": "string",
  "role": "pcl|pml|supervisor",
  "pml_id": 2
}
```

Field wajib: `username`, `password`, `nama_lengkap`, `role`.

Response sukses (`201`):
```json
{
  "success": true,
  "data": {
    "id": 10,
    "username": "baru",
    "nama_lengkap": "User Baru",
    "role": "pcl"
  }
}
```

#### GET /api/users/:id
Ambil detail 1 user.

#### PUT /api/users/:id
Update user.

Body JSON (opsional sesuai kebutuhan):
```json
{
  "username": "string",
  "nama_lengkap": "string",
  "no_hp": "string",
  "email": "string",
  "role": "string",
  "pml_id": 2,
  "password": "string"
}
```

Catatan:
- Jika `password` diisi, backend akan hash ulang.
- Username akan divalidasi agar tidak duplikat.

#### DELETE /api/users/:id
Hapus user.

Error umum users:
- `401` token tidak ada
- `403` role tidak berhak
- `404` data user tidak ditemukan
- `500` error server

---

### 4) Kecamatan & Desa

#### GET /api/kecamatan
Ambil daftar kecamatan.

Response:
```json
{
  "success": true,
  "data": [
    { "id": 1, "nama_kecamatan": "Kecamatan A" }
  ]
}
```

#### GET /api/desa
Ambil desa berdasarkan query `kecamatan_id`.

Query params:
- `kecamatan_id` (required)

Contoh:
- `/api/desa?kecamatan_id=1`

Response:
```json
{
  "success": true,
  "data": [
    { "id": 1, "nama_desa": "Desa A" }
  ]
}
```

#### GET /api/desa/:id
Ambil desa berdasarkan id kecamatan dari path.

Contoh:
- `/api/desa/1`

Catatan implementasi:
- Query SQL menggunakan kolom `id_kecamatan`.
- Handler juga mendukung fallback `req.query.id`.

---

### 5) Segmen

#### GET /api/segmen
List semua segmen.

Response:
```json
{
  "success": true,
  "data": [
    { "id": 1, "nomor_segmen": "001" }
  ],
  "count": 1
}
```

#### POST /api/segmen
Tambah segmen dengan JSON atau import multipart.

Mode JSON body:
```json
{
  "nomor_segmen": "001"
}
```
atau
```json
{
  "segments": ["001", "002", "003"]
}
```

Mode import file:
- `Content-Type: multipart/form-data`
- field file: `file`
- format didukung: `.csv`, `.xlsx`, `.xls`

Response sukses:
```json
{
  "success": true,
  "message": "Segmen berhasil diproses (1 ditambahkan, 0 duplikat)",
  "inserted": 1,
  "skipped": 0
}
```

#### POST /api/segmen/add
Alias ke handler yang sama dengan `POST /api/segmen`.

#### POST /api/segmen/import
Alias ke handler yang sama dengan `POST /api/segmen`.

#### DELETE /api/segmen
Hapus semua segmen.

#### DELETE /api/segmen/:id
Hapus segmen berdasarkan id.

#### DELETE /api/segmen/all/delete
Rute ini dimount, tetapi karena urutan route, request bisa tertangkap lebih dulu oleh `DELETE /api/segmen/:id`.
Disarankan pakai `DELETE /api/segmen` untuk hapus semua.

---

### 6) Ubinan

Semua endpoint ubinan membutuhkan token.

Role behavior penting:
- `GET /api/ubinan`:
  - role `pcl`: hanya data miliknya (`user_id = token.id`)
  - role `pml`: data user dengan `u.pml_id = token.id`
  - role lain (misal supervisor): melihat lebih luas (tanpa filter role khusus)
- `DELETE /api/ubinan/:id`: hanya role `pml` dan `supervisor`
- `POST /api/ubinan/:id/revisi`: hanya role `pml` dan `supervisor`
- `GET /api/ubinan/export`: role `pcl` ditolak

#### POST /api/ubinan
Tambah data ubinan (tanpa upload foto).

Body JSON minimum:
```json
{
  "nama_petani": "string",
  "desa": "string",
  "kecamatan": "string",
  "tanggal_panen": "YYYY-MM-DD",
  "subround": 1,
  "nomor_segmen": "001",
  "nomor_sub_segmen": "01",
  "status": "sedang diperiksa",
  "berat_plot": 12.5
}
```

Field wajib:
- `nama_petani`, `desa`, `kecamatan`, `tanggal_panen`, `subround`, `nomor_segmen`, `nomor_sub_segmen`

Catatan:
- Jika `berat_plot` diisi, backend otomatis menghitung `gkp`, `gkg`, `ku`.
- Jika `status` tidak diisi, default `sedang diperiksa`.

Response:
```json
{
  "success": true,
  "message": "Data ubinan berhasil ditambahkan",
  "data": { "id": 123 }
}
```

#### GET /api/ubinan
List data ubinan (limit 200).

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "nama_petani": "Petani A",
      "status": "sedang diperiksa",
      "pcl_name": "Nama PCL"
    }
  ],
  "count": 1
}
```

#### GET /api/ubinan/:id
Detail ubinan by id.

Response:
```json
{
  "success": true,
  "data": {
    "id": 123,
    "nama_petani": "Petani A",
    "foto_penyampaian_uang": "https://storage.googleapis.com/...",
    "foto_penyampaian_uang_url": "https://signed-url..."
  }
}
```

Catatan:
- Jika `GCS_BUCKET` tersedia, backend menambahkan signed URL (berlaku 1 jam) untuk setiap foto.

#### PUT /api/ubinan/:id
Update status/berat ubinan.

Body JSON yang umum:
```json
{
  "status": "selesai",
  "berat_plot": 20.2
}
```

Aturan validasi:
- Jika status baru `selesai`, `berat_plot` wajib.
- Ada aturan transisi status (FSM):
  - `sedang diperiksa -> selesai|revisi`
  - `revisi -> selesai|sedang diperiksa`
  - `selesai -> revisi`
  - `belum -> sedang diperiksa|tidak bisa`
  - `sudah -> sedang diperiksa|selesai`
  - `tidak bisa -> (tidak ada transisi)`
- Jika transisi dari `revisi` ke `sedang diperiksa`, `note_revisi` dan `revised_at` dihapus.

#### DELETE /api/ubinan/:id
Hapus data ubinan dan file terkait di GCS (jika ada).

Role yang boleh: `pml`, `supervisor`.

#### POST /api/ubinan/:id/upload
Upload foto ubinan (multipart/form-data).

- Max file: 10MB per file
- Format: JPG, JPEG, PNG, GIF, WEBP, HEIC/HEIF
- Semua gambar di-resize/kompres ke JPG (max width 1280, quality 80)

Field file yang didukung:
- `foto_penyampaian_uang`
- `foto_ktp_petani`
- `foto_timbangan_ubinan`
- `foto_proses_ubinan`
- `foto_plot_setelah_panen`

Response:
```json
{
  "success": true,
  "message": "File berhasil diunggah",
  "files": {
    "foto_ktp_petani": "https://storage.googleapis.com/<bucket>/ktp_petani/...jpg"
  }
}
```

#### POST /api/ubinan/:id/revisi
Kirim catatan revisi, sekaligus ubah status menjadi `revisi`.

Body JSON:
```json
{
  "note_revisi": "Perbaiki data berat plot"
}
```

Role yang boleh: `pml`, `supervisor`.

Response:
```json
{
  "success": true,
  "message": "Revisi berhasil dikirim",
  "data": { "id": 123 }
}
```

#### GET /api/ubinan/export
Export data ubinan ke file Excel (`.xlsx`).

- Header response: `Content-Disposition: attachment`
- Role `pcl`: ditolak (`403`)
- Role `pml`: data difilter berdasarkan `u.pml_id = token.id`
- Role lain: dapat seluruh data

---

## Kode Error Umum

- `200` sukses
- `201` data berhasil dibuat
- `400` request/body tidak valid
- `401` token/kredensial tidak valid
- `403` role tidak punya akses
- `404` data tidak ditemukan
- `405` method tidak diizinkan
- `500` kesalahan server/database

## Contoh cURL

### Login
```bash
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

### List Ubinan
```bash
curl http://localhost:3000/api/ubinan \
  -H "Authorization: Bearer <JWT_TOKEN>"
```

### Upload Foto Ubinan
```bash
curl -X POST http://localhost:3000/api/ubinan/123/upload \
  -H "Authorization: Bearer <JWT_TOKEN>" \
  -F "foto_ktp_petani=@C:/path/ktp.jpg" \
  -F "foto_plot_setelah_panen=@C:/path/plot.jpg"
```

## Catatan Implementasi Penting

- Middleware `verifyToken` tersedia di `middleware/auth.js`, namun sebagian besar controller saat ini melakukan verifikasi JWT langsung di dalam handler.
- Endpoint `POST /api/auth/logout` bersifat stateless (token tidak dicabut di server).
- Beberapa route segmen adalah alias ke handler yang sama.
