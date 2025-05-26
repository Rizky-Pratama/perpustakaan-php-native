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
$query_buku = "SELECT * FROM buku WHERE id_buku = '$id_buku'";
$result_buku = mysqli_query($conn, $query_buku);

// Periksa apakah buku ditemukan
if (mysqli_num_rows($result_buku) == 0) {
  header("Location: index.php");
  exit;
}

$buku = mysqli_fetch_assoc($result_buku);

// Query untuk mengambil data kategori
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

// Proses form edit buku
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $judul = mysqli_real_escape_string($conn, $_POST['judul']);
  $pengarang = mysqli_real_escape_string($conn, $_POST['pengarang']);
  $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
  $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
  $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
  $jumlah_halaman = mysqli_real_escape_string($conn, $_POST['jumlah_halaman']);
  $stok = mysqli_real_escape_string($conn, $_POST['stok']);
  $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);

  // Update data di database
  $query = "UPDATE buku SET 
                judul = '$judul',
                pengarang = '$pengarang',
                penerbit = '$penerbit',
                tahun_terbit = '$tahun_terbit',
                isbn = '$isbn',
                jumlah_halaman = '$jumlah_halaman',
                stok = '$stok',
                id_kategori = " . ($id_kategori ? "'$id_kategori'" : "NULL") . "
              WHERE id_buku = '$id_buku'";

  if (mysqli_query($conn, $query)) {
    // Redirect dengan pesan sukses
    $_SESSION['notification'] = "Buku berhasil diperbarui!";
    header("Location: index.php");
    exit;
  } else {
    $error = "Error: " . mysqli_error($conn);
  }
}
?>

<h2>Edit Buku</h2>

<?php if (isset($error)): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form action="" method="POST" class="form-wrapper">
      <div class="mb-3">
        <label for="judul" class="form-label">Judul</label>
        <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($buku['judul']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="pengarang" class="form-label">Pengarang</label>
        <input type="text" class="form-control" id="pengarang" name="pengarang" value="<?php echo htmlspecialchars($buku['pengarang']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="penerbit" class="form-label">Penerbit</label>
        <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
        <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($buku['tahun_terbit']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="isbn" class="form-label">ISBN</label>
        <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($buku['isbn']); ?>">
      </div>
      <div class="mb-3">
        <label for="jumlah_halaman" class="form-label">Jumlah Halaman</label>
        <input type="number" class="form-control" id="jumlah_halaman" name="jumlah_halaman" min="1" value="<?php echo htmlspecialchars($buku['jumlah_halaman']); ?>">
      </div>
      <div class="mb-3">
        <label for="stok" class="form-label">Stok</label>
        <input type="number" class="form-control" id="stok" name="stok" min="0" value="<?php echo htmlspecialchars($buku['stok']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="id_kategori" class="form-label">Kategori</label>
        <select class="form-select" id="id_kategori" name="id_kategori">
          <option value="">Pilih Kategori</option>
          <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
            <option value="<?php echo $kategori['id_kategori']; ?>" <?php echo ($buku['id_kategori'] == $kategori['id_kategori']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>