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


// Periksa apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data JSON dari body request
    $input = json_decode(file_get_contents('php://input'), true);
    $absensi_id = $input['absensi_id'] ?? null;

    if ($absensi_id) {
        // Query untuk menghapus data absensi
        $sql = "DELETE FROM absensi WHERE absensi_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $absensi_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID absensi tidak valid.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
}

$conn->close();
?>
