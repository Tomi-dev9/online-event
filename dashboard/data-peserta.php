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

// Aksi hapus peserta
if (isset($_GET['hapus'])) {
    $id_peserta = $_GET['hapus']; // Ambil id peserta dari parameter URL
    $id_peserta = (int) $id_peserta; // Sanitasi input

    // Cek apakah ada absensi yang terkait dengan peserta
    $sql_check = "SELECT * FROM absensi WHERE user_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_peserta);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    // Jika ada absensi yang terkait, hapus terlebih dahulu
    if ($result_check->num_rows > 0) {
        $sql_delete_absensi = "DELETE FROM absensi WHERE user_id = ?";
        $stmt_delete_absensi = $conn->prepare($sql_delete_absensi);
        $stmt_delete_absensi->bind_param("i", $id_peserta);
        $stmt_delete_absensi->execute();
        $stmt_delete_absensi->close();
    }

    // Hapus peserta
    $sql = "DELETE FROM peserta WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_peserta);
    if ($stmt->execute()) {
        // Pengalihan setelah sukses
        echo "<script>
                window.location.href = 'data-peserta.php'; // Ganti dengan halaman yang sesuai
              </script>";
    } else {
        echo "<script>
              </script>";
    }
    $stmt->close();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-6">
        <!-- Tombol Kembali -->
        <a href="./dashboard.php" class="inline-block mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
            &larr; Kembali
        </a>

        <!-- Tombol Tambah Peserta -->
        <a href="tambah_peserta.php" class="inline-block mb-4 ml-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
            Tambah Peserta
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
                        <th class="py-2 px-4">No WhatsApp</th>
                        <th class="py-2 px-4">Acara yang Diikuti</th>
                        <th class="py-2 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "
                    SELECT peserta.*, events.event_name 
                    FROM peserta
                    INNER JOIN events ON peserta.event_id = events.event_id
                    ";                                  
                    $result = $conn->query($sql);
                    $no = 1;

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='hover:bg-gray-100 border-b'>";
                            echo "<td class='py-2 px-4 text-center'>" . $no . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['phone_number']) . "</td>";
                            echo "<td class='py-2 px-4'>" . htmlspecialchars($row['event_name']) . "</td>"; // Nama acara dari tabel events
                            echo "<td class='py-2 px-4 text-center'>
                                    <button onclick=\"confirmDelete(" . $row['user_id'] . ")\" class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded ml-2'>
                                        Hapus
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
    </script>
    <script>
    function confirmDelete(userId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data peserta akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?hapus=' + userId;
            }
        });
    }
    </script>

</body>
</html>
