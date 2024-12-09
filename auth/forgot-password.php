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
    $email = $_POST['email'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Format email tidak valid!');
                window.location.href = 'lupa-password.php';
              </script>";
        exit();
    }

    // Cek apakah email ada dalam database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Membuat token reset password yang unik
        $token = bin2hex(random_bytes(50));

        // Simpan token ke database dengan status belum digunakan
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = NOW() + INTERVAL 1 HOUR WHERE email = :email");
        $stmt->execute(['token' => $token, 'email' => $email]);

        // Kirim email dengan tautan reset password
        $resetLink = "http://localhost/reset-password.php?token=" . $token; // Ganti dengan URL yang sesuai
        $subject = "Reset Password Anda";
        $message = "Klik tautan berikut untuk mereset password Anda: $resetLink";
        $headers = "From: no-reply@domain.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "<script>
                    alert('Tautan reset password telah dikirim ke email Anda!');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal mengirim email. Coba lagi.');
                    window.location.href = 'lupa-password.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Email tidak ditemukan.');
                window.location.href = 'lupa-password.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
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
            <h1 class="text-2xl font-semibold text-center mb-6">Lupa Password</h1>
            <p class="text-gray-600 text-sm text-center mb-6">
                Masukkan email yang terdaftar, kami akan mengirimkan tautan untuk mereset password Anda.
            </p>
            <form action="lupa-password.php" method="post" class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Email..." required>
                </div>
                <!-- Submit Button -->
                <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">
                    Kirim Tautan Reset Password
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
