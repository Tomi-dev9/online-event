<?php
// Menyimpan gambar sertifikat yang sudah diedit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengecek apakah ada data gambar yang dikirimkan
    if (isset($_POST['certificateImage']) && isset($_POST['participantName'])) {
        $imageData = $_POST['certificateImage'];
        $participantName = $_POST['participantName'];

        // Menghapus prefix data URL (data:image/png;base64,)
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = base64_decode($imageData);

        // Menentukan nama file sertifikat berdasarkan nama peserta
        $fileName = '../sertifikat/' . preg_replace('/[^a-zA-Z0-9]/', '_', $participantName) . '_sertifikat.pngs';

        // Menyimpan file gambar ke folder sertifikat
        file_put_contents($fileName, $imageData);

        echo "Sertifikat berhasil disimpan.";
    } else {
        echo "Data tidak lengkap.";
    }
} else {
    echo "Metode request tidak valid.";
}
?>
