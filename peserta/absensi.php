<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

session_start();

// Ambil event_id dari URL
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Cek apakah event_id valid
$sql_event = "SELECT event_name FROM events WHERE event_id = $event_id";
$result_event = $conn->query($sql_event);

if ($result_event && $result_event->num_rows > 0) {
    $event_name = $result_event->fetch_assoc()['event_name'];
} else {
    die("Acara tidak ditemukan.");
}

// Proses Absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (!empty($email)) {
        // Cek apakah email terdaftar di acara ini
        $sql_check = "SELECT user_id, nama FROM peserta WHERE event_id = $event_id AND email = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_check = $stmt->get_result();

        if ($result_check && $result_check->num_rows > 0) {
            $peserta = $result_check->fetch_assoc();
            $user_id = $peserta['user_id'];
            $nama = $peserta['nama'];

            // Cek apakah peserta sudah absen sebelumnya
            $sql_check_absen = "SELECT * FROM absensi WHERE event_id = $event_id AND user_id = ?";
            $stmt_absen = $conn->prepare($sql_check_absen);
            $stmt_absen->bind_param("i", $user_id);
            $stmt_absen->execute();
            $result_absen = $stmt_absen->get_result();

            if ($result_absen && $result_absen->num_rows > 0) {
                $error_message = "Anda sudah melakukan absensi sebelumnya.";
            } else {
                // Catat absensi
                $waktu_absensi = date('Y-m-d H:i:s');
                $jumlah_kehadiran = 1;

                $sql_absen = "INSERT INTO absensi (event_id, user_id, waktu_absensi, jumlah_kehadiran, nama) 
                              VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_absen);
                $stmt_insert->bind_param("iisis", $event_id, $user_id, $waktu_absensi, $jumlah_kehadiran, $nama);

                if ($stmt_insert->execute()) {
                    $success_message = "Absensi berhasil dicatat untuk $nama!";
                } else {
                    $error_message = "Error saat mencatat absensi: " . $conn->error;
                }
            }
        } else {
            $error_message = "Email tidak terdaftar di acara ini. Silakan daftar terlebih dahulu.";
        }
    } else {
        $error_message = "Harap isi email Anda.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Acara</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex items-center justify-between">
        <!-- Tombol Kembali -->
        <button onclick="history.back()" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-200">
            Kembali
        </button>
        <!-- Judul Acara -->
        <h1 class="text-2xl font-bold flex-1 text-center">Absensi Acara: <?php echo htmlspecialchars($event_name); ?></h1>
    </div>
</header>


<main class="container mx-auto p-6 bg-white mt-6 rounded-lg shadow-md">
    <?php if (isset($success_message)): ?>
        <p class="text-green-600 mb-4"><?php echo $success_message; ?></p>
    <?php elseif (isset($error_message)): ?>
        <p class="text-red-600 mb-4"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Masukkan Email Anda untuk Absensi:</label>
            <input type="email" name="email" id="email" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <input type="submit" value="Absen Sekarang" class="w-full p-2 bg-blue-600 text-white rounded hover:bg-blue-500 cursor-pointer">
        </div>
    </form>
</main>

</body>
</html>

<?php $conn->close(); ?>
