<?php

require_once '../config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('Location: ../login.php');
    exit; // Tambahkan exit agar kode berhenti setelah redirect
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>
    <h2 class="text-center fw-bold text-primary mt-3">Dashboard</h2>
    <div class="container d-flex justify-content-center align-items-center py-4">
        <div class="row g-4">
            <?php
            $stats = [
                ["query" => "SELECT total_price FROM orders WHERE payment_status = 'pending'", "label" => "Total Pendings", "icon" => "bi-hourglass-split"],
                ["query" => "SELECT total_price FROM orders WHERE payment_status = 'completed'", "label" => "Completed Payments", "icon" => "bi-check-circle"],
                ["query" => "SELECT * FROM products", "label" => "Total Products", "icon" => "bi-box"],
                ["query" => "SELECT * FROM orders", "label" => "Orders Placed", "icon" => "bi-cart"],
                ["query" => "SELECT * FROM users WHERE role = 'user'", "label" => "Users", "icon" => "bi-person"],
                ["query" => "SELECT * FROM users WHERE role = 'admin'", "label" => "Admins", "icon" => "bi-person-badge"],
                ["query" => "SELECT * FROM users", "label" => "Total Accounts", "icon" => "bi-people"],
                ["query" => "SELECT * FROM messages", "label" => "Total Messages", "icon" => "bi-envelope"],
            ];

            $color_classes = [
                "Total Pendings" => "text-warning",
                "Completed Payments" => "text-success",
                "Total Products" => "text-primary",
                "Orders Placed" => "text-info",
                "Users" => "text-dark",
                "Admins" => "text-dark",
                "Total Accounts" => "text-secondary",
                "Total Messages" => "text-danger",
            ];


            foreach ($stats as $stat) {
                $result = mysqli_query($conn, $stat["query"]) or die('Query failed');
                $count = (strpos($stat["query"], "total_price") !== false) ? 0 : mysqli_num_rows($result);
                if (strpos($stat["query"], "total_price") !== false) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $count += $row['total_price'];
                    }
                    $count = "IDR " . number_format($count, 0, ',', '.');
                }
            ?>
                <div class="col-md-3">
                    <div class="card shadow-sm text-center p-3">
                        <div class="card-body">
                            <i class="bi <?= $stat["icon"] ?> fs-1 text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $stat["label"] ?>"></i>
                            <h3 class="my-2 <?= $color_classes[$stat["label"]]; ?> animate-number"> <?= $count; ?> </h3>
                            <p class="text-muted"> <?= $stat["label"] ?> </p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js" defer></script>
</body>

</html>