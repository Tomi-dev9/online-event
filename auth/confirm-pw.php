<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

session_start();

if (isset($_POST['submit'])) {
    $email = $_SESSION['email']; // Pastikan email disimpan di session dari proses sebelumnya
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validasi password
    if ($password !== $confirmPassword) {
        echo "<script>
                alert('Password dan konfirmasi password tidak cocok!');
                window.location.href = 'confirm-pw.php';
              </script>";
        exit();
    }

    // Hash password sebelum menyimpan ke database
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Update password di tabel users
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);

    if ($stmt->execute()) {
        // Hapus data dari tabel password_reset setelah password berhasil diubah
        $deleteStmt = $conn->prepare("DELETE FROM password_reset WHERE email = ?");
        $deleteStmt->bind_param("s", $email);
        $deleteStmt->execute();

        echo "<script>
                alert('Password berhasil diubah. Silakan login.');
                window.location.href = 'login.php';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan. Coba lagi.');
                window.location.href = 'confirm-pw.php';
              </script>";
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
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
                Masukkan password baru Anda.
            </p>
            <form action="confirm-pw.php" method="post" class="space-y-4">
                <!-- Password Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Password baru..." required>
                </div>
                <!-- Konfirmasi Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Konfirmasi password..." required>
                </div>
                <!-- Submit Button -->
                <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">
                    Ubah Password
                </button>
            </form>
            <!-- Back to Login -->
            <div class="text-center mt-4">
                <a href="login.php" class="text-blue-500 hover:underline">Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>

</html>
