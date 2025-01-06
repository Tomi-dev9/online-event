<?php
// Cek apakah ada parameter 'image' di URL
if (isset($_GET['image']) && $_GET['image']) {
    $image_path = $_GET['image'];

    // Cek apakah file gambar ada
    if (!file_exists($image_path)) {
        echo "<script>alert('File gambar tidak ditemukan.');</script>";
        exit;
    }
} else {
    echo "<script>alert('Tidak ada gambar yang dipilih untuk diedit.');</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template Sertifikat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        #canvas {
            border: 1px solid #000;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 bg-white shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Edit Template Sertifikat</h1>

        <!-- Preview Template -->
        <div class="mb-6">
            <h2 class="text-lg font-bold mb-2">Preview Template</h2>
            <canvas id="canvas" width="800" height="600"></canvas>
        </div>

        <!-- Menu Pilihan Objek -->
        <div class="mb-6">
            <h2 class="text-lg font-bold mb-2">Pilih Objek yang Akan Ditambahkan</h2>
            <select id="object_select" class="border rounded-lg p-2 mb-4">
                <option value="text">Teks</option>
                <option value="signature">Tanda Tangan</option>
            </select>
        </div>

        <!-- Form untuk Mengedit Template -->
        <div class="mb-6" id="text_form">
            <h2 class="text-lg font-bold mb-2">Tambah Teks ke Template</h2>
            <input
                type="text"
                id="text_to_add"
                class="border rounded-lg p-2 mb-4"
                placeholder="Masukkan teks untuk sertifikat..."
                required
            />
            <button type="button" id="add_object" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                Tambah Objek
            </button>
        </div>

        <!-- Tanda Tangan -->
        <div class="mb-6" id="signature_form" style="display: none;">
            <h2 class="text-lg font-bold mb-2">Tambah Tanda Tangan</h2>
            <button type="button" id="add_signature" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                Tambah Tanda Tangan
            </button>
        </div>

        <!-- Tombol untuk Mengunduh sebagai PDF -->
        <div class="mb-6">
            <button type="button" id="download_pdf" class="bg-red-500 text-white px-4 py-2 rounded-lg">
                Unduh sebagai PDF
            </button>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        const textInput = document.getElementById('text_to_add');
        const addObjectButton = document.getElementById('add_object');
        const addSignatureButton = document.getElementById('add_signature');
        const downloadPdfButton = document.getElementById('download_pdf');
        const objectSelect = document.getElementById('object_select');
        const textForm = document.getElementById('text_form');
        const signatureForm = document.getElementById('signature_form');

        let image = new Image();
        image.src = "<?= htmlspecialchars($image_path) ?>";
        image.onload = function() {
            // Gambar dimuat pertama kali
            ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
        };

        // Menyimpan objek yang ditambahkan
        let addedObjects = [];

        // Menambahkan objek berdasarkan pilihan
        addObjectButton.addEventListener('click', function() {
            const selectedObject = objectSelect.value;

            if (selectedObject === 'text') {
                const text = textInput.value;
                if (text) {
                    const x = 100; // Posisi X
                    const y = 100; // Posisi Y
                    const font = '20px Arial';
                    ctx.font = font;
                    ctx.fillStyle = 'white';
                    ctx.fillText(text, x, y);

                    // Menyimpan objek teks
                    addedObjects.push({ type: 'text', text, x, y, font });
                }
            } else if (selectedObject === 'signature') {
                const signature = new Image();
                signature.src = 'path/to/signature.png'; // Ganti dengan path tanda tangan
                signature.onload = function() {
                    const x = 500; // Posisi X tanda tangan
                    const y = 500; // Posisi Y tanda tangan
                    ctx.drawImage(signature, x, y, 100, 50); // Ukuran tanda tangan

                    // Menyimpan objek tanda tangan
                    addedObjects.push({ type: 'signature', x, y, signature });
                };
            }
        });

        // Fungsi untuk mengunduh canvas sebagai PDF
        downloadPdfButton.addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Mengonversi canvas ke gambar base64
            const imgData = canvas.toDataURL('image/png');
            
            // Menambahkan gambar ke PDF
            doc.addImage(imgData, 'PNG', 10, 10, 180, 160);

            // Mengunduh PDF
            doc.save('template_sertifikat.pdf');
        });

        // Menampilkan form sesuai pilihan objek
        objectSelect.addEventListener('change', function() {
            const selectedObject = objectSelect.value;

            if (selectedObject === 'text') {
                textForm.style.display = 'block';
                signatureForm.style.display = 'none';
            } else if (selectedObject === 'signature') {
                textForm.style.display = 'none';
                signatureForm.style.display = 'block';
            }
        });

        // Fungsi untuk memindahkan objek
        let dragging = false;
        let dragOffsetX, dragOffsetY;

        canvas.addEventListener('mousedown', function(e) {
            const mouseX = e.offsetX;
            const mouseY = e.offsetY;

            // Cek apakah mouse berada di atas objek
            for (let i = 0; i < addedObjects.length; i++) {
                const obj = addedObjects[i];

                if (obj.type === 'text') {
                    const textWidth = ctx.measureText(obj.text).width;
                    const textHeight = parseInt(obj.font);
                    if (mouseX >= obj.x && mouseX <= obj.x + textWidth &&
                        mouseY >= obj.y - textHeight && mouseY <= obj.y) {
                        dragging = true;
                        dragOffsetX = mouseX - obj.x;
                        dragOffsetY = mouseY - obj.y;
                        break;
                    }
                } else if (obj.type === 'signature') {
                    const signatureWidth = 100;
                    const signatureHeight = 50;
                    if (mouseX >= obj.x && mouseX <= obj.x + signatureWidth &&
                        mouseY >= obj.y && mouseY <= obj.y + signatureHeight) {
                        dragging = true;
                        dragOffsetX = mouseX - obj.x;
                        dragOffsetY = mouseY - obj.y;
                        break;
                    }
                }
            }
        });

        canvas.addEventListener('mousemove', function(e) {
            if (dragging) {
                const mouseX = e.offsetX;
                const mouseY = e.offsetY;

                // Update posisi objek yang sedang dipindahkan
                for (let i = 0; i < addedObjects.length; i++) {
                    const obj = addedObjects[i];
                    if (obj.type === 'text') {
                        if (mouseX - dragOffsetX >= 0 && mouseY - dragOffsetY >= 0) {
                            obj.x = mouseX - dragOffsetX;
                            obj.y = mouseY - dragOffsetY;
                        }
                    } else if (obj.type === 'signature') {
                        if (mouseX - dragOffsetX >= 0 && mouseY - dragOffsetY >= 0) {
                            obj.x = mouseX - dragOffsetX;
                            obj.y = mouseY - dragOffsetY;
                        }
                    }
                }

                // Clear canvas and redraw image and objects
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

                // Redraw all objects
                for (let i = 0; i < addedObjects.length; i++) {
                    const obj = addedObjects[i];
                    if (obj.type === 'text') {
                        ctx.font = obj.font;
                        ctx.fillStyle = 'white';
                        ctx.fillText(obj.text, obj.x, obj.y);
                    } else if (obj.type === 'signature') {
                        ctx.drawImage(obj.signature, obj.x, obj.y, 100, 50);
                    }
                }
            }
        });

        canvas.addEventListener('mouseup', function() {
            dragging = false;
        });
    </script>
</body>
</html>
