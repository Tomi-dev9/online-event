<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT event_id, event_name, event_date, start_time, end_time, description, image FROM events";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SikilatAbsensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-poppins">

   <!-- Header -->
<header class="bg-blue-600 shadow-md py-4 sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center px-4">
        <div class="text-xl font-semibold text-white">SikilatAbsensi</div>
        <button class="lg:hidden text-gray-800" aria-label="Toggle navigation" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="hidden lg:flex space-x-6">
            <a class="text-white hover:text-gray-300 transition-600" href="./auth/login.php">Login</a>
        </nav>
    </div>
</header>

    <!-- Hero Section -->
    <section class="bg-blue-600 text-white py-16">
        <div class="container mx-auto text-center px-4">
            <h1 class="text-3xl sm:text-4xl font-bold mb-4">Solusi Absensi untuk Kegiatan Online dan Offline</h1>
            <p class="text-lg sm:text-xl mb-8">Membantu mencatat kehadiran dan mengelola sertifikat acara Anda dengan mudah.</p>
        </div>
    </section>

    <!-- Event Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-8">Daftar Event</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                            <?php 
                                $imagePath = !empty($row['image']) && file_exists("./dashboard/img/" . $row['image']) 
                                    ? "./dashboard/img/" . htmlspecialchars($row['image']) 
                                    : 'default.jpg'; 
                            ?>
                            <img src="<?php echo $imagePath; ?>" alt="Gambar <?php echo htmlspecialchars($row['event_name']); ?>" class="w-full h-56 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($row['event_name']); ?></h3>
                                <p class="text-gray-600 mt-2">Tanggal: <?php echo htmlspecialchars($row['event_date']); ?></p>
                                <p class="text-gray-600 mt-2">Waktu: <?php echo htmlspecialchars($row['start_time']) . " - " . htmlspecialchars($row['end_time']); ?></p>
                                <p class="text-gray-600 mt-4">Deskripsi:</p>
                                <p class="text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                                <a href="./auth/login.php" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition duration-200">Daftar</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-gray-600 col-span-full">Tidak ada acara yang sedang berlangsung.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<!-- Meet Our Team Section -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-8 text-center">Meet Our Team</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            <!-- Team Member 1 -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <img src="./image/DSCF5322.JPG" alt="Team Member 1" class="w-full h-56 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800">Tomingse Lingga</h3>
                    <p class="text-gray-600 mt-2">Web Developer</p>
                </div>
            </div>
            <!-- Team Member 2 -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <img src="./image/raihan.JPG" alt="Team Member 2" class="w-full h-56 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800">Muhhammad Raihan Hanafi</h3>
                    <p class="text-gray-600 mt-2">Web Developer</p>
                </div>
            </div>
            <!-- Team Member 3 -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <img src="./image/farhan.jpg" alt="Team Member 3" class="w-full h-56 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800">Muhhammad Farhan Saz</h3>
                    <p class="text-gray-600 mt-2">Frontend Developer</p>
                </div>
            </div>
            <!-- Team Member 4 -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <img src="./image/destia.jpg" alt="Team Member 4" class="w-full h-56 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800">Destia</h3>
                    <p class="text-gray-600 mt-2">UI/UX Designer</p>
                </div>
            </div>
            <!-- Team Member 5 -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <img src="./image/WhatsApp Image 2025-01-05 at 12.45.26_2d169853.jpg" alt="Team Member 5" class="w-full h-56 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800">Rizki Yehezkiel Sigalingging</h3>
                    <p class="text-gray-600 mt-2">Frontend Developer</p>
                </div>
            </div>
        </div>
    </div>
</section>


    <!-- Footer Section -->
    <footer class="bg-gray-800 text-gray-300 py-12">
        <div class="container mx-auto px-4">
            <p class="text-center">&copy; 2025 PBL-106. All rights reserved.</p>
        </div>
    </footer>

    
    <!-- Mobile Menu Toggle Script -->
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const navbar = document.querySelector('nav');

        menuToggle.addEventListener('click', () => {
            navbar.classList.toggle('hidden');
        });
    </script>
</body>
</html>
