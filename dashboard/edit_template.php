<?php
// Koneksi ke database
$host = 'localhost'; // Sesuaikan dengan konfigurasi Anda
$user = 'root'; // Sesuaikan dengan username database Anda
$password = ''; // Sesuaikan dengan password database Anda
$database = 'absensi_online'; // Nama database

$conn = new mysqli($host, $user, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek apakah ada parameter 'image' di URL
if (isset($_GET['image']) && $_GET['image']) {
    $image_path = htmlspecialchars($_GET['image']); // Sanitasi input

    // Cek apakah file gambar ada
    if (!file_exists($image_path)) {
        echo "<script>alert('File gambar tidak ditemukan.');</script>";
        exit;
    }
} else {
    echo "<script>alert('Tidak ada gambar yang dipilih untuk diedit.');</script>";
    exit;
}

// Periksa apakah ada data gambar yang dikirim
if (isset($_POST['image'])) {
    $imageData = $_POST['image'];

    // Menghilangkan prefix data URL
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = base64_decode($imageData);

    // Tentukan path untuk menyimpan gambar
    $filePath = 'uploads/sertifikat_' . uniqid() . '.png'; // Ubah path sesuai kebutuhan

    // Simpan gambar ke server
    if (file_put_contents($filePath, $imageData)) {
        // Simpan path gambar ke database
        $query = "INSERT INTO sertifikatpdf (file_path) VALUES ('$filePath')";
        if ($conn->query($query) === TRUE) {
            echo "Sertifikat berhasil disimpan!";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Gagal menyimpan gambar.";
    }
} else {
    echo "Tidak ada gambar yang diterima.";
}
// Cek apakah file PDF dikirimkan
if (isset($_FILES['pdf'])) {
    $pdf = $_FILES['pdf'];

    // Tentukan folder tujuan
    $targetDir = 'uploads/sertifikat/';
    $targetFile = $targetDir . basename($pdf['name']);

    // Pindahkan file ke folder tujuan
    if (move_uploaded_file($pdf['tmp_name'], $targetFile)) {
        echo "Sertifikat berhasil disimpan di server.";
    } else {
        echo "Gagal menyimpan sertifikat.";
    }
} else {
    echo "Tidak ada file PDF yang diterima.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Editor</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-4">
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold text-center mb-6">Certificate Editor</h1>

        <div class="flex justify-center mb-4">
            <canvas id="certificateCanvas" width="800" height="600" class="border"></canvas>
        </div>

        <div class="flex justify-center gap-4 mb-4">
            <input type="text" id="textInput" placeholder="Enter text" class="border px-2 py-1">
            <button id="addTextBtn" class="bg-blue-500 text-white px-4 py-2 rounded">Add Text</button>
            <input type="file" id="signatureInput" accept="image/*" class="hidden">
            <label for="signatureInput" class="bg-green-500 text-white px-4 py-2 rounded cursor-pointer">Upload Signature</label>
        </div>

        <div class="flex justify-center gap-4 mb-4">
            <button id="downloadPDFBtn" class="bg-purple-500 text-white px-4 py-2 rounded">Download as PDF</button>
            <button id="resetCanvasBtn" class="bg-red-500 text-white px-4 py-2 rounded">Reset</button>
        </div>

        <div class="mt-6 text-center">
    <h2 class="text-xl font-bold mb-4">Daftar Peserta</h2>
    <div class="space-y-2">
        <?php
        // Ambil semua data peserta
        $query = "SELECT nama, phone_number FROM peserta";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="flex items-center gap-4">';
                echo '<p class="text-lg">' . htmlspecialchars($row['nama']) . '</p>';
                // Menampilkan PDF jika tersedia
                if (!empty($row['pdf_file'])) {
                    echo '<a href="' . htmlspecialchars($row['pdf_file']) . '" target="_blank" class="bg-blue-500 text-white px-4 py-2 rounded">Lihat Sertifikat (PDF)</a>';
                } else {
                    echo '<button class="bg-gray-500 text-white px-4 py-2 rounded" disabled>PDF Tidak Tersedia</button>';
                }
                if (!empty($row['phone_number'])) {
                    echo '<a href="https://wa.me/' . htmlspecialchars($row['phone_number']) . '?text=Halo%20' . urlencode($row['nama']) . ',%20sertifikat%20Anda%20sudah%20siap." target="_blank" class="bg-green-500 text-white px-4 py-2 rounded">Kirim WhatsApp</a>';
                } else {
                    echo '<button class="bg-gray-500 text-white px-4 py-2 rounded" disabled>Nomor Tidak Ditemukan</button>';
                }
                echo '</div>';
            }
        } else {
            echo '<p>Tidak ada data peserta.</p>';
        }
        ?>
    </div>
</div>

        </div>
    </div>

    <script>
// Initialize fabric.js canvas
const canvas = new fabric.Canvas('certificateCanvas');

// Load a background image for the certificate
fabric.Image.fromURL('<?php echo $image_path; ?>', (img) => {
    const canvasWidth = canvas.getWidth();
    const canvasHeight = canvas.getHeight();

    const scaleX = canvasWidth / img.width;
    const scaleY = canvasHeight / img.height;
    const scale = Math.min(scaleX, scaleY);

    img.scale(scale);
    img.set({
        left: (canvasWidth - img.width * scale) / 2,
        top: (canvasHeight - img.height * scale) / 2,
        selectable: false,
        evented: false,
    });

    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
});

// Add text to canvas
document.getElementById('addTextBtn').addEventListener('click', () => {
    const text = document.getElementById('textInput').value;
    if (text) {
        const textObj = new fabric.Text(text, {
            left: 100,
            top: 100,
            fontSize: 24,
            fill: 'black',
        });
        canvas.add(textObj);
    }
});
// Download as PDF
document.getElementById('downloadPDFBtn').addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({ orientation: 'landscape', unit: 'px', format: [800, 600] });

    pdf.html(document.querySelector('canvas'), {
        callback: function (doc) {
            // Mengirimkan file PDF ke server untuk disimpan
            const pdfData = doc.output('blob'); // Menghasilkan file PDF dalam bentuk blob

            const formData = new FormData();
            formData.append('pdf', pdfData, 'sertifikat_' + new Date().getTime() + '.pdf');

            // Kirim PDF ke server menggunakan AJAX
            fetch('upload_sertifikat.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(data => {
                alert('Sertifikat berhasil diunduh dan disimpan!');
            })
            .catch(error => {
                alert('Gagal mengunduh sertifikat.');
            });
        },
    });
});


