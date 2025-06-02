<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID tidak ditemukan.";
    header('Location: index.php');
    exit;
}

$id = $koneksi->real_escape_string($_GET['id']);

// Hapus gambar dulu
$result = $koneksi->query("SELECT gambar FROM barang WHERE id='$id'");
$data = $result->fetch_assoc();

if ($data['gambar'] && file_exists('uploads/' . $data['gambar'])) {
    unlink('uploads/' . $data['gambar']);
}

// Hapus data
if ($koneksi->query("DELETE FROM barang WHERE id='$id'")) {
    $_SESSION['success'] = "Data barang berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus data barang.";
}

header('Location: index.php');
