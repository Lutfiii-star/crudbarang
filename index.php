<?php
session_start();
include 'koneksi.php';

$search = $_GET['search'] ?? '';
$jenis_filter = $_GET['jenis'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 8;
$offset = ($page - 1) * $limit;

$where = [];
if ($search) {
    $searchEsc = $koneksi->real_escape_string($search);
    $where[] = "(nama_barang LIKE '%$searchEsc%' OR jenis_barang LIKE '%$searchEsc%')";
}
if ($jenis_filter) {
    $jenisEsc = $koneksi->real_escape_string($jenis_filter);
    $where[] = "jenis_barang = '$jenisEsc'";
}
$whereSql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$totalRes = $koneksi->query("SELECT COUNT(*) AS total FROM barang $whereSql");
$totalRow = $totalRes->fetch_assoc()['total'];
$totalPage = ceil($totalRow / $limit);

$data = $koneksi->query("SELECT * FROM barang $whereSql ORDER BY id DESC LIMIT $limit OFFSET $offset");

$jenisListRes = $koneksi->query("SELECT DISTINCT jenis_barang FROM barang");
$jenisList = [];
while ($j = $jenisListRes->fetch_assoc()) {
    $jenisList[] = $j['jenis_barang'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PT TIMBANG MOMOT | Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #343a40;
            margin: 0;
        }

        .navbar {
            background-color: #007bff;
            padding: 0.8rem 1rem;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .navbar-brand {
            font-size: 1.1rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .nav-search input {
            width: 100%;
            border-radius: 20px 0 0 20px;
            padding: 6px 14px;
            border: 1px solid #ced4da;
            font-size: 1rem;
        }

        .nav-search button {
            border-radius: 0 20px 20px 0;
            padding: 6px 20px;
            background-color: #ffc107;
            border: none;
            color: #212529;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .nav-search button:hover {
            background-color: #e0a800;
        }

        .filter-dropdown select {
            border-radius: 20px;
            border: 1px solid #ced4da;
            padding: 6px 12px;
            font-size: 1rem;
            background-color: #fff;
            min-width: 150px;
        }

        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .table img {
            border-radius: 6px;
        }

        .pagination {
            justify-content: center;
            margin-top: 30px;
        }

        .pagination .page-link {
            border-radius: 20px !important;
            color: #007bff;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
        }

        @media (max-width: 768px) {
            .navbar .container-fluid {
                flex-direction: column;
                align-items: stretch;
            }

            .nav-search,
            .filter-dropdown,
            .btn {
                width: 100% !important;
                margin-top: 0.5rem;
            }

            .nav-search input {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between gap-2">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="logo.jpg" alt="Logo" />
                <span class="ms-2">PT TIMBANG MOMOT</span>
            </a>

            <!-- Search Form -->
            <form class="d-flex flex-grow-1 me-2 nav-search" method="GET" action="index.php">
                <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>" />
                <button type="submit"><i class="bi bi-search"></i></button>
            </form>

            <!-- Tambah Button -->
            <a href="tambah.php" class="btn btn-warning btn-sm me-2">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>

            <!-- Filter Dropdown -->
            <form method="GET" action="index.php" class="filter-dropdown">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>" />
                <select name="jenis" onchange="this.form.submit()" class="form-select form-select-sm">
                    <option value="" <?= $jenis_filter == '' ? 'selected' : '' ?>>Semua Jenis</option>
                    <?php foreach ($jenisList as $jenis): ?>
                        <option value="<?= htmlspecialchars($jenis) ?>" <?= $jenis_filter == $jenis ? 'selected' : '' ?>><?= htmlspecialchars($jenis) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </nav>

    <!-- Table -->
    <div class="container my-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Harga Jual</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data->num_rows > 0): ?>
                        <?php $no = $offset + 1; ?>
                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center">
                                    <?php if ($row['gambar'] && file_exists('uploads/' . $row['gambar'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar" style="width: 80px; height: 60px; object-fit: cover;" />
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/80x60?text=No+Image" alt="No Image" />
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['jenis_barang']) ?></td>
                                <td class="text-success fw-bold">Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Produk tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPage > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($p = 1; $p <= $totalPage; $p++): ?>
                        <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&jenis=<?= urlencode($jenis_filter) ?>&page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <!-- Bootstrap & SweetAlert scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
