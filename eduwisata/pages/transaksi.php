<?php
require '../config.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

function getTransaksi($pdo, $year = null, $month = null) {
    $sql = 'SELECT rt.id_transaksi, rt.waktu, rt.jumlah, rt.totalharga, rt.jenispembayaran, t.nama AS nama_tiket, p.nama AS nama_pegawai
            FROM riwayat_transaksi rt
            JOIN tiket t ON rt.id_tiket = t.id_tiket
            JOIN pegawai p ON rt.id_pegawai = p.id_pegawai';
    $params = [];
    if ($year && $month) {
        $sql .= ' WHERE YEAR(rt.waktu) = ? AND MONTH(rt.waktu) = ?';
        $params = [$year, $month];
    }
    $sql .= ' ORDER BY rt.waktu DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Proses permintaan AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = isset($_POST['tahun']) ? $_POST['tahun'] : null;
    $month = isset($_POST['bulan']) ? $_POST['bulan'] : null;
    $transaksi = getTransaksi($pdo, $year, $month);
    echo json_encode($transaksi);
    exit();
}

// Ambil semua data transaksi untuk tampilan awal
$transaksi = getTransaksi($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styles for the sidebar */
        .sidebar {
            background-color: #333;
            color: white;
            padding: 20px;
            height: 100%;
        }

        .sidebar h2 {
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar ul li a:hover {
            background-color: #555;
        }

        /* Styles for the main content */
        .container {
            display: flex;
            height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .main-content header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .main-content h1 {
            margin: 0;
        }

        .filters {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filters label {
            margin-right: 5px;
        }

        .filters input, .filters button {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .filters button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filters button:hover {
            background-color: #45a049;
        }

        /* Styles for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Admin Dashboard</h2>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="barang.php"><i class="fas fa-box"></i> Tiket</a></li>
                    <li><a href="kasir.php"><i class="fas fa-user"></i> Kasir</a></li>
                    <li><a href="transaksi.php"><i class="fas fa-history"></i> Riwayat Transaksi</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header>
                <h1>Data Transaksi</h1>
                <div class="filters">
                    <label for="filter-month">Filter Bulan dan Tahun:</label>
                    <input type="month" id="filter-month">
                    <button id="filter-button"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </header>
            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nama Tiket</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Nama Kasir</th>
                        <th>Jenis Pembayaran</th>
                    </tr>
                </thead>
                <tbody id="transaksi-body">
                    <?php if (count($transaksi) > 0): ?>
                        <?php foreach ($transaksi as $trans): ?>
                            <tr>
                                <td><?= $trans['id_transaksi'] ?></td>
                                <td><?= $trans['waktu'] ?></td>
                                <td><?= $trans['nama_tiket'] ?></td>
                                <td><?= $trans['jumlah'] ?></td>
                                <td>Rp <?= number_format($trans['totalharga'], 0, ',', '.') ?></td>
                                <td><?= $trans['nama_pegawai'] ?></td>
                                <td><?= $trans['jenispembayaran'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Tidak ada data transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#filter-button').on('click', function() {
                const monthYear = $('#filter-month').val();
                const year = monthYear ? monthYear.split('-')[0] : null;
                const month = monthYear ? monthYear.split('-')[1] : null;
                const filterData = {
                    tahun: year,
                    bulan: month
                };

                $.ajax({
                    url: 'transaksi.php',
                    type: 'POST',
                    data: filterData,
                    success: function(response) {
                        const transaksi = JSON.parse(response);
                        let rows = '';

                        if (transaksi.length > 0) {
                            transaksi.forEach((trans) => {
                                rows += `<tr>
                                    <td>${trans.id_transaksi}</td>
                                    <td>${trans.waktu}</td>
                                    <td>${trans.nama_tiket}</td>
                                    <td>${trans.jumlah}</td>
                                    <td>Rp ${new Intl.NumberFormat('id-ID').format(trans.totalharga)}</td>
                                    <td>${trans.nama_pegawai}</td>
                                    <td>${trans.jenispembayaran}</td>
                                </tr>`;
                            });
                        } else {
                            rows = '<tr><td colspan="7">Tidak ada data transaksi.</td></tr>';
                        }
                        $('#transaksi-body').html(rows);
                    }
                });
            });
        });
    </script>
</body>
</html>
