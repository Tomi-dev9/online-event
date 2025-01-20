<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah sesi email tersedia
if (!isset($_SESSION['email'])) {
    // Redirect ke halaman login jika belum login
    header("Location: ../auth/login.php");
    exit();
}

// Ambil data dari database (contoh: event, peserta, kehadiran, dll)
include('../services/services.php'); // Pastikan Anda sudah membuat koneksi ke database

// Query untuk mengambil data event
$query_event = "SELECT * FROM acara WHERE admin_email = '" . $_SESSION['email'] . "'";
$result_event = mysqli_query($conn, $query_event);

// Query untuk mengambil data peserta
$query_peserta = "SELECT * FROM peserta WHERE event_id IN (SELECT event_id FROM acara WHERE admin_email = '" . $_SESSION['email'] . "')";
$result_peserta = mysqli_query($conn, $query_peserta);

// Query untuk mengambil laporan kehadiran
$query_kehadiran = "SELECT * FROM absensi WHERE event_id IN (SELECT event_id FROM acara WHERE admin_email = '" . $_SESSION['email'] . "')";
$result_kehadiran = mysqli_query($conn, $query_kehadiran);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hidden-content {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Layout -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white w-64 p-6 flex flex-col space-y-3">
            <div class="text-2xl font-bold">Dashboard</div>
            
            <!-- Navigasi Sidebar -->
            <div class="flex-1">
                <ul class="space-y-4">
                    <li>
                        <a href="#" class="flex items-center space-x-3 hover:bg-blue-700 p-2 rounded-lg transition" onclick="showContent('event')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3h8v4M6 7h12l2 12H4L6 7z"></path></svg>
                            <span>Event</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 hover:bg-blue-700 p-2 rounded-lg transition" onclick="showContent('peserta')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c1.656 0 3-1.343 3-3s-1.344-3-3-3-3 1.343-3 3 1.344 3 3 3zm0 0v4m0 0l-4 4m4-4l4 4"></path></svg>
                            <span>Data Peserta</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 hover:bg-blue-700 p-2 rounded-lg transition" onclick="showContent('laporan')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3v18h12V3H6zm0 0L3 6m3-3h12m-3 3l3-3"></path></svg>
                            <span>Laporan Kehadiran</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center space-x-3 hover:bg-blue-700 p-2 rounded-lg transition" onclick="showContent('sertifikat')">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12h-1V8a4 4 0 00-8 0v4H6a2 2 0 00-2 2v4a2 2 0 002 2h10a2 2 0 002-2v-4a2 2 0 00-2-2z"></path></svg>
                            <span>Buat Sertifikat</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="flex items-center space-x-3 hover:bg-red-700 p-2 rounded-lg transition bg-red-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m6 0H9m3 0V6m0 6v6"></path></svg>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
            <div id="content-event" class="content-tab hidden">

                <?php
                        // Tambah Event
                        if (isset($_POST['add_event'])) {
                            $event_name = $_POST['event_name'];
                            $event_date = $_POST['event_date'];
                            $start_time = $_POST['start_time'];
                            $description = $_POST['description'];
                            $end_time = $_POST['end_time'];
                            $image_name = null;

                            // Handle image upload if there is a file
                            if (!empty($_FILES['event_image']['name'])) {
                                $image_name = time() . '_' . $_FILES['event_image']['name'];
                                $target_dir = "img/";
                                if (!is_dir($target_dir)) {
                                    mkdir($target_dir, 0755, true);
                                }
                                move_uploaded_file($_FILES['event_image']['tmp_name'], $target_dir . $image_name);
                            }

                            // Prepare the SQL query to insert the event into the database (without status)
                            $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, description, start_time, end_time, image) VALUES (?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssssss", $event_name, $event_date, $description, $start_time, $end_time, $image_name);
                            

                            // Execute the query
                            if ($stmt->execute()) {
                                header("Location: dashboard.php");
                                exit();
                            } else {
                                echo "Error: " . $stmt->error;
                            }
                        }


                        // Handle Edit Event
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_event'])) {
                            $event_id = $_POST['event_id'];
                            $event_name = $_POST['event_name'];
                            $event_date = $_POST['event_date'];
                            $description = $_POST['description'];
                            $start_time = $_POST['start_time'];
                            $end_time = $_POST['end_time'];

                            if (!empty($_FILES['event_image']['name'])) {
                                $image_name = time() . '_' . $_FILES['event_image']['name'];
                                move_uploaded_file($_FILES['event_image']['tmp_name'], "img/" . $image_name);
                            }

                            $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ?, start_time = ?, end_time = ?, description = ?, image = ? WHERE event_id = ?");
                            $stmt->bind_param("ssssssi", $event_name, $event_date, $start_time, $end_time, $description, $image_name, $event_id);
                            $stmt->execute();
                            header("Location: dashboard.php");
                            exit();
                        }

                        // Hapus Event
                        if (isset($_GET['delete_event'])) {
                            $event_id = $_GET['delete_event'];
                            $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
                            $stmt->bind_param("i", $event_id);
                            $stmt->execute();
                            header("Location: dashboard.php");
                            exit();
                        }

                        // Ambil data event
                        $result = $conn->query("SELECT * FROM events");
                        $events = $result->fetch_all(MYSQLI_ASSOC);
                        ?>
                        <!DOCTYPE html>
                        <html lang="id">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <script src="https://cdn.tailwindcss.com"></script>
                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        </head>
                        <body class="bg-gray-100 font-poppins">
                            <div class="">
                                <div class="">
                                    <div class="flex-row justify-between items-center mb-6">
                                        <h1 class="text-2xl font-semibold text-center">Daftar Event</h1>
                                        <button onclick="openModal('addModal')" class="inset-0 bg-blue-500 text-white px-3 py-2 rounded-lg">Tambah Event</button>
                                    </div>

                                    <!-- Tabel Event -->
                                    <table class="table-fixed bg-white border ">
                                        <thead>
                                            <tr>
                                                <th class="py-2 px-4 border">No</th>
                                                <th class="py-2 px-4 border">Nama Event</th>
                                                <th class="py-2 px-4 border">Gambar</th>
                                                <th class="py-2 px-4 border">Tanggal</th>
                                                <th class="py-2 px-4 border">Waktu</th>
                                                <th class="py-2 px-4 border">Deskripsi</th>
                                                <th class="py-2 px-4 border">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($events as $index => $event): ?>
                                            <tr>
                                                <td class="py-2 px-4 border text-center"><?php echo $index + 1; ?></td>
                                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($event['event_name']); ?></td>
                                                <td class="py-2 px-4 border text-center">
                                                    <?php if ($event['image']): ?>
                                                        <img src="img/<?php echo $event['image']; ?>" alt="Gambar Event" class="w-16 h-16 object-cover mx-auto">
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($event['event_date']); ?></td>
                                                <td class="py-2 px-4 border"><?php echo htmlspecialchars($event['start_time'] . ' - ' . $event['end_time']); ?></td>
                                                <td class="py-2 px-4 border text-center"><?php echo htmlspecialchars($event['description']); ?></td>
                                                <td class="py-2 px-4 border text-center">
                                                    <button onclick="openQRModal(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Buat QR</button>
                                                    <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                                                    <button onclick="hapusEvent(<?php echo $event['event_id']; ?>)" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Modal Tambah Event -->
                            <div id="addModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                                <div class="bg-white p-6 rounded-lg w-96">
                                    <h2 class="text-lg font-semibold mb-4">Tambah Event</h2>
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="add_event" value="1">
                                        <div class="mb-4">
                                            <label for="event_name">Nama Event</label>
                                            <input type="text" name="event_name" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_image">Gambar</label>
                                            <input type="file" name="event_image" class="w-full p-2 border rounded" accept="image/*">
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_date">Tanggal</label>
                                            <input type="date" name="event_date" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="start_time">Waktu Mulai</label>
                                            <input type="time" name="start_time" class="w-full p-2 border rounded" required>
                                        </div>

                                        <div class="mb-4">
                                            <label for="end_time">Waktu Selesai</label>
                                            <input type="time" name="end_time" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="description">Deskripsi :</label>
                                            <input type="text" name="description" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" onclick="closeModal('addModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal QR Code -->
                            <div id="qrModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                                <div class="bg-white p-6 rounded-lg w-96">
                                    <h2 class="text-lg font-semibold mb-4">QR Code</h2>
                                    <div id="qrCodeContainer" class="mb-4"></div>
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="closeModal('qrModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Tutup</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Edit Event -->
                            <div id="EditModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                                <div class="bg-white p-6 rounded-lg w-96">
                                    <h2 class="text-lg font-semibold mb-4">Edit Event</h2>
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="edit_event" value="1">
                                        <input type="hidden" name="event_id" id="event_id">
                                        <div class="mb-4">
                                            <label for="event_name">Nama Event</label>
                                            <input type="text" name="event_name" id="event_name" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_image">Gambar</label>
                                            <input type="file" name="event_image" class="w-full p-2 border rounded" accept="image/*">
                                        </div>
                                        <div class="mb-4">
                                            <label for="event_date">Tanggal</label>
                                            <input type="date" name="event_date" id="event_date" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="start_time">Waktu Mulai</label>
                                            <input type="time" name="start_time" id="start_time" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="end_time">Waktu Selesai</label>
                                            <input type="time" name="end_time" id="end_time" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="description">Deskripsi :</label>
                                            <input type="text" name="description" id="description" class="w-full p-2 border rounded" required>
                                        </div>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" onclick="closeModal('EditModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <script>
                                function openModal(modalId) {
                                    document.getElementById(modalId).classList.remove('hidden');
                                }

                                function closeModal(modalId) {
                                    document.getElementById(modalId).classList.add('hidden');
                                }

                                function openEditModal(event) {
                            document.getElementById('event_id').value = event.event_id;
                            document.getElementById('event_name').value = event.event_name;
                            document.getElementById('event_date').value = event.event_date;
                            document.getElementById('start_time').value = event.start_time;
                            document.getElementById('end_time').value = event.end_time;
                            document.getElementById('description').value = event.description;
                            openModal('EditModal');
                        }
                                function openQRModal(event) {
                                    var qrCodeContainer = document.getElementById('qrCodeContainer');
                                    qrCodeContainer.innerHTML = '<img src="https://api.qrserver.com/v1/create-qr-code/?data=' + encodeURIComponent('https://your-site.com/attend/' + event.event_id) + '&size=150x150" alt="QR Code">';
                                    openModal('qrModal');
                                }

                                function hapusEvent(eventId) {
                            Swal.fire({
                                title: 'Apakah Anda yakin?',
                                text: "Event ini akan dihapus secara permanen!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ya, hapus!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = `dashboard.php?delete_event=${eventId}`;
                                }
                            });
                        }

                            </script>
                        </body>
                        </html>
            </div>
            <div id="content-peserta" class="content-tab hidden">
                <?php

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
                window.location.href = 'dashboard.php'; // Ganti dengan halaman yang sesuai
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 min-h-screen size-full items-center">

    <!-- Tombol Tambah Peserta -->
    <div class="justify-end w-full px-4 py-2 mt-4">
        <a href="tambah_peserta.php" class="px-4 py-2 bg-green-500 text-white font-medium rounded hover:bg-green-600 transition">
            Tambah Peserta
        </a>
    </div>

    <!-- Tabel Peserta -->
    <div class="size-full w-full max-w-screen-lg p-4 mt-4">
    <table id="pesertaTable" class="md:table-fixed w-full">
            <thead>
                <tr>
                    <th class="py-1 px-2 border bg-blue-500 text-white">No</th>
                    <th class="py-1 px-2 border bg-blue-500 text-white">Nama</th>
                    <th class="py-1 px-2 border bg-blue-500 text-white">Email</th>
                    <th class="py-1 px-2 border bg-blue-500 text-white">No WhatsApp</th>
                    <th class="py-1 px-2 border bg-blue-500 text-white">Acara yang Diikuti</th>
                    <th class="py-1 px-2 border bg-blue-500 text-white">Aksi</th>
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
                        echo "<tr class='hover:bg-gray-50 border-b'>";
                        echo "<td class='py-2 px-4 text-center'>" . $no . "</td>";
                        echo "<td class='py-2 px-4'>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td class='py-2 px-4'>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td class='py-2 px-4'>" . htmlspecialchars($row['phone_number']) . "</td>";
                        echo "<td class='py-2 px-4'>" . htmlspecialchars($row['event_name']) . "</td>"; 
                        echo "<td class='py-2 px-4 text-center'>
                                <button onclick=\"confirmDelete(" . $row['user_id'] . ")\" class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded'>
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#pesertaTable').DataTable({
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "paginate": {
                        "previous": "Sebelumnya",
                        "next": "Berikutnya"
                    }
                }
            });
        });

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



            </div>
            <div id="content-laporan" class="content-tab hidden">
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

