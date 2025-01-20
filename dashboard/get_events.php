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


// Ambil data acara
$sql = "SELECT event_id, event_name FROM events";
$result = $conn->query($sql);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events); // Kirim data acara dalam format JSON
?>
