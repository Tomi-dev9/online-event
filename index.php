<?php include './services/services.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SikilatAbsensi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>
<header class="header">
    <div class="logo">SikilatAbsensi</div>
    <nav class="navbar">
        <a href="#">Home</a>
        <a href="./peserta/sertifikat.php">Sertifikat</a>
        <a class="login" href="./auth/login.php">Log In</a>
    </nav>
    <button class="menu-toggle" aria-label="Toggle menu">☰</button>
</header>

<style>
    /* Base styles */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #5BC0DE;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .logo {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }

    .navbar {
        display: flex;
        gap: 1rem;
    }

    .navbar a {
        text-decoration: none;
        color: #333;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .navbar a:hover {
        background-color: #5BC0DE;
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #333;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            align-items: flex-start;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            display: none;
            width: 100%;
        }

        .navbar a {
            width: 100%;
            text-align: left;
        }

        .menu-toggle {
            display: block;
        }

        .navbar.active {
            display: flex;
        }
    }
</style>

<script>
    // JavaScript to toggle the navbar visibility
    const menuToggle = document.querySelector('.menu-toggle');
    const navbar = document.querySelector('.navbar');

    menuToggle.addEventListener('click', () => {
        navbar.classList.toggle('active');
    });
</script>

<body>
    <!-- Hero Section -->
    <section class="hero">
        <h1>"Solusi Absensi untuk Kegiatan Online dan Offline dengan SikilatAbsensi"</h1>
    </section>

    <!-- Event Section -->
    <section class="event-section">
        <h2>Event yang sedang berlangsung</h2>
        <div class="event-cards">
            <div class="event-card">
                <img src="/api/placeholder/400/320" alt="Webinar 1">
                <div class="card-content">
                    <h3>Webinar 1</h3>
                    <a href="#" class="btn">Daftar</a>
                </div>
            </div>
            <div class="event-card">
                <img src="/api/placeholder/400/320" alt="Webinar 2">
                <div class="card-content">
                    <h3>Webinar 2</h3>
                    <a href="#" class="btn">Daftar</a>
                </div>
            </div>
            <div class="event-card">
                <img src="/api/placeholder/400/320" alt="Webinar 3">
                <div class="card-content">
                    <h3>Webinar 3</h3>
                    <a href="#" class="btn">Daftar</a>
                </div>
            </div>
        </div>
    </section>
    <?php include "layout/footer.html"; ?>
</body>
</html>