<?php
// Include header
require_once '../../includes/header.php';

// Periksa apakah ID buku diberikan
if (!isset($_GET['id']) || empty($_GET['id'])) {
  header("Location: index.php");
  exit;
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);

// Hapus data buku dari database
$query = "DELETE FROM buku WHERE id_buku = '$id_buku'";

if (mysqli_query($conn, $query)) {
  // Redirect dengan pesan sukses
  $_SESSION['notification'] = "Buku berhasil dihapus!";
  header("Location: index.php");
  exit;
} else {
  $_SESSION['notification'] = "Error: " . mysqli_error($conn);
  header("Location: index.php");
  exit;
}
