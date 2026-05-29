# 🛒 TokoKu API

Backend API untuk platform e-commerce **TokoKu** dibangun menggunakan **Laravel 13** + **Laravel Sanctum** untuk autentikasi berbasis token.

> **Base URL:** `http://localhost:8000/api`  
> **Header wajib di semua request:** `Accept: application/json`

---

## 📋 Daftar Isi

- [Persyaratan](#persyaratan)
- [Instalasi & Setup](#instalasi--setup)
- [Struktur Database](#struktur-database)
- [Menjalankan Server](#menjalankan-server)
- [Autentikasi](#autentikasi)
- [Endpoint Kategori](#endpoint-kategori)
- [Endpoint Produk](#endpoint-produk)
- [Endpoint Pesanan](#endpoint-pesanan)
- [Format Respons](#format-respons)
- [Setup Postman](#setup-postman)
- [Fitur Bonus](#fitur-bonus)

---

## Persyaratan

- PHP >= 8.3
- Composer
- MySQL
- Laravel 13
- Laravel Sanctum

---

## Instalasi & Setup

### 1. Clone / Buka Project

```bash
cd c:\laragon\www\tokoku-api
```

### 2. Install Dependensi

```bash
composer install
```

### 3. Konfigurasi Environment

Pastikan file `.env` sudah dikonfigurasi:

```env
APP_NAME=TokoKu
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tokoku_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate App Key

```bash
php artisan key:generate
```

### 5. Migrasi Database

```bash
php artisan migrate
```

### 6. Isi Data Dummy (Opsional / Bonus)

```bash
php artisan db:seed
```

Data yang akan dibuat:
- 1 user demo (`test@example.com`)
- 3 kategori (Elektronik, Fashion, Makanan & Minuman)
- 10 produk tersebar di setiap kategori

### 7. Reset Database (Jika Diperlukan)

```bash
php artisan migrate:fresh --seed
```

---

## Struktur Database

```
users          → id, name, email, password, created_at
categories     → id, name, slug, description, created_at
products       → id, category_id (FK), name, slug, description, price, stock, is_active, created_at
orders         → id, user_id (FK), total_price, status, notes, created_at
order_items    → id, order_id (FK), product_id (FK), quantity, unit_price
```

**Status Order:** `pending` | `processing` | `done` | `cancelled`

---

## Menjalankan Server

```bash
php artisan serve
```

Server berjalan di `http://localhost:8000`

---

## Format Respons

Semua respons menggunakan format JSON standar berikut:

```json
// ✅ Sukses
{
    "success": true,
    "message": "Pesan sukses",
    "data": { }
}

// ❌ Gagal / Error
{
    "success": false,
    "message": "Pesan error",
    "errors": { }
}
```

---

## Autentikasi

Endpoint autentikasi menggunakan **Laravel Sanctum** dengan Bearer Token.

### `POST /api/auth/register` — Registrasi

Tidak memerlukan token.

**Request Body:**
```json
{
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response `201 Created`:**
```json
{
    "success": true,
    "message": "Registrasi berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "Budi Santoso",
            "email": "budi@example.com"
        },
        "token": "1|abcdefghijklmnopqrstuvwxyz1234567890"
    }
}
```

**Error `422` — Email duplikat:**
```json
{
    "message": "Email sudah terdaftar.",
    "errors": { "email": ["Email sudah terdaftar."] }
}
```

---

### `POST /api/auth/login` — Login

Tidak memerlukan token.

**Request Body:**
```json
{
    "email": "budi@example.com",
    "password": "password123"
}
```

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "Budi Santoso",
            "email": "budi@example.com"
        },
        "token": "2|newtoken..."
    }
}
```

**Error `401` — Password salah:**
```json
{
    "success": false,
    "message": "Email atau password salah."
}
```

---

### `POST /api/auth/logout` — Logout

**Auth:** `Bearer {token}`

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Logout berhasil. Token telah dicabut."
}
```

**Error `401` — Tanpa token:**
```json
{ "message": "Unauthenticated." }
```

---

### `GET /api/auth/profile` — Profil User

**Auth:** `Bearer {token}`

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Data profil berhasil diambil",
    "data": {
        "id": 1,
        "name": "Budi Santoso",
        "email": "budi@example.com",
        "created_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

---

## Endpoint Kategori

### `GET /api/categories` — List Semua Kategori

Tidak memerlukan token.

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Data kategori berhasil diambil",
    "data": [
        {
            "id": 1,
            "name": "Elektronik",
            "slug": "elektronik",
            "description": "Perangkat elektronik dan gadget terbaru",
            "products_count": 4,
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

---

### `POST /api/categories` — Buat Kategori Baru

**Auth:** `Bearer {token}`

**Request Body:**
```json
{
    "name": "Olahraga",
    "description": "Perlengkapan olahraga dan fitness"
}
```

**Response `201 Created`:**
```json
{
    "success": true,
    "message": "Kategori berhasil dibuat",
    "data": {
        "id": 4,
        "name": "Olahraga",
        "slug": "olahraga",
        "description": "Perlengkapan olahraga dan fitness",
        "created_at": "..."
    }
}
```

---

### `GET /api/categories/{id}` — Detail Kategori

Tidak memerlukan token. Menampilkan detail kategori beserta daftar produk aktifnya.

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Detail kategori berhasil diambil",
    "data": {
        "id": 1,
        "name": "Elektronik",
        "slug": "elektronik",
        "products": [
            {
                "id": 1,
                "name": "Smartphone Samsung Galaxy A55",
                "price": "4599000.00",
                "stock": 25,
                "is_active": true
            }
        ]
    }
}
```

**Error `404` — ID tidak ditemukan:**
```json
{ "success": false, "message": "Kategori tidak ditemukan" }
```

---

### `PUT /api/categories/{id}` — Update Kategori

**Auth:** `Bearer {token}`

**Request Body:**
```json
{
    "name": "Elektronik & Gadget",
    "description": "Deskripsi baru yang diperbarui"
}
```

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Kategori berhasil diperbarui",
    "data": { ... }
}
```

**Error `422` — Data kosong:**
```json
{
    "message": "Nama kategori wajib diisi.",
    "errors": { "name": ["Nama kategori wajib diisi."] }
}
```

---

### `DELETE /api/categories/{id}` — Hapus Kategori

**Auth:** `Bearer {token}`

Kategori hanya dapat dihapus jika tidak memiliki produk.

**Response `200 OK`:**
```json
{ "success": true, "message": "Kategori berhasil dihapus" }
```

**Error `400` — Masih memiliki produk:**
```json
{
    "success": false,
    "message": "Kategori tidak dapat dihapus karena masih memiliki 4 produk."
}
```

---

## Endpoint Produk

### `GET /api/products` — List Produk Aktif

Tidak memerlukan token. Mendukung **pagination**, **pencarian**, dan **filter kategori**.

**Query Parameters:**

| Parameter | Tipe | Deskripsi | Contoh |
|-----------|------|-----------|--------|
| `search` | string | Cari berdasarkan nama produk | `?search=laptop` |
| `category_id` | integer | Filter berdasarkan ID kategori | `?category_id=1` |

**Contoh Request:**
```
GET /api/products?search=samsung&category_id=1
```

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Data produk berhasil diambil",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Smartphone Samsung Galaxy A55",
                "price": "4599000.00",
                "stock": 25,
                "is_active": true,
                "category": { "id": 1, "name": "Elektronik" }
            }
        ],
        "total": 10,
        "per_page": 10,
        "last_page": 1
    }
}
```

---

### `POST /api/products` — Buat Produk Baru

**Auth:** `Bearer {token}`

**Request Body:**
```json
{
    "category_id": 1,
    "name": "Mouse Gaming Logitech G502",
    "description": "Mouse gaming presisi tinggi dengan DPI 16000",
    "price": 750000,
    "stock": 20
}
```

**Response `201 Created`:**
```json
{
    "success": true,
    "message": "Produk berhasil dibuat",
    "data": {
        "id": 11,
        "category_id": 1,
        "name": "Mouse Gaming Logitech G502",
        "slug": "mouse-gaming-logitech-g502",
        "price": "750000.00",
        "stock": 20,
        "is_active": true,
        "category": { "id": 1, "name": "Elektronik" }
    }
}
```

**Error `422` — Validasi gagal:**
```json
{
    "message": "Harga produk wajib diisi.",
    "errors": {
        "price": ["Harga produk wajib diisi."],
        "category_id": ["Kategori yang dipilih tidak valid."]
    }
}
```

---

### `GET /api/products/{id}` — Detail Produk

Tidak memerlukan token. Menampilkan detail produk beserta kategorinya.

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Detail produk berhasil diambil",
    "data": {
        "id": 1,
        "name": "Smartphone Samsung Galaxy A55",
        "slug": "smartphone-samsung-galaxy-a55",
        "description": "Smartphone flagship ...",
        "price": "4599000.00",
        "stock": 25,
        "is_active": true,
        "category": {
            "id": 1,
            "name": "Elektronik",
            "slug": "elektronik"
        }
    }
}
```

**Error `404`:**
```json
{ "success": false, "message": "Produk tidak ditemukan" }
```

---

### `PUT /api/products/{id}` — Update Produk

**Auth:** `Bearer {token}`

**Request Body:** sama seperti `POST /api/products`

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Produk berhasil diperbarui",
    "data": { ... }
}
```

---

### `PATCH /api/products/{id}/toggle` — Toggle Status Aktif

**Auth:** `Bearer {token}`

Mengaktifkan produk yang nonaktif, atau menonaktifkan produk yang aktif.

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Status produk berhasil diubah menjadi nonaktif",
    "data": {
        "id": 1,
        "name": "Smartphone Samsung Galaxy A55",
        "is_active": false
    }
}
```

---

### `DELETE /api/products/{id}` — Hapus Produk

**Auth:** `Bearer {token}`

**Response `200 OK`:**
```json
{ "success": true, "message": "Produk berhasil dihapus" }
```

---

## Endpoint Pesanan

Semua endpoint pesanan memerlukan autentikasi.

### `POST /api/orders` — Buat Pesanan Baru

**Auth:** `Bearer {token}`

Total harga dihitung otomatis berdasarkan harga dan jumlah produk. Stok produk akan berkurang otomatis setelah pesanan berhasil dibuat.

**Request Body:**
```json
{
    "notes": "Tolong kirim cepat",
    "items": [
        { "product_id": 1, "quantity": 2 },
        { "product_id": 5, "quantity": 1 }
    ]
}
```

**Response `201 Created`:**
```json
{
    "success": true,
    "message": "Pesanan berhasil dibuat",
    "data": {
        "id": 1,
        "user_id": 1,
        "total_price": "9283000.00",
        "status": "pending",
        "notes": "Tolong kirim cepat",
        "items": [
            {
                "id": 1,
                "product_id": 1,
                "quantity": 2,
                "unit_price": "4599000.00",
                "product": { "id": 1, "name": "Smartphone Samsung Galaxy A55" }
            },
            {
                "id": 2,
                "product_id": 5,
                "quantity": 1,
                "unit_price": "85000.00",
                "product": { "id": 5, "name": "Kaos Polos Premium Cotton" }
            }
        ]
    }
}
```

**Error `400` — Stok tidak mencukupi:**
```json
{
    "success": false,
    "message": "Stok produk 'Laptop ASUS VivoBook 15' tidak mencukupi. Stok tersedia: 10."
}
```

**Error `400` — Produk tidak aktif:**
```json
{
    "success": false,
    "message": "Produk 'Nama Produk' tidak tersedia."
}
```

---

### `GET /api/orders` — List Pesanan Milik User

**Auth:** `Bearer {token}`

Hanya menampilkan pesanan milik user yang sedang login.

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Data pesanan berhasil diambil",
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "total_price": "9283000.00",
            "status": "pending",
            "notes": "Tolong kirim cepat",
            "items": [ ... ]
        }
    ]
}
```

---

### `GET /api/orders/{id}` — Detail Pesanan

**Auth:** `Bearer {token}`

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Detail pesanan berhasil diambil",
    "data": {
        "id": 1,
        "total_price": "9283000.00",
        "status": "pending",
        "items": [ ... ],
        "user": { "id": 1, "name": "Budi Santoso" }
    }
}
```

**Error `403` — Pesanan milik user lain:**
```json
{
    "success": false,
    "message": "Anda tidak memiliki akses ke pesanan ini"
}
```

---

### `PATCH /api/orders/{id}/status` — Update Status Pesanan

**Auth:** `Bearer {token}`

**Request Body:**
```json
{ "status": "processing" }
```

**Status yang valid:** `pending` | `processing` | `done` | `cancelled`

**Response `200 OK`:**
```json
{
    "success": true,
    "message": "Status pesanan berhasil diperbarui",
    "data": {
        "id": 1,
        "status": "processing",
        ...
    }
}
```

**Error `422` — Status tidak valid:**
```json
{
    "message": "Status tidak valid. Pilihan: pending, processing, done, cancelled.",
    "errors": { "status": ["Status tidak valid..."] }
}
```

---

## Setup Postman

### 1. Buat Environment Baru

- Nama: **TokoKu Local**
- Variables:

| Variable | Initial Value |
|----------|--------------|
| `base_url` | `http://localhost:8000/api` |
| `auth_token` | *(biarkan kosong)* |

### 2. Gunakan Variable di Request

- URL semua request: `{{base_url}}/auth/login`
- Tab **Authorization** → Type: **Bearer Token** → Token: `{{auth_token}}`

### 3. Script Otomatis Simpan Token

Di request `POST /api/auth/login`, tab **Tests**, tambahkan script:

```javascript
const json = pm.response.json();
if (json.success && json.data.token) {
    pm.environment.set("auth_token", json.data.token);
    console.log("Token disimpan:", json.data.token);
}
```

Token akan tersimpan otomatis setiap kali login berhasil.

### 4. Urutan Testing yang Disarankan

```
1.  POST   {{base_url}}/auth/register          → Daftar akun baru
2.  POST   {{base_url}}/auth/login             → Login (token tersimpan otomatis)
3.  GET    {{base_url}}/auth/profile           → Cek profil user
4.  GET    {{base_url}}/categories             → Lihat semua kategori
5.  GET    {{base_url}}/categories/1           → Detail kategori + produk
6.  POST   {{base_url}}/categories             → Buat kategori baru
7.  PUT    {{base_url}}/categories/1           → Update kategori
8.  GET    {{base_url}}/products               → Lihat semua produk (pagination)
9.  GET    {{base_url}}/products?search=samsung  → Cari produk
10. GET    {{base_url}}/products?category_id=1   → Filter by kategori
11. POST   {{base_url}}/products               → Buat produk baru
12. PATCH  {{base_url}}/products/1/toggle      → Toggle aktif/nonaktif
13. POST   {{base_url}}/orders                 → Buat pesanan
14. GET    {{base_url}}/orders                 → Lihat pesanan saya
15. GET    {{base_url}}/orders/1               → Detail pesanan
16. PATCH  {{base_url}}/orders/1/status        → Update status pesanan
17. DELETE {{base_url}}/products/1             → Hapus produk
18. DELETE {{base_url}}/categories/4           → Hapus kategori (yang kosong)
19. POST   {{base_url}}/auth/logout            → Logout
```

---

## Fitur Bonus

| Fitur | Endpoint | Contoh |
|-------|----------|--------|
| Pencarian produk | `GET /api/products` | `?search=laptop` |
| Filter by kategori | `GET /api/products` | `?category_id=2` |
| Seeder data dummy | `php artisan db:seed` | 3 kategori + 10 produk |

---

## Struktur File

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── ProductController.php
│   │   └── OrderController.php
│   └── Requests/
│       ├── StoreProductRequest.php
│       └── StoreOrderRequest.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Product.php
│   ├── Order.php
│   └── OrderItem.php
database/
├── migrations/
│   ├── ..._create_categories_table.php
│   ├── ..._create_products_table.php
│   ├── ..._create_orders_table.php
│   └── ..._create_order_items_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── CategoryProductSeeder.php
routes/
└── api.php
```

---

## Ringkasan Endpoint

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| POST | `/api/auth/register` | ❌ | Registrasi |
| POST | `/api/auth/login` | ❌ | Login |
| POST | `/api/auth/logout` | ✅ | Logout |
| GET | `/api/auth/profile` | ✅ | Profil user |
| GET | `/api/categories` | ❌ | List kategori |
| POST | `/api/categories` | ✅ | Buat kategori |
| GET | `/api/categories/{id}` | ❌ | Detail kategori |
| PUT | `/api/categories/{id}` | ✅ | Update kategori |
| DELETE | `/api/categories/{id}` | ✅ | Hapus kategori |
| GET | `/api/products` | ❌ | List produk + filter |
| POST | `/api/products` | ✅ | Buat produk |
| GET | `/api/products/{id}` | ❌ | Detail produk |
| PUT | `/api/products/{id}` | ✅ | Update produk |
| PATCH | `/api/products/{id}/toggle` | ✅ | Toggle aktif |
| DELETE | `/api/products/{id}` | ✅ | Hapus produk |
| GET | `/api/orders` | ✅ | List pesanan saya |
| POST | `/api/orders` | ✅ | Buat pesanan |
| GET | `/api/orders/{id}` | ✅ | Detail pesanan |
| PATCH | `/api/orders/{id}/status` | ✅ | Update status |
