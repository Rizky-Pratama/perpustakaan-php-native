<?php
// Include header
require_once '../../includes/header.php';

// Cek apakah pengguna adalah admin
requireAdmin();

// Update status terlambat jika ada
updateStatusTerlambat();

// Filter status
$filter = isset($_GET['status']) ? $_GET['status'] : '';
$status_options = ['', 'dipinjam', 'terlambat', 'dikembalikan'];

// Ambil daftar semua peminjaman dengan filter jika ada
$daftar_peminjaman = getDaftarPeminjaman($filter);

// Notifikasi
$notification = '';
if (isset($_SESSION['notification'])) {
  $notification = $_SESSION['notification'];
  unset($_SESSION['notification']);
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Kelola Peminjaman</h2>
</div>

<?php if (!empty($notification)): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $notification; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="card mb-4">
  <div class="card-body">
    <div class="d-flex align-items-center">
      <span class="me-3">Filter Status:</span>
      <div class="btn-group">
        <a href="admin.php" class="btn btn-outline-primary <?php echo $filter === '' ? 'active' : ''; ?>">Semua</a>
        <a href="admin.php?status=menunggu" class="btn btn-outline-primary <?php echo $filter === 'menunggu' ? 'active' : ''; ?>">Menunggu Persetujuan</a>
        <a href="admin.php?status=proses_kembali" class="btn btn-outline-primary <?php echo $filter === 'proses_kembali' ? 'active' : ''; ?>">Permintaan Kembali</a>
        <a href="admin.php?status=dipinjam" class="btn btn-outline-primary <?php echo $filter === 'dipinjam' ? 'active' : ''; ?>">Dipinjam</a>
        <a href="admin.php?status=terlambat" class="btn btn-outline-primary <?php echo $filter === 'terlambat' ? 'active' : ''; ?>">Terlambat</a>
        <a href="admin.php?status=dikembalikan" class="btn btn-outline-primary <?php echo $filter === 'dikembalikan' ? 'active' : ''; ?>">Dikembalikan</a>
        <a href="admin.php?status=ditolak" class="btn btn-outline-primary <?php echo $filter === 'ditolak' ? 'active' : ''; ?>">Ditolak</a>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Peminjam</th>
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
                <td><?php echo htmlspecialchars($peminjaman['nama_lengkap']); ?></td>
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
                  if ($peminjaman['status'] === 'terlambat') {
                    $denda = hitungDenda($peminjaman['tanggal_kembali']);
                    echo 'Rp ' . number_format($denda, 0, ',', '.');
                  } elseif ($peminjaman['status'] === 'dikembalikan' && $peminjaman['denda'] > 0) {
                    echo 'Rp ' . number_format($peminjaman['denda'], 0, ',', '.');
                  } else {
                    echo '-';
                  }
                  ?>
                </td>
                <td>
                  <a href="detail.php?id=<?php echo $peminjaman['id_peminjaman']; ?>" class="btn btn-sm btn-info">
                    <i class="bi bi-eye"></i> Detail
                  </a>

                  <?php if ($peminjaman['status'] === 'menunggu'): ?>
                    <a href="proses_admin.php?action=approve&id=<?php echo $peminjaman['id_peminjaman']; ?>"
                      class="btn btn-sm btn-success"
                      onclick="return confirm('Setujui permintaan peminjaman ini?')">
                      <i class="bi bi-check-circle"></i> Setujui
                    </a>
                    <a href="proses_admin.php?action=reject&id=<?php echo $peminjaman['id_peminjaman']; ?>"
                      class="btn btn-sm btn-danger"
                      onclick="return confirm('Tolak permintaan peminjaman ini?')">
                      <i class="bi bi-x-circle"></i> Tolak
                    </a>

                  <?php elseif ($peminjaman['status'] === 'proses_kembali'): ?>
                    <a href="proses_admin.php?action=confirm_return&id=<?php echo $peminjaman['id_peminjaman']; ?>"
                      class="btn btn-sm btn-success"
                      onclick="return confirm('Konfirmasi pengembalian buku ini?')">
                      <i class="bi bi-check-circle"></i> Konfirmasi Kembali
                    </a>

                  <?php elseif ($peminjaman['status'] === 'dipinjam' || $peminjaman['status'] === 'terlambat'): ?>
                    <a href="proses_admin.php?action=return&id=<?php echo $peminjaman['id_peminjaman']; ?>"
                      class="btn btn-sm btn-success"
                      onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                      <i class="bi bi-box-arrow-in-left"></i> Kembalikan
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">Tidak ada data peminjaman</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Statistik Peminjaman</h5>
      </div>
      <div class="card-body">
        <?php
        // Menghitung statistik peminjaman
        $sql_menunggu = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'menunggu'";
        $sql_proses_kembali = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'proses_kembali'";
        $sql_dipinjam = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'dipinjam'";
        $sql_terlambat = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'terlambat'";
        $sql_dikembalikan = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'dikembalikan'";
        $sql_all = "SELECT COUNT(*) AS total FROM peminjaman";

        $result_menunggu = mysqli_query($conn, $sql_menunggu);
        $result_proses_kembali = mysqli_query($conn, $sql_proses_kembali);
        $result_dipinjam = mysqli_query($conn, $sql_dipinjam);
        $result_terlambat = mysqli_query($conn, $sql_terlambat);
        $result_dikembalikan = mysqli_query($conn, $sql_dikembalikan);
        $result_all = mysqli_query($conn, $sql_all);

        $count_menunggu = mysqli_fetch_assoc($result_menunggu)['total'];
        $count_proses_kembali = mysqli_fetch_assoc($result_proses_kembali)['total'];
        $count_dipinjam = mysqli_fetch_assoc($result_dipinjam)['total'];
        $count_terlambat = mysqli_fetch_assoc($result_terlambat)['total'];
        $count_dikembalikan = mysqli_fetch_assoc($result_dikembalikan)['total'];
        $count_all = mysqli_fetch_assoc($result_all)['total'];
        ?>

        <div class="row">
          <div class="col-6 mb-3">
            <div class="p-3 bg-primary bg-opacity-10 rounded">
              <h6>Total Peminjaman</h6>
              <h3><?php echo $count_all; ?></h3>
            </div>
          </div>
          <div class="col-6 mb-3">
            <div class="p-3 bg-warning bg-opacity-10 rounded">
              <h6>Menunggu Persetujuan</h6>
              <h3><?php echo $count_menunggu; ?></h3>
            </div>
          </div>
          <div class="col-6 mb-3">
            <div class="p-3 bg-info bg-opacity-10 rounded">
              <h6>Proses Pengembalian</h6>
              <h3><?php echo $count_proses_kembali; ?></h3>
            </div>
          </div>
          <div class="col-6 mb-3">
            <div class="p-3 bg-primary bg-opacity-10 rounded">
              <h6>Buku Dipinjam</h6>
              <h3><?php echo $count_dipinjam; ?></h3>
            </div>
          </div>
          <div class="col-6 mb-3">
            <div class="p-3 bg-danger bg-opacity-10 rounded">
              <h6>Buku Terlambat</h6>
              <h3><?php echo $count_terlambat; ?></h3>
            </div>
          </div>
          <div class="col-6 mb-3">
            <div class="p-3 bg-success bg-opacity-10 rounded">
              <h6>Buku Dikembalikan</h6>
              <h3><?php echo $count_dikembalikan; ?></h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Buku Dengan Peminjaman Terbanyak</h5>
      </div>
      <div class="card-body">
        <?php
        $sql_top_books = "SELECT b.judul, COUNT(p.id_peminjaman) as jumlah_peminjaman
                          FROM peminjaman p
                          JOIN buku b ON p.id_buku = b.id_buku
                          GROUP BY p.id_buku
                          ORDER BY jumlah_peminjaman DESC
                          LIMIT 5";
        $result_top_books = mysqli_query($conn, $sql_top_books);
        ?>

        <?php if (mysqli_num_rows($result_top_books) > 0): ?>
          <ol class="list-group list-group-numbered">
            <?php while ($book = mysqli_fetch_assoc($result_top_books)): ?>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                  <?php echo htmlspecialchars($book['judul']); ?>
                </div>
                <span class="badge bg-primary rounded-pill"><?php echo $book['jumlah_peminjaman']; ?> peminjaman</span>
              </li>
            <?php endwhile; ?>
          </ol>
        <?php else: ?>
          <p class="text-center">Belum ada data peminjaman</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
