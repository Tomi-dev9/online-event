<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
session_start();

// Proses reset password jika token valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validasi token
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (isset($_POST['submit'])) {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Validasi password
            if ($new_password !== $confirm_password) {
                echo "<script>alert('Password tidak cocok.');</script>";
            } else {
                // Hash password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password dan reset token
                $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = :token");
                $stmt->execute(['password' => $hashed_password, 'token' => $token]);

                echo "<script>alert('Password Anda berhasil direset. Silakan login dengan password baru.'); window.location.href = 'login.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Token tidak valid atau sudah kadaluarsa.'); window.location.href = 'lupa-password.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
</head>
<body class="bg-gray-100 font-poppins">
    <!-- Navbar -->
    <nav class="bg-blue-500 p-4">
        <a href="../index.php" class="text-white font-semibold text-lg">Home</a>
    </nav>

    <!-- Container -->
    <div class="container mx-auto mt-10 max-w-md">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-semibold text-center mb-6">Reset Password</h1>
            <p class="text-gray-600 text-sm text-center mb-6">
                Masukkan password baru Anda untuk melanjutkan.
            </p>
            <form action="" method="post" class="space-y-4">
                <!-- Password Baru -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Password Baru..." required>
                </div>
                <!-- Konfirmasi Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Konfirmasi Password..." required>
                </div>
                <!-- Submit Button -->
                <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">
                    Reset Password
                </button>
            </form>
            <!-- Kembali ke Login -->
            <div class="text-center mt-4">
                <a href="login.php" class="text-blue-500 hover:underline">Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>
</html>
