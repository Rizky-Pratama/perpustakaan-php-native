<?php
// Include header
require_once '../../includes/header.php';

// Query untuk mengambil data kategori
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

// Proses form tambah buku
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $judul = mysqli_real_escape_string($conn, $_POST['judul']);
  $pengarang = mysqli_real_escape_string($conn, $_POST['pengarang']);
  $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
  $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
  $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
  $jumlah_halaman = mysqli_real_escape_string($conn, $_POST['jumlah_halaman']);
  $stok = mysqli_real_escape_string($conn, $_POST['stok']);
  $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);

  // Insert data ke database
  $query = "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, isbn, jumlah_halaman, stok, id_kategori) 
              VALUES ('$judul', '$pengarang', '$penerbit', '$tahun_terbit', '$isbn', '$jumlah_halaman', '$stok', '$id_kategori')";

  if (mysqli_query($conn, $query)) {
    // Redirect dengan pesan sukses
    $_SESSION['notification'] = "Buku berhasil ditambahkan!";
    header("Location: index.php");
    exit;
  } else {
    $error = "Error: " . mysqli_error($conn);
  }
}
?>

<h2>Tambah Buku Baru</h2>

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
        <input type="text" class="form-control" id="judul" name="judul" required>
      </div>
      <div class="mb-3">
        <label for="pengarang" class="form-label">Pengarang</label>
        <input type="text" class="form-control" id="pengarang" name="pengarang" required>
      </div>
      <div class="mb-3">
        <label for="penerbit" class="form-label">Penerbit</label>
        <input type="text" class="form-control" id="penerbit" name="penerbit" required>
      </div>
      <div class="mb-3">
        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
        <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" min="1900" max="<?php echo date('Y'); ?>" required>
      </div>
      <div class="mb-3">
        <label for="isbn" class="form-label">ISBN</label>
        <input type="text" class="form-control" id="isbn" name="isbn">
      </div>
      <div class="mb-3">
        <label for="jumlah_halaman" class="form-label">Jumlah Halaman</label>
        <input type="number" class="form-control" id="jumlah_halaman" name="jumlah_halaman" min="1">
      </div>
      <div class="mb-3">
        <label for="stok" class="form-label">Stok</label>
        <input type="number" class="form-control" id="stok" name="stok" min="0" required>
      </div>
      <div class="mb-3">
        <label for="id_kategori" class="form-label">Kategori</label>
        <select class="form-select" id="id_kategori" name="id_kategori">
          <option value="">Pilih Kategori</option>
          <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
            <option value="<?php echo $kategori['id_kategori']; ?>">
              <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>

<?php
// Include footer
require_once INCLUDES_PATH . 'footer.php';
?>