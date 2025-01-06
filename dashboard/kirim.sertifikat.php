<?php
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Ambil data peserta
    $query = "SELECT * FROM peserta WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $peserta = mysqli_fetch_assoc($result);

    $email = $peserta['email'];
    $file_path = "../uploads/sertifikat_" . $peserta['user_id'] . ".pdf";

    // Kirim email dengan sertifikat
    $to = $email;
    $subject = "Sertifikat Anda";
    $message = "Berikut adalah sertifikat Anda untuk acara " . $peserta['event_id'];
    $headers = "From: admin@example.com";

    // Tambahkan file PDF sebagai attachment
    mail($to, $subject, $message, $headers);

    echo "Sertifikat berhasil dikirim ke $email!";
}
?>
