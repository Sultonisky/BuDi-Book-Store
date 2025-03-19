<?php

require_once '../config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('Location: ../login.php');
    exit;
}

if (isset($_POST['send_msg_btn'])) {

    $input_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['input_name']));
    $input_email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['input_email']));
    $input_message = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['input_message']));

    $stmt = $conn->prepare("SELECT * FROM messages WHERE name = ? AND email = ? AND message = ?");
    $stmt->bind_param("sss", $input_name, $input_email, $input_message);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> Messages already send!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO messages (user_id, name, email, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $input_name, $input_email, $input_message);
        $insert_success = $stmt->execute();

        if ($insert_success) {
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> Messages send successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle"></i> Messages could not be send!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact BuDi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>


    <!-- 🔹 Contact Us Section -->
    <section class="container py-3">
        <?php if (!empty($message)) {
            echo $message;
        } ?>
        <h2 class="fw-bold text-center text-primary mb-5">Get in Touch</h2>

        <div class="row g-5">
            <!-- Kolom Kiri: Formulir Kontak -->
            <div class="col-lg-6">
                <div class="card shadow-sm p-4">
                    <h5 class="fw-bold text-primary">Send Us message</h5>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Username</label>
                            <input type="text" class="form-control" id="name" name="input_name" placeholder="your name..." required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="input_email" placeholder="your email..." required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="input_message" rows="4" placeholder="write a message for us" required></textarea>
                        </div>
                        <button type="submit" name="send_msg_btn" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Informasi Kontak -->
            <div class="col-lg-6">
                <div class="p-5">
                    <h5 class="fw-bold text-primary">Contact Us</h5>
                    <p class="text-muted"><i class="bi bi-geo-alt"></i> Jl. Raya Buku No. 123, Jakarta, Indonesia</p>
                    <p class="text-muted"><i class="bi bi-envelope"></i> support@bukudigital.com</p>
                    <p class="text-muted"><i class="bi bi-telephone"></i> +62 777-8888-9999</p>
                    <h5 class="fw-bold text-primary mt-3">Follow Us</h5>
                    <a href="#" class="text-muted me-2"><i class="bi bi-facebook fs-4"></i></a>
                    <a href="#" class="text-muted me-2"><i class="bi bi-instagram fs-4"></i></a>
                    <a href="#" class="text-muted me-2"><i class="bi bi-twitter fs-4"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-youtube fs-4"></i></a>
                </div>
            </div>
        </div>


    </section>



    <?php require_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>