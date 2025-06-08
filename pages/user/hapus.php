<?php
// Include config dan database
require_once '../../config/config.php';
require_once CONFIG_PATH . 'database.php';
require_once INCLUDES_PATH . 'auth_functions.php';

// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Cek apakah pengguna adalah admin
requireAdmin();

// Pastikan ID user diberikan
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $_SESSION['notification'] = "ID user tidak valid";
  header("Location: index.php");
  exit;
}

$id_user = mysqli_real_escape_string($conn, $_GET['id']);

// Cek apakah user mencoba menghapus dirinya sendiri
if ($id_user == $_SESSION['user_id']) {
  $_SESSION['notification'] = "Anda tidak dapat menghapus akun Anda sendiri!";
  header("Location: index.php");
  exit;
}

// Lakukan penghapusan user
$query = "DELETE FROM users WHERE id_user = '$id_user'";

if (mysqli_query($conn, $query)) {
  $_SESSION['notification'] = "User berhasil dihapus";
} else {
  $_SESSION['notification'] = "Gagal menghapus user: " . mysqli_error($conn);
}

header("Location: index.php");
exit;
