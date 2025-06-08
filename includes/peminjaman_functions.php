<?php

/**
 * File berisi fungsi-fungsi untuk peminjaman dan pengembalian buku
 */

/**
 * Mengecek ketersediaan stok buku
 *
 * @param int $id_buku
 * @return int Jumlah stok yang tersedia, 0 jika tidak ada
 */
function cekStokBuku($id_buku)
{
  global $conn;

  $id_buku = mysqli_real_escape_string($conn, $id_buku);
  $query = "SELECT stok FROM buku WHERE id_buku = '$id_buku'";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    return (int)$row['stok'];
  }

  return 0;
}

/**
 * Mengupdate stok buku setelah peminjaman/pengembalian
 *
 * @param int $id_buku
 * @param int $jumlah Nilai positif untuk penambahan, negatif untuk pengurangan
 * @return boolean
 */
function updateStokBuku($id_buku, $jumlah)
{
  global $conn;

  $id_buku = mysqli_real_escape_string($conn, $id_buku);
  $jumlah = (int)$jumlah;

  $query = "UPDATE buku SET stok = stok + ($jumlah) WHERE id_buku = '$id_buku'";
  return mysqli_query($conn, $query);
}

/**
 * Mendapatkan daftar peminjaman berdasarkan status atau ID user
 *
 * @param string $status Status peminjaman (opsional)
 * @param int $id_user ID user (opsional)
 * @return array|false
 */
function getDaftarPeminjaman($status = null, $id_user = null)
{
  global $conn;

  $where = [];
  $query = "SELECT p.*, b.judul, b.pengarang, u.nama_lengkap
              FROM peminjaman p
              JOIN buku b ON p.id_buku = b.id_buku
              JOIN users u ON p.id_user = u.id_user";

  if ($status) {
    $status = mysqli_real_escape_string($conn, $status);
    $where[] = "p.status = '$status'";
  }

  if ($id_user) {
    $id_user = mysqli_real_escape_string($conn, $id_user);
    $where[] = "p.id_user = '$id_user'";
  }

  if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
  }

  $query .= " ORDER BY p.tanggal_pinjam DESC";

  $result = mysqli_query($conn, $query);
  $data = [];

  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }
    return $data;
  }

  return false;
}

/**
 * Mendapatkan detail peminjaman berdasarkan ID
 *
 * @param int $id_peminjaman
 * @return array|false
 */
function getDetailPeminjaman($id_peminjaman)
{
  global $conn;

  $id_peminjaman = mysqli_real_escape_string($conn, $id_peminjaman);
  $query = "SELECT p.*, b.judul, b.pengarang, u.nama_lengkap
              FROM peminjaman p
              JOIN buku b ON p.id_buku = b.id_buku
              JOIN users u ON p.id_user = u.id_user
              WHERE p.id_peminjaman = '$id_peminjaman'";

  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    return mysqli_fetch_assoc($result);
  }

  return false;
}

/**
 * Menghitung denda keterlambatan
 *
 * @param string $tanggal_kembali Format YYYY-MM-DD
 * @param float $denda_per_hari Jumlah denda per hari keterlambatan
 * @return float
 */
function hitungDenda($tanggal_kembali, $denda_per_hari = 2000)
{
  $today = date('Y-m-d');
  $tanggal_kembali = date('Y-m-d', strtotime($tanggal_kembali));

  if ($today <= $tanggal_kembali) {
    return 0;
  }

  $selisih = strtotime($today) - strtotime($tanggal_kembali);
  $hari_terlambat = floor($selisih / (60 * 60 * 24));

  return $hari_terlambat * $denda_per_hari;
}

/**
 * Update status peminjaman menjadi "terlambat" jika lewat tanggal kembali
 *
 * @return boolean
 */
function updateStatusTerlambat()
{
  global $conn;

  $today = date('Y-m-d');
  $query = "UPDATE peminjaman
              SET status = 'terlambat'
              WHERE status = 'dipinjam' AND tanggal_kembali < '$today'";

  return mysqli_query($conn, $query);
}

/**
 * Cek apakah user sudah meminjam buku yang sama dan belum dikembalikan
 *
 * @param int $id_user
 * @param int $id_buku
 * @return boolean
 */
