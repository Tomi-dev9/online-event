<?php

include 'services/koneksi.php';

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);

   
    $stmt = $conn->prepare("SELECT link_sertifikat FROM peserta WHERE nama = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $linkSertifikat = $row['link_sertifikat'];
    } else {
        $error = "Sertifikat tidak ditemukan untuk nama: " . htmlspecialchars($name);
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unduh Sertifikat</title>
    
</head>
<body>
    <div class="container">
        <h1>Terima Kasih Sudah Mengikuti Acara Kami</h1>
        <p>Untuk sertifikat Anda, silakan isi nama di bawah ini:</p>
        <form method="POST">
            <input type="text" name="name" placeholder="Masukkan nama Anda" required>
            <br>
            <button type="submit" name="submit">Unduh Sertifikat</button>
        </form>

        <?php
        if (!empty($error)) {
            echo '<p class="error">' . $error . '</p>';
        }

        if (!empty($linkSertifikat)) {
            echo '<p class="success">Unduh sertifikat Anda di sini: <a href="' . htmlspecialchars($linkSertifikat) . '" target="_blank">[Link Sertifikat]</a></p>';
        }
        ?>
    </div>
</body>
</html>