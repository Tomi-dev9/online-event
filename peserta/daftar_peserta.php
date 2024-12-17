<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

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
    $email = $_POST['email'] ?? '';

    if (!empty($nama) && !empty($email)) {
        $sql_daftar = "INSERT INTO peserta (event_id, nama, email, waktu_daftar) 
                       VALUES ($event_id, '$nama', '$email', NOW())";
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

<header class="bg-green-600 text-white p-4">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold">Pendaftaran Acara: <?php echo htmlspecialchars($event_name); ?></h1>
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
            <label for="email" class="block text-gray-700">Email:</label>
            <input type="email" name="email" id="email" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label for="username" class="block text-gray-700">Username:</label>
            <input type="text" name="username" id="username" class="w-full p-2 border rounded" required>
        </div>
        <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-500">
            Daftar Sekarang
        </button>
    </form>
</main>

</body>
</html>

<?php $conn->close(); ?>
