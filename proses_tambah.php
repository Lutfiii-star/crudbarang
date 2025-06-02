<?php
session_start();
include 'koneksi.php';

// Ambil dan sanitasi data input
$jenis_barang   = $_POST['jenis_barang'] ?? '';
$nama_barang    = trim($_POST['nama_barang'] ?? '');
$harga_beli_str = $_POST['harga_beli'] ?? '0';
$harga_jual_str = $_POST['harga_jual'] ?? '0';

// Hapus Rp, titik, dan koma dari harga
$harga_beli = (float) str_replace(['Rp', '.', ','], '', $harga_beli_str);
$harga_jual = (float) str_replace(['Rp', '.', ','], '', $harga_jual_str);

// Validasi input dasar
if (empty($jenis_barang) || empty($nama_barang) || $harga_beli <= 0 || $harga_jual <= 0) {
    $_SESSION['error'] = "Semua field wajib diisi dengan benar.";
    header('Location: tambah.php');
    exit;
}

// Validasi harga jual > harga beli
if ($harga_jual <= $harga_beli) {
    $_SESSION['error'] = "Harga jual harus lebih besar dari harga beli.";
    header('Location: tambah.php');
    exit;
}

// Proses upload gambar jika ada
$gambar = '';
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $fileTmp  = $_FILES['gambar']['tmp_name'];
    $fileName = time() . '_' . basename($_FILES['gambar']['name']);
    $targetDir  = 'uploads/';
    $targetFile = $targetDir . $fileName;

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExt = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedTypes)) {
        $_SESSION['error'] = "Tipe file tidak diizinkan. Hanya jpg, jpeg, png, gif.";
        header('Location: tambah.php');
        exit;
    }

    // Buat folder upload jika belum ada
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (!move_uploaded_file($fileTmp, $targetFile)) {
        $_SESSION['error'] = "Gagal mengupload gambar.";
        header('Location: tambah.php');
        exit;
    }

    $gambar = $fileName;
}

// Simpan data ke database
$stmt = $koneksi->prepare("INSERT INTO barang (jenis_barang, nama_barang, harga_beli, harga_jual, gambar) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdds", $jenis_barang, $nama_barang, $harga_beli, $harga_jual, $gambar);

if ($stmt->execute()) {
    $_SESSION['success'] = "Data barang berhasil ditambahkan.";
    header('Location: index.php');
} else {
    $_SESSION['error'] = "Gagal menambahkan data barang: " . $stmt->error;
    header('Location: tambah.php');
}

$stmt->close();
$koneksi->close();