<!-- Main Content -->
<main class="p-4">
    <div class=" justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Laporan Peserta</h2>
    </div>

    <?php if ($result_peserta && $result_peserta->num_rows > 0): ?>
        <div class="overflow-x-auto">
            <table class="">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">No</th>
                        <th class="border border-gray-300 px-4 py-2">Nama</th>
                        <th class="border border-gray-300 px-4 py-2">Email</th>
                        <th class="border border-gray-300 px-4 py-2">Acara</th>
                        <th class="border border-gray-300 px-4 py-2">Waktu Absensi</th>
                        <th class="border border-gray-300 px-4 py-2">Nomor Telepon</th>
                        <th class="border border-gray-300 px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $result_peserta->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2 text-center"><?php echo $no++; ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['event_name']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['waktu_absensi']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <button onclick="hapusAbsensi(<?php echo htmlspecialchars($row['absensi_id']); ?>)" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-600">Tidak ada peserta yang absensi untuk acara ini.</p>
    <?php endif; ?>
</main>

</body>
<script>
  function hapusAbsensi(absensiId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
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
                    Swal.fire(
                        'Dihapus!',
                        'Data absensi berhasil dihapus.',
                        'success'
                    ).then(() => {
                        location.reload(); // Reload halaman setelah penghapusan
                    });
                } else {
                    Swal.fire(
                        'Gagal!',
                        'Gagal menghapus data absensi: ' + data.message,
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Kesalahan!',
                    'Terjadi kesalahan saat menghapus data.',
                    'error'
                );
            });
        }
    });
}
</script>

