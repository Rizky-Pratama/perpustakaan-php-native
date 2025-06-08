<?php
// Include header
require_once '../includes/header.php';

// Cek apakah pengguna adalah admin
requireAdmin();

// Update status terlambat jika ada
updateStatusTerlambat();

// Query untuk statistik
$sql_user = "SELECT COUNT(*) AS total FROM users";
$sql_admin = "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'";
$sql_user_active = "SELECT COUNT(*) AS total FROM users WHERE active = 1";

$sql_buku = "SELECT COUNT(*) AS total FROM buku";
$sql_kategori = "SELECT COUNT(*) AS total FROM kategori";
$sql_stok = "SELECT SUM(stok) AS total FROM buku";

$sql_peminjaman = "SELECT COUNT(*) AS total FROM peminjaman";
$sql_peminjaman_aktif = "SELECT COUNT(*) AS total FROM peminjaman WHERE status IN ('dipinjam', 'terlambat')";
$sql_peminjaman_terlambat = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'terlambat'";
$sql_denda = "SELECT SUM(denda) AS total FROM peminjaman WHERE status = 'dikembalikan'";

// Eksekusi query
$result_user = mysqli_query($conn, $sql_user);
$result_admin = mysqli_query($conn, $sql_admin);
$result_user_active = mysqli_query($conn, $sql_user_active);

$result_buku = mysqli_query($conn, $sql_buku);
$result_kategori = mysqli_query($conn, $sql_kategori);
$result_stok = mysqli_query($conn, $sql_stok);

$result_peminjaman = mysqli_query($conn, $sql_peminjaman);
$result_peminjaman_aktif = mysqli_query($conn, $sql_peminjaman_aktif);
$result_peminjaman_terlambat = mysqli_query($conn, $sql_peminjaman_terlambat);
$result_denda = mysqli_query($conn, $sql_denda);

// Ambil data statistik
$count_user = mysqli_fetch_assoc($result_user)['total'];
$count_admin = mysqli_fetch_assoc($result_admin)['total'];
$count_user_active = mysqli_fetch_assoc($result_user_active)['total'];

$count_buku = mysqli_fetch_assoc($result_buku)['total'];
$count_kategori = mysqli_fetch_assoc($result_kategori)['total'];
$count_stok = mysqli_fetch_assoc($result_stok)['total'] ?? 0;

$count_peminjaman = mysqli_fetch_assoc($result_peminjaman)['total'];
$count_peminjaman_aktif = mysqli_fetch_assoc($result_peminjaman_aktif)['total'];
$count_peminjaman_terlambat = mysqli_fetch_assoc($result_peminjaman_terlambat)['total'];
$total_denda = mysqli_fetch_assoc($result_denda)['total'] ?? 0;

// Query untuk buku paling populer
$sql_popular_books = "SELECT b.id_buku, b.judul, COUNT(p.id_peminjaman) AS jumlah_peminjaman
                      FROM peminjaman p
                      JOIN buku b ON p.id_buku = b.id_buku
                      GROUP BY p.id_buku
                      ORDER BY jumlah_peminjaman DESC
                      LIMIT 5";
$result_popular_books = mysqli_query($conn, $sql_popular_books);

// Query untuk user paling aktif
$sql_active_users = "SELECT u.id_user, u.nama_lengkap, COUNT(p.id_peminjaman) AS jumlah_peminjaman
                     FROM peminjaman p
                     JOIN users u ON p.id_user = u.id_user
                     GROUP BY p.id_user
                     ORDER BY jumlah_peminjaman DESC
                     LIMIT 5";
$result_active_users = mysqli_query($conn, $sql_active_users);

// Query untuk peminjaman terbaru
$sql_recent_loans = "SELECT p.*, b.judul, u.nama_lengkap
                     FROM peminjaman p
                     JOIN buku b ON p.id_buku = b.id_buku
                     JOIN users u ON p.id_user = u.id_user
                     ORDER BY p.created_at DESC
                     LIMIT 10";
$result_recent_loans = mysqli_query($conn, $sql_recent_loans);
?>

