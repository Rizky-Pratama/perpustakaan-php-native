<?php
// Memulai session
session_start();

// Import file konfigurasi global
require_once dirname(__DIR__) . '/config/config.php';

// Import file koneksi database
require_once CONFIG_PATH . 'database.php';

// Import auth functions
require_once INCLUDES_PATH . 'auth_functions.php';

// Update status peminjaman yang terlambat jika user login
if (isLoggedIn()) {
  require_once INCLUDES_PATH . 'peminjaman_functions.php';
  updateStatusTerlambat();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Perpustakaan Sederhana</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?php echo CSS_URL; ?>style.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Perpustakaan</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>">Beranda</a>
          </li>

          <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?> <!-- Menu untuk Admin -->
              <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>pages/dashboard.php">
                  <i class="bi bi-speedometer2"></i> Dashboard
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="bukuDropdown" role="button" data-bs-toggle="dropdown">
                  <i class="bi bi-book"></i> Kelola Buku
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/buku/">Daftar Buku</a></li>
                  <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/buku/tambah.php">Tambah Buku Baru</a></li>
                  <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/kategori/">Kelola Kategori</a></li>
                </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>pages/user/">
                  <i class="bi bi-people"></i> Kelola User
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="peminjamanDropdown" role="button" data-bs-toggle="dropdown">
                  <i class="bi bi-journal-text"></i> Kelola Peminjaman
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/peminjaman/admin.php">Semua Peminjaman</a></li>
                  <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/peminjaman/admin.php?status=dipinjam">Peminjaman Aktif</a></li>
                  <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/peminjaman/admin.php?status=terlambat">Peminjaman Terlambat</a></li>
                </ul>
              </li>
            <?php else: ?>
              <!-- Menu untuk User biasa -->
              <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>pages/buku/">
                  <i class="bi bi-book"></i> Katalog Buku
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="peminjamanUserDropdown" role="button" data-bs-toggle="dropdown">
                  <i class="bi bi-journal-text"></i> Peminjaman
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/peminjaman/index.php">Peminjaman Aktif</a></li>
                  <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/peminjaman/history.php">Riwayat Peminjaman</a></li>
                </ul>
              </li>
            <?php endif; ?>
          <?php endif; ?>
        </ul>

        <ul class="navbar-nav">
          <?php if (isLoggedIn()): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i> <?php echo $_SESSION['user_name']; ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>pages/profile.php">Profil Saya</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>auth/logout.php">Logout</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>auth/login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>auth/register.php">Daftar</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
