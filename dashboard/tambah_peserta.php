<?php
session_start();

// Periksa apakah sesi email tersedia
if (!isset($_SESSION['email'])) {
    header("Location: /login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses tambah peserta
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $event_id = $_POST['event_id'];

    $sql = "INSERT INTO peserta (nama, email, phone_number, event_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama, $email, $phone_number, $event_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Peserta berhasil ditambahkan'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menambahkan peserta');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peserta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <!-- Tombol Kembali -->
        <a href="data-peserta.php" class="inline-block mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
            &larr; Kembali
        </a>

        <!-- Judul -->
        <h1 class="text-3xl font-bold mb-6 text-center">Tambah Peserta</h1>

        <!-- Form Tambah Peserta -->
        <form action="" method="post" class="space-y-4">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">No. WhatsApp</label>
                <input type="text" id="phone_number" name="phone_number" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
            </div>
            <div>
                <label for="event_id" class="block text-sm font-medium text-gray-700">Acara</label>
                <select id="event_id" name="event_id" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                    <?php
                    // Menampilkan daftar acara
                    $result = $conn->query("SELECT * FROM events");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['event_id'] . "'>" . $row['event_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">Tambah Peserta</button>
        </form>
    </div>
</body>
</html>
