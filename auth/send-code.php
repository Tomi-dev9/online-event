<?php
//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Database connection
$host = 'localhost';
$db = 'absensi_online';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
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

            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Nonaktifkan debug output untuk produksi
                $mail->isSMTP();                                         //Kirim menggunakan SMTP
                $mail->Host       = 'smtp.gmail.com';                    //Server SMTP
                $mail->SMTPAuth   = true;                                //Aktifkan autentikasi SMTP
                $mail->Username   = 'tomingselingga2512@gmail.com';              //SMTP username
                $mail->Password   = 'gxdffjwkukztjbsv';               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         //Enkripsi TLS implisit
                $mail->Port       = 465;                                 //Port TCP untuk TLS implisit

                //Recipients
                $mail->setFrom('no-reply@example.com', 'absensi_online');
                $mail->addAddress($email, 'Pengguna');                  //Email penerima

                //Content
                $mail->isHTML(true);                                     //Set format email ke HTML
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
                echo 'Email penggantian password berhasil dikirim.';
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
