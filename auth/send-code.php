<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
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

    // Email user (replace with actual email address from your logic)
    $email = 'user@example.com';

    // Query to get the verification code from the database
    $stmt = $pdo->prepare("SELECT kode_verifikasi FROM password_reset WHERE email = :email ORDER BY created_at DESC LIMIT 1");
    $stmt->execute(['email' => $email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $kodeVerifikasi = $result['kode_verifikasi'];

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Nonaktifkan debug output untuk produksi
            $mail->isSMTP();                                         //Kirim menggunakan SMTP
            $mail->Host       = 'smtp.gmail.com';                    //Server SMTP
            $mail->SMTPAuth   = true;                                //Aktifkan autentikasi SMTP
            $mail->Username   = 'linggtomii@gmail.com';              //SMTP username
            $mail->Password   = 'szvp hadm bdgy pjoy';               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         //Enkripsi TLS implisit
            $mail->Port       = 465;                                 //Port TCP untuk TLS implisit

            //Recipients
            $mail->setFrom('no-reply@example.com', 'Sistem Kami');
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
} catch (PDOException $e) {
    echo 'Koneksi ke database gagal: ' . $e->getMessage();
}

?>
