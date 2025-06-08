-- Membuat database perpustakaan
CREATE DATABASE IF NOT EXISTS perpustakaan;

USE perpustakaan;

-- Membuat tabel kategori
CREATE TABLE IF NOT EXISTS kategori (
  id_kategori INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL
);

-- Membuat tabel buku
CREATE TABLE IF NOT EXISTS buku (
  id_buku INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(255) NOT NULL,
  pengarang VARCHAR(255) NOT NULL,
  penerbit VARCHAR(255) NOT NULL,
  tahun_terbit YEAR NOT NULL,
  isbn VARCHAR(20),
  jumlah_halaman INT,
  stok INT NOT NULL DEFAULT 0,
  id_kategori INT,
  tanggal_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE SET NULL
);

-- Tabel users untuk autentikasi dan role management
CREATE TABLE IF NOT EXISTS users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel peminjaman
CREATE TABLE IF NOT EXISTS peminjaman (
  id_peminjaman INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  id_buku INT NOT NULL,
  tanggal_pinjam DATE NOT NULL,
  tanggal_kembali DATE NOT NULL,
  status ENUM('menunggu', 'ditolak', 'dipinjam', 'dikembalikan', 'terlambat', 'proses_kembali') NOT NULL DEFAULT 'menunggu',
  denda DECIMAL(10,2) DEFAULT 0.00,
  keterangan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_buku) REFERENCES buku(id_buku) ON DELETE CASCADE
);

-- Data awal kategori
INSERT INTO kategori (nama_kategori) VALUES
('Fiksi'),
('Non-Fiksi'),
('Pendidikan'),
('Teknologi'),
('Sejarah');

-- Data awal buku
INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, isbn, jumlah_halaman, stok, id_kategori) VALUES
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, '9789793062792', 529, 10, 1),
('Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 1980, '9789799144850', 535, 5, 1),
('Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, '9786024125189', 320, 8, 2),
('Pemrograman PHP', 'John Doe', 'Informatika', 2022, '9781234567890', 450, 15, 4);

-- Admin default dengan password "password"
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$ndu62a9Zzr0RMjj8zNwSWOhOwfpZrZfyI7XYVEWAvq3dZKN4BVjJe', 'Administrator', 'admin@perpus.com', 'admin');

-- User default dengan password "password"
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('user', '$2y$10$ndu62a9Zzr0RMjj8zNwSWOhOwfpZrZfyI7XYVEWAvq3dZKN4BVjJe', 'User Biasa', 'user@perpus.com', 'user');
