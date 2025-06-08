<?php
// Include header
require_once 'includes/header.php';

// Query untuk mengambil data buku terbaru
$query = "SELECT b.*, k.nama_kategori
          FROM buku b
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
          ORDER BY b.tanggal_input DESC
          LIMIT 6";
$result = mysqli_query($conn, $query);
?>

<div class="card bg-light mb-4">
  <div class="card-body">
    <h1 class="display-4">Selamat Datang di Sistem Perpustakaan</h1>
    <p class="lead">Sistem perpustakaan untuk mengelola peminjaman dan pengembalian buku.</p>
    <hr class="my-4">
    <?php if (!isLoggedIn()): ?>
      <p>Silakan login untuk memulai peminjaman buku.</p>
      <div>
        <a class="btn btn-primary btn-lg me-2" href="auth/login.php" role="button">Login</a>
        <a class="btn btn-outline-secondary btn-lg" href="auth/register.php" role="button">Daftar</a>
      </div> <?php elseif (isAdmin()): ?>
      <p>Gunakan menu di atas untuk mengakses fitur administrasi perpustakaan.</p>
      <div>
        <a class="btn btn-primary btn-lg me-2" href="pages/dashboard.php" role="button">
          <i class="bi bi-speedometer2"></i> Dashboard Admin
        </a>
        <a class="btn btn-success btn-lg" href="pages/peminjaman/admin.php" role="button">
          <i class="bi bi-journal-text"></i> Data Peminjaman
        </a>
      </div>
    <?php else: ?>
      <p>Pilih buku yang ingin Anda pinjam atau kelola peminjaman Anda.</p>
      <div>
        <a class="btn btn-primary btn-lg me-2" href="pages/buku/" role="button">Katalog Buku</a>
        <a class="btn btn-success btn-lg" href="pages/peminjaman/index.php" role="button">Peminjaman Saya</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<h2 class="mt-5">Buku Terbaru</h2>
<div class="row">
  <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($row['judul']); ?></h5>
            <h6 class="card-subtitle mb-2 text-muted">
              <?php echo htmlspecialchars($row['pengarang']); ?> |
              <?php echo htmlspecialchars($row['tahun_terbit']); ?>
            </h6>
            <p class="card-text">
              <strong>Kategori:</strong> <?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada kategori'); ?><br>
              <strong>Penerbit:</strong> <?php echo htmlspecialchars($row['penerbit']); ?><br>
              <strong>Stok:</strong>
              <?php if ($row['stok'] > 0): ?>
                <span class="badge bg-success"><?php echo $row['stok']; ?> tersedia</span>
              <?php else: ?>
                <span class="badge bg-danger">Tidak tersedia</span>
              <?php endif; ?>
            </p>
          </div>
          <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between">
              <a href="<?php echo BASE_URL; ?>pages/buku/detail.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-info">Detail</a>
              <?php if (isLoggedIn() && !isAdmin() && $row['stok'] > 0): ?>
                <a href="<?php echo BASE_URL; ?>pages/peminjaman/pinjam.php?id_buku=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-success">
                  <i class="bi bi-book"></i> Pinjam
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="col-12">
      <div class="alert alert-info">Belum ada data buku.</div>
    </div>
  <?php endif; ?>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
