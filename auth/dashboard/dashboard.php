<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah sesi email tersedia
if (!isset($_SESSION['email'])) {
    // Redirect ke halaman login jika belum login
    header("Location: /login.php");
    exit();
}

// Jika pengguna sudah login, Anda dapat melanjutkan dengan logika lainnya di sini
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-poppins">
    <!-- Navbar -->
    <nav class="bg-blue-500 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-lg font-semibold">Dashboard</h1>
            <a href="../login.php" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg font-medium transition">Logout</a>
        </div>
    </nav>

    <!-- Welcome Section -->
    <div class="container mx-auto mt-10 max-w-3xl">
        <div class="bg-white shadow-lg rounded-lg p-6 text-center">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Selamat datang, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h2>
            <p class="text-gray-600">Anda berhasil masuk ke dashboard. Di sini Anda dapat mengakses semua fitur yang tersedia.</p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container mx-auto mt-8 max-w-4xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-blue-100 shadow-md rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Event Anda</h3>
                <p class="text-gray-600">Lihat dan kelola acara yang Anda buat.</p>
                <a href="#" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-medium transition">Kelola Event</a>
            </div>

            <!-- Card 2 -->
            <div class="bg-green-100 shadow-md rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Data Peserta</h3>
                <p class="text-gray-600">Kelola daftar peserta yang sudah mendaftar.</p>
                <a href="#" class="mt-4 inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg font-medium transition">Lihat Data</a>
            </div>

            <!-- Card 3 -->
            <div class="bg-yellow-100 shadow-md rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Laporan Kehadiran</h3>
                <p class="text-gray-600">Pantau kehadiran peserta secara real-time.</p>
                <a href="#" class="mt-4 inline-block bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg font-medium transition">Lihat Laporan</a>
            </div>
        </div>
    </div>
</body>
</html>
