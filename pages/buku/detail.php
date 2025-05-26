<?php
// Include header
require_once '../../includes/header.php';

// Periksa apakah ID buku diberikan
if (!isset($_GET['id']) || empty($_GET['id'])) {
  header("Location: index.php");
  exit;
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);

// Query untuk mengambil data buku berdasarkan ID
$query = "SELECT b.*, k.nama_kategori 
          FROM buku b
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
          WHERE b.id_buku = '$id_buku'";
$result = mysqli_query($conn, $query);

// Periksa apakah buku ditemukan
if (mysqli_num_rows($result) == 0) {
  header("Location: index.php");
  exit;
}

$buku = mysqli_fetch_assoc($result);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Detail Buku</h2>
  <a href="index.php" class="btn btn-secondary">Kembali</a>
</div>

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <h4 class="card-title"><?php echo htmlspecialchars($buku['judul']); ?></h4>
        <h6 class="card-subtitle mb-3 text-muted">
          oleh <?php echo htmlspecialchars($buku['pengarang']); ?>
        </h6>

        <div class="table-responsive">
          <table class="table table-bordered">
            <tr>
              <th width="200">ISBN</th>
              <td><?php echo htmlspecialchars($buku['isbn'] ?? '-'); ?></td>
            </tr>
            <tr>
              <th>Kategori</th>
              <td><?php echo htmlspecialchars($buku['nama_kategori'] ?? 'Tidak ada kategori'); ?></td>
            </tr>
            <tr>
              <th>Penerbit</th>
              <td><?php echo htmlspecialchars($buku['penerbit']); ?></td>
            </tr>
            <tr>
              <th>Tahun Terbit</th>
              <td><?php echo htmlspecialchars($buku['tahun_terbit']); ?></td>
            </tr>
            <tr>
              <th>Jumlah Halaman</th>
              <td><?php echo htmlspecialchars($buku['jumlah_halaman'] ?? '-'); ?></td>
            </tr>
            <tr>
              <th>Stok</th>
              <td><?php echo htmlspecialchars($buku['stok']); ?></td>
            </tr>
            <tr>
              <th>Tanggal Input</th>
              <td><?php echo date('d-m-Y H:i', strtotime($buku['tanggal_input'])); ?></td>
            </tr>
          </table>
        </div>

        <div class="mt-3">
          <a href="edit.php?id=<?php echo $buku['id_buku']; ?>" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
          </a>
          <a href="hapus.php?id=<?php echo $buku['id_buku']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus buku ini?')">
            <i class="bi bi-trash"></i> Hapus
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>