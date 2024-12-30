<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mendapatkan daftar peserta dari semua acara
$sql_peserta = "
SELECT 
    absensi.absensi_id, 
    absensi.event_id, 
    absensi.user_id, 
    absensi.waktu_absensi, 
    absensi.jumlah_kehadiran, 
    absensi.nama, 
    peserta.email, 
    peserta.phone_number, 
    events.event_name
FROM 
    absensi
LEFT JOIN 
    peserta ON absensi.user_id = peserta.user_id
LEFT JOIN 
    events ON absensi.event_id = events.event_id";

    


$result_peserta = $conn->query($sql_peserta);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peserta</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Laporan Peserta</h1>
        <a href="./dashboard.php" class="text-white hover:text-gray-200">Kembali</a>
    </div>
</header>

<!-- Main Content -->
<main class="container mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
    <h2 class="text-2xl font-semibold mb-4">Daftar Peserta</h2>

    <?php if ($result_peserta && $result_peserta->num_rows > 0): ?>
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">No</th>
                    <th class="border border-gray-300 px-4 py-2">Nama</th>
                    <th class="border border-gray-300 px-4 py-2">Email</th>
                    <th class="border border-gray-300 px-4 py-2">Acara</th>
                    <th class="border border-gray-300 px-4 py-2">Waktu Absensi</th>
                    <th class="border border-gray-300 px-4 py-2">Nomor Telepon</th>
                    <th class="border border-gray-300 px-4 py-2">Jumlah Kehadiran</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php while ($row = $result_peserta->fetch_assoc()): ?>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center"><?php echo $no++; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['waktu_absensi']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['jumlah_kehadiran']); ?></td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
    <!-- Tombol Kirim Sertifikat -->
    <a href="kirim_sertifikat.php?absensi_id=<?php echo htmlspecialchars($row['absensi_id']); ?>" 
       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Kirim Sertifikat</a>
    <!-- Tombol Hapus -->
    <button 
        onclick="hapusAbsensi(<?php echo htmlspecialchars($row['absensi_id']); ?>)" 
        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">
        Hapus
    </button>
</td>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-600">Tidak ada peserta yang absensi untuk acara ini.</p>
    <?php endif; ?>
</main>

</body>
<script>
    function hapusAbsensi(absensiId) {
        if (confirm('Apakah Anda yakin ingin menghapus data absensi ini?')) {
            fetch('hapus_absensi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ absensi_id: absensiId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data absensi berhasil dihapus.');
                    // Reload halaman atau hapus baris dari tabel
                    location.reload();
                } else {
                    alert('Gagal menghapus data absensi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus data.');
            });
        }
    }
</script>

</html>

<?php
$conn->close();
?>