<?php
// Query untuk mendapatkan jumlah permintaan yang membutuhkan persetujuan
$sql_menunggu = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'menunggu'";
$sql_proses_kembali = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'proses_kembali'";
$result_menunggu = mysqli_query($conn, $sql_menunggu);
$result_proses_kembali = mysqli_query($conn, $sql_proses_kembali);
$count_menunggu = mysqli_fetch_assoc($result_menunggu)['total'];
$count_proses_kembali = mysqli_fetch_assoc($result_proses_kembali)['total'];
$total_pending = $count_menunggu + $count_proses_kembali;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2>Dashboard Admin</h2>
  <div>
    <button class="btn btn-outline-primary" onclick="window.print()">
      <i class="bi bi-printer"></i> Print Dashboard
    </button>
  </div>
</div>

<?php if ($total_pending > 0): ?>
  <div class="alert alert-warning" role="alert">
    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Perhatian!</h4>
    <p>Terdapat <?php echo $total_pending; ?> permintaan yang membutuhkan persetujuan anda:</p>
    <hr>
    <p class="mb-0">
      <?php if ($count_menunggu > 0): ?>
        <a href="<?php echo BASE_URL; ?>pages/peminjaman/admin.php?status=menunggu" class="btn btn-sm btn-warning">
          <i class="bi bi-clock"></i> <?php echo $count_menunggu; ?> Permintaan Peminjaman
        </a>
      <?php endif; ?>

      <?php if ($count_proses_kembali > 0): ?>
        <a href="<?php echo BASE_URL; ?>pages/peminjaman/admin.php?status=proses_kembali" class="btn btn-sm btn-info">
          <i class="bi bi-arrow-return-left"></i> <?php echo $count_proses_kembali; ?> Permintaan Pengembalian
        </a>
      <?php endif; ?>
    </p>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-md-9">
    <!-- Statistik Utama -->
    <div class="row mb-4">
      <!-- Statistik User -->
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header bg-primary text-white">
            <i class="bi bi-people"></i> Statistik User
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-8">
                <h5 class="card-title">Total User</h5>
              </div>
              <div class="col-4 text-end">
                <h2><?php echo $count_user; ?></h2>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-2">
              <span>Admin</span>
              <span class="badge bg-danger"><?php echo $count_admin; ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>User Biasa</span>
              <span class="badge bg-info"><?php echo $count_user - $count_admin; ?></span>
            </div>
            <div class="d-flex justify-content-between">
              <span>User Aktif</span>
              <span class="badge bg-success"><?php echo $count_user_active; ?></span>
            </div>
          </div>
          <div class="card-footer">
            <a href="<?php echo BASE_URL; ?>pages/user/" class="btn btn-sm btn-outline-primary w-100">
              <i class="bi bi-arrow-right"></i> Kelola User
            </a>
          </div>
        </div>
      </div>

      <!-- Statistik Buku -->
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header bg-success text-white">
            <i class="bi bi-book"></i> Statistik Buku
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-8">
                <h5 class="card-title">Total Buku</h5>
              </div>
              <div class="col-4 text-end">
                <h2><?php echo $count_buku; ?></h2>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-2">
              <span>Kategori</span>
              <span class="badge bg-info"><?php echo $count_kategori; ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Total Stok</span>
              <span class="badge bg-primary"><?php echo $count_stok; ?></span>
            </div>
            <div class="d-flex justify-content-between">
              <span>Rata-rata Stok/Buku</span>
              <span class="badge bg-warning text-dark">
                <?php echo $count_buku > 0 ? round($count_stok / $count_buku, 1) : 0; ?>
              </span>
            </div>
          </div>
          <div class="card-footer">
            <a href="<?php echo BASE_URL; ?>pages/buku/" class="btn btn-sm btn-outline-success w-100">
              <i class="bi bi-arrow-right"></i> Kelola Buku
            </a>
          </div>
        </div>
      </div>

      <!-- Statistik Peminjaman -->
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header bg-info text-white">
            <i class="bi bi-journal-text"></i> Statistik Peminjaman
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-8">
                <h5 class="card-title">Total Peminjaman</h5>
              </div>
              <div class="col-4 text-end">
                <h2><?php echo $count_peminjaman; ?></h2>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-2">
              <span>Peminjaman Aktif</span>
              <span class="badge bg-primary"><?php echo $count_peminjaman_aktif; ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Terlambat</span>
              <span class="badge bg-danger"><?php echo $count_peminjaman_terlambat; ?></span>
            </div>
            <div class="d-flex justify-content-between">
              <span>Total Denda</span>
              <span class="badge bg-warning text-dark">
                Rp <?php echo number_format($total_denda, 0, ',', '.'); ?>
              </span>
            </div>
          </div>
          <div class="card-footer">
            <a href="<?php echo BASE_URL; ?>pages/peminjaman/admin.php" class="btn btn-sm btn-outline-info w-100">
              <i class="bi bi-arrow-right"></i> Lihat Peminjaman
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Peminjaman Terbaru -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Peminjaman Terbaru</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Peminjam</th>
                <th>Judul Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($result_recent_loans) > 0): ?>
                <?php while ($loan = mysqli_fetch_assoc($result_recent_loans)): ?>
                  <tr>
                    <td><?php echo $loan['id_peminjaman']; ?></td>
                    <td><?php echo htmlspecialchars($loan['nama_lengkap']); ?></td>
                    <td><?php echo htmlspecialchars($loan['judul']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($loan['tanggal_pinjam'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($loan['tanggal_kembali'])); ?></td>
                    <td>
                      <?php if ($loan['status'] === 'dipinjam'): ?>
                        <span class="badge bg-primary">Dipinjam</span>
                      <?php elseif ($loan['status'] === 'terlambat'): ?>
                        <span class="badge bg-danger">Terlambat</span>
                      <?php else: ?>
                        <span class="badge bg-success">Dikembalikan</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="<?php echo BASE_URL; ?>pages/peminjaman/detail.php?id=<?php echo $loan['id_peminjaman']; ?>"
                        class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i>
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center">Belum ada data peminjaman</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer text-end">
        <a href="<?php echo BASE_URL; ?>pages/peminjaman/admin.php" class="btn btn-sm btn-primary">
          Lihat Semua Peminjaman
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <!-- Buku Paling Populer -->
    <div class="card mb-4">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-star"></i> Buku Terpopuler</h5>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <?php if (mysqli_num_rows($result_popular_books) > 0): ?>
            <?php $rank = 1; ?>
            <?php while ($book = mysqli_fetch_assoc($result_popular_books)): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <span class="badge bg-secondary me-2"><?php echo $rank++; ?></span>
                  <?php echo htmlspecialchars($book['judul']); ?>
                </div>
                <span class="badge bg-primary rounded-pill">
                  <?php echo $book['jumlah_peminjaman']; ?>x
                </span>
              </li>
            <?php endwhile; ?>
          <?php else: ?>
            <li class="list-group-item text-center">Belum ada data peminjaman</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <!-- User Paling Aktif -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-person-check"></i> User Teraktif</h5>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <?php if (mysqli_num_rows($result_active_users) > 0): ?>
            <?php $rank = 1; ?>
            <?php while ($user = mysqli_fetch_assoc($result_active_users)): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <span class="badge bg-secondary me-2"><?php echo $rank++; ?></span>
                  <?php echo htmlspecialchars($user['nama_lengkap']); ?>
                </div>
                <span class="badge bg-primary rounded-pill">
                  <?php echo $user['jumlah_peminjaman']; ?>x
                </span>
              </li>
            <?php endwhile; ?>
          <?php else: ?>
            <li class="list-group-item text-center">Belum ada data peminjaman</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <!-- Link Cepat -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-link-45deg"></i> Link Cepat</h5>
      </div>
      <div class="card-body p-0">
        <div class="list-group list-group-flush">
          <a href="<?php echo BASE_URL; ?>pages/buku/tambah.php" class="list-group-item list-group-item-action">
            <i class="bi bi-plus-circle"></i> Tambah Buku Baru
          </a>
          <a href="<?php echo BASE_URL; ?>pages/user/tambah.php" class="list-group-item list-group-item-action">
            <i class="bi bi-person-plus"></i> Tambah User Baru
          </a>
          <a href="<?php echo BASE_URL; ?>pages/kategori/" class="list-group-item list-group-item-action">
            <i class="bi bi-tags"></i> Kelola Kategori
          </a>
          <a href="<?php echo BASE_URL; ?>pages/peminjaman/admin.php?status=terlambat" class="list-group-item list-group-item-action">
            <i class="bi bi-exclamation-circle"></i> Lihat Keterlambatan
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
