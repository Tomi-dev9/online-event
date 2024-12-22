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
$isLoggedIn = isset($_SESSION['email']) && isset($_SESSION['email']); // Periksa apakah pengguna sudah login

// Query untuk mengambil data dari tabel events
$sql = "SELECT event_id, event_name, event_date, start_time, end_time, image FROM events";  // Fixed the query
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Acara - SikilatAbsensi</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">SikilatAbsensi</h1>
        <div>
            <?php if ($isLoggedIn): ?>
                <span class="mr-4">Halo, <?php echo htmlspecialchars($_SESSION['email']); ?>!</span>
                <a href="../dashboard/logout.php" class="text-white hover:text-gray-200">Logout</a>
            <?php else: ?>
                <a href="../auth/login.php" class="text-white hover:text-gray-200">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="container mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Daftar Acara yang Sedang Berlangsung</h2>
    
    <!-- List of Events -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <!-- Menampilkan gambar -->
                    <?php 
                        $imagePath = !empty($row['image']) ? "../dashboard/img/" . htmlspecialchars($row['image'] ) : ''; 
                    ?>
                    <img src="<?php echo $imagePath; ?>" alt="Gambar <?php echo htmlspecialchars($row['event_name']); ?>" class="w-full h-40 object-cover rounded-md mb-4">
                    
                    <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($row['event_name']); ?></h3>
                    <p class="text-gray-600">Tanggal: <?php echo htmlspecialchars($row['event_date']); ?></p>
                    <p class="text-gray-600">Waktu: <?php echo htmlspecialchars($row['start_time']) . " - " . htmlspecialchars($row['end_time']); ?></p>
                    
                    <!-- Tombol untuk melakukan absensi -->
                    <a href="./absensi.php?event_id=<?php echo $row['event_id']; ?>" 
                       class="mt-4 inline-block bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-500">
                        Absensi
                    </a>
                    
                    <!-- Tombol untuk mendaftar peserta -->
                    <a href="./daftar_peserta.php?event_id=<?php echo $row['event_id']; ?>" 
                       class="mt-2 inline-block bg-green-600 text-white py-2 px-4 rounded hover:bg-green-500">
                        Daftar
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-600">Tidak ada acara yang sedang berlangsung.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>

<?php
$conn->close();
?>
