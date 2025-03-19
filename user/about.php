<?php

require_once '../config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('Location: ../login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About BuDi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>

    <!-- 🔹 About Us Section -->
    <section class="container py-5">
        <div class="row align-items-center">
            <!-- Kolom Kiri: Gambar -->
            <div class="col-md-6 text-center">
                <img src="../img/bg-store.jpg" alt="About BukuDigital" class="img-fluid rounded shadow">
            </div>

            <!-- Kolom Kanan: Deskripsi -->
            <div class="col-md-6">
                <h2 class="fw-bold text-primary">About Us</h2>
                <p class="text-muted">
                    Selamat datang di <span class="fw-bold text-primary">Buku</span><span class="fw-bold text-warning">Digital</span>, tempat terbaik untuk menemukan berbagai buku digital berkualitas dengan harga terjangkau.

                    Kami memahami bahwa dunia digital telah mengubah cara kita mengakses informasi. Oleh karena itu, kami menyediakan beragam koleksi buku, mulai dari novel fiksi, buku edukasi, teknologi, bisnis, hingga pengembangan diri. Semua tersedia dalam format digital yang bisa dibaca kapan saja dan di mana saja.
                </p>

                <h5 class="fw-bold text-primary mt-3">Kenapa Memilih <span class="fw-bold text-primary">"Buku</span><span class="fw-bold text-warning">Digital"</span>?</h5>
                <ul class="text-muted list-unstyled">
                    <li><i class="bi bi-check-circle text-primary"></i> Ribuan judul buku tersedia dalam berbagai kategori.</li>
                    <li><i class="bi bi-check-circle text-primary"></i> Akses cepat & mudah tanpa harus ke toko fisik.</li>
                    <li><i class="bi bi-check-circle text-primary"></i> Harga terjangkau dengan berbagai diskon menarik.</li>
                    <li><i class="bi bi-check-circle text-primary"></i> Dukungan pelanggan 24/7 untuk membantu kebutuhan Anda.</li>
                </ul>

                <p class="text-muted">
                    Kami percaya bahwa membaca adalah jendela dunia, dan dengan teknologi, jendela itu bisa terbuka lebih luas dari sebelumnya. Bergabunglah dengan ribuan pelanggan lain yang telah menikmati pengalaman membaca digital yang lebih mudah, nyaman, dan menyenangkan!
                </p>

                <!-- Tombol Call to Action -->
                <a href="shop.php" class="btn btn-primary">
                    Jelajahi Koleksi Buku <i class="bi bi-arrow-right"></i>
                </a>
                <a href="contact.php" class="btn btn-outline-primary ms-2">
                    Hubungi Kami <i class="bi bi-chat-dots"></i>
                </a>
            </div>
        </div>
    </section>



    <?php require_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>