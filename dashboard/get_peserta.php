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

// Ambil event_id dari request POST
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Query untuk mengambil peserta berdasarkan event_id
    $sql_peserta = "SELECT absensi.user_id, absensi.nama 
                    FROM absensi
                    LEFT JOIN peserta ON absensi.user_id = peserta.user_id
                    WHERE absensi.event_id = ?";
    
    $stmt_peserta = $conn->prepare($sql_peserta);
    $stmt_peserta->bind_param('i', $event_id);
    $stmt_peserta->execute();
    $result_peserta = $stmt_peserta->get_result();

    if ($result_peserta->num_rows > 0) {
        echo '<option value="">Pilih Peserta</option>';
        while ($row = $result_peserta->fetch_assoc()) {
            echo '<option value="' . $row['user_id'] . '">' . htmlspecialchars($row['nama']) . '</option>';
        }
    } else {
        echo '<option value="">Tidak ada peserta untuk event ini</option>';
    }
}
?>
