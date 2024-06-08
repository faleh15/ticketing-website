<?php
require '../config.php';
session_start();

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idpegawai = $_POST['idpegawai'] ?? null;
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($idpegawai) {
        // Update
        $stmt = $pdo->prepare('UPDATE pegawai SET nama = ?, password = ?, role = ? WHERE id_pegawai = ?');
        $stmt->execute([$nama, $password, $role, $idpegawai]); // Tambahkan $role ke dalam array
    } else {
        // Create
        $stmt = $pdo->prepare('INSERT INTO pegawai (nama, password, role) VALUES (?, ?, ?)');
        $stmt->execute([$nama, $password, $role]);
    }
    header('Location: kasir.php');
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $idpegawai = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM pegawai WHERE id_pegawai = ?');
    $stmt->execute([$idpegawai]);
    header('Location: kasir.php');
    exit();
}

// Fetch Data for Read
$stmt = $pdo->query('SELECT * FROM pegawai');
$pegawais = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kasir</title>
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

        .popup form select {
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
                <h1>Data Kasir</h1>
                <button class="insert-button"><i class="fas fa-plus"></i> Insert Data</button>
            </header>
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pegawais as $index => $pegawai): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $pegawai['nama'] ?></td>
                        <td><?= $pegawai['role'] == 1 ? 'Admin' : 'Kasir' ?></td>
                        <td class="action-buttons">
                            <div class="edit-delete-icons">
                                <i class="fas fa-edit edit-button" onclick="openUpdatePopup(<?= $pegawai['id_pegawai'] ?>, '<?= $pegawai['nama'] ?>', '<?= $pegawai['password'] ?>', '<?= $pegawai['role'] ?>')"></i>
                                <i class="fas fa-trash-alt delete-button" onclick="deleteData(<?= $pegawai['id_pegawai'] ?>)"></i>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
    <!-- Insert and Update Popup -->
    <div class="overlay" id="insertOverlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h2 id="popupTitle">Insert Data</h2>
            <form id="form" action="" method="post">
                <input type="hidden" id="idpegawai" name="idpegawai">
                <label for="nama">Nama:</label><br>
                <input type="text" id="nama" name="nama" required><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br>
                <label for="role">Role:</label><br>
                <select id="role" name="role" required>
                    <option value="1">Admin</option>
                    <option value="2">Kasir</option>
                </select><br>
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
            document.getElementById('idpegawai').value = '';
            insertOverlay.style.display = 'flex';
        }
        function openUpdatePopup(idpegawai, nama, password, role) {
            popupTitle.textContent = 'Update Data';
            submitButton.textContent = 'Update';
            document.getElementById('idpegawai').value = idpegawai;
            document.getElementById('nama').value = nama;
            document.getElementById('password').value = password;
            document.getElementById('role').value = role;
            insertOverlay.style.display = 'flex';
        }
        function closePopup() {
            insertOverlay.style.display = 'none';
        }
        function deleteData(idpegawai) {
            if (confirm('Are you sure you want to delete this item?')) {
                window.location.href = 'barang.php?delete=' + idpegawai;
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
