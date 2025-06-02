<?php
$koneksi = new mysqli("localhost", "root", "", "pt_timbang_momot");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
