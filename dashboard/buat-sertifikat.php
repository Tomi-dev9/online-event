Untuk memperbaiki masalah di mana tombol "Simpan Data" tidak menampilkan preview sertifikat, kita perlu memastikan bahwa fungsi `updateTextOnCanvas` dipanggil dengan benar dan bahwa canvas di-render ulang setelah teks ditambahkan. Selain itu, kita perlu memastikan bahwa template gambar dan tanda tangan diunggah dengan benar sebelum menambahkan teks.

Berikut adalah perbaikan yang dapat Anda lakukan:

1. **Pastikan template gambar diunggah sebelum menambahkan teks.**
2. **Pastikan tanda tangan diunggah sebelum menambahkan teks.**
3. **Pastikan teks ditambahkan ke canvas setelah template dan tanda tangan diunggah.**

Berikut adalah kode yang telah diperbaiki:

```php
<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

// Periksa apakah sesi email tersedia
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Query untuk mengambil data dari tabel events
$sql = "SELECT * FROM events";
$result = $conn->query($sql);

if (!$result) {
    die("Query gagal: " . $conn->error);
}

// Query untuk mengambil data peserta
$sql_peserta = "SELECT * FROM peserta";
$result_peserta = $conn->query($sql_peserta);

if (!$result_peserta) {
    die("Query gagal: " . $conn->error);
}

$peserta_data = [];
while ($row = $result_peserta->fetch_assoc()) {
    $peserta_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Sertifikat</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.1/fabric.min.js"></script>
</head>
<body>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-center">Preview Sertifikat</h2>

        <!-- Form untuk mengedit data sertifikat -->
        <div class="mt-6">
            <!-- save data -->
            <button id="saveData" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Simpan Data
            </button>
            <label for="participantName" class="block text-lg font-semibold text-gray-700">Nama Peserta</label>
            <select id="participantName" class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg">
                <option value="">Pilih Nama Peserta</option>
                <?php foreach ($peserta_data as $peserta): ?>
                    <option value="<?php echo htmlspecialchars($peserta['nama']); ?>">
                        <?php echo htmlspecialchars($peserta['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="eventSelect" class="block text-lg font-semibold text-gray-700 mt-4">Pilih Acara</label>
            <input type="text" id="eventName" class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg" placeholder="Nama Acara" readonly>

            <!-- Upload Template -->
            <label for="templateUpload" class="block text-lg font-semibold text-gray-700 mt-4">Unggah Template Sertifikat</label>
            <input type="file" id="templateUpload" class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg" accept="image/*,application/pdf">

            <!-- Upload Tanda Tangan -->
            <label for="signatureUpload" class="block text-lg font-sem ibold text-gray-700 mt-4">Unggah Tanda Tangan</label>
            <input type="file" id="signatureUpload" class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg" accept="image/*">
        </div>

        <!-- Area untuk preview template sertifikat -->
        <div class="border border-gray-300 p-6 rounded-lg mt-6 shadow-md bg-gray-50">
            <div class="text-center">
                <canvas id="certificateCanvas" class="w-full h-96 bg-gray-200"></canvas>
            </div>
        </div>
        <div class="mt-6 text-center">
            <button id="saveCertificateBtn" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Simpan Sertifikat
            </button>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let canvas = new fabric.Canvas('certificateCanvas');
            let templateImage = null;
            let signatureImage = null;

            // Fungsi untuk mengunggah dan menampilkan template
            $('#templateUpload').on('change', function(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith('image')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (templateImage) {
                            canvas.remove(templateImage); // Hapus template sebelumnya
                        }
                        fabric.Image.fromURL(e.target.result, function(img) {
                            templateImage = img;
                            const scaleFactor = Math.min(canvas.width / img.width, canvas.height / img.height);
                            img.set({
                                left: 0,
                                top: 0,
                                scaleX: scaleFactor,
                                scaleY: scaleFactor
                            });
                            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Fungsi untuk mengunggah tanda tangan
            $('#signatureUpload').on('change', function(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith('image')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (signatureImage) {
                            canvas.remove(signatureImage); // Hapus tanda tangan sebelumnya
                        }
                        fabric.Image.fromURL(e.target.result, function(img) {
                            signatureImage = img;
                            const scaleFactor = 0.3; // Sesuaikan ukuran tanda tangan
                            img.set({
                                left: canvas.width - img.width * scaleFactor - 20, // Posisi tanda tangan di pojok kanan bawah
                                top: canvas.height - img.height * scaleFactor - 20,
                                scaleX: scaleFactor,
                                scaleY: scaleFactor
                            });
                            canvas.add(img);
                            canvas.renderAll(); // Render ulang canvas untuk menampilkan tanda tangan
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Fungsi untuk memperbarui teks nama peserta dan acara pada canvas
            function updateTextOnCanvas(participantName, eventName) {
                canvas.clear(); // Hapus semua konten sebelumnya
                if (templateImage) {
                    canvas.setBackgroundImage(templateImage, canvas.renderAll.bind(canvas)); // Set background image if exists
                }
                const textObjName = new fabric.Text('Nama Peserta: ' + participantName, {
                    left: 100,
                    top: 100,
                    fontSize: 24,
                    fontFamily: 'Arial',
                    fill: '#000000',
                    selectable: false
                });
                const textObjEvent = new fabric.Text('Acara: ' + eventName, {
                    left: 100,
                    top: 150,
                    fontSize: 24,
                    fontFamily: 'Arial',
                    fill: '#000000',
                    selectable: false
                });

                canvas.add(textObjName);
                canvas.add(textObjEvent);
                canvas.renderAll(); // Render ulang canvas
            }

            // Tombol Simpan Data
            $('#saveData').click(function() {
                const participantName = $('#participantName').val();
                const eventName = $('#eventName').val();

                if (!participantName || !eventName) {
                    alert("Nama peserta dan nama acara harus diisi!");
                    return;
                }

                // Update teks pada canvas
                updateTextOnCanvas(participantName, eventName);
                // Menampilkan preview sertifikat
                $('#saveCertificateBtn').prop('disabled', false); // Aktifkan tombol simpan sertifikat
            });

            // Simpan sertifikat
            $('#saveCertificateBtn'). click(function() {
                const participantName = $('#participantName').val();
                if (!participantName) {
                    alert("Nama peserta harus diisi!");
                    return;
                }
                // Simpan gambar sertifikat yang sudah diedit
                const dataUrl = canvas.toDataURL();
                const formData = new FormData();
                formData.append("participantName", participantName);
                formData.append("certificateImage", dataUrl);

                // Kirim data sertifikat ke server
                $.ajax({
                    url: "save_certificate.php",  // Ganti dengan URL server Anda untuk menyimpan sertifikat
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        alert("Sertifikat berhasil disimpan.");
                    },
                    error: function() {
                        alert("Terjadi kesalahan saat menyimpan sertifikat.");
                    }
                });
            });
        });
    </script>
</body>
</html>