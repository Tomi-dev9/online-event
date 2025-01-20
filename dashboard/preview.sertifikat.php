<?php
// Mengambil data yang dikirimkan dari halaman sebelumnya
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : '';
$nama = isset($_GET['nama']) ? $_GET['nama'] : '';

// Mendapatkan data acara dari database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query untuk mendapatkan data acara
$sql = "SELECT event_name, event_date FROM events WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// Query untuk mendapatkan nama peserta yang sudah absensi
$sql_peserta = "SELECT nama FROM absensi WHERE event_id = ?";
$stmt_peserta = $conn->prepare($sql_peserta);
$stmt_peserta->bind_param("i", $event_id);
$stmt_peserta->execute();
$result_peserta = $stmt_peserta->get_result();
$peserta_data = [];
while ($row = $result_peserta->fetch_assoc()) {
    $peserta_data[] = $row;
}

$conn->close();

if (!$event) {
    echo "Acara tidak ditemukan.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Sertifikat</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
</head>
<body>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-center">Preview Sertifikat</h2>

        <!-- Form untuk mengedit data sertifikat -->
        <div class="mt-6">
            <label for="participantName" class="block text-lg font-semibold text-gray-700">Nama Peserta</label>
            <select id="participantName" class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg">
                <option value="">Pilih Nama Peserta</option>
                <?php foreach ($peserta_data as $peserta): ?>
                    <option value="<?php echo htmlspecialchars($peserta['nama']); ?>" <?php echo ($nama == $peserta['nama']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($peserta['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="templateUpload" class="block text-lg font-semibold text-gray-700 mt-4">Unggah Template Sertifikat</label>
            <input type="file" id="templateUpload" class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg" accept="image/*">

            <label for="textInput" class="block text-lg font-semibold text-gray-700 mt-4">Tambahkan Teks</label>
            <input type="text" id="textInput" class="w-full mt-2 px-4 py-3 border border-gray-300 rounded-lg" placeholder="Masukkan teks">

            <button id="addTextBtn" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md mt-4 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Tambahkan Teks
            </button>

            <button id="loadPreviewBtn" class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md mt-4 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Tampilkan Preview
            </button>
        </div>

        <!-- Area untuk preview template sertifikat -->
        <div class="border border-gray-300 p-6 rounded-lg mt-6 shadow-md bg-gray-50">
            <div class="text-center">
                <canvas id="canvas-id" width="800" height="600" style="border: 1px solid #ccc;"></canvas>
            </div>
        </div>

        <div class="mt-6 text-center">
            <button id="saveCertificateBtn" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Simpan Sertifikat
            </button>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        let canvas = new fabric.Canvas('canvas-id');
        let templateImage = null;

        $('#loadPreviewBtn').click(function () {
            const fileInput = $('#templateUpload')[0].files[0];
            if (!fileInput) {
                alert("Harap unggah template sertifikat terlebih dahulu.");
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                fabric.Image.fromURL(e.target.result, function (img) {
                    if (templateImage) {
                        canvas.remove(templateImage);
                    }
                    templateImage = img;
                    canvas.setBackgroundImage(templateImage, canvas.renderAll.bind(canvas), {
                        scaleX: canvas.width / img.width,
                        scaleY: canvas.height / img.height
                    });
                });
            };
            reader.readAsDataURL(fileInput);
        });

        $('#addTextBtn').click(function () {
            const text = $('#textInput').val();
            if (!text) {
                alert("Harap masukkan teks terlebih dahulu.");
                return;
            }

            const textObj = new fabric.Text(text, {
                left: 100,
                top: 100,
                fontSize: 24,
                fill: '#000000',
                selectable: true
            });
            canvas.add(textObj);
        });

        $('#saveCertificateBtn').click(function () {
            if (!templateImage) {
                alert("Harap unggah template dan buat perubahan sebelum menyimpan.");
                return;
            }

            const dataUrl = canvas.toDataURL();
            const formData = new FormData();
            formData.append("certificateImage", dataUrl);

            $.ajax({
                url: "save_certificate.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    alert("Sertifikat berhasil disimpan.");
                },
                error: function () {
                    alert("Terjadi kesalahan saat menyimpan sertifikat.");
                }
            });
        });
    });
    </script>
</body>
</html>
