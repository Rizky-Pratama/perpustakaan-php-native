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

<div class="jumbotron">
  <h1 class="display-4">Selamat Datang di Sistem Perpustakaan</h1>
  <p class="lead">Sistem sederhana untuk mengelola data buku perpustakaan.</p>
  <hr class="my-4">
  <p>Gunakan menu di atas untuk mengakses fitur pengelolaan buku dan kategori.</p>
  <a class="btn btn-primary btn-lg" href="pages/buku/" role="button">Kelola Buku</a>
</div>

<h2 class="mt-5">Buku Terbaru</h2>
<div class="row">
  <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($row['judul']); ?></h5>
            <h6 class="card-subtitle mb-2 text-muted">
              <?php echo htmlspecialchars($row['pengarang']); ?> |
              <?php echo htmlspecialchars($row['tahun_terbit']); ?>
            </h6>
            <p class="card-text">
              <strong>Kategori:</strong> <?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada kategori'); ?><br>
              <strong>Penerbit:</strong> <?php echo htmlspecialchars($row['penerbit']); ?><br>
              <strong>Stok:</strong> <?php echo htmlspecialchars($row['stok']); ?>
            </p>
            <a href="<?php echo BASE_URL; ?>pages/buku/detail.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-info">Detail</a>
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