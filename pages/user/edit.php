<?php
// Include header
require_once '../../includes/header.php';

// Cek apakah pengguna adalah admin
requireAdmin();

// Pastikan ID user diberikan
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $_SESSION['notification'] = "ID user tidak valid";
  header("Location: index.php");
  exit;
}

$id_user = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data user
$query = "SELECT * FROM users WHERE id_user = '$id_user'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
  $_SESSION['notification'] = "User tidak ditemukan";
  header("Location: index.php");
  exit;
}

$user = mysqli_fetch_assoc($result);

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validasi input
  $nama_lengkap = trim($_POST['nama_lengkap']);
  $email = trim($_POST['email']);
  $role = $_POST['role'];
  $active = isset($_POST['active']) ? 1 : 0;
  $password = $_POST['password'];

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
    $check_query = "SELECT * FROM users WHERE email = '$email' AND id_user != '$id_user'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
      $errors[] = "Email sudah digunakan";
    }
  }

  // Jika tidak ada error, update data user
  if (empty($errors)) {
    // Buat query update
    $query = "UPDATE users SET
              nama_lengkap = '$nama_lengkap',
              email = '$email',
              role = '$role',
              active = $active";

    // Jika password diisi, update juga password
    if (!empty($password)) {
      $hashed_password = hashPassword($password);
      $query .= ", password = '$hashed_password'";
    }

    $query .= " WHERE id_user = '$id_user'";

    if (mysqli_query($conn, $query)) {
      $_SESSION['notification'] = "Data user berhasil diperbarui";
      header("Location: index.php");
      exit;
    } else {
      $errors[] = "Gagal memperbarui user: " . mysqli_error($conn);
    }
  }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Edit User: <?php echo htmlspecialchars($user['username']); ?></h2>
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

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password">
        <div class="form-text">Kosongkan jika tidak ingin mengganti password</div>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Role *</label>
        <select class="form-select" id="role" name="role" required>
          <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
          <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="active" name="active"
          <?php echo $user['active'] ? 'checked' : ''; ?>>
        <label class="form-check-label" for="active">User Aktif</label>
      </div>

      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
