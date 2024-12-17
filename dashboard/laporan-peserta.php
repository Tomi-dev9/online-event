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

// Query untuk mendapatkan nama acara
$sql_event = "SELECT event_name FROM events WHERE event_id = $event_id";
$result_event = $conn->query($sql_event);
$event_name = ($result_event && $result_event->num_rows > 0) ? $result_event->fetch_assoc()['event_name'] : "Acara Tidak Ditemukan";

// Query untuk mendapatkan daftar peserta
$sql_peserta = "SELECT absensi_id,user_id, waktu_absensi, nama FROM absensi WHERE event_id = $event_id";
$result_peserta = $conn->query($sql_peserta);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peserta - <?php echo htmlspecialchars($event_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Laporan Peserta</h1>
        <a href="./dashboard.php" class="text-white hover:text-gray-200 ">Kembali</a>
    </div>
</header>

<!-- Main Content -->
<main class="container mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
    <h2 class="text-2xl font-semibold mb-4">Daftar Peserta : <?php echo htmlspecialchars($event_name); ?></h2>

    <?php if ($result_peserta && $result_peserta->num_rows > 0): ?>
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">No</th>
                    <th class="border border-gray-300 px-4 py-2">Nama</th>
                    <th class="border border-gray-300 px-4 py-2">Email</th>
                    <th class="border border-gray-300 px-4 py-2">Waktu Absensi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php while ($row = $result_peserta->fetch_assoc()): ?>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center"><?php echo $no++; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['waktu_Absensi']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-600">Tidak ada peserta yang absensi untuk acara ini.</p>
    <?php endif; ?>
</main>

</body>
</html>

<?php
$conn->close();
?>
