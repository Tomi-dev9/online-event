<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tomingselingga2512@gmail.com';
        $mail->Password = 'gxdffjwkukztjbsv';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Pengaturan email
        $mail->setFrom('no-reply@example.com', 'Admin Absensi');
        $mail->addAddress($email);
        $mail->Subject = 'Sertifikat Acara';
        $mail->Body = 'Berikut adalah sertifikat Anda. Terima kasih!';
        $mail->addAttachment('sertifikat/sertifikat.pdf'); // Lampiran sertifikat

        $mail->send();
        echo "Sertifikat berhasil dikirim ke $email.";
    } catch (Exception $e) {
        echo "Gagal mengirim sertifikat: {$mail->ErrorInfo}";
    }
}
?>
