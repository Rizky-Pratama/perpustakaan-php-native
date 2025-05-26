<?php
// Include header
require_once '../../includes/header.php';

// Query untuk mengambil semua buku dengan nama kategori
$query = "SELECT b.*, k.nama_kategori 
          FROM buku b
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
          ORDER BY b.id_buku DESC";
$result = mysqli_query($conn, $query);

// Pesan notifikasi jika ada
$notification = '';
if (isset($_SESSION['notification'])) {
  $notification = $_SESSION['notification'];
  unset($_SESSION['notification']);
}
?>

<h2>Kelola Data Buku</h2>

<?php if (!empty($notification)): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $notification; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="mb-3">
  <a href="tambah.php" class="btn btn-primary">
    <i class="bi bi-plus-lg"></i> Tambah Buku
  </a>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Penerbit</th>
            <th>Tahun</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php $no = 1; ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                <td><?php echo htmlspecialchars($row['pengarang']); ?></td>
                <td><?php echo htmlspecialchars($row['penerbit']); ?></td>
                <td><?php echo htmlspecialchars($row['tahun_terbit']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada kategori'); ?></td>
                <td><?php echo htmlspecialchars($row['stok']); ?></td>
                <td>
                  <a href="detail.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-info">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="edit.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="hapus.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus buku ini?')">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">Tidak ada data buku</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php
// Include footer
include INCLUDES_PATH . 'footer.php';
?>