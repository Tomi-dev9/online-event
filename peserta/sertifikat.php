<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Peserta</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .certificate {
            width: 700px;
            height: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            border: 2px solid #ddd;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .certificate h1 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .certificate p {
            font-size: 18px;
            color: #34495e;
            line-height: 1.6;
        }

        .certificate .details {
            margin-top: 30px;
            font-size: 20px;
            font-weight: bold;
        }

        .certificate .details p {
            margin: 5px 0;
        }

        .certificate .footer {
            margin-top: 50px;
            font-size: 16px;
            color: #7f8c8d;
        }

        .certificate .footer .signature {
            margin-top: 30px;
            font-size: 18px;
            font-style: italic;
        }

        .certificate .footer .date {
            margin-top: 10px;
            font-size: 16px;
        }

        .certificate .qr-code {
            margin-top: 30px;
        }

        .certificate img {
            width: 100px;
            height: 100px;
        }
    </style>
</head>
<body>

    <div class="certificate">
        <h1>SERTIFIKAT PESERTA</h1>
        <p>Dengan ini kami menyatakan bahwa</p>
        
        <div class="details">
            <p><strong>Nama Peserta: <?php echo htmlspecialchars($nama_peserta); ?></strong></p>
            <p><strong>Telah mengikuti acara:</strong></p>
            <p><strong><?php echo htmlspecialchars($nama_acara); ?></strong></p>
            <p><strong>Pada tanggal:</strong> <?php echo htmlspecialchars($tanggal_acara); ?></p>
        </div>

        <div class="footer">
            <p>Terima kasih atas partisipasi Anda.</p>
            <div class="signature">
                <p>TTD,</p>
                <p><strong>Nama Penyelenggara</strong></p>
            </div>
            <div class="date">
                <p>Tanggal Sertifikat: <?php echo date("d F Y"); ?></p>
            </div>
        </div>

        <div class="qr-code">
            <p>Scan QR code untuk verifikasi:</p>
            <img src="path/to/qr-code.png" alt="QR Code">
        </div>
    </div>

</body>
</html>
