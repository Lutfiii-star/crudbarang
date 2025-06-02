<?php
session_start();
include 'koneksi.php';

$id = $_POST['id'] ?? '';
$jenis_barang = $_POST['jenis_barang'] ?? '';
$nama_barang = $_POST['nama_barang'] ?? '';
$harga_beli = $_POST['harga_beli'] ?? '0';
$harga_jual = $_POST['harga_jual'] ?? '0';

// Validasi ID
if (!$id) {
    $_SESSION['error'] = "ID tidak ditemukan.";
    header('Location: index.php');
    exit;
}

// Bersihkan harga dari karakter non-numerik
$harga_beli = (int)str_replace(['Rp', '.', ' '], '', $harga_beli);
$harga_jual = (int)str_replace(['Rp', '.', ' '], '', $harga_jual);

// Ambil data lama untuk mengetahui gambar sebelumnya
$result = $koneksi->query("SELECT gambar FROM barang WHERE id='$id'");
$data_lama = $result->fetch_assoc();
$gambar = $data_lama['gambar'];

// Proses upload gambar baru jika ada
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['gambar']['tmp_name'];
    $fileName = basename($_FILES['gambar']['name']);
    $fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $fileName);
    $fileName = time() . '_' . $fileName;

    $targetDir = 'uploads/';
    $targetFile = $targetDir . $fileName;

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExt = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if (in_array($fileExt, $allowedTypes)) {
        if (move_uploaded_file($fileTmp, $targetFile)) {
            // Hapus gambar lama jika ada
            if ($gambar && file_exists($targetDir . $gambar)) {
                unlink($targetDir . $gambar);
            }
            $gambar = $fileName;
        } else {
            $_SESSION['error'] = "Gagal mengupload gambar.";
            header("Location: edit.php?id=$id");
            exit;
        }
    } else {
        $_SESSION['error'] = "Tipe file tidak diizinkan. Hanya jpg, jpeg, png, gif.";
        header("Location: edit.php?id=$id");
        exit;
    }
}

// Pastikan id berupa integer untuk bind_param
$id_int = (int)$id;

// Persiapkan dan jalankan query update
$stmt = $koneksi->prepare("UPDATE barang SET jenis_barang=?, nama_barang=?, harga_beli=?, harga_jual=?, gambar=? WHERE id=?");
$stmt->bind_param("ssiisi", $jenis_barang, $nama_barang, $harga_beli, $harga_jual, $gambar, $id_int);

if ($stmt->execute()) {
    $_SESSION['success'] = "Data berhasil diperbarui.";
    header('Location: index.php');
} else {
    $_SESSION['error'] = "Gagal mengupdate data barang.";
    header("Location: edit.php?id=$id");
}

$stmt->close();
$koneksi->close();
?>
