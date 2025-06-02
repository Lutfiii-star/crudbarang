<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$result = $koneksi->query("SELECT * FROM barang WHERE id=$id");
if ($result->num_rows === 0) {
    $_SESSION['error'] = "Data tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Barang | PT TIMBANG MOMOT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f4f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #007bff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1rem;
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

        .form-container {
            background-color: #fff;
            max-width: 600px;
            margin: 0 auto 50px;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgb(0 123 255 / 0.15);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 10px 30px;
            font-weight: 700;
            border-radius: 8px;
        }

        .btn-secondary {
            padding: 10px 30px;
            font-weight: 600;
            border-radius: 8px;
        }

        .img-preview {
            max-width: 140px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .drop-area {
            border: 2px dashed #007bff;
            padding: 20px;
            background-color: #f8f9fa;
            color: #6c757d;
            transition: background-color 0.3s, color 0.3s;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
        }

        .drop-area.dragover {
            background-color: #007bff;
            color: #fff;
            border-color: #0056b3;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="logo.jpg" alt="Logo" class="logo-circle me-2" />
                <span>PT TIMBANG MOMOT</span>
            </a>
        </div>
    </nav>
    <div class="container">
        <h2 class="text-center text-primary fw-bold mt-4 mb-4">Edit Barang</h2>
        <div class="form-container">
            <form action="proses_edit.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>" />

                <div class="mb-3">
                    <label class="form-label">Jenis Barang</label><br />
                    <?php
                    $jenis_list = ['Elektronik', 'Perabot', 'Pakaian', 'Lainnya'];
                    foreach ($jenis_list as $jenis) {
                        $checked = $data['jenis_barang'] == $jenis ? 'checked' : '';
                        echo <<<HTML
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_barang" id="$jenis" value="$jenis" $checked required />
                                <label class="form-check-label" for="$jenis">$jenis</label>
                            </div>
                        HTML;
                    }
                    ?>
                </div>
                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" class="form-control"
                        value="<?= htmlspecialchars($data['nama_barang']) ?>" required />
                </div>

                <!-- Harga Beli -->
                <div class="mb-3">
                    <label for="harga_beli" class="form-label">Harga Beli</label>
                    <input type="text" name="harga_beli" id="harga_beli" class="form-control"
                        value="Rp <?= number_format($data['harga_beli'], 0, ',', '.') ?>" required />
                </div>

                <!-- Harga Jual -->
                <div class="mb-3">
                    <label for="harga_jual" class="form-label">Harga Jual</label>
                    <input type="text" name="harga_jual" id="harga_jual" class="form-control"
                        value="Rp <?= number_format($data['harga_jual'], 0, ',', '.') ?>" required />
                </div>

                <!-- Gambar -->
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar (jpg, png, gif)</label><br />
                    <?php if ($data['gambar'] && file_exists('uploads/' . $data['gambar'])): ?>
                        <img id="img-preview" src="uploads/<?= htmlspecialchars($data['gambar']) ?>" alt="Gambar Lama" class="img-preview d-block mb-2" />
                    <?php else: ?>
                        <img id="img-preview" src="#" alt="Preview Gambar" class="img-preview d-none mb-2" />
                        <p><em>Tidak ada gambar.</em></p>
                    <?php endif; ?>

                    <div id="drop-area" class="drop-area">
                        <p class="mb-2">Seret dan lepas gambar di sini atau klik untuk memilih.</p>
                        <input type="file" name="gambar" id="gambar" class="form-control d-none" accept=".jpg,.jpeg,.png,.gif" />
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('gambar').click()">Pilih Gambar</button>
                    </div>
                    <small class="text-muted d-block mt-1">Abaikan jika tidak ingin mengganti gambar.</small>
                </div>

                <!-- Tombol -->
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function formatRupiah(angka, prefix = 'Rp ') {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + rupiah;
        }

        document.querySelectorAll('#harga_beli, #harga_jual').forEach(function(input) {
            input.addEventListener('keyup', function(e) {
                input.value = formatRupiah(this.value);
            });

            input.value = formatRupiah(input.value);
        });

        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('gambar');
        const imgPreview = document.getElementById('img-preview');

        dropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });

        dropArea.addEventListener('dragleave', function() {
            dropArea.classList.remove('dragover');
        });

        dropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            dropArea.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFile();
            }
        });

        fileInput.addEventListener('change', handleFile);

        function handleFile() {
            const file = fileInput.files[0];
            if (file && file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        }
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= addslashes($_SESSION['error']) ?>',
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: true,
            });
        <?php unset($_SESSION['error']); endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= addslashes($_SESSION['success']) ?>',
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        <?php unset($_SESSION['success']); endif; ?>
    </script>
</body>

</html>
