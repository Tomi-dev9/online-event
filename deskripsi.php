<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil event_id dari URL
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Query untuk mendapatkan detail acara
$sql = "SELECT * FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data ditemukan
if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
} else {
    echo "<p class='text-center text-red-500'>Acara tidak ditemukan.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deskripsi Acara</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10 px-4">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($event['event_name']); ?></h1>
            <img class="w-full h-64 object-cover rounded-md mb-4" src="<?php echo !empty($event['image']) && file_exists("./dashboard/img/" . $event['image']) 
                ? "./dashboard/img/" . htmlspecialchars($event['image']) 
                : 'default.jpg'; ?>" 
                alt="Gambar <?php echo htmlspecialchars($event['event_name']); ?>">
            <p class="text-gray-700 mb-2"><strong>Tanggal:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
            <p class="text-gray-700 mb-2"><strong>Waktu:</strong> <?php echo htmlspecialchars($event['start_time']) . " - " . htmlspecialchars($event['end_time']); ?></p>
            <p class="text-gray-700 mb-4"><strong>Deskripsi:</strong> <?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            <a href="index.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Kembali ke Daftar Event</a>
        </div>
    </div>
</body>
</html>

<?php
// Tutup koneksi
$conn->close();
?>
