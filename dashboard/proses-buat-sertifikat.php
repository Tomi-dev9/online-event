<?php
require './fpdf/fpdf.php';

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $peserta_id = $_POST['peserta_id']; // Ambil peserta_id dari form

    // Ambil nama peserta dari tabel absensi berdasarkan peserta_id
    $sql_peserta = "SELECT nama FROM absensi WHERE user_id = ?";
    $stmt_peserta = $conn->prepare($sql_peserta);
    $stmt_peserta->bind_param('i', $peserta_id);
    $stmt_peserta->execute();
    $result_peserta = $stmt_peserta->get_result();
    $peserta = $result_peserta->fetch_assoc();
    $nama_peserta = $peserta['nama'];

    // Simpan ke tabel sertifikat
    $tanggal_terbit = date('Y-m-d'); // Atau ambil dari input form jika diperlukan
    $query_insert = "INSERT INTO sertifikat (event_id, nama, tanggal_terbit) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($query_insert);
    $stmt_insert->bind_param('iss', $event_id, $nama_peserta, $tanggal_terbit);
    $stmt_insert->execute();

    // Bersihkan nama peserta untuk digunakan sebagai nama file
    $nama_peserta_file = preg_replace('/[^a-zA-Z0-9-_]/', '_', $nama_peserta); // Mengganti karakter yang tidak valid dengan underscore

    // Pastikan direktori sertifikat ada
    if (!file_exists('../sertifikat')) {
        mkdir('../sertifikat', 0777, true);
    }

    // Buat file PDF sertifikat
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, "SERTIFIKAT", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Diberikan kepada:", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $nama_peserta, 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Atas partisipasinya dalam acara", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 10, $tanggal_terbit, 0, 1, 'C');
    
    // Tentukan path untuk menyimpan file PDF
    $pdf_file_path = "../sertifikat/$nama_peserta_file.pdf";

    // Output PDF ke file
    $pdf->Output('F', $pdf_file_path);

    // Cek apakah file berhasil dibuat
    if (file_exists($pdf_file_path)) {
        echo "Sertifikat berhasil dibuat!";
    } else {
        echo "Terjadi kesalahan saat membuat sertifikat.";
    }
}

header("Location: dashboard.php?message=Sertifikat berhasil dibuat");
exit();
?>