</html>

<?php
$conn->close();
?>
            </div>
<!-- Form untuk memilih acara -->
<div class="">
    <div id="content-sertifikat" class="content-tab hidden">
        <!-- Form untuk memilih acara -->
        <div class="mt-8 flex-wrap">
            <label for="eventSelect" class="block text-lg font-semibold text-gray-700">Pilih Acara</label>
            <select id="eventSelect" class="w-full mt-3 px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="" disabled selected>Pilih acara dari daftar</option>
                <!-- Opsi acara akan diisi secara dinamis -->
            </select>
        </div>
        
        <!-- Tombol untuk menghasilkan sertifikat -->
        <div class="mt-6 text-center">
            <button id="generateCertificatesBtn" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Generate Sertifikat
            </button>
        </div>

        <!-- Tombol untuk melihat sertifikat (disembunyikan awalnya) -->
        <div id="viewCertificateBtnContainer" class="mt-6 text-center hidden">
            <button id="viewCertificateBtn" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Lihat Sertifikat
            </button>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('generateCertificatesBtn').addEventListener('click', function() {
    const event_id = document.getElementById('eventSelect').value;

    if (!event_id) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Pilih acara terlebih dahulu!',
        });
        return;
    }

    // Kirim data ke server atau lakukan proses generate sertifikat
    // Simulasi proses berhasil
    setTimeout(function() {
        // Notifikasi berhasil
        Swal.fire({
            icon: 'success',
            title: 'Sertifikat Berhasil Dibuat!',
            text: 'Klik tombol di bawah untuk melihat sertifikat.',
            showConfirmButton: false,
            timer: 2000,
        }).then(() => {
            // Tampilkan tombol lihat sertifikat
            document.getElementById('viewCertificateBtnContainer').classList.remove('hidden');
        });
    }, 1000); // Simulasi proses berhasil dalam 1 detik

    // Anda bisa mengganti bagian ini dengan pengiriman data ke server atau proses lainnya
});

