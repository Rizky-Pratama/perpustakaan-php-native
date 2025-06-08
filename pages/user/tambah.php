<?php
// Include header
require_once '../../includes/header.php';

// Cek apakah pengguna adalah admin
requireAdmin();

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validasi input
  $username = trim($_POST['username']);
  $nama_lengkap = trim($_POST['nama_lengkap']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $role = $_POST['role'];
  $active = isset($_POST['active']) ? 1 : 0;

  $errors = [];

  // Validasi username
  if (empty($username)) {
    $errors[] = "Username harus diisi";
  } else {
    // Cek username sudah digunakan atau belum
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
      $errors[] = "Username sudah digunakan";
    }
  }

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
    // Cek email sudah digunakan atau belum
    $check_query = "SELECT * FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
      $errors[] = "Email sudah digunakan";
    }
  }

  // Validasi password
  if (empty($password)) {
    $errors[] = "Password harus diisi";
  } else if (strlen($password) < 6) {
    $errors[] = "Password minimal 6 karakter";
  } else if ($password !== $confirm_password) {
    $errors[] = "Konfirmasi password tidak cocok";
  }

  // Jika tidak ada error, simpan data user
  if (empty($errors)) {
    $hashed_password = hashPassword($password);

    // Query untuk menyimpan user baru
    $query = "INSERT INTO users (username, password, nama_lengkap, email, role, active)
              VALUES ('$username', '$hashed_password', '$nama_lengkap', '$email', '$role', $active)";

    if (mysqli_query($conn, $query)) {
      $_SESSION['notification'] = "User baru berhasil ditambahkan";
      header("Location: index.php");
      exit;
    } else {
      $errors[] = "Gagal menambahkan user: " . mysqli_error($conn);
    }
  }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Tambah User Baru</h2>
  <a href="index.php" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Kembali
  </a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form method="POST" action="">
      <div class="mb-3">
        <label for="username" class="form-label">Username *</label>
        <input type="text" class="form-control" id="username" name="username"
          value="<?php echo isset($username) ? $username : ''; ?>" required>
      </div>

      <div class="mb-3">
        <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
          value="<?php echo isset($nama_lengkap) ? $nama_lengkap : ''; ?>" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email *</label>
        <input type="email" class="form-control" id="email" name="email"
          value="<?php echo isset($email) ? $email : ''; ?>" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password *</label>
        <input type="password" class="form-control" id="password" name="password" required>
        <div class="form-text">Password minimal 6 karakter</div>
      </div>

      <div class="mb-3">
        <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Role *</label>
        <select class="form-select" id="role" name="role" required>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="active" name="active" checked>
        <label class="form-check-label" for="active">User Aktif</label>
      </div>

      <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
