# Sistem Perpustakaan Sederhana

Aplikasi pengelolaan perpustakaan berbasis web yang dibangun dengan PHP native (tanpa framework) dan MySQL. Aplikasi ini menyediakan fitur manajemen data buku dan kategori dengan antarmuka yang user-friendly.

## Fitur Utama

- **Manajemen Buku**: CRUD (Create, Read, Update, Delete) untuk data buku
- **Manajemen Kategori**: CRUD untuk kategori buku
- **Tampilan Responsif**: Dibangun dengan Bootstrap 5 untuk pengalaman pengguna yang baik di berbagai perangkat
- **Clean Code**: Menggunakan pendekatan konstanta untuk pengelolaan path dan URL
- **Relasi Database**: Mendukung relasi antara buku dan kategori

## Teknologi yang Digunakan

- **PHP** 7.4+ (Native, tanpa framework)
- **MySQL/MariaDB** untuk database
- **Bootstrap 5** untuk UI
- **JavaScript** untuk interaksi pada sisi klien

## Cara Instalasi

### Prasyarat

- Web server (Apache/Nginx)
- PHP 7.4 atau lebih baru
- MySQL/MariaDB
- Laragon/XAMPP/WAMP atau sejenisnya

### Langkah Instalasi

1. Clone atau download repository ini ke direktori web server Anda
2. Import file `database.sql` ke dalam MySQL/MariaDB Anda
3. Sesuaikan konfigurasi database pada file `config/database.php`
4. Sesuaikan konstanta URL pada file `config/config.php` sesuai dengan struktur direktori Anda
5. Akses aplikasi melalui browser: `http://localhost/perpustakaan_pwl`

## Struktur Kode

### Pendekatan Penggunaan Konstanta untuk Path

Proyek perpustakaan ini menggunakan pendekatan konstanta untuk mengelola path dan URL. Pendekatan ini lebih bersih (clean code) dibandingkan dengan menggunakan variabel `$base_path` di setiap file.

### Cara Kerja

1. File `config/config.php` mendefinisikan semua konstanta path dan URL yang dibutuhkan:

   - `ROOT_PATH` - Path absolut ke direktori root proyek
   - `CONFIG_PATH` - Path ke direktori config
   - `INCLUDES_PATH` - Path ke direktori includes
   - `ASSETS_PATH` - Path ke direktori aset
   - `BASE_URL` - URL base untuk proyek
   - `ASSETS_URL` - URL untuk aset
   - `CSS_URL` - URL untuk file CSS
   - `JS_URL` - URL untuk file JavaScript

2. Setiap file PHP harus meng-include file config.php (baik langsung atau melalui `header.php`)

3. Semua path dan URL menggunakan konstanta yang telah didefinisikan

### Cara Membuat File Baru

Saat membuat file PHP baru, ikuti pola berikut:

#### Untuk file di direktori root:

```php
<?php
// Include header
require_once 'includes/header.php';

// Kode anda di sini
...

// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
```

#### Untuk file di subdirektori (misalnya pages/nama_modul/):

```php
<?php
// Include header
require_once '../../includes/header.php';

// Kode anda di sini
...

// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
```

### Cara Menggunakan Link dan URL

Untuk membuat link ke halaman lain, gunakan konstanta BASE_URL:

```php
<a href="<?php echo BASE_URL; ?>pages/nama_modul/nama_file.php">Link Text</a>
```

Untuk memanggil file CSS:

```php
<link rel="stylesheet" href="<?php echo CSS_URL; ?>nama_file.css">
```

Untuk memanggil file JavaScript:

```php
<script src="<?php echo JS_URL; ?>nama_file.js"></script>
```

### Keuntungan Pendekatan Ini

1. **Konsistensi**: Semua path didefinisikan di satu tempat, yaitu `config/config.php`
2. **Keamanan**: Menggunakan konstanta mencegah perubahan path yang tidak disengaja
3. **Pemeliharaan lebih mudah**: Jika struktur folder berubah, hanya perlu mengubah di satu tempat
4. **Clean Code**: Kode menjadi lebih bersih karena tidak perlu menentukan `$base_path` di setiap file
5. **Reliable**: Path selalu konsisten di seluruh aplikasi

