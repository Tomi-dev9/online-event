<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

session_start(); // Memulai sesi untuk login

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Hashing password diabaikan di query karena ini hanya contoh.
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if ($user && password_verify($password, $user['password'])) {
            // Simpan sesi
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
        
            // Debugging: Cek nilai session
            var_dump($_SESSION);
        
            // Redirect sesuai role
            if ($user['role'] === 'penyelenggara') {
                header("Location: dashboard/dashboard.php");
            } elseif ($user['role'] === 'peserta') {
                header("Location: peserta/home.php");
            }
            exit();
        } else {
            echo "Email atau password salah.";
        }        
    } else {
        echo "Email tidak ditemukan.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            <h1 class="text-2xl font-semibold text-center mb-6">Login</h1>
            <form action="" method="post" class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Email..." required>
                </div>
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Password..." required>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">
                    LOGIN
                </button>
            </form>
            <!-- Tambahan Link -->
            <div class="text-center mt-4 space-y-2">
                <a href="register.php" class="text-blue-500 hover:underline">Belum punya akun? Daftar</a>
                <br>
                <a href="forgot-password.php" class="text-red-500 hover:underline">Lupa Password?</a>
            </div>
        </div>
    </div>
</body>
</html>
