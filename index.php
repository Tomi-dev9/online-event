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
<body>
    <?php include "layout/header.html"; ?>
    <!-- Hero Section -->
    <section class="hero">
        <h1>"Solusi terbaik untuk mencatat dan mengelola kehadiran peserta acara online."</h1>
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