<?php
    require '../config.php';
    session_start();
    // Pastikan pengguna sudah login
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
    
    // Retrieve total number of Pegawai
    $stmt_pegawai = $pdo->query('SELECT COUNT(*) AS total_pegawai FROM pegawai');
    $total_pegawai = $stmt_pegawai->fetchColumn();

    // Retrieve total number of Tiket
    $stmt_tiket = $pdo->query('SELECT COUNT(*) AS total_tiket FROM tiket');
    $total_tiket = $stmt_tiket->fetchColumn();

    // Retrieve total sales amount
    $stmt_transaksi = $pdo->query('SELECT SUM(totalharga) AS total_penjualan FROM riwayat_transaksi');
    $total_penjualan = $stmt_transaksi->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            <h2>Admin Dashboard</h2>
            <nav>
                <ul>
                    <li><a href="barang.php"><i class="fas fa-box"></i> Tiket</a></li>
                    <li><a href="kasir.php"><i class="fas fa-user"></i> Kasir</a></li>
                    <li><a href="transaksi.php"><i class="fas fa-history"></i> Riwayat Transaksi</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Selamat Datang, Admin</h1>
            <!-- Dashboard Data -->
            <div class="dashboard">
                <div class="dashboard-item">
                    <i class="fas fa-users"></i>
                    <h2>Total Pegawai</h2>
                    <p><?php echo $total_pegawai; ?></p>
                </div>
                <div class="dashboard-item">
                    <i class="fas fa-ticket-alt"></i>
                    <h2>Total Tiket</h2>
                    <p><?php echo $total_tiket; ?></p>
                </div>
                <div class="dashboard-item">
                    <i class="fas fa-coins"></i>
                    <h2>Total Penjualan</h2>
                    <p><?php echo '$' . number_format($total_penjualan, 2); ?></p>
                </div>
            </div>
        </main>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