document.getElementById('viewCertificateBtn').addEventListener('click', function() {
    const event_id = document.getElementById('eventSelect').value;
    if (event_id) {
        window.location.href = 'preview.sertifikat.php?event_id=' + event_id;
    }
});
</script>


<script>
    // Fungsi untuk menampilkan konten berdasarkan tab yang dipilih
    function showContent(tabName) {
        const tabs = document.querySelectorAll('.content-tab');
        tabs.forEach(tab => tab.classList.add('hidden'));

        const activeTab = document.getElementById('content-' + tabName);
        if (activeTab) {
            activeTab.classList.remove('hidden');
        }

        localStorage.setItem('lastVisitedTab', tabName);
    }

    // Set default tab saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        const lastTab = localStorage.getItem('lastVisitedTab') || 'event';
        showContent(lastTab);
        fetchEvents();
    });

    // Fungsi untuk mengambil data acara dari database
    function fetchEvents() {
        fetch('get_events.php')
            .then(response => response.json())
            .then(data => {
                const eventSelect = document.getElementById('eventSelect');
                data.forEach(event => {
                    const option = document.createElement('option');
                    option.value = event.event_id;
                    option.textContent = event.event_name;
                    eventSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching events:', error));
    }

// Fungsi untuk mengambil data peserta dari database absensi
function fetchParticipants(event_id) {
    fetch(`get_participants.php?event_id=${event_id}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);  // Log untuk memeriksa data yang diterima
            const participantSelect = document.getElementById('participantSelect');
            // Menghapus opsi yang ada sebelumnya
            participantSelect.innerHTML = '<option value="" disabled selected>Pilih peserta dari daftar</option>';

            // Memasukkan peserta ke dalam dropdown
            if (data.length > 0) {
                data.forEach(participant => {
                    const option = document.createElement('option');
                    option.value = participant.participant_id; // ID peserta
                    option.textContent = participant.nama; // Nama peserta
                    participantSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Tidak ada peserta untuk event ini';
                participantSelect.appendChild(option);
            }
        })
        .catch(error => console.error('Error fetching participants:', error));
}



    // Fungsi untuk generate sertifikat
    function generateCertificates(eventId) {
        const signatureInput = document.getElementById('signatureUpload');
        const signatureFile = signatureInput.files[0];

        if (!signatureFile) {
            alert('Unggah tanda tangan terlebih dahulu.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            const signatureUrl = event.target.result;

            fetch(`get_participants.php?event_id=${eventId}`)
                .then(response => response.json())
                .then(participants => {
                    if (participants.length > 0) {
                        const certificateContent = document.getElementById('certificateContent');
                        certificateContent.innerHTML = '';

                        participants.forEach(participant => {
                            const certificate = document.createElement('div');
                            certificate.classList.add('certificate', 'mb-4', 'p-4', 'border', 'border-gray-300', 'rounded');
                            certificate.innerHTML = `
                                <h3 class="text-xl font-bold">Sertifikat Partisipasi</h3>
                                <p>Nama: ${participant.nama}</p>
                                <p>Acara: ${participant.event_name}</p>
                                <p>Tanggal: ${new Date().toLocaleDateString()}</p>
                                <img src="${signatureUrl}" alt="Tanda Tangan" class="mt-4 w-32 mx-auto">
                            `;
                            certificateContent.appendChild(certificate);
                        });

                        saveCertificates(eventId, participants);
                        document.getElementById('certificatePreview').classList.remove('hidden');
                    } else {
                        alert('Tidak ada peserta untuk acara ini.');
                    }
                })
                .catch(error => console.error('Error generating certificates:', error));
        };

        reader.readAsDataURL(signatureFile);
    }

    // Fungsi untuk menyimpan sertifikat ke database
    function saveCertificates(eventId, participants) {
        participants.forEach(participant => {
            fetch('save_certificate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    event_id: eventId,
                    nama: participant.nama,
                    tanggal_terbit: new Date().toISOString().split('T')[0]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Sertifikat berhasil disimpan');
                } else {
                    console.log('Gagal menyimpan sertifikat');
                }
            })
            .catch(error => console.error('Error saving certificate:', error));
        });
    }

    // Fungsi untuk mengirim sertifikat ke WhatsApp
    document.getElementById('sendToWhatsAppBtn').addEventListener('click', function () {
        const phoneNumber = "1234567890"; // Ganti dengan nomor telepon peserta dari database
        const message = encodeURIComponent("Berikut adalah sertifikat Anda!");
        const url = `https://wa.me/${phoneNumber}?text=${message}`;
        window.open(url, "_blank");
    });

    // Event listener untuk tombol "Generate Sertifikat"
    document.getElementById('generateCertificatesBtn').addEventListener('click', function () {
        const eventId = document.getElementById('eventSelect').value;

        if (eventId) {
            generateCertificates(eventId);
        } else {
            alert('Pilih acara terlebih dahulu.');
        }
    });
</script>

</body>
</html>