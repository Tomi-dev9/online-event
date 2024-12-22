<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SikilatAbsensi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header */
        .header {
            background-color: #007bff;
            padding: 1rem 0;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar {
            display: flex;
            gap: 1rem;
        }

        .navbar a {
            text-decoration: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }

        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(to right, #007bff, #5bc0de);
            color: white;
            padding: 5rem 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: white;
            color: #007bff;
            padding: 0.8rem 2rem;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            color: white;
        }

        /* Event Section */
        .event-section {
            background-color: #f8f9fa;
            padding: 3rem 0;
        }

        .event-section h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #007bff;
        }

        .event-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .event-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .event-card:hover {
            transform: translateY(-10px);
        }

        .event-card img {
            width: 100%;
            height: auto;
        }

        .card-content {
            padding: 1rem;
        }

        .card-content h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .btn-secondary {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 0.5rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            margin-top: 1rem;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-secondary:hover {
            background-color: #0056b3;
        }

        /* Responsive Navbar */
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .navbar {
                flex-direction: column;
                display: none;
                background: #007bff;
                position: absolute;
                top: 100%;
                right: 0;
                left: 0;
                padding: 1rem;
            }

            .navbar.active {
                display: flex;
            }
        }

        .footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">SikilatAbsensi</div>
            <button class="menu-toggle" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="navbar">
                <a class="login" href="./auth/login.php">Login</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Solusi Absensi untuk Kegiatan Online dan Offline</h1>
            <p>Membantu mencatat kehadiran dan mengelola sertifikat acara Anda dengan mudah.</p>
            <a href="./auth/login.php" class="btn-primary">Lihat Event</a>
        </div>
    </section>

    <!-- Event Section -->
    <section id="event-section" class="event-section">
        <div class="container">
            <h2></h2>
            <div class="event-cards">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <img src="<?= htmlspecialchars($event['image']) ?>" alt="Poster <?= htmlspecialchars($event['event_name']) ?>">
                            <div class="card-content">
                                <h3><?= htmlspecialchars($event['event_name']) ?></h3>
                                <p><?= htmlspecialchars($event['event_date']) ?></p>
                                <a href="daftar_event.php?id=<?= htmlspecialchars($event['id']) ?>" class="btn-secondary">Daftar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align:center;">.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 SikilatAbsensi. All Rights Reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.querySelector('.menu-toggle');
            const navbar = document.querySelector('.navbar');

            menuToggle.addEventListener('click', () => {
                navbar.classList.toggle('active');
            });
        });
    </script>
</body>
</html>
