<?php
// Include header
require_once '../../includes/header.php';

// Mengharuskan login
requireLogin();

// Update status terlambat jika ada
updateStatusTerlambat();

// Ambil riwayat peminjaman user yang sedang login (status dikembalikan)
$daftar_peminjaman = getDaftarPeminjaman('dikembalikan', $_SESSION['user_id']);

// Notifikasi
$notification = '';
if (isset($_SESSION['notification'])) {
  $notification = $_SESSION['notification'];
  unset($_SESSION['notification']);
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Riwayat Peminjaman</h2>
  <a href="index.php" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Peminjaman Aktif
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
            <th>No</th>
            <th>Judul Buku</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>Denda</th>
            <th>Dikembalikan Pada</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($daftar_peminjaman && count($daftar_peminjaman) > 0): ?>
            <?php $no = 1; ?>
            <?php foreach ($daftar_peminjaman as $peminjaman): ?>
              <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($peminjaman['judul']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_pinjam'])); ?></td>
                <td><?php echo date('d/m/Y', strtotime($peminjaman['tanggal_kembali'])); ?></td>
                <td>
                  <span class="badge bg-success">Dikembalikan</span>
                </td>
                <td>
                  <?php
                  if ($peminjaman['denda'] > 0) {
                    echo 'Rp ' . number_format($peminjaman['denda'], 0, ',', '.');
                  } else {
                    echo '-';
                  }
                  ?>
                </td>
                <td><?php echo date('d/m/Y', strtotime($peminjaman['updated_at'])); ?></td>
                <td>
                  <a href="detail.php?id=<?php echo $peminjaman['id_peminjaman']; ?>" class="btn btn-sm btn-info">
                    <i class="bi bi-eye"></i> Detail
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">Belum ada riwayat peminjaman</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-header">
    <h5 class="card-title mb-0">Informasi</h5>
  </div>
  <div class="card-body">
    <p class="mb-0">
      <i class="bi bi-info-circle"></i> Riwayat peminjaman menampilkan semua buku yang pernah Anda pinjam dan sudah dikembalikan.
      Untuk melihat peminjaman yang masih aktif, silakan kunjungi halaman <a href="index.php">Peminjaman Aktif</a>.
    </p>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
