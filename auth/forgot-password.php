<?php
// Inisialisasi pesan untuk SweetAlert
$sweetAlertMessage = '';

// Jika form disubmit
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);

    // Cek apakah email terdaftar di database
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

    // Query untuk cek email
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika email terdaftar, kirim kode verifikasi (simulasi)
        $verificationCode = rand(100000, 999999);  // Simulasi kode verifikasi
        // Kirim email dengan kode verifikasi (di sini kita hanya menampilkan pesan sukses)
        // Gunakan fungsi mail() atau library seperti PHPMailer untuk mengirim email

        // Set pesan sukses
        $sweetAlertMessage = "Kode verifikasi telah dikirim ke email Anda.";
    } else {
        // Jika email tidak terdaftar
        $sweetAlertMessage = "Email tidak ditemukan. Pastikan email yang Anda masukkan benar.";
    }

    $stmt->close();
    $conn->close();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                Masukkan email yang terdaftar, kami akan mengirimkan kode verifikasi untuk mereset password Anda.
            </p>
            <form action="send-code.php" method="post" class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Email..." required>
                </div>
                <!-- Submit Button -->
                <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">
                    Kirim Kode Verifikasi
                </button>
            </form>
            <!-- Back to Login -->
            <div class="text-center mt-4">
                <a href="login.php" class="text-blue-500 hover:underline">Kembali ke Login</a>
            </div>
        </div>
    </div>

    <!-- SweetAlert -->
    <?php if ($sweetAlertMessage): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $sweetAlertMessage == "Kode verifikasi telah dikirim ke email Anda." ? "success" : "error"; ?>',
                title: '<?php echo $sweetAlertMessage; ?>',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
</body>

</html>
