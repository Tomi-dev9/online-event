<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "absensi_online");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil file berdasarkan ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT file_path FROM absensi WHERE absensi_id = $id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $file_path = $data['file_path'];

        if (file_exists($file_path)) {
            header("Content-Disposition: attachment; filename=" . basename($file_path));
            header("Content-Type: application/octet-stream");
            readfile($file_path);
            exit;
        } else {
            echo "File tidak ditemukan.";
        }
    } else {
        echo "Sertifikat tidak ditemukan.";
    }
} else {
    echo "ID tidak valid.";
}
?>
