<?php
require_once __DIR__ . '/vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (isset($_GET['username'])) {
    $username = htmlspecialchars($_GET['username']);
    $filePath = __DIR__ . "/temp_qr/{$username}.png";

    // Opsi QR Code
    $options = new QROptions([
        'eccLevel' => QRCode::ECC_L, // Tingkat error correction
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'imageBase64' => false, // Jika ingin langsung menampilkan gambar
    ]);

    // Generate QR Code
    $qrCode = new QRCode($options);
    $qrCode->render($username, $filePath);

    // Tampilkan QR Code
    header('Content-Type: image/png');
    readfile($filePath);
    exit();
} else {
    echo "Username tidak ditemukan.";
}
?>
