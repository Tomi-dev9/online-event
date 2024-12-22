<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

// Ambil event_id dari URL
$eventname = $_GET['event_id'] ?? null;

if (!$eventname) {
    die("Event ID tidak ditemukan.");
}

// URL untuk absensi event
$baseUrl = 'http://localhost/Absensi_online/absensi.php';
$data = $baseUrl . '?event_name=' . $eventname;

// Path untuk menyimpan file QR Code
$fileDir = 'image/';
$fileName = 'qr-code-event-' . $eventname . '.svg';
$file = $fileDir . $fileName;

// Pastikan direktori image ada
if (!is_dir($fileDir)) {
    mkdir($fileDir, 0777, true);
}

// Membuat QR Code
$qrCode = new QrCode($data);

// Membuat writer untuk PNG
$writer = new SvgWriter();


// Menulis QR Code ke file
$result = $writer->write($qrCode);
$result->saveToFile($file);

echo "QR Code untuk Event ID: <b>$eventname</b> berhasil dibuat dan disimpan di <b>$file</b>.<br>";
echo "<img src='$file' alt='QR Code Event $eventname' />";
?>