// Add signature to canvas (Multiple signatures allowed)
document.getElementById('signatureInput').addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            fabric.Image.fromURL(event.target.result, (img) => {
                img.scaleToWidth(150);
                img.set({
                    left: 100,  // You can adjust the position as needed
                    top: 100,   // You can adjust the position as needed
                    hasControls: true,
                    hasBorders: true,
                    selectable: true, // Make the signature selectable
                });
                canvas.add(img);
            });
        };
        reader.readAsDataURL(file);
    }
});

// Add event listener for Backspace or Delete key to remove the selected signature
document.addEventListener('keydown', (e) => {
    // Check if the key pressed is Backspace or Delete
    if (e.key === 'Backspace' || e.key === 'Delete') {
        const activeObject = canvas.getActiveObject();
        if (activeObject) {
            canvas.remove(activeObject);  // Remove the selected object (signature)
        }
    }
});

// Download as PDF
document.getElementById('downloadPDFBtn').addEventListener('click', () => {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({ orientation: 'landscape', unit: 'px', format: [800, 600] });

    pdf.html(document.querySelector('canvas'), {
        callback: function (doc) {
            doc.save('certificate.pdf');
        },
    });
});

// Reset canvas
document.getElementById('resetCanvasBtn').addEventListener('click', () => {
    canvas.clear();
    fabric.Image.fromURL('<?php echo $image_path; ?>', (img) => {
        const canvasWidth = canvas.getWidth();
        const canvasHeight = canvas.getHeight();

        const scaleX = canvasWidth / img.width;
        const scaleY = canvasHeight / img.height;
        const scale = Math.min(scaleX, scaleY);

        img.scale(scale);
        img.set({
            left: (canvasWidth - img.width * scale) / 2,
            top: (canvasHeight - img.height * scale) / 2,
            selectable: false,
            evented: false,
        });

        canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
    });
});

    </script>
</body>
</html>
