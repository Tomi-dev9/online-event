<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tambah Event
if (isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
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
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, start_time, end_time, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $event_name, $event_date, $start_time, $end_time, $image_name);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: event.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}


// Edit Event
if (isset($_POST['edit_event'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $image_name = $_POST['current_image'];

    if (!empty($_FILES['event_image']['name'])) {
        $image_name = time() . '_' . $_FILES['event_image']['name'];
        move_uploaded_file($_FILES['event_image']['tmp_name'], "img/" . $image_name);
    }

    $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ?, start_time = ?, end_time = ?, image = ? WHERE event_id = ?");
    $stmt->bind_param("sssssi", $event_name, $event_date, $start_time, $end_time, $image_name, $event_id);
    $stmt->execute();
    header("Location: event.php");
    exit();
}

// Hapus Event
if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];
    $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    header("Location: event.php");
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
    <title>Kelola Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-poppins">
    <!-- Navbar -->
    <nav class="bg-blue-500 p-4 flex justify-between items-center">
        <a href="./dashboard.php" class="text-white font-semibold text-lg">Kembali</a>  
    </nav>

    <!-- Container -->
    <div class="container mx-auto mt-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Daftar Event</h1>
                <button onclick="openModal('addModal')" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Tambah Event</button>
            </div>

            <!-- Tabel Event -->
            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border">No</th>
                        <th class="py-2 px-4 border">Nama Event</th>
                        <th class="py-2 px-4 border">Gambar</th>
                        <th class="py-2 px-4 border">Tanggal</th>
                        <th class="py-2 px-4 border">Waktu</th>
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
                        <td class="py-2 px-4 border text-center">
                        <button onclick="openQRModal(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Buat QR</button>
                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                            <a href="?delete_event=<?php echo $event['event_id']; ?>" onclick="return confirm('Yakin ingin menghapus event ini?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
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
                    <label for="end_time">Waktu Selesai :</label>
                    <input type="time" name="end_time" class="w-full p-2 border rounded" required>
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
                    <h2 class="text-lg font-semibold mb-4">QR Code Event</h2>
                    <div id="qrCodeContainer" class="text-center mb-4">
                        <!-- QR code akan muncul di sini -->
                    </div>
                    <button type="button" onclick="closeModal('qrModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Tutup</button>
                </div>
     <!-- Modal Edit Event -->
     <div id="editModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-lg font-semibold mb-4">Edit Event</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_event" value="1">
                <input type="hidden" id="edit_event_id" name="event_id">
                <input type="hidden" id="current_image" name="current_image">
                <div class="mb-4">
                    <label for="edit_event_name" class="block text-sm font-medium text-gray-700">Nama Event</label>
                    <input type="text" id="edit_event_name" name="event_name" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="edit_event_image" class="block text-sm font-medium text-gray-700">Gambar Event</label>
                    <input type="file" id="edit_event_image" name="event_image" class="w-full p-2 border rounded" accept="image/*">
                </div>

                <div class="mb-4">
                    <label for="edit_event_date" class="block text-sm font-medium text-gray-700">Tanggal Event</label>
                    <input type="date" id="edit_event_date" name="event_date" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="edit_start_time" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                    <input type="time" id="edit_start_time" name="start_time" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="edit_end_time" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                    <input type="time" id="edit_end_time" name="end_time" class="w-full p-2 border rounded" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('editModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>

    <script>
         function openQRModal(event) {
            // Tampilkan modal
            openModal('qrModal');

            // Buat URL untuk QR Code
            var eventUrl = "generate_qr.php?event_id=" + event.event_id;

            // Menggunakan API atau library untuk menghasilkan QR Code
            var qrCodeImage = '<img src="https://api.qrserver.com/v1/create-qr-code/?data=' + encodeURIComponent(eventUrl) + '&size=150x150" alt="QR Code">';

            // Masukkan QR code ke dalam modal
            document.getElementById('qrCodeContainer').innerHTML = qrCodeImage;
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function openEditModal(event) {
            document.getElementById('edit_event_id').value = event.event_id;
            document.getElementById('edit_event_name').value = event.event_name;
            document.getElementById('edit_event_date').value = event.event_date;
            document.getElementById('edit_start_time').value = event.start_time;
            document.getElementById('edit_end_time').value = event.end_time;
            document.getElementById('current_image').value = event.image;
            openModal('editModal');
        }
    </script>
</body>
</html>