function cekPeminjamanAktif($id_user, $id_buku)
{
  global $conn;

  $id_user = mysqli_real_escape_string($conn, $id_user);
  $id_buku = mysqli_real_escape_string($conn, $id_buku);

  $query = "SELECT id_peminjaman
              FROM peminjaman
              WHERE id_user = '$id_user'
                AND id_buku = '$id_buku'
                AND status IN ('menunggu', 'dipinjam', 'terlambat', 'proses_kembali')";

  $result = mysqli_query($conn, $query);

  return ($result && mysqli_num_rows($result) > 0);
}

/**
 * Mendapatkan jumlah peminjaman dengan status tertentu
 *
 * @param string $status Status peminjaman
 * @return int Jumlah peminjaman
 */
function getJumlahPeminjamanByStatus($status)
{
  global $conn;

  $status = mysqli_real_escape_string($conn, $status);
  $query = "SELECT COUNT(*) AS total FROM peminjaman WHERE status = '$status'";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total'];
  }

  return 0;
}

/**
 * Memproses permintaan peminjaman (approve/reject)
 *
 * @param int $id_peminjaman ID peminjaman
 * @param string $action 'approve' atau 'reject'
 * @param string $keterangan Catatan opsional
 * @return boolean
 */
function prosesPermintaanPeminjaman($id_peminjaman, $action, $keterangan = '')
{
  global $conn;

  $id_peminjaman = mysqli_real_escape_string($conn, $id_peminjaman);
  $keterangan = mysqli_real_escape_string($conn, $keterangan);

  // Ambil data peminjaman
  $query_get = "SELECT id_buku FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'";
  $result_get = mysqli_query($conn, $query_get);

  if (!$result_get || mysqli_num_rows($result_get) === 0) {
    return false;
  }

  $peminjaman = mysqli_fetch_assoc($result_get);
  $id_buku = $peminjaman['id_buku'];

  if ($action === 'approve') {
    // Update status jadi dipinjam
    $query = "UPDATE peminjaman
              SET status = 'dipinjam',
                  keterangan = CONCAT(keterangan, ' | Disetujui: $keterangan'),
                  updated_at = NOW()
              WHERE id_peminjaman = '$id_peminjaman'";

    $result = mysqli_query($conn, $query);

    if ($result) {
      // Kurangi stok buku
      updateStokBuku($id_buku, -1);
      return true;
    }
  } elseif ($action === 'reject') {
    // Update status jadi ditolak
    $query = "UPDATE peminjaman
              SET status = 'ditolak',
                  keterangan = CONCAT(keterangan, ' | Ditolak: $keterangan'),
                  updated_at = NOW()
              WHERE id_peminjaman = '$id_peminjaman'";

    return mysqli_query($conn, $query);
  }

  return false;
}

/**
 * Memproses permintaan pengembalian
 *
 * @param int $id_peminjaman ID peminjaman
 * @param string $keterangan Catatan opsional
 * @return boolean
 */
function prosesPermintaanPengembalian($id_peminjaman, $keterangan = '')
{
  global $conn;

  $id_peminjaman = mysqli_real_escape_string($conn, $id_peminjaman);
  $keterangan = mysqli_real_escape_string($conn, $keterangan);

  // Ambil data peminjaman
  $query_get = "SELECT id_buku, status, tanggal_kembali FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'";
  $result_get = mysqli_query($conn, $query_get);

  if (!$result_get || mysqli_num_rows($result_get) === 0) {
    return false;
  }

  $peminjaman = mysqli_fetch_assoc($result_get);
  $id_buku = $peminjaman['id_buku'];
  $status_awal = $peminjaman['status'];

  // Hitung denda jika terlambat
  $denda = 0;
  if ($status_awal === 'terlambat') {
    $denda = hitungDenda($peminjaman['tanggal_kembali']);
  }

  // Update status jadi dikembalikan
  $query = "UPDATE peminjaman
            SET status = 'dikembalikan',
                denda = '$denda',
                keterangan = CONCAT(keterangan, ' | Dikembalikan: $keterangan'),
                updated_at = NOW()
            WHERE id_peminjaman = '$id_peminjaman'";

  $result = mysqli_query($conn, $query);

  if ($result) {
    // Tambah stok buku
    updateStokBuku($id_buku, 1);
    return true;
  }

  return false;
}
