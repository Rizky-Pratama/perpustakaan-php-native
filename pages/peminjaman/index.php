<?php
// Include header
require_once '../../includes/header.php';

// Mengharuskan login
requireLogin();

// Update status terlambat jika ada
updateStatusTerlambat();

// Ambil daftar peminjaman user yang sedang login
$daftar_peminjaman = getDaftarPeminjaman(null, $_SESSION['user_id']);

// Notifikasi
$notification = '';
if (isset($_SESSION['notification'])) {
  $notification = $_SESSION['notification'];
  unset($_SESSION['notification']);
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Peminjaman Aktif</h2>
  <div>
    <a href="history.php" class="btn btn-outline-secondary me-2">
      <i class="bi bi-clock-history"></i> Riwayat Peminjaman
    </a>
    <a href="<?php echo BASE_URL; ?>pages/buku/index.php" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Pinjam Buku Baru
    </a>
  </div>
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
            <th>Judul Buku</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>Denda</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($daftar_peminjaman && count($daftar_peminjaman) > 0): ?>
            <?php foreach ($daftar_peminjaman as $peminjaman): ?>
              <tr>
                <td><?php echo $peminjaman['id_peminjaman']; ?></td>
                <td><?php echo htmlspecialchars($peminjaman['judul']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_pinjam'])); ?></td>
                <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_kembali'])); ?></td>
                <td>
                  <?php if ($peminjaman['status'] === 'dipinjam'): ?>
                    <span class="badge bg-primary">Dipinjam</span>
                  <?php elseif ($peminjaman['status'] === 'terlambat'): ?>
                    <span class="badge bg-danger">Terlambat</span>
                  <?php elseif ($peminjaman['status'] === 'menunggu'): ?>
                    <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                  <?php elseif ($peminjaman['status'] === 'ditolak'): ?>
                    <span class="badge bg-secondary">Ditolak</span>
                  <?php elseif ($peminjaman['status'] === 'proses_kembali'): ?>
                    <span class="badge bg-info">Proses Pengembalian</span>
                  <?php else: ?>
                    <span class="badge bg-success">Dikembalikan</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                  $denda = 0;
                  if ($peminjaman['status'] === 'terlambat') {
                    $denda = hitungDenda($peminjaman['tanggal_kembali']);
                    echo 'Rp ' . number_format($denda, 0, ',', '.');
                  } else {
                    echo '-';
                  }
                  ?>
                </td>
                <td>
                  <?php if ($peminjaman['status'] === 'dipinjam' || $peminjaman['status'] === 'terlambat'): ?>
                    <a href="kembali.php?id=<?php echo $peminjaman['id_peminjaman']; ?>"
                      class="btn btn-sm btn-success"
                      onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                      <i class="bi bi-box-arrow-in-left"></i> Ajukan Pengembalian
                    </a>
                  <?php elseif ($peminjaman['status'] === 'menunggu'): ?>
                    <span class="text-muted small">Menunggu persetujuan admin</span>
                  <?php elseif ($peminjaman['status'] === 'proses_kembali'): ?>
                    <span class="text-muted small">Menunggu konfirmasi admin</span>
                  <?php else: ?>
                    <a href="detail.php?id=<?php echo $peminjaman['id_peminjaman']; ?>" class="btn btn-sm btn-info">
                      <i class="bi bi-eye"></i> Detail
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">Anda belum pernah meminjam buku</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-header">
    <h5 class="card-title mb-0">Informasi Peminjaman</h5>
  </div>
  <div class="card-body">
    <ul>
      <li>Maksimal peminjaman adalah 14 hari</li>
      <li>Denda keterlambatan Rp 2.000/hari</li>
      <li>Silahkan mengembalikan buku secara tepat waktu</li>
    </ul>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
