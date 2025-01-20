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

// Inisialisasi variabel untuk pesan SweetAlert
$sweetAlertMessage = '';
$redirectUrl = '';

// Jika form disubmit
if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);

    // Validasi input
    if ($password !== $confirmPassword) {
        $sweetAlertMessage = "Password dan Konfirmasi Password Tidak Cocok!";
    } else {
        // Cek apakah email sudah terdaftar
        $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
        $stmtCheckEmail = $conn->prepare($checkEmailQuery);
        $stmtCheckEmail->bind_param("s", $email);
        $stmtCheckEmail->execute();
        $result = $stmtCheckEmail->get_result();

        if ($result->num_rows > 0) {
            // Jika email sudah terdaftar
            $sweetAlertMessage = "Email Sudah Terdaftar! Gunakan email lain untuk mendaftar.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Query insert ke tabel user
            $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nama, $username, $email, $hashedPassword, $role);

            // Eksekusi query
            if ($stmt->execute()) {
                $sweetAlertMessage = "Pendaftaran Berhasil! Terima kasih telah mendaftar. Silakan login.";
                $redirectUrl = './login.php';
            } else {
                $sweetAlertMessage = "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $stmtCheckEmail->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>

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
            <h1 class="text-2xl font-semibold text-center mb-6">Daftar</h1>
            <form action="" method="post" class="space-y-4">
                <!-- Nama Lengkap -->
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Nama Lengkap..." required>
                </div>
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Username..." required>
                </div>
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
                <!-- Konfirmasi Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Konfirmasi Password..." required>
                </div>
                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Pilih Role</label>
                    <select id="role" name="role" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="penyelenggara">Penyelenggara</option>
                        <option value="peserta">Peserta</option>
                    </select>
                </div>
                <!-- Submit Button -->
                <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">
                    Daftar
                </button>
            </form>
            <!-- Tambahan Link -->
            <div class="text-center mt-4">
                <a href="login.php" class="text-blue-500 hover:underline">Sudah punya akun? Login</a>
            </div>
        </div>
    </div>

    <?php if ($sweetAlertMessage): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $sweetAlertMessage == "Pendaftaran Berhasil! Terima kasih telah mendaftar. Silakan login." ? "success" : "error"; ?>',
                title: '<?php echo $sweetAlertMessage; ?>',
                confirmButtonText: 'OK'
            }).then(function() {
                <?php if ($redirectUrl): ?>
                    window.location.href = '<?php echo $redirectUrl; ?>';
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>
</body>
</html>
