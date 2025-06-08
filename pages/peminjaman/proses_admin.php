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

// Cek apakah pengguna adalah admin
requireAdmin();

// Validasi action dan ID
if (isset($_POST['action']) && isset($_POST['id'])) {
  // Handle form POST submission (for rejection with comment)
  $action = $_POST['action'];
  $id_peminjaman = mysqli_real_escape_string($conn, $_POST['id']);
  $keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($conn, $_POST['keterangan']) : '';
} else if (isset($_GET['action']) && isset($_GET['id']) && !empty($_GET['id'])) {
  // Handle GET request for approve and return
  $action = $_GET['action'];
  $id_peminjaman = mysqli_real_escape_string($conn, $_GET['id']);
  $keterangan = '';
} else {
  $_SESSION['notification'] = "Parameter tidak valid";
  header("Location: admin.php");
  exit;
}

// Ambil data peminjaman
$peminjaman = getDetailPeminjaman($id_peminjaman);

// Pastikan peminjaman ditemukan
if (!$peminjaman) {
  $_SESSION['notification'] = "Data peminjaman tidak ditemukan";
  header("Location: admin.php");
  exit;
}

// Ambil keterangan jika ada
$keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($conn, $_POST['keterangan']) : '';

// Proses berdasarkan action
switch ($action) {
  case 'approve':
    // Proses persetujuan peminjaman
    if ($peminjaman['status'] !== 'menunggu') {
      $_SESSION['notification'] = "Status peminjaman tidak valid untuk disetujui";
      header("Location: admin.php");
      exit;
    }

    if (prosesPermintaanPeminjaman($id_peminjaman, 'approve', $keterangan)) {
      $_SESSION['notification'] = "Permintaan peminjaman berhasil disetujui";
    } else {
      $_SESSION['notification'] = "Gagal menyetujui peminjaman";
    }

    header("Location: admin.php");
    exit;
    break;

  case 'reject':
    // Proses penolakan peminjaman
    if ($peminjaman['status'] !== 'menunggu') {
      $_SESSION['notification'] = "Status peminjaman tidak valid untuk ditolak";
      header("Location: admin.php");
      exit;
    }

    if (prosesPermintaanPeminjaman($id_peminjaman, 'reject', $keterangan)) {
      $_SESSION['notification'] = "Permintaan peminjaman ditolak";
    } else {
      $_SESSION['notification'] = "Gagal menolak peminjaman";
    }

    header("Location: admin.php");
    exit;
    break;

  case 'confirm_return':
    // Proses konfirmasi pengembalian
    if ($peminjaman['status'] !== 'proses_kembali') {
      $_SESSION['notification'] = "Status peminjaman tidak valid untuk konfirmasi pengembalian";
      header("Location: admin.php");
      exit;
    }

    if (prosesPermintaanPengembalian($id_peminjaman, $keterangan)) {
      $_SESSION['notification'] = "Pengembalian buku berhasil dikonfirmasi";
    } else {
      $_SESSION['notification'] = "Gagal mengkonfirmasi pengembalian buku";
    }

    header("Location: admin.php");
    exit;
    break;

  case 'return':
    // Proses pengembalian langsung oleh admin
    if ($peminjaman['status'] === 'dikembalikan') {
      $_SESSION['notification'] = "Buku ini sudah dikembalikan";
      header("Location: admin.php");
      exit;
    }

    // Hitung denda jika terlambat
    $denda = 0;
    if ($peminjaman['status'] === 'terlambat') {
      $denda = hitungDenda($peminjaman['tanggal_kembali']);
    }

    // Update status peminjaman
    $id_buku = $peminjaman['id_buku'];
    $query = "UPDATE peminjaman
              SET status = 'dikembalikan',
                  denda = '$denda',
                  updated_at = NOW()
              WHERE id_peminjaman = '$id_peminjaman'";

    if (mysqli_query($conn, $query)) {
      // Update stok buku (tambah 1)
      updateStokBuku($id_buku, 1);

      $_SESSION['notification'] = "Buku berhasil dikembalikan" . ($denda > 0 ? " dengan denda Rp " . number_format($denda, 0, ',', '.') : "");
    } else {
      $_SESSION['notification'] = "Gagal mengembalikan buku: " . mysqli_error($conn);
    }

    header("Location: admin.php");
    exit;
    break;

  default:
    $_SESSION['notification'] = "Action tidak valid";
    header("Location: admin.php");
    exit;
    break;
}
