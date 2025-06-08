<?php
// Include header
require_once '../../includes/header.php';

// Mengharuskan login
requireLogin();

// Validasi ID peminjaman
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $_SESSION['notification'] = "ID peminjaman tidak valid";
  header("Location: index.php");
  exit;
}

$id_peminjaman = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data peminjaman
$peminjaman = getDetailPeminjaman($id_peminjaman);

// Pastikan peminjaman ditemukan
if (!$peminjaman) {
  $_SESSION['notification'] = "Data peminjaman tidak ditemukan";
  header("Location: index.php");
  exit;
}

// Jika bukan admin, pastikan peminjaman milik user yang login
if (!isAdmin() && $peminjaman['id_user'] != $_SESSION['user_id']) {
  $_SESSION['notification'] = "Anda tidak memiliki akses ke data ini";
  header("Location: index.php");
  exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Detail Peminjaman</h2>
  <a href="<?php echo isAdmin() ? 'admin.php' : 'index.php'; ?>" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Kembali
  </a>
</div>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Informasi Peminjaman #<?php echo $peminjaman['id_peminjaman']; ?></h5>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <strong>Judul Buku</strong>
          </div>
          <div class="col-md-8">
            <?php echo htmlspecialchars($peminjaman['judul']); ?>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <strong>Pengarang</strong>
          </div>
          <div class="col-md-8">
            <?php echo htmlspecialchars($peminjaman['pengarang']); ?>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <strong>Peminjam</strong>
          </div>
          <div class="col-md-8">
            <?php echo htmlspecialchars($peminjaman['nama_lengkap']); ?>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <strong>Tanggal Pinjam</strong>
          </div>
          <div class="col-md-8">
            <?php echo date('d/m/Y', strtotime($peminjaman['tanggal_pinjam'])); ?>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <strong>Tanggal Kembali</strong>
          </div>
          <div class="col-md-8">
            <?php echo date('d/m/Y', strtotime($peminjaman['tanggal_kembali'])); ?>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <strong>Status</strong>
          </div>
          <div class="col-md-8">
            <?php if ($peminjaman['status'] === 'menunggu'): ?>
              <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
            <?php elseif ($peminjaman['status'] === 'ditolak'): ?>
              <span class="badge bg-secondary">Ditolak</span>
            <?php elseif ($peminjaman['status'] === 'dipinjam'): ?>
              <span class="badge bg-primary">Dipinjam</span>
            <?php elseif ($peminjaman['status'] === 'terlambat'): ?>
              <span class="badge bg-danger">Terlambat</span>
            <?php elseif ($peminjaman['status'] === 'proses_kembali'): ?>
              <span class="badge bg-info">Proses Pengembalian</span>
            <?php else: ?>
              <span class="badge bg-success">Dikembalikan</span>
              <small class="text-muted ms-2">
                pada <?php echo date('d/m/Y', strtotime($peminjaman['updated_at'])); ?>
              </small>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($peminjaman['denda'] > 0): ?>
          <div class="row mb-3">
            <div class="col-md-4">
              <strong>Denda</strong>
            </div>
            <div class="col-md-8 text-danger">
              Rp <?php echo number_format($peminjaman['denda'], 0, ',', '.'); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($peminjaman['keterangan'])): ?>
          <div class="row mb-3">
            <div class="col-md-4">
              <strong>Keterangan</strong>
            </div>
            <div class="col-md-8">
              <?php echo nl2br(htmlspecialchars($peminjaman['keterangan'])); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Status</h5>
      </div>
      <div class="card-body">
        <div class="text-center mb-3">
          <?php if ($peminjaman['status'] === 'menunggu'): ?>
            <div class="alert alert-no-autoclose alert-warning mb-3">
              <i class="bi bi-clock"></i> Menunggu persetujuan admin
            </div>
            <p>
              <strong>Tanggal pengajuan:</strong><br>
              <?php echo date('d/m/Y', strtotime($peminjaman['created_at'])); ?>
            </p>
            <small class="text-muted">Peminjaman akan diproses setelah disetujui oleh admin.</small>

          <?php elseif ($peminjaman['status'] === 'ditolak'): ?>
            <div class="alert alert-no-autoclose alert-secondary mb-3">
              <i class="bi bi-x-circle"></i> Peminjaman ditolak
            </div>
            <p>
              <strong>Alasan:</strong><br>
              <?php
              $keterangan = $peminjaman['keterangan'];
              if (strpos($keterangan, 'Ditolak:') !== false) {
                preg_match('/Ditolak: (.+)/', $keterangan, $matches);
                echo isset($matches[1]) ? $matches[1] : 'Tidak ada keterangan';
              } else {
                echo 'Tidak ada keterangan';
              }
              ?>
            </p>

          <?php elseif ($peminjaman['status'] === 'dipinjam'): ?>
            <div class="alert alert-no-autoclose alert-primary mb-3">
              <i class="bi bi-info-circle"></i> Buku masih dalam peminjaman
            </div>
            <p>
              <strong>Sisa waktu peminjaman:</strong><br>
              <?php
              $today = new DateTime();
              $due_date = new DateTime($peminjaman['tanggal_kembali']);
              $interval = $today->diff($due_date);

              if ($due_date < $today) {
                echo '<span class="text-danger">Terlambat ' . $interval->days . ' hari</span>';
              } else {
                echo '<span class="text-success">' . $interval->days . ' hari lagi</span>';
              }
              ?>
            </p>

          <?php elseif ($peminjaman['status'] === 'terlambat'): ?>
            <div class="alert alert-no-autoclose alert-danger mb-3">
              <i class="bi bi-exclamation-triangle"></i> Buku terlambat dikembalikan
            </div>
            <p>
              <strong>Keterlambatan:</strong><br>
              <?php
              $today = new DateTime();
              $due_date = new DateTime($peminjaman['tanggal_kembali']);
              $interval = $today->diff($due_date);
              echo '<span class="text-danger">' . $interval->days . ' hari</span>';
              ?>
            </p>
            <p class="text-danger">
              <strong>Estimasi denda: </strong>
              Rp <?php echo number_format($interval->days * 2000, 0, ',', '.'); ?>
            </p>

          <?php elseif ($peminjaman['status'] === 'proses_kembali'): ?>
            <div class="alert alert-no-autoclose alert-info mb-3">
              <i class="bi bi-arrow-return-left"></i> Dalam proses pengembalian
            </div>
            <p>
              <small class="text-muted">Pengembalian menunggu konfirmasi dari admin perpustakaan.</small>
            </p>

          <?php else: ?>
            <div class="alert alert-no-autoclose alert-success mb-3">
              <i class="bi bi-check-circle"></i> Buku sudah dikembalikan
            </div>
            <?php if ($peminjaman['denda'] > 0): ?>
              <p>
                <strong>Denda yang dibayarkan:</strong><br>
                <span class="text-danger">Rp <?php echo number_format($peminjaman['denda'], 0, ',', '.'); ?></span>
              </p>
            <?php endif; ?>
          <?php endif; ?>

          <?php if (($peminjaman['status'] === 'dipinjam' || $peminjaman['status'] === 'terlambat') && $peminjaman['id_user'] == $_SESSION['user_id']): ?>
            <a href="kembali.php?id=<?php echo $peminjaman['id_peminjaman']; ?>"
              class="btn btn-success"
              onclick="return confirm('Yakin ingin mengajukan pengembalian buku ini?')">
              <i class="bi bi-box-arrow-in-left"></i> Ajukan Pengembalian
            </a>
          <?php endif; ?>

          <?php if (isAdmin()): ?>
            <?php if ($peminjaman['status'] === 'menunggu'): ?>
              <div class="mt-4">
                <h6 class="mb-3">Tindakan Admin:</h6>
                <div class="d-grid gap-2">
                  <a href="proses_admin.php?action=approve&id=<?php echo $peminjaman['id_peminjaman']; ?>"
                    class="btn btn-success"
                    onclick="return confirm('Setujui permintaan peminjaman ini?')">
                    <i class="bi bi-check-circle"></i> Setujui Peminjaman
                  </a>

                  <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">
                    <i class="bi bi-x-circle"></i> Tolak Peminjaman
                  </button>
                </div>
              </div>
            <?php elseif ($peminjaman['status'] === 'proses_kembali'): ?>
              <div class="mt-4">
                <h6 class="mb-3">Tindakan Admin:</h6>
                <div class="d-grid gap-2">
                  <a href="proses_admin.php?action=confirm_return&id=<?php echo $peminjaman['id_peminjaman']; ?>"
                    class="btn btn-success"
                    onclick="return confirm('Konfirmasi pengembalian buku ini?')">
                    <i class="bi bi-check-circle"></i> Konfirmasi Pengembalian
                  </a>
                </div>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (isAdmin() && $peminjaman['status'] === 'menunggu'): ?>
  <!-- Modal untuk alasan penolakan -->
  <div class="modal fade" id="tolakModal" tabindex="-1" aria-labelledby="tolakModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="proses_admin.php" method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="tolakModalLabel">Tolak Peminjaman</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="id" value="<?php echo $peminjaman['id_peminjaman']; ?>">

            <div class="mb-3">
              <label for="keterangan" class="form-label">Alasan Penolakan</label>
              <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
              <div class="form-text">Berikan alasan mengapa permintaan peminjaman ini ditolak.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Tolak Peminjaman</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
