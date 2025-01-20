<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}

if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    error_log("Received event_id: " . $event_id);  // Log untuk memeriksa event_id

    if ($event_id > 0) {
        $sql = "SELECT absensi.user_id, absensi.nama, events.event_name 
                FROM absensi 
                JOIN events ON absensi.event_id = events.event_id 
                WHERE absensi.event_id = ?";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('i', $event_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $participants = [];
            while ($row = $result->fetch_assoc()) {
                $participants[] = [
                    'participant_id' => $row['user_id'], // Menambahkan ID peserta
                    'nama' => $row['nama'],
                    'event_name' => $row['event_name']
                ];
            }

            if (!empty($participants)) {
                header('Content-Type: application/json');
                echo json_encode($participants, JSON_PRETTY_PRINT);
            } else {
                echo json_encode(["message" => "Tidak ada peserta untuk event ini."]);
            }

            $stmt->close();
        } else {
            echo json_encode(["error" => "Gagal menyiapkan statement SQL: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "event_id tidak valid."]);
    }
} else {
    echo json_encode(["error" => "event_id tidak ditemukan atau kosong."]);
}

$conn->close();
?>
