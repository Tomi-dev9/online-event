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
    $kodeVerifikasi = $_POST['kode_verifikasi'];

    // Validasi kode verifikasi
    $stmt = $conn->prepare("SELECT * FROM password_reset WHERE email = ? AND kode_verifikasi = ? AND created_at >= NOW() - INTERVAL 24 HOUR");
    $stmt->bind_param("si", $email, $kodeVerifikasi);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Kode valid, redirect ke halaman reset password
        echo "<script>
                alert('Kode verifikasi valid. Silakan reset password Anda.');
                window.location.href = 'confirm-pw.php';
              </script>";
    } else {
        echo "<script>
                alert('Kode verifikasi tidak valid atau telah kadaluarsa.');
                window.location.href = 'verify_code.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kode</title>
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
            <h1 class="text-2xl font-semibold text-center mb-6">Verifikasi Kode</h1>
            <p class="text-gray-600 text-sm text-center mb-6">
                Masukkan kode verifikasi yang telah dikirimkan ke email Anda.
            </p>
            <form action="verify_code.php" method="post" class="space-y-4">
                <!-- Kode Verifikasi -->
                <div>
                    <label for="kode_verifikasi" class="block text-sm font-medium text-gray-700">Kode Verifikasi</label>
                    <input type="text" id="kode_verifikasi" name="kode_verifikasi" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Masukkan kode verifikasi..." required>
                </div>
                <!-- Submit Button -->
                <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">
                    Verifikasi Kode
                </button>
            </form>
            <!-- Back to Forgot Password -->
            <div class="text-center mt-4">
                <a href="forgot-password.php" class="text-blue-500 hover:underline">Kembali ke Lupa Password</a>
            </div>
        </div>
    </div>
</body>

</html>
