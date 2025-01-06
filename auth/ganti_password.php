<?php
session_start();

// Cek apakah kode verifikasi ada di sesi
if (!isset($_SESSION['kode_verifikasi'])) {
    header("Location: form_reset_password.php"); // Redirect ke halaman reset jika kode verifikasi tidak ditemukan
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil password baru dari form
    $passwordBaru = $_POST['password_baru'];
    $konfirmasiPassword = $_POST['konfirmasi_password'];

    // Validasi password
    if ($passwordBaru !== $konfirmasiPassword) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Update password di database
        $email = $_SESSION['email'];
        $hashedPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);

        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "absensi_online";

        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Update password
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->execute([
                'password' => $hashedPassword,
                'email' => $email
            ]);

            // Hapus kode verifikasi dari sesi setelah berhasil
            unset($_SESSION['kode_verifikasi']);
            header("Location: login.php"); // Redirect ke halaman login setelah sukses
            exit();
        } catch (PDOException $e) {
            $error = "Gagal memperbarui password: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            text-align: center;
        }

        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Ganti Password</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Kotak untuk password baru -->
            <div class="form-group">
                <label for="password_baru">Password Baru:</label>
                <input type="password" id="password_baru" name="password_baru" required>
            </div>

            <!-- Kotak untuk konfirmasi password -->
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password:</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
            </div>

            <!-- Kotak untuk tombol submit -->
            <div class="form-group">
                <button type="submit">Ganti Password</button>
            </div>
        </form>

    </div>

</body>
</html>
