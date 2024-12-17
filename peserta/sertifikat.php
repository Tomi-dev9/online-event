<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);

   
    $stmt = $conn->prepare("SELECT link_sertifikat FROM peserta WHERE nama = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $linkSertifikat = $row['link_sertifikat'];
    } else {
        $error = "Sertifikat tidak ditemukan untuk nama: " . htmlspecialchars($name);
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unduh Sertifikat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
</head>
<body class="bg-gray-100 font-poppins">
    <!-- Container -->
    <div class="container mx-auto mt-10 max-w-lg bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold text-center mb-4">Terima Kasih Sudah Mengikuti Acara Kami</h1>
        <p class="text-gray-600 text-center mb-6">Untuk sertifikat Anda, silakan isi email yang terdaftar di bawah ini:</p>
        
        <!-- Form -->
        <form method="POST" class="space-y-4">
            <input 
                type="email" 
                name="email" 
                placeholder="Masukkan email Anda" 
                required 
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
            >
            <button 
                type="submit" 
                name="submit" 
                class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600"
            >
                Unduh Sertifikat
            </button>
            <a 
    href="../index.php" 
    class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold text-center block hover:bg-blue-600"
>
    Kembali
</a>

        </form>

        <!-- PHP Message Handling -->
        <div class="mt-6">
            <?php
            if (!empty($error)) {
                echo '<p class="text-red-500 text-center font-semibold">' . htmlspecialchars($error) . '</p>';
            }

            if (!empty($linkSertifikat)) {
                echo '<p class="text-green-500 text-center font-semibold">Unduh sertifikat Anda di sini: <a href="' . htmlspecialchars($linkSertifikat) . '" target="_blank" class="text-blue-500 hover:underline">[Link Sertifikat]</a></p>';
            }
            ?>
        </div>
    </div>
</body>
</html>

