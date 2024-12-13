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
    $quota = $_POST['quota'];
    $image_name = null;

  if (!empty($_FILES['event_image']['name'])) {
        $image_name = time() . '_' . $_FILES['event_image']['name'];
        $target_dir = "uploads/";
        move_uploaded_file($_FILES['event_image']['tmp_name'], $target_dir . $image_name);
    }

    $stmt = $conn->prepare("INSERT INTO event (event_name, event_date, start_time, end_time, quota, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $event_name, $event_date, $start_time, $end_time, $quota, $image_name);
    $stmt->execute();
    header("Location: event.php");
    exit();
}

// Edit Event
if (isset($_POST['edit_event'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $quota = $_POST['quota'];
    $image_name = null;

    $stmt = $conn->prepare("UPDATE event SET event_name = ?, event_date = ?, start_time = ?, end_time = ?, quota = ? WHERE event_id = ?");
    $stmt->bind_param("ssssii", $event_name, $event_date, $start_time, $end_time, $quota, $event_id);
    $stmt->execute();
    header("Location:event.php");
    exit();
}

// Hapus Event
if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];

    $stmt = $conn->prepare("DELETE FROM event WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    header("Location:event.php");
    exit();
}

// Ambil data event untuk ditampilkan
$result = $conn->query("SELECT * FROM event");
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
        <a href="dashboard.php" class="text-white font-semibold text-lg">Dashboard</a>
        <a href="logout.php" class="text-white font-semibold">Logout</a>
    </nav>

    <!-- Container -->
    <div class="container mx-auto mt-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Kelola Event</h1>
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
                        <th class="py-2 px-4 border">Waktu Mulai</th>
                        <th class="py-2 px-4 border">Waktu Selesai</th>
                        <th class="py-2 px-4 border">Kuota</th>
                        <th class="py-2 px-4 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $index => $event): ?>
                    <tr>
                        <td class="py-2 px-4 border text-center"><?php echo $index + 1; ?></td>
                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($event['event_name']); ?></td>
                        <td class="py-2 px-4 border"><img src="<?php echo $event['image']; ?>" alt="Event Image" class="w-16 h-16 object-cover"></td>
                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($event['event_date']); ?></td>
                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($event['start_time']); ?></td>
                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($event['end_time']); ?></td>
                        <td class="py-2 px-4 border text-center"><?php echo htmlspecialchars($event['quota']); ?></td>
                        <td class="py-2 px-4 border text-center">
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
            <form action="" method="post">
                <input type="hidden" name="add_event" value="1">
                <div class="mb-4">
                <label for="event_image" class="block text-sm font-medium text-gray-700">Gambar Event</label>
                <input type="file" id="event_image" name="event_image" class="w-full p-2 border rounded" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="event_name" class="block text-sm font-medium text-gray-700">Nama Event</label>
                    <input type="text" id="event_name" name="event_name" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="event_date" class="block text-sm font-medium text-gray-700">Tanggal Event</label>
                    <input type="date" id="event_date" name="event_date" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                    <input type="time" id="start_time" name="start_time" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="end_time" class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                    <input type="time" id="end_time" name="end_time" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="quota" class="block text-sm font-medium text-gray-700">Kuota</label>
                    <input type="number" id="quota" name="quota" class="w-full p-2 border rounded" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('addModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Event -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-lg font-semibold mb-4">Edit Event</h2>
            <form action="" method="post">
                <input type="hidden" name="edit_event" value="1">
                <input type="hidden" id="edit_event_id" name="event_id">
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
                <div class="mb-4">
                    <label for="edit_quota" class="block text-sm font-medium text-gray-700">Kuota</label>
                    <input type="number" id="edit_quota" name="quota" class="w-full p-2 border rounded" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('editModal')" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
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
            document.getElementById('edit_event_id').value = event.event_id;
            document.getElementById('edit_event_name').value = event.event_name;
            document.getElementById('edit_event_date').value = event.event_date;
            document.getElementById('edit_start_time').value = event.start_time;
            document.getElementById('edit_end_time').value = event.end_time;
            document.getElementById('edit_quota').value = event.quota;
            document.getElementById('edit_event_image').src = event.image;
            openModal('editModal');
        }
    </script>
</body>
</html>
