<?php
require 'config.php';

// Mulai sesi
session_start();

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $password = $_POST['password'];

    // Query untuk memeriksa nama dan password
    $stmt = $pdo->prepare("SELECT * FROM pegawai WHERE nama = :nama AND password = :password");
    $stmt->execute(['nama' => $nama, 'password' => $password]);
    $user = $stmt->fetch();

    if ($user) {
        // Simpan informasi user di sesi
        $_SESSION['user_id'] = $user['id_pegawai'];
        $_SESSION['role'] = $user['role'];

        // Arahkan berdasarkan peran
        if ($user['role'] == 1) {
            header("Location: pages/index.php");
            exit();
        } elseif ($user['role'] == 2) {
            header("Location: pages_kasir/index.php");
            exit();
        }
    } else {
        $error_message = "Nama atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('1.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-form-container {
            background-color: rgba(255, 255, 255, 0.8); /* Menggunakan warna latar belakang semi-transparan untuk kontras dengan gambar */
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-form-container h1 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .login-form-container .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        .login-form-container label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        .login-form-container input[type="text"],
        .login-form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .login-form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .login-form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        .login-form-container .signup-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-form-container .signup-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-form-container .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-form-container">
        <h1>Sistem Eduwisata <br> Lontar Sewu</h1>
        <h1>Login</h1>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>

