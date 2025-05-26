<?php
// Include config untuk konstanta path
require_once '../../config/config.php';

// Include koneksi database
require_once CONFIG_PATH . 'database.php';

// Start session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Proses Tambah Kategori
if (isset($_POST['tambah'])) {
  $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

  $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";

  if (mysqli_query($conn, $query)) {
    $_SESSION['notification'] = "Kategori berhasil ditambahkan!";
  } else {
    $_SESSION['notification'] = "Error: " . mysqli_error($conn);
  }

  header("Location: index.php");
  exit;
}

// Proses Edit Kategori
if (isset($_POST['edit'])) {
  $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
  $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

  $query = "UPDATE kategori SET nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_kategori'";

  if (mysqli_query($conn, $query)) {
    $_SESSION['notification'] = "Kategori berhasil diperbarui!";
  } else {
    $_SESSION['notification'] = "Error: " . mysqli_error($conn);
  }

  header("Location: index.php");
  exit;
}

// Proses Hapus Kategori
if (isset($_GET['hapus'])) {
  $id_kategori = mysqli_real_escape_string($conn, $_GET['hapus']);

  // Periksa apakah kategori digunakan oleh buku
  $query_check = "SELECT COUNT(*) as total FROM buku WHERE id_kategori = '$id_kategori'";
  $result_check = mysqli_query($conn, $query_check);
  $row = mysqli_fetch_assoc($result_check);

  if ($row['total'] > 0) {
    // Update ID kategori menjadi NULL untuk buku-buku yang menggunakan kategori ini
    $query_update = "UPDATE buku SET id_kategori = NULL WHERE id_kategori = '$id_kategori'";
    mysqli_query($conn, $query_update);
  }

  // Hapus kategori
  $query = "DELETE FROM kategori WHERE id_kategori = '$id_kategori'";

  if (mysqli_query($conn, $query)) {
    $_SESSION['notification'] = "Kategori berhasil dihapus!";
  } else {
    $_SESSION['notification'] = "Error: " . mysqli_error($conn);
  }

  header("Location: index.php");
  exit;
}

// Jika tidak ada operasi yang dilakukan, redirect ke index
header("Location: index.php");
exit;
