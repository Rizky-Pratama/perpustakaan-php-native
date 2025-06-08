<?php
// Include config dan database
require_once '../../config/config.php';
require_once CONFIG_PATH . 'database.php';
require_once INCLUDES_PATH . 'auth_functions.php';
require_once INCLUDES_PATH . 'peminjaman_functions.php';

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Mengharuskan login
requireLogin();

// Validasi ID peminjaman
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $_SESSION['notification'] = "ID peminjaman tidak valid";
  header("Location: index.php");
  exit;
}

$id_peminjaman = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data peminjaman
$peminjaman = getDetailPeminjaman($id_peminjaman);

// Pastikan peminjaman ditemukan dan milik user yang login
if (!$peminjaman || $peminjaman['id_user'] != $_SESSION['user_id']) {
  $_SESSION['notification'] = "Data peminjaman tidak ditemukan";
  header("Location: index.php");
  exit;
}

// Pastikan status masih dipinjam atau terlambat
if ($peminjaman['status'] === 'dikembalikan') {
  $_SESSION['notification'] = "Buku ini sudah dikembalikan";
  header("Location: index.php");
  exit;
}

// Hitung denda jika terlambat
$denda = 0;
if ($peminjaman['status'] === 'terlambat') {
  $denda = hitungDenda($peminjaman['tanggal_kembali']);
}

// Proses pengajuan pengembalian buku
$id_buku = $peminjaman['id_buku'];
$tanggal_kembali_aktual = date('Y-m-d');

// Update status peminjaman menjadi proses_kembali
$query = "UPDATE peminjaman
          SET status = 'proses_kembali',
              updated_at = NOW()
          WHERE id_peminjaman = '$id_peminjaman'";

if (mysqli_query($conn, $query)) {
  $_SESSION['notification'] = "Pengajuan pengembalian buku berhasil. Silahkan kembalikan buku ke petugas perpustakaan untuk konfirmasi.";
} else {
  $_SESSION['notification'] = "Gagal mengajukan pengembalian buku: " . mysqli_error($conn);
}

// Kembali ke halaman daftar peminjaman
header("Location: index.php");
exit;
