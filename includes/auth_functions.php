<?php

/**
 * File berisi fungsi-fungsi untuk autentikasi dan manajemen user
 */

/**
 * Mengecek apakah user sudah login
 *
 * @return boolean
 */
function isLoggedIn()
{
  return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Mengecek apakah user memiliki role admin
 *
 * @return boolean
 */
function isAdmin()
{
  return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Mengalihkan ke halaman login jika belum login
 */
function requireLogin()
{
  if (!isLoggedIn()) {
    $_SESSION['error'] = "Silakan login terlebih dahulu!";
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
  }
}

/**
 * Mengalihkan ke halaman utama jika bukan admin
 */
function requireAdmin()
{
  requireLogin();

  if (!isAdmin()) {
    $_SESSION['error'] = "Anda tidak memiliki akses ke halaman ini!";
    header("Location: " . BASE_URL . "index.php");
    exit;
  }
}

/**
 * Mengenkripsi password
 *
 * @param string $password
 * @return string
 */
function hashPassword($password)
{
  return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifikasi password
 *
 * @param string $password Password yang diinput
 * @param string $hash Hash password dari database
 * @return boolean
 */
function verifyPassword($password, $hash)
{
  return password_verify($password, $hash);
}

/**
 * Mendapatkan data user berdasarkan ID
 *
 * @param int $id_user
 * @return array|false
 */
function getUserById($id_user)
{
  global $conn;

  $id_user = mysqli_real_escape_string($conn, $id_user);
  $query = "SELECT * FROM users WHERE id_user = '$id_user'";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    return mysqli_fetch_assoc($result);
  }

  return false;
}

/**
 * Mendapatkan data user berdasarkan username
 *
 * @param string $username
 * @return array|false
 */
function getUserByUsername($username)
{
  global $conn;

  $username = mysqli_real_escape_string($conn, $username);
  $query = "SELECT * FROM users WHERE username = '$username'";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    return mysqli_fetch_assoc($result);
  }

  return false;
}
