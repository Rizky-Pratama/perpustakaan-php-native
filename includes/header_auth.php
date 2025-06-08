<?php
// Import file konfigurasi global
require_once dirname(__DIR__) . '/config/config.php';

// Import file koneksi database
require_once CONFIG_PATH . 'database.php';

// Import fungsi autentikasi
require_once INCLUDES_PATH . 'auth_functions.php';

// Set page title jika belum ada
if (!isset($page_title)) {
  $page_title = 'Perpustakaan';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?> - Sistem Perpustakaan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?php echo CSS_URL; ?>style.css">
  <style>
    .auth-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      background-color: #f8f9fa;
      padding: 20px;
    }
  </style>
</head>

<body>
  <div class="auth-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="text-center mb-4">
            <h1 class="display-5 fw-bold text-primary">Sistem Perpustakaan</h1>
            <p class="lead">Selamat datang di sistem perpustakaan sederhana</p>
          </div>
