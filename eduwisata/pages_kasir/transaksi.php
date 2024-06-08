<?php
require '../config.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$id_pegawai = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_ticket'])) {
        // Proses pembelian tiket
        $jumlah = $_POST['jumlah'];
        $id_tiket = $_POST['id_tiket'];
        $jenispembayaran = $_POST['metode_pembayaran'];
        $jumlah_uang = $_POST['jumlah_uang'];
        $totalharga = $_POST['total_harga'];

        if ($jumlah_uang < $totalharga) {
            echo "Jumlah uang tidak mencukupi.";
        } else {
            // Dapatkan harga tiket berdasarkan id_tiket
            $stmt = $pdo->prepare('SELECT nama, harga FROM tiket WHERE id_tiket = ?');
            $stmt->execute([$id_tiket]);
            $tiket = $stmt->fetch();

            if ($tiket) {
                $nama_tiket = $tiket['nama'];
                $harga = $tiket['harga'];
                $totalharga = $jumlah * $harga;

                // Simpan data pembelian ke riwayat_transaksi
                $stmt = $pdo->prepare('INSERT INTO riwayat_transaksi (jumlah, totalharga, id_tiket, id_pegawai, waktu, jenispembayaran) VALUES (?, ?, ?, ?, NOW(), ?)');
                $stmt->execute([$jumlah, $totalharga, $id_tiket, $id_pegawai, $jenispembayaran]);

                // Dapatkan ID transaksi yang baru dimasukkan
                $id_transaksi = $pdo->lastInsertId();

                // Alihkan ke halaman yang sama dengan ID transaksi untuk memicu pembuatan PDF
                header('Location: ' . $_SERVER['PHP_SELF'] . '?id_transaksi=' . $id_transaksi);
                exit();
            } else {
                echo "Jenis tiket tidak ditemukan.";
            }
        }
    }
}

// Ambil data tiket untuk formulir
$tickets = $pdo->query('SELECT id_tiket, nama, harga FROM tiket')->fetchAll(PDO::FETCH_ASSOC);
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            height: 100vh;
            margin: 0;
        }
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

        .container {
            display: flex;
            height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .form-container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        .form-container input[type="number"],
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Gaya untuk pop up notifikasi */
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: white;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .popup h2 {
            margin: 0;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Kasir Dashboard</h2>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="transaksi.php"><i class="fas fa-ticket-alt"></i> Tiket</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <div class="main-content">
            <div class="form-container">
                <h1>Form Pembelian Tiket</h1>
                <form id="ticketForm" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                    <label for="jumlah-tiket">Jumlah Tiket:</label>
                    <input type="number" id="jumlah-tiket" name="jumlah" min="1" required>
                    <label for="jenis-tiket">Pilih Tiket:</label>
                    <select id="jenis-tiket" name="id_tiket" required>
                        <option value="#" disabled selected>Pilih</option>
                        <?php foreach ($tickets as $ticket): ?>
                            <option value="<?= $ticket['id_tiket'] ?>" data-harga="<?= $ticket['harga'] ?>"><?= $ticket['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="pilih-pembayaran">Pilih Pembayaran:</label>
                    <select id="pilih-pembayaran" name="metode_pembayaran" required>
                        <option value="#" disabled selected>Pilih</option>
                        <option value="Cash">Cash</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                    <label for="total-harga">Total Harga:</label>
                    <input type="number" id="total-harga" name="total_harga" readonly>
                    <label for="jumlah-uang">Jumlah Uang:</label>
                    <input type="number" id="jumlah-uang" name="jumlah_uang" min="0" required>
                    <label for="kembalian">Kembalian:</label>
                    <input type="number" id="kembalian" name="kembalian" readonly>
                    <input type="submit" name="submit_ticket" value="Beli Tiket">
                </form>
            </div>
        </div>
    </div>

    <div id="popup" class="popup">
        <h2>Transaksi Berhasil!</h2>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jumlahTiketInput = document.getElementById('jumlah-tiket');
            const jenisTiketSelect = document.getElementById('jenis-tiket');
            const totalHargaInput = document.getElementById('total-harga');
            const jumlahUangInput = document.getElementById('jumlah-uang');
            const kembalianInput = document.getElementById('kembalian');
            const ticketForm = document.getElementById('ticketForm');

            function calculateTotalHarga() {
                const jumlahTiket = jumlahTiketInput.value;
                const selectedTiket = jenisTiketSelect.options[jenisTiketSelect.selectedIndex];
                const hargaTiket = selectedTiket.getAttribute('data-harga');
                const totalHarga = jumlahTiket * hargaTiket;
                totalHargaInput.value = totalHarga;
            }

            function calculateKembalian() {
                const jumlahUang = jumlahUangInput.value;
                const totalHarga = totalHargaInput.value;
                const kembalian = jumlahUang - totalHarga;
                kembalianInput.value = kembalian >= 0 ? kembalian : 0;
            }

            jumlahTiketInput.addEventListener('input', calculateTotalHarga);
            jenisTiketSelect.addEventListener('change', calculateTotalHarga);
            jumlahUangInput.addEventListener('input', calculateKembalian);

            ticketForm.addEventListener('submit', function (event) {
                if (jumlahUangInput.value < totalHargaInput.value) {
                    event.preventDefault();
                    alert('Jumlah uang tidak mencukupi untuk melakukan transaksi.');
                }
            });
        });

        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const popup = document.getElementById('popup');
            if (urlParams.has('id_transaksi')) {
                popup.style.display = 'block';
                setTimeout(function() {
                    popup.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>
