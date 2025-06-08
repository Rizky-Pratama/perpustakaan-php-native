<?php
// Include header
require_once '../includes/header.php';

// Mengharuskan login
requireLogin();

$id_user = $_SESSION['user_id'];
$user = getUserById($id_user);

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validasi input
  $nama_lengkap = trim($_POST['nama_lengkap']);
  $email = trim($_POST['email']);
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];

  $errors = [];

  // Validasi nama lengkap
  if (empty($nama_lengkap)) {
    $errors[] = "Nama lengkap harus diisi";
  }

  // Validasi email
  if (empty($email)) {
    $errors[] = "Email harus diisi";
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid";
  } else {
    // Cek email sudah digunakan atau belum (kecuali oleh user ini sendiri)
    $email = mysqli_real_escape_string($conn, $email);
    $check_query = "SELECT * FROM users WHERE email = '$email' AND id_user != '$id_user'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
      $errors[] = "Email sudah digunakan";
    }
  }

  // Validasi jika ingin mengganti password
  if (!empty($new_password)) {
    // Verifikasi password lama
    if (empty($current_password)) {
      $errors[] = "Password saat ini harus diisi";
    } else if (!verifyPassword($current_password, $user['password'])) {
      $errors[] = "Password saat ini salah";
    }

    // Validasi password baru
    if (strlen($new_password) < 6) {
      $errors[] = "Password baru minimal 6 karakter";
    } else if ($new_password !== $confirm_password) {
      $errors[] = "Konfirmasi password baru tidak cocok";
    }
  }

  // Jika tidak ada error, update data user
  if (empty($errors)) {
    $nama_lengkap = mysqli_real_escape_string($conn, $nama_lengkap);

    // Buat query update
    $query = "UPDATE users SET
              nama_lengkap = '$nama_lengkap',
              email = '$email'";

    // Jika password baru diisi, update password
    if (!empty($new_password)) {
      $hashed_password = hashPassword($new_password);
      $query .= ", password = '$hashed_password'";
    }

    $query .= " WHERE id_user = '$id_user'";

    if (mysqli_query($conn, $query)) {
      // Update informasi user pada session
      $_SESSION['notification'] = "Profil berhasil diperbarui";

      // Redirect untuk refresh halaman
      header("Location: profile.php");
      exit;
    } else {
      $errors[] = "Gagal memperbarui profil: " . mysqli_error($conn);
    }
  }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Profil Saya</h2>
</div>

<?php if (isset($_SESSION['notification'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $_SESSION['notification'];
    unset($_SESSION['notification']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        Edit Profil
      </div>
      <div class="card-body">
        <form method="POST" action="">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            <div class="form-text">Username tidak dapat diubah</div>
          </div>

          <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
              value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" class="form-control" id="email" name="email"
              value="<?php echo htmlspecialchars($user['email']); ?>" required>
          </div>

          <hr>

          <h5>Ganti Password</h5>
          <div class="mb-3">
            <label for="current_password" class="form-label">Password Saat Ini</label>
            <input type="password" class="form-control" id="current_password" name="current_password">
          </div>

          <div class="mb-3">
            <label for="new_password" class="form-label">Password Baru</label>
            <input type="password" class="form-control" id="new_password" name="new_password">
            <div class="form-text">Kosongkan jika tidak ingin mengganti password</div>
          </div>

          <div class="mb-3">
            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
          </div>

          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        Informasi Akun
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between">
            <span>Role</span>
            <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-info'; ?>">
              <?php echo $user['role']; ?>
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Status</span>
            <span class="badge <?php echo $user['active'] ? 'bg-success' : 'bg-secondary'; ?>">
              <?php echo $user['active'] ? 'Aktif' : 'Nonaktif'; ?>
            </span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Terdaftar</span>
            <span><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
