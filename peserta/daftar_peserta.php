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

// Pastikan email ada di session
if (!isset($_SESSION['email'])) {
    die("Anda harus login terlebih dahulu.");
}

$email = $_SESSION['email'];  // Ambil email dari session

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

// Proses Pendaftaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    
    if (!empty($nama) && !empty($phone_number)) {
        // Menggunakan email yang sudah ada di session
        $sql_daftar = "INSERT INTO peserta (event_id, nama, email, phone_number, waktu_daftar) 
                       VALUES ($event_id, '$nama', '$email', '$phone_number', NOW())";
        if ($conn->query($sql_daftar) === TRUE) {
            $success_message = "Pendaftaran berhasil!";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    } else {
        $error_message = "Harap isi semua field.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Peserta</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold">Pendaftaran Acara: <?php echo htmlspecialchars($event_name); ?></h1>
        <!-- Tombol Kembali -->
        <a href="./daftar.php" class="inline-block mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
            &larr; Kembali
        </a>
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
            <label for="nama" class="block text-gray-700">Nama:</label>
            <input type="text" name="nama" id="nama" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label for="phone_number" class="block text-gray-700">Nomor Whatsapp :</label>
            <input type="number" name="phone_number" id="phone_number" class="w-full p-2 border rounded" required>
        </div>
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-green-500">
            Daftar Sekarang
        </button>
    </form>
</main>

</body>
</html>

<?php $conn->close(); ?>
