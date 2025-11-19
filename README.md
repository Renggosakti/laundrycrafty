# LaundryCrafty - Sistem Informasi Laundry

> Aplikasi Sistem Informasi Laundry Berbasis Web dengan PHP Native & MySQL

## 📋 Informasi Proyek

| **Detail** | **Keterangan** |
|------------|----------------|
| **Nama** | Arya Rangga Putra Pratama |
| **NRP** | 5025241072 |
| **Kelas** | Pemrograman Web A |
| **Dosen** | Bapak Fajar |
| **Hosting** | Rumahweb Shared Hosting |
| **Domain** | [laundrycraft.web.id](http://laundrycraft.web.id) |
| **Database** | MySQL 8.x |
| **Password DB** | `laundry123` |

## Tentang LaundryCrafty

**LaundryCrafty** adalah sistem informasi berbasis web yang dikembangkan untuk usaha laundry modern dengan tujuan meningkatkan efisiensi operasional, akurasi pencatatan transaksi, dan kemudahan dalam mengelola data pelanggan serta layanan laundry.

### Keunikan Proyek
- ✅ **Deployed di hosting produksi** (bukan localhost)
- ✅ **PHP Native tanpa framework** - mudah dipelajari
- ✅ **Multi-role system** (Admin & Kasir)
- ✅ **Real-time calculation & reporting**
- ✅ **Modern UI dengan responsive design**

## Latar Belakang Masalah

Usaha laundry konvensional masih menghadapi berbagai kendala:

| **Masalah** | **Solusi LaundryCrafty** |
|-------------|--------------------------|
| Pencatatan manual tidak terstruktur | ✅ Database terintegrasi |
| Perhitungan manual rentan error | ✅ Kalkulasi otomatis |
| Sulit monitoring status cucian | ✅ Tracking real-time |
| Laporan keuangan tidak akurat | ✅ Laporan otomatis + grafik |
| Tidak ada sistem keamanan | ✅ Login system dengan role |

## Fitur Utama

### Autentikasi & User Management
- [x] **Login System** dengan session management
- [x] **Register** akun baru (Admin/Kasir)
- [x] **Role-Based Access Control**
- [x] **Auto logout** setelah 30 menit idle
- [x] **Password encryption** dengan `password_hash()`

### Manajemen Pelanggan
- [x] **CRUD Data Pelanggan** (Create, Read, Update, Delete)
- [x] **Pencarian** pelanggan
- [x] **Validasi input** (no HP, email)
- [x] **Prevention delete** jika ada transaksi

### Manajemen Layanan
- [x] **Kelola jenis layanan** laundry
- [x] **5 Layanan default**: Cuci Kering, Cuci Setrika, Express, Cuci Sepatu, Cuci Karpet
- [x] **Atur harga & durasi** pengerjaan
- [x] **Card layout** untuk tampilan yang menarik

### Manajemen Transaksi
- [x] **Buat transaksi baru** dengan form yang user-friendly
- [x] **Kalkulasi otomatis**: `Total = Berat × Harga per Kg`
- [x] **Estimasi tanggal selesai** otomatis
- [x] **Update status**: `Proses → Selesai → Diambil`
- [x] **Filter & pencarian** transaksi
- [x] **Color-coded status badges**

### Laporan & Statistik
- [x] **Dashboard real-time** dengan statistik
- [x] **Grafik pendapatan** 7 hari terakhir (Chart.js)
- [x] **Laporan pendapatan** dengan filter tanggal
- [x] **Export/Print** laporan
- [x] **Format Rupiah** & Tanggal Indonesia

## Teknologi yang Digunakan

### Frontend
- **HTML5** - Struktur website
- **CSS3** - Styling dengan gradient dan animasi
- **JavaScript (Vanilla)** - Interaksi client-side
- **Chart.js 3.9.1** - Visualisasi grafik
- **Font Awesome 6.4.0** - Icon library

### Backend
- **PHP 8.x Native** - Server-side scripting
- **MySQL 8.x** - Database management
- **MySQLi Extension** - Database connection
- **Session Management** - Authentication system

### Hosting & Deployment
- **Rumahweb Shared Hosting**
- **Apache Web Server**
- **phpMyAdmin** - Database management
- **cPanel** - Hosting control panel
- **Domain**: laundrycraft.web.id

### Development Tools
- **Visual Studio Code** - Code editor
- **Git & GitHub** - Version control
- **FileZilla** - FTP client
- **Chrome DevTools** - Debugging

## Struktur Database

### Diagram Tabel

```
users → transaksi ← pelanggan
            ↑
        layanan
```

### Struktur Tabel

#### Tabel `users`
```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','kasir') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 👥 Tabel `pelanggan`
```sql
CREATE TABLE pelanggan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  alamat TEXT,
  no_hp VARCHAR(15),
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 📦 Tabel `layanan`
```sql
CREATE TABLE layanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_layanan VARCHAR(100) NOT NULL,
  harga_per_kg INT NOT NULL,
  durasi INT NOT NULL,
  deskripsi TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 🛒 Tabel `transaksi`
```sql
CREATE TABLE transaksi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pelanggan_id INT NOT NULL,
  layanan_id INT NOT NULL,
  user_id INT NOT NULL,
  berat FLOAT NOT NULL,
  total_harga INT NOT NULL,
  tanggal_masuk DATE NOT NULL,
  tanggal_selesai DATE NOT NULL,
  status ENUM('proses','selesai','diambil') DEFAULT 'proses',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id),
  FOREIGN KEY (layanan_id) REFERENCES layanan(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 📁 Struktur File Project

```
laundrycrafty/
├── 📄 config.php                 # Konfigurasi database & helper functions
├── 📄 login.php                  # Halaman login
├── 📄 register.php               # Halaman registrasi
├── 📄 logout.php                 # Logout script
├── 📄 index.php                  # Dashboard utama
├── 🗂️ pelanggan/
│   ├── list.php                  # Daftar pelanggan
│   ├── tambah.php                # Form tambah pelanggan
│   ├── edit.php                  # Form edit pelanggan
│   └── hapus.php                 # Hapus pelanggan
├── 🗂️ layanan/
│   ├── list.php                  # Daftar layanan
│   ├── tambah.php                # Form tambah layanan
│   ├── edit.php                  # Form edit layanan
│   └── hapus.php                 # Hapus layanan
├── 🗂️ transaksi/
│   ├── list.php                  # Daftar transaksi
│   ├── tambah.php                # Form tambah transaksi
│   ├── edit.php                  # Detail & update status
│   └── hapus.php                 # Hapus transaksi
├── 🗂️ laporan/
│   └── pendapatan.php            # Laporan keuangan
├── 🗃️ laundrycrafty.sql          # Database structure + sample data
└── 📄 README.md                  # Dokumentasi ini
```

## 🚀 Instalasi & Deployment

### 📦 Prerequisites
- Hosting dengan PHP 8.x
- MySQL 8.x database
- Akses cPanel & phpMyAdmin

### 🔧 Langkah Deployment

#### 1. 📤 Upload File ke Hosting
```bash
# Upload semua file ke public_html via FileZilla atau cPanel File Manager
# Pastikan struktur folder tetap sama
```

#### 2. 🗄️ Setup Database
```sql
-- Buat database baru di cPanel
-- Import file laundrycrafty.sql via phpMyAdmin
-- Atau jalankan query manual untuk buat tabel
```

#### 3. ⚙️ Konfigurasi Database
Edit `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'username_database');  // Sesuaikan
define('DB_PASS', 'laundry123');         // Password database
define('DB_NAME', 'laundrycrafty');      // Nama database
define('BASE_URL', 'http://laundrycraft.web.id/');
```

#### 4. 🔐 Set Permissions
```bash
# Set permission folder jika perlu upload
chmod 755 uploads/
```

#### 5. ✅ Testing
1. Akses: `http://laundrycraft.web.id`
2. Login dengan akun default:
   - **Username**: `admin`
   - **Password**: `password`
3. Test semua fitur CRUD

## 🛡️ Keamanan Sistem

### 🔒 Security Features
- ✅ **Password Hashing** - `password_hash()` & `password_verify()`
- ✅ **SQL Injection Prevention** - Prepared statements dengan MySQLi
- ✅ **XSS Prevention** - `htmlspecialchars()` untuk output
- ✅ **Session Security** - Regeneration ID & secure cookies
- ✅ **Input Validation** - Clean function untuk sanitize input
- ✅ **Role-Based Access** - Middleware untuk akses terbatas
- ✅ **Brute-force Protection** - Login delay & generic error messages

### 🚫 Error Handling
- Custom error messages
- SQL error prevention
- Form validation client & server side

## 📊 Perhitungan Otomatis

### 💰 Kalkulasi Harga
```php
$total_harga = $berat * $harga_per_kg;
// Contoh: 3kg × Rp7.000 = Rp21.000
```

### ⏳ Estimasi Tanggal Selesai
```php
$tanggal_selesai = date('Y-m-d', strtotime($tanggal_masuk . " + $durasi days"));
// Contoh: Masuk 19 Nov + 2 hari = Selesai 21 Nov
```

### 🔢 Format Display
- **Rupiah**: `Rp 25.000`
- **Tanggal**: `19 November 2025`
- **Status**: `🟡 Proses` / `🟢 Selesai` / `🔵 Diambil`

## 🎨 User Interface

### ✨ Design Features
- **Modern Gradient** - Warna ungu-biru dengan glassmorphism
- **Responsive Design** - Compatible desktop, tablet, mobile
- **Animated Background** - Particle effects
- **Card-based Layout** - Modern UI components
- **Smooth Transitions** - CSS animations & transitions
- **Color-coded Status** - Visual status indicators

### 📱 UI Components
- Navigation sidebar
- Statistics cards
- Data tables dengan pagination
- Form modals
- Toast notifications
- Chart visualizations

## 🔄 Alur Sistem

### 📥 Tambah Transaksi
```
1. Kasir buka form transaksi
2. Pilih pelanggan (existing/new)
3. Pilih layanan & input berat
4. Sistem kalkulasi otomatis
5. Konfirmasi & simpan
6. Status awal: "Proses"
```

### 🔁 Update Status
```
1. Buka detail transaksi
2. Update status: "Proses" → "Selesai"
3. Saat diambil: "Selesai" → "Diambil"
4. Transaksi complete
```

## 🐛 Troubleshooting

### ❌ Common Issues & Solutions

| **Problem** | **Solution** |
|-------------|--------------|
| Database connection failed | Check config.php credentials |
| Session tidak persistent | Set session timeout di config |
| Stored procedure error | Use direct queries instead |
| View database error | Use JOIN queries in PHP |
| Permission denied | Set correct file permissions (644/755) |

### 🔍 Debug Mode
```php
// Di config.php - untuk development
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## 📈 Performance Optimization

### ⚡ Optimization Tips
- **Database indexing** pada foreign keys
- **Query optimization** dengan JOIN yang efisien
- **Caching** untuk data statis
- **Asset compression** untuk CSS/JS
- **Lazy loading** untuk data tables

## 🔗 Links & References

### 🌐 Live Demo
- **URL**: [http://laundrycraft.web.id](http://laundrycraft.web.id)
- **Demo Login**: 
  - Username: `admin`
  - Password: `laundry123`

### 📚 Documentation
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Chart.js Documentation](https://www.chartjs.org/docs/)

### 🐛 Reporting Issues
Gunakan [GitHub Issues](https://github.com/username/laundrycrafty/issues) untuk melaporkan bug atau request fitur.

## Developer

**Arya Rangga Putra Pratama**
- NRP: 5025241072
- Kelas: Pemrograman Web A

## Acknowledgments

- Dosen Pengampu: **Bapak Fajar**
- Hosting Provider: **Rumahweb**
- Icons: **Font Awesome**
- Charts: **Chart.js**

---

`#PHPNative` `#WebDevelopment` `#LaundrySystem` `#UniversityProject`

</div>
