<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

session_start(); // Mulai sesi

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "absensi_online";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pastikan email datang dari form
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Query untuk mendapatkan kode verifikasi dari database
        $stmt = $pdo->prepare("SELECT kode_verifikasi, created_at FROM password_reset WHERE email = :email ORDER BY created_at DESC LIMIT 1");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $kodeVerifikasi = $result['kode_verifikasi'];
            $createdAt = new DateTime($result['created_at']);
            $now = new DateTime();

            // Cek apakah kode verifikasi sudah kadaluarsa (lebih dari 24 jam)
            $interval = $createdAt->diff($now);
            if ($interval->h >= 24) {
                echo 'Kode verifikasi telah kadaluarsa. Silakan coba lagi.';
                exit();
            }

            // Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'tomingselingga2512@gmail.com'; // Ganti dengan email Anda
                $mail->Password   = 'gxdffjwkukztjbsv';    // Ganti dengan app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Recipients
                $mail->setFrom('no-reply@example.com', 'absensi_online');
                $mail->addAddress($email, 'Pengguna');

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Permintaan Penggantian Password';
                $mail->Body    = '<p>Halo,</p>
                                   <p>Kami menerima permintaan untuk mengganti password akun Anda. Jika Anda tidak meminta penggantian ini, abaikan email ini.</p>
                                   <p>Berikut adalah kode verifikasi untuk mengganti password Anda:</p>
                                   <h2 style="text-align: center;">' . htmlspecialchars($kodeVerifikasi) . '</h2>
                                   <p>Harap diingat bahwa kode ini hanya berlaku selama 24 jam.</p>
                                   <p>Terima kasih,</p>
                                   <p>Tim Kami</p>';
                $mail->AltBody = "Halo,\n\nKami menerima permintaan untuk mengganti password akun Anda. Jika Anda tidak meminta penggantian ini, abaikan email ini.\n\nBerikut adalah kode verifikasi untuk mengganti password Anda:\n$kodeVerifikasi\n\nHarap diingat bahwa kode ini hanya berlaku selama 24 jam.\n\nTerima kasih,\nTim Kami";

                $mail->send();

                // Simpan email ke sesi dan redirect ke halaman masukkan kode
                $_SESSION['email'] = $email;
                header("Location: masukkan_kode.php");
                exit();
            } catch (Exception $e) {
                echo "Email tidak dapat dikirim. Kesalahan: {$mail->ErrorInfo}";
            }
        } else {
            echo 'Kode verifikasi tidak ditemukan untuk email ini.';
        }
    } else {
        echo 'Email tidak ditemukan.';
    }
} catch (PDOException $e) {
    echo 'Koneksi ke database gagal: ' . $e->getMessage();
}
?>
