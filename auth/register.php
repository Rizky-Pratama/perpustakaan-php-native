<?php
// Mulai session
session_start();

// Include config
require_once '../config/config.php';

// Cek jika user sudah login, redirect ke halaman utama
if (isset($_SESSION['user_id'])) {
  header("Location: " . BASE_URL);
  exit();
}

// Set judul halaman
$page_title = "Pendaftaran";

// Include header
require_once INCLUDES_PATH . 'header_auth.php';
?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card shadow">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Daftar Akun Baru</h4>
      </div>
      <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form action="proses_register.php" method="POST">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Daftar</button>
          </div>
        </form>

        <div class="mt-3 text-center">
          <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
          <p><a href="<?php echo BASE_URL; ?>">Kembali ke Beranda</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer_auth.php';
?>
