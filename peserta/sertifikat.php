<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil event_id dari URL
if (isset($_GET['event_id']) && is_numeric($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']); // Pastikan hanya angka
} else {
    die("Event ID tidak valid.");
}

// Query untuk mengambil data sertifikat
$query = "SELECT * FROM sertifikat WHERE event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $eventId);
$stmt->execute();
$result = $stmt->get_result();

// Tampilkan tautan unduh sertifikat
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $filePath = './sertifikat/' . $row['event_id'] . '_' . $row['nama'] . '.pdf';
        if (file_exists($filePath)) {
            echo '<a href="' . $filePath . '" 
                     class="mt-2 inline-block bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-500">
                     Unduh Sertifikat
                  </a>';
        } else {
            echo '<p class="text-red-500">Sertifikat tidak ditemukan untuk ' . htmlspecialchars($row['nama']) . '.</p>';
        }
    }
} else {
    echo '<p class="text-red-500">Tidak ada sertifikat yang tersedia untuk acara ini.</p>';
}

// Tutup koneksi
$conn->close();
?>
