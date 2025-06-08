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
  // Ambil dan bersihkan data dari form
  $nama_lengkap = trim(mysqli_real_escape_string($conn, $_POST['nama_lengkap']));
  $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
  $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
  $password = $_POST['password'];
  $konfirmasi_password = $_POST['konfirmasi_password'];

  // Validasi input
  $errors = [];

  if (empty($nama_lengkap)) {
    $errors[] = "Nama lengkap harus diisi";
  }

  if (empty($email)) {
    $errors[] = "Email harus diisi";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid";
  }

  if (empty($username)) {
    $errors[] = "Username harus diisi";
  } elseif (strlen($username) < 4) {
    $errors[] = "Username minimal 4 karakter";
  }

  if (empty($password)) {
    $errors[] = "Password harus diisi";
  } elseif (strlen($password) < 6) {
    $errors[] = "Password minimal 6 karakter";
  }

  if ($password !== $konfirmasi_password) {
    $errors[] = "Konfirmasi password tidak cocok";
  }

  // Cek apakah username sudah digunakan
  $query = "SELECT id_user FROM users WHERE username = '$username'";
  $result = mysqli_query($conn, $query);
  if (mysqli_num_rows($result) > 0) {
    $errors[] = "Username sudah digunakan";
  }

  // Cek apakah email sudah digunakan
  $query = "SELECT id_user FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $query);
  if (mysqli_num_rows($result) > 0) {
    $errors[] = "Email sudah digunakan";
  }

  // Jika ada error
  if (!empty($errors)) {
    $_SESSION['error'] = implode("<br>", $errors);
    header("Location: register.php");
    exit();
  }

  // Hash password
  $password_hash = hashPassword($password);

  // Simpan data user ke database
  $query = "INSERT INTO users (nama_lengkap, email, username, password, role)
            VALUES ('$nama_lengkap', '$email', '$username', '$password_hash', 'user')";

  if (mysqli_query($conn, $query)) {
    // Jika berhasil, redirect ke halaman login dengan pesan sukses
    $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
    header("Location: login.php");
    exit();
  } else {
    // Jika gagal
    $_SESSION['error'] = "Pendaftaran gagal: " . mysqli_error($conn);
    header("Location: register.php");
    exit();
  }
} else {
  // Jika akses langsung ke file ini tanpa submit form
  header("Location: register.php");
  exit();
}
