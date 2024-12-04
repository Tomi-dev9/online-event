<?php
session_start();
include "services/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Validasi CSRF token
    if (!isset($_SESSION['csrf_token']) || $csrfToken !== $_SESSION['csrf_token']) {
        echo "<script>
                alert('Invalid CSRF token.');
                window.location.href = 'login.php';
              </script>";
        exit();
    }

    // Validasi input kosong
    if (empty($email) || empty($password)) {
        echo "<script>
                alert('Email dan password tidak boleh kosong.');
                window.location.href = 'login.php';
              </script>";
        exit();
    }

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Format email tidak valid.');
                window.location.href = 'login.php';
              </script>";
        exit();
    }

    try {
        // Cek apakah email ada di database tabel admin
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password
        if ($admin && password_verify($password, $admin['PASSWORD'])) {
            $_SESSION['email'] = $admin['email'];
            header("Location: dashboard/dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
