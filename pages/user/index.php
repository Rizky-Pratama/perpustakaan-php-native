<?php
// Include header
require_once '../../includes/header.php';

// Cek apakah pengguna adalah admin
requireAdmin();

// Query untuk mengambil semua user
$query = "SELECT * FROM users ORDER BY id_user DESC";
$result = mysqli_query($conn, $query);

// Pesan notifikasi jika ada
$notification = '';
if (isset($_SESSION['notification'])) {
  $notification = $_SESSION['notification'];
  unset($_SESSION['notification']);
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Kelola User</h2>
  <a href="tambah.php" class="btn btn-primary">
    <i class="bi bi-person-plus"></i> Tambah User Baru
  </a>
</div>

<?php if (!empty($notification)): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $notification; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Terdaftar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($user = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?php echo $user['id_user']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                  <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                    <?php echo $user['role']; ?>
                  </span>
                </td>
                <td>
                  <span class="badge <?php echo $user['active'] ? 'bg-success' : 'bg-secondary'; ?>">
                    <?php echo $user['active'] ? 'Aktif' : 'Nonaktif'; ?>
                  </span>
                </td>
                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                <td>
                  <a href="edit.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <?php if ($user['id_user'] != $_SESSION['user_id']): ?>
                    <a href="hapus.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Yakin ingin menghapus user ini?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">Tidak ada data user</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
