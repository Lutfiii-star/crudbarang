<?php
session_start();
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tambah Barang | PT TIMBANG MOMOT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #007bff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img.logo-circle {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 2px solid white;
            margin-right: 10px;
        }

        .navbar-brand span {
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
        }

        h2 {
            text-align: center;
            color: #007bff;
            font-weight: bold;
            margin-top: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            max-width: 620px;
            margin: 30px auto;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        #drop-area {
            cursor: pointer;
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        #drop-area.dragover {
            background-color: #e0f0ff;
        }

        #preview-image {
            max-height: 200px;
            margin-top: 15px;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="logo.jpg" alt="Logo" class="logo-circle me-2" />
                <span>PT TIMBANG MOMOT</span>
            </a>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-4">
        <h2 class="mb-4">Tambah Barang</h2>
        <div class="form-container">
            <form action="proses_tambah.php" method="POST" enctype="multipart/form-data" onsubmit="return validasiHarga()">
                <div class="mb-3">
                    <label class="form-label">Jenis Barang</label><br />
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_barang" id="elektronik" value="Elektronik" required />
                        <label class="form-check-label" for="elektronik">Elektronik</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_barang" id="perabot" value="Perabot" />
                        <label class="form-check-label" for="perabot">Perabot</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_barang" id="pakaian" value="Pakaian" />
                        <label class="form-check-label" for="pakaian">Pakaian</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_barang" id="lainnya" value="Lainnya" />
                        <label class="form-check-label" for="lainnya">Lainnya</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" class="form-control" placeholder="Masukkan nama barang" required />
                </div>

                <div class="mb-3">
                    <label for="harga_beli" class="form-label">Harga Beli</label>
                    <input type="text" name="harga_beli" id="harga_beli" class="form-control" placeholder="Masukkan harga beli" required />
                </div>

                <div class="mb-3">
                    <label for="harga_jual" class="form-label">Harga Jual</label>
                    <input type="text" name="harga_jual" id="harga_jual" class="form-control" placeholder="Masukkan harga jual" required />
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Gambar (jpg, png, gif)</label>
                    <div id="drop-area">
                        <p>Seret dan lepaskan gambar di sini atau klik untuk memilih</p>
                        <input type="file" name="gambar" id="gambar" class="form-control d-none" accept=".jpg,.jpeg,.png,.gif" />
                        <img id="preview-image" src="#" alt="Preview" class="img-fluid mt-3 d-none" />
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    <a href="index.php" class="btn btn-secondary ms-2 px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function formatRupiah(angka, prefix = 'Rp ') {
            let number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                let separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] !== undefined ? rupiah + "," + split[1] : rupiah;
            return prefix + rupiah;
        }

        function setFormattedValue(id) {
            const input = document.getElementById(id);
            let numericValue = input.value.replace(/[^0-9]/g, "");
            if (numericValue === "") {
                input.value = "";
                return;
            }
            input.value = formatRupiah(numericValue);
            input.setSelectionRange(input.value.length, input.value.length);
        }

        document.getElementById("harga_beli").addEventListener("input", function() {
            setFormattedValue("harga_beli");
        });

        document.getElementById("harga_jual").addEventListener("input", function() {
            setFormattedValue("harga_jual");
        });

        function validasiHarga() {
            const beli = parseInt(document.getElementById('harga_beli').value.replace(/[^0-9]/g, ''));
            const jual = parseInt(document.getElementById('harga_jual').value.replace(/[^0-9]/g, ''));

            if (isNaN(beli) || isNaN(jual)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid',
                    text: 'Harga beli dan harga jual harus berupa angka.',
                    showConfirmButton: true
                });
                return false;
            }

            if (jual <= beli) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Harga Tidak Valid',
                    text: 'Harga jual harus lebih besar dari harga beli.',
                    showConfirmButton: true
                });
                return false;
            }
            return true;
        }
        const dropArea = document.getElementById("drop-area");
        const fileInput = document.getElementById("gambar");
        const previewImage = document.getElementById("preview-image");

        dropArea.addEventListener("click", () => fileInput.click());

        ["dragenter", "dragover"].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.add("dragover");
            });
        });

        ["dragleave", "drop"].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.remove("dragover");
            });
        });

        dropArea.addEventListener("drop", (e) => {
            const file = e.dataTransfer.files[0];
            if (file && file.type.match("image.*")) {
                fileInput.files = e.dataTransfer.files;
                previewImage.src = URL.createObjectURL(file);
                previewImage.classList.remove("d-none");
            }
        });

        fileInput.addEventListener("change", function() {
            const file = this.files[0];
            if (file && file.type.match("image.*")) {
                previewImage.src = URL.createObjectURL(file);
                previewImage.classList.remove("d-none");
            }
        });

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= addslashes($_SESSION['error']) ?>',
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: true,
            });
        <?php unset($_SESSION['error']);
        endif; ?>
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>