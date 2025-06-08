<?php
// Mulai session
session_start();

// Include config
require_once '../config/config.php';

// Include database dan functions
require_once CONFIG_PATH . 'database.php';
require_once INCLUDES_PATH . 'auth_functions.php';

// Cek jika form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
  $password = $_POST['password'];

  // Validasi input
  if (empty($username) || empty($password)) {
    $_SESSION['error'] = "Username dan password harus diisi";
    header("Location: login.php");
    exit();
  }

  // Cek username di database
  $user = getUserByUsername($username);

  if ($user && verifyPassword($password, $user['password'])) {
    // Jika password cocok, simpan data user ke session
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['user_name'] = $user['nama_lengkap'];
    $_SESSION['user_username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];

    // Redirect ke halaman utama
    header("Location: " . BASE_URL);
    exit();
  } else {
    // Jika username atau password salah
    $_SESSION['error'] = "Username atau password salah";
    header("Location: login.php");
    exit();
  }
} else {
  // Jika akses langsung ke file ini tanpa submit form
  header("Location: login.php");
  exit();
}
