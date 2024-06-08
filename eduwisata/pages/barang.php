<?php
require '../config.php';
session_start();

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idTiket = $_POST['idTiket'] ?? null;
    $namaTiket = $_POST['namaTiket'];
    $harga = $_POST['harga'];

    if ($idTiket) {
        // Update
        $stmt = $pdo->prepare('UPDATE tiket SET nama = ?, harga = ? WHERE id_tiket = ?');
        $stmt->execute([$namaTiket, $harga, $idTiket]);
    } else {
        // Create
        $stmt = $pdo->prepare('INSERT INTO tiket (nama, harga) VALUES (?, ?)');
        $stmt->execute([$namaTiket, $harga]);
    }
    header('Location: barang.php');
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $idTiket = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tiket WHERE id_tiket = ?');
    $stmt->execute([$idTiket]);
    header('Location: barang.php');
    exit();
}

// Fetch Data for Read
$stmt = $pdo->query('SELECT * FROM tiket');
$tikets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tiket</title>
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

        .insert-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .insert-button:hover {
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

        /* Styles for the overlay and popup */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            max-width: 600px;
            width: 90%;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
            color: #333;
        }

        .popup form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .popup form label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
            width: 80%;
        }

        .popup form input[type="text"],
        .popup form input[type="password"] {
            width: 80%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .popup form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .popup form button:hover {
            background-color: #45a049;
        }

        .edit-delete-icons {
            display: flex;
            gap: 10px;
        }

        .edit-delete-icons i {
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .edit-delete-icons i:hover {
            color: #333;
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
                <h1>Data Tiket</h1>
                <button class="insert-button"><i class="fas fa-plus"></i> Insert Data</button>
            </header>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Tiket</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tikets as $index => $tiket): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $tiket['nama'] ?></td>
                        <td>Rp <?= $tiket['harga'] ?></td>
                        <td class="action-buttons">
                            <div class="edit-delete-icons">
                                <i class="fas fa-edit edit-button" onclick="openUpdatePopup(<?= $tiket['id_tiket'] ?>, '<?= $tiket['nama'] ?>', '<?= $tiket['harga'] ?>')"></i>
                                <i class="fas fa-trash-alt delete-button" onclick="deleteData(<?= $tiket['id_tiket'] ?>)"></i>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
    <div class="overlay" id="insertOverlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h2 id="popupTitle">Insert Data</h2>
            <form id="form" action="" method="post">
                <input type="hidden" id="idTiket" name="idTiket">
                <label for="namaTiket">Nama Tiket:</label><br>
                <input type="text" id="namaTiket" name="namaTiket" required><br>
                <label for="harga">Harga:</label><br>
                <input type="text" id="harga" name="harga" required><br>
                <button type="submit" id="submitButton">Submit</button>
            </form>
        </div>
    </div>
    <script>
        const insertButton = document.querySelector('.insert-button');
        const insertOverlay = document.getElementById('insertOverlay');
        const form = document.getElementById('form');
        const popupTitle = document.getElementById('popupTitle');
        const submitButton = document.getElementById('submitButton');

        insertButton.addEventListener('click', () => {
            openInsertPopup();
        });
        function openInsertPopup() {
            form.reset();
            popupTitle.textContent = 'Insert Data';
            submitButton.textContent = 'Submit';
            document.getElementById('idTiket').value = '';
            insertOverlay.style.display = 'flex';
        }
        function openUpdatePopup(idTiket, namaTiket, harga) {
            popupTitle.textContent = 'Update Data';
            submitButton.textContent = 'Update';
            document.getElementById('idTiket').value = idTiket;
            document.getElementById('namaTiket').value = namaTiket;
            document.getElementById('harga').value = harga;
            insertOverlay.style.display = 'flex';
        }
        function closePopup() {
            insertOverlay.style.display = 'none';
        }
        function deleteData(idTiket) {
            if (confirm('Are you sure you want to delete this item?')) {
                window.location.href = 'barang.php?delete=' + idTiket;
            }
        }
        window.addEventListener('click', (e) => {
            if (e.target == insertOverlay) {
                closePopup();
            }
        });
    </script>
</body>
</html>

