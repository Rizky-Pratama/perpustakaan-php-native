<?php
// Include header
require_once '../../includes/header.php';

// Mengharuskan login
requireLogin();

// Ambil ID buku jika ada dari parameter URL
$id_buku = isset($_GET['id_buku']) ? mysqli_real_escape_string($conn, $_GET['id_buku']) : '';

// Inisialisasi variabel
$buku = null;

// Jika ada ID buku, ambil data buku
if (!empty($id_buku)) {
  $query = "SELECT b.*, k.nama_kategori
            FROM buku b
            LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
            WHERE b.id_buku = '$id_buku'";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    $buku = mysqli_fetch_assoc($result);
  } else {
    $_SESSION['error'] = "Buku tidak ditemukan";
    header("Location: " . BASE_URL . "pages/buku/index.php");
    exit;
  }
}

// Proses form peminjaman
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_buku = mysqli_real_escape_string($conn, $_POST['id_buku']);
  $tanggal_pinjam = date('Y-m-d'); // Tanggal hari ini
  $lama_pinjam = (int)$_POST['lama_pinjam'];
  $tanggal_kembali = date('Y-m-d', strtotime("+$lama_pinjam days"));
  $id_user = $_SESSION['user_id'];
  $keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($conn, $_POST['keterangan']) : '';

  $errors = [];

  // Cek stok buku
  $stok = cekStokBuku($id_buku);
  if ($stok <= 0) {
    $errors[] = "Mohon maaf, buku ini sedang tidak tersedia";
  }

  // Cek apakah user sudah meminjam buku yang sama dan belum dikembalikan
  if (cekPeminjamanAktif($id_user, $id_buku)) {
    $errors[] = "Anda sudah meminjam buku ini dan belum mengembalikannya";
  }

  // Jika tidak ada error, simpan data peminjaman
  if (empty($errors)) {
    $query = "INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_kembali, status, keterangan)
              VALUES ('$id_user', '$id_buku', '$tanggal_pinjam', '$tanggal_kembali', 'menunggu', '$keterangan')";

    if (mysqli_query($conn, $query)) {
      // Status 'menunggu' - stok tidak dikurangi sampai admin menyetujui
      $_SESSION['notification'] = "Permintaan peminjaman buku berhasil diajukan dan menunggu persetujuan admin";
      header("Location: index.php");
      exit;
    } else {
      $errors[] = "Gagal mengajukan peminjaman buku: " . mysqli_error($conn);
    }
  }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Pinjam Buku</h2>
  <a href="index.php" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Kembali
  </a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row">
  <?php if ($buku): ?>
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="">
            <input type="hidden" name="id_buku" value="<?php echo $buku['id_buku']; ?>">

            <div class="row mb-3">
              <div class="col-md-4">
                <strong>Judul Buku</strong>
              </div>
              <div class="col-md-8">
                <?php echo htmlspecialchars($buku['judul']); ?>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <strong>Pengarang</strong>
              </div>
              <div class="col-md-8">
                <?php echo htmlspecialchars($buku['pengarang']); ?>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <strong>Penerbit</strong>
              </div>
              <div class="col-md-8">
                <?php echo htmlspecialchars($buku['penerbit']); ?>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <strong>Tahun Terbit</strong>
              </div>
              <div class="col-md-8">
                <?php echo $buku['tahun_terbit']; ?>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <strong>Kategori</strong>
              </div>
              <div class="col-md-8">
                <?php echo htmlspecialchars($buku['nama_kategori']); ?>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <strong>Stok</strong>
              </div>
              <div class="col-md-8">
                <?php echo $buku['stok']; ?> buku
              </div>
            </div>

            <div class="mb-3">
              <label for="lama_pinjam" class="form-label">Lama Peminjaman (hari) *</label>
              <select class="form-select" id="lama_pinjam" name="lama_pinjam" required>
                <?php for ($i = 1; $i <= 14; $i++): ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?> hari</option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="keterangan" class="form-label">Keterangan/Alasan Peminjaman</label>
              <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Jelaskan alasan peminjaman atau informasi tambahan lainnya..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" <?php echo ($buku['stok'] <= 0) ? 'disabled' : ''; ?>>
              <i class="bi bi-book"></i> Ajukan Peminjaman
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-header">
          Informasi Peminjaman
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <i class="bi bi-info-circle"></i> Maksimal peminjaman adalah 14 hari
            </li>
            <li class="list-group-item">
              <i class="bi bi-exclamation-triangle"></i> Denda keterlambatan Rp 2.000/hari
            </li>
            <li class="list-group-item">
              <i class="bi bi-book"></i> Maksimal meminjam 3 buku per orang
            </li>
          </ul>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="col-12">
      <div class="alert alert-info">
        Silahkan pilih buku yang ingin dipinjam dari daftar buku.
        <a href="<?php echo BASE_URL; ?>pages/buku/index.php" class="alert-link">Lihat daftar buku</a>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>
