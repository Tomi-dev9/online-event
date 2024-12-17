<?php
session_start();

// Periksa apakah sesi email tersedia
if (!isset($_SESSION['email'])) {
    header("Location: /login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <!-- Tombol Kembali -->
        <a href="./dashboard.php" class="inline-block mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
            &larr; Kembali
        </a>

        <!-- Judul -->
        <h1 class="text-3xl font-bold mb-6 text-center">Data Peserta</h1>

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table id="pesertaTable" class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-blue-500 text-white">
                        <th class="py-2 px-4">No</th>
                        <th class="py-2 px-4">Nama</th>
                        <th class="py-2 px-4">Email</th>
                        <th class="py-2 px-4">Username</th>
                        <th class="py-2 px-4">Acara yang Diikuti</th>
                        <th class="py-2 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM peserta";
                    $result = $conn->query($sql);
                    $no = 1;

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='hover:bg-gray-100 border-b'>";
                            echo "<td class='py-2 px-4 text-center'>" . $no . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['events'] ?? 'Tidak ada acara') . "</td>";
                            echo "<td class='py-2 px-4 text-center'>
                                    <button onclick=\"buatQR('" . htmlspecialchars($row['username']) . "')\" 
                                        class='bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded'>
                                        Buat QR
                                    </button>
                                    <button onclick=\"kirimSertifikat('" . htmlspecialchars($row['email']) . "')\" 
                                        class='bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded'>
                                        Kirim Sertifikat
                                    </button>
                                  </td>";
                            echo "</tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='6' class='py-4 text-center'>Tidak ada data peserta.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#pesertaTable').DataTable();
        });

        function buatQR(username) {
            window.open('buat_qr.php?username=' + encodeURIComponent(username), '_blank');
        }

        function kirimSertifikat(email) {
            fetch('kirim_sertifikat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.text())
            .then(data => alert(data))
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
