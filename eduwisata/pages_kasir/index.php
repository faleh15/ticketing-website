<?php
require '../config.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil data pengguna dari sesi
$id_pegawai = $_SESSION['user_id'];

// Ambil data penjualan hari ini dan total pendapatan
$tanggalHariIni = date('Y-m-d');

// Query untuk menghitung total penjualan hari ini
$stmt = $pdo->prepare('SELECT COUNT(*) as total_penjualan, SUM(totalharga) as total_pendapatan FROM riwayat_transaksi WHERE id_pegawai = ? AND DATE(waktu) = ?');
$stmt->execute([$id_pegawai, $tanggalHariIni]);
$dataHariIni = $stmt->fetch();

$totalPenjualanHariIni = $dataHariIni['total_penjualan'] ?? 0;
$totalPendapatanHariIni = $dataHariIni['total_pendapatan'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Dashboard</title>
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

        .main-content h1 {
            margin-top: 0;
            font-size: 36px;
            color: #333;
        }

        .dashboard {
            display: flex;
            justify-content: space-around;
            margin-top: 50px;
        }

        .dashboard-item {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 30%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-item:hover {
            transform: translateY(-5px);
        }

        .dashboard-item h2 {
            margin-top: 0;
            color: #333;
        }

        .dashboard-item p {
            color: #666;
            font-size: 24px;
            margin: 10px 0;
        }

        .dashboard-item i {
            font-size: 48px;
            color: #ff6f61;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Kasir Dashboard</h2>
            <nav>
                <ul>
                    <li><a href="transaksi.php"><i class="fas fa-box"></i> Tiket</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Selamat Datang di Halaman Kasir</h1>
            <!-- Dashboard Data -->
            <div class="dashboard">
                <div class="dashboard-item">
                    <i class="fas fa-ticket-alt"></i>
                    <h2>Penjualan Hari Ini</h2>
                    <p><?= $totalPenjualanHariIni ?></p>
                </div>
                <div class="dashboard-item">
                    <i class="fas fa-coins"></i>
                    <h2>Total Pendapatan</h2>
                    <p>Rp <?= number_format($totalPendapatanHariIni, 0, ',', '.') ?></p>
                </div>
            </div>
        </main>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
