<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah sesi email tersedia
if (!isset($_SESSION['email'])) {
    // Redirect ke halaman login jika belum login
    header("Location: ../auth/login.php");
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
<body class="bg-gray-50 font-poppins">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-500 to-blue-700 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Dashboard</h1>
            <a href="./logout.php" class="bg-red-500 hover:bg-red-600 text-white py-2 px-6 rounded-lg font-medium transition">Logout</a>
        </div>
    </nav>

    <!-- Welcome Section -->
    <div class="container mx-auto mt-10 max-w-4xl">
        <div class="bg-white shadow-lg rounded-lg p-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Selamat datang, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h2>
            <p class="text-gray-600">Kelola aktivitas dan data Anda dengan mudah melalui dashboard ini.</p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container mx-auto mt-12 max-w-6xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white shadow-md rounded-lg p-6 transform hover:scale-105 transition duration-300">
                <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c1.656 0 3-1.343 3-3s-1.344-3-3-3-3 1.343-3 3 1.344 3 3 3zm0 0v4m0 0l-4 4m4-4l4 4"></path></svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Event Anda</h3>
                <p class="text-gray-600">Lihat dan kelola acara yang Anda buat.</p>
                <a href="event.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-medium transition">Kelola Event</a>
            </div>

            <!-- Card 2 -->
            <div class="bg-white shadow-md rounded-lg p-6 transform hover:scale-105 transition duration-300">
                <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-8 8a4 4 0 018 0v1H8v-1z"></path></svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Data Peserta</h3>
                <p class="text-gray-600">Kelola daftar peserta yang sudah mendaftar.</p>
                <a href="data-peserta.php" class="mt-4 inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-6 rounded-lg font-medium transition">Lihat Data</a>
            </div>

            <!-- Card 3 -->
            <div class="bg-white shadow-md rounded-lg p-6 transform hover:scale-105 transition duration-300">
                <div class="flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c1.656 0 3-1.343 3-3s-1.344-3-3-3-3 1.343-3 3 1.344 3 3 3zm0 0v4m0 0l-4 4m4-4l4 4"></path></svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Laporan Kehadiran</h3>
                <p class="text-gray-600">Pantau kehadiran peserta secara real-time.</p>
                <a href="laporan-peserta.php" class="mt-4 inline-block bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-6 rounded-lg font-medium transition">Lihat Laporan</a>
            </div>
        </div>
    </div>
</body>
</html>
