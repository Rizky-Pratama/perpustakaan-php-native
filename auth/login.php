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
$page_title = "Login";

// Include header
require_once INCLUDES_PATH . 'header_auth.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Login</h4>
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

        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Login</button>
          </div>
        </form>

        <div class="mt-3 text-center">
          <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
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