## Struktur Folder

```
perpustakaan_pwl/
├── assets/               # File statis (CSS, JavaScript, dll)
│   ├── css/              # File CSS
│   └── js/               # File JavaScript
├── config/               # File konfigurasi
│   ├── config.php        # Konstanta path dan URL
│   └── database.php      # Konfigurasi koneksi database
├── includes/             # Komponen yang digunakan berulang
│   ├── footer.php        # Footer untuk semua halaman
│   └── header.php        # Header untuk semua halaman
├── pages/                # Halaman-halaman aplikasi
│   ├── buku/             # CRUD untuk buku
│   │   ├── detail.php    # Detail buku
│   │   ├── edit.php      # Form edit buku
│   │   ├── hapus.php     # Proses hapus buku
│   │   ├── index.php     # Daftar buku
│   │   └── tambah.php    # Form tambah buku
│   └── kategori/         # CRUD untuk kategori
│       ├── index.php     # Daftar dan form kategori
│       └── proses_kategori.php # Proses CRUD kategori
├── database.sql          # File SQL untuk struktur database
├── index.php             # Halaman utama
└── README.md             # Dokumentasi proyek
```

## Fitur yang Diimplementasikan

### Manajemen Buku

- Menampilkan daftar buku dengan informasi lengkap
- Menambah data buku baru dengan relasi ke kategori
- Melihat detail informasi buku
- Mengubah data buku yang sudah ada
- Menghapus data buku

### Manajemen Kategori

- Menampilkan daftar kategori beserta jumlah buku di setiap kategori
- Menambah kategori baru
- Mengedit nama kategori
- Menghapus kategori dengan penanganan relasi ke buku

### Tampilan UI

- Halaman beranda dengan daftar buku terbaru
- Tampilan yang responsif pada berbagai ukuran layar
- Notifikasi untuk operasi CRUD yang berhasil

## Rencana Pengembangan Selanjutnya

- Sistem autentikasi (login/logout)
- Manajemen anggota perpustakaan
- Sistem peminjaman dan pengembalian buku
- Pencarian dan filter data buku
- Laporan dan statistik perpustakaan

## Konfigurasi Editor & Coding Style

Proyek ini menggunakan beberapa tools untuk menjaga konsistensi kode:

### EditorConfig

File `.editorconfig` mengatur konfigurasi dasar editor seperti indentasi, line ending, dan charset:

- 2 spasi untuk file PHP
- 2 spasi untuk file CSS/JS/JSON
- UTF-8 encoding
- LF line endings

### Prettier

File `.prettierrc` mengatur formatter kode:

- Max line width: 100 karakter
- Single quotes untuk string
- Semicolon di akhir statement
- Konfigurasi khusus untuk PHP

### VSCode Settings

Folder `.vscode` berisi:

- `settings.json`: Konfigurasi editor VSCode
- `extensions.json`: Rekomendasi ekstensi yang berguna untuk proyek ini

### Cara Penggunaan

1. Install ekstensi EditorConfig dan Prettier di editor Anda
2. Untuk VSCode, install ekstensi yang direkomendasikan dengan:
   - Buka Command Palette (Ctrl+Shift+P)
   - Ketik dan pilih "Extensions: Show Recommended Extensions"
3. Kode akan diformat secara otomatis saat menyimpan file

## Kontribusi

Jika Anda ingin berkontribusi pada proyek ini:

1. Fork repository
2. Buat branch fitur baru (`git checkout -b feature/nama-fitur`)
3. Commit perubahan Anda (`git commit -m 'Menambahkan fitur X'`)
4. Push ke branch (`git push origin feature/nama-fitur`)
5. Ajukan Pull Request
6. Pastikan kode Anda sesuai dengan standar format yang ditentukan

## Lisensi

MIT License - Bebas digunakan, dimodifikasi, dan didistribusikan dengan menyertakan lisensi asli.
