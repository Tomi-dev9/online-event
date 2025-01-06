<?php
session_start();

// Cek apakah email sudah ada di sesi
if (!isset($_SESSION['email'])) {
    header("Location: form_reset_password.php"); // Redirect ke halaman reset jika email tidak ditemukan
    exit();
}

$email = $_SESSION['email'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil kode verifikasi dari form
        $kodeVerifikasi = $_POST['kode_verifikasi'];

        // Query untuk memeriksa kode verifikasi
        $stmt = $pdo->prepare("SELECT created_at FROM password_reset WHERE email = :email AND kode_verifikasi = :kode_verifikasi ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([
            'email' => $email,
            'kode_verifikasi' => $kodeVerifikasi
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $createdAt = new DateTime($result['created_at']);
            $now = new DateTime();

            // Cek apakah kode verifikasi masih berlaku (tidak lebih dari 24 jam)
            $interval = $createdAt->diff($now);
            if ($interval->h >= 24) {
                echo '<p style="color: red;">Kode verifikasi telah kadaluarsa. Silakan minta kode baru.</p>';
            } else {
                // Jika kode valid, redirect ke halaman ganti password
                $_SESSION['kode_verifikasi'] = $kodeVerifikasi; // Simpan kode ke sesi
                header("Location: ganti_password.php");
                exit();
            }
        } else {
            echo '<p style="color: red;">Kode verifikasi salah. Silakan coba lagi.</p>';
        }
    }
} catch (PDOException $e) {
    echo 'Koneksi ke database gagal: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukkan Kode Verifikasi</title>
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
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        p {
            text-align: center;
            font-size: 14px;
            color: #555;
        }

        .form-group {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        input[type="text"] {
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
    </style>
</head>
<body>

    <div class="container">
        <h2>Masukkan Kode Verifikasi</h2>
        <p>Kami telah mengirimkan kode verifikasi ke email Anda: <strong><?php echo htmlspecialchars($email); ?></strong></p>
        
        <?php if (isset($result) && !$result): ?>
            <p class="error">Kode verifikasi salah. Silakan coba lagi.</p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="kode_verifikasi">Kode Verifikasi:</label>
                <input type="text" id="kode_verifikasi" name="kode_verifikasi" required>
            </div>
            <div class="form-group">
                <button type="submit">Verifikasi</button>
            </div>
        </form>
    </div>

</body>
</html>