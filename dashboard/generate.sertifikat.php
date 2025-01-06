<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "absensi_online");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses unggah template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['template'])) {
    $file_name = $_FILES['template']['name'];
    $file_tmp = $_FILES['template']['tmp_name'];
    $upload_dir = "uploads/templates/";

    // Validasi format file
    $allowed_types = ['png', 'jpg', 'jpeg'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_types)) {
        echo "<script>alert('Hanya format PNG atau JPG yang diperbolehkan.');</script>";
    } else {
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_path = $upload_dir . basename($file_name);

        if (move_uploaded_file($file_tmp, $file_path)) {
            echo "<script>alert('Template berhasil diunggah!');</script>";
        } else {
            echo "<script>alert('Gagal mengunggah template.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sertifikat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 bg-white shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Kelola Sertifikat</h1>

        <!-- Upload Template Sertifikat -->
        <div class="mb-6">
            <h2 class="text-lg font-bold mb-2">Upload Template Sertifikat (PNG/JPG)</h2>
            <form method="POST" enctype="multipart/form-data" class="flex items-center">
                <input
                    type="file"
                    name="template"
                    class="border rounded-lg p-2 w-2/3 mr-4"
                    accept=".png,.jpg,.jpeg"
                />
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                    Unggah Template
                </button>
            </form>
            <p class="text-sm text-gray-500 mt-2">
                *Pastikan template dalam format PNG atau JPG.
            </p>
        </div>

        <!-- Preview Template -->
        <?php if (isset($file_path)): ?>
            <div class="mb-6">
                <h2 class="text-lg font-bold mb-2">Preview Template</h2>
                <img src="<?= htmlspecialchars($file_path) ?>" class="w-full max-w-md border rounded-lg" alt="Template Preview">
            </div>

            <!-- Tombol Edit Template -->
            <div class="mb-6">
                <h2 class="text-lg font-bold mb-2">Edit Template</h2>
                <a href="edit_template.php?image=<?= urlencode($file_path) ?>" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                    Edit Sertifikat
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
