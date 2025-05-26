<?php
// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'perpustakaan';

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Mengecek koneksi
if (!$conn) {
  die("Koneksi gagal: " . mysqli_connect_error());
}
