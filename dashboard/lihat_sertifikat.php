<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "absensi_online");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data berdasarkan ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "
        SELECT 
            absensi.nama AS peserta_nama,
            events.event_name,
            absensi.waktu_absensi,
            absensi.file_path
        FROM 
            absensi
        LEFT JOIN 
            events ON absensi.event_id = events.event_id
        WHERE 
            absensi.absensi_id = $id
    ";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo "<h1>Sertifikat</h1>";
        echo "<p>Nama: " . $data['peserta_nama'] . "</p>";
        echo "<p>Acara: " . $data['event_name'] . "</p>";
        echo "<p>Tanggal: " . $data['waktu_absensi'] . "</p>";
        echo "<img src='" . $data['file_path'] . "' alt='Sertifikat' style='width:100%;max-width:600px;'>";
    } else {
        echo "Sertifikat tidak ditemukan.";
    }
} else {
    echo "ID tidak valid.";
}
?>
