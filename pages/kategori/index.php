<?php
// Include header
require_once '../../includes/header.php';

// Query untuk mengambil semua kategori
$query = "SELECT k.*, COUNT(b.id_buku) as jumlah_buku
          FROM kategori k
          LEFT JOIN buku b ON k.id_kategori = b.id_kategori
          GROUP BY k.id_kategori
          ORDER BY k.nama_kategori ASC";
$result = mysqli_query($conn, $query);

// Pesan notifikasi jika ada
$notification = '';
if (isset($_SESSION['notification'])) {
  $notification = $_SESSION['notification'];
  unset($_SESSION['notification']);
}
?>

<h2>Kelola Kategori Buku</h2>

<?php if (!empty($notification)): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $notification; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5>Tambah Kategori Baru</h5>
      </div>
      <div class="card-body">
        <form action="proses_kategori.php" method="POST">
          <div class="mb-3">
            <label for="nama_kategori" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
          </div>
          <div class="mb-3">
            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5>Daftar Kategori</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Kategori</th>
                <th>Jumlah Buku</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($result) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                    <td><?php echo htmlspecialchars($row['jumlah_buku']); ?></td>
                    <td>
                      <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id_kategori']; ?>">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <a href="proses_kategori.php?hapus=<?php echo $row['id_kategori']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                        <i class="bi bi-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center">Tidak ada data kategori</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal container - ditempatkan di luar tabel -->
<?php if (mysqli_num_rows($result) > 0): ?>
  <?php
  // Reset pointer ke awal hasil query
  mysqli_data_seek($result, 0);
  while ($row = mysqli_fetch_assoc($result)): ?>
    <!-- Modal Edit -->
    <div class="modal fade" id="editModal<?php echo $row['id_kategori']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['id_kategori']; ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel<?php echo $row['id_kategori']; ?>">Edit Kategori</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="proses_kategori.php" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id_kategori" value="<?php echo $row['id_kategori']; ?>">
              <div class="mb-3">
                <label for="edit_nama_kategori<?php echo $row['id_kategori']; ?>" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="edit_nama_kategori<?php echo $row['id_kategori']; ?>" name="nama_kategori" value="<?php echo htmlspecialchars($row['nama_kategori']); ?>" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
