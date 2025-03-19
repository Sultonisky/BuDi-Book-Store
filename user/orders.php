<?php

require_once '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit(); // Pastikan tidak ada kode lain yang dieksekusi
}

$user_id = $_SESSION['user_id'];

$insert_success = $_SESSION['insert_success'] ?? false;
$placed_on = $_SESSION['placed_on'] ?? '-';
$total_products = $_SESSION['total_products'] ?? '-';
$total_price = $_SESSION['total_price'] ?? 0;
$payment_method = $_SESSION['payment_method'] ?? '-';

unset($_SESSION['insert_success']); // Hapus agar tidak muncul terus menerus
unset($_SESSION['placed_on']);
unset($_SESSION['total_products']);
unset($_SESSION['total_price']);
unset($_SESSION['payment_method']);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders BuDi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>

    <?php if (!empty($insert_success) && $insert_success): ?>
        <div class="container my-2">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card text-center border-0 shadow-sm p-4">
                        <div class="card-body">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h4 class="mt-3 text-success">Pesanan Berhasil!</h4>
                            <p class="text-muted">Terima kasih telah berbelanja di toko kami. Pesanan Anda sedang diproses.</p>

                            <ul class="list-group text-start mb-3">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><i class="bi bi-calendar-check"></i> Tanggal Pesanan:</span>
                                    <strong><?= isset($placed_on) ? htmlspecialchars($placed_on) : '-'; ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><i class="bi bi-box"></i> Produk:</span>
                                    <strong><?= isset($total_products) ? htmlspecialchars($total_products) : '-'; ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><i class="bi bi-cash-stack"></i> Total Harga:</span>
                                    <strong>IDR <?= isset($total_price) ? number_format($total_price, 0, ',', '.') : '0'; ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><i class="bi bi-credit-card"></i> Metode Pembayaran:</span>
                                    <strong><?= isset($payment_method) ? htmlspecialchars($payment_method) : '-'; ?></strong>
                                </li>
                            </ul>

                            <div class="d-flex justify-content-center gap-3">
                                <a href="shop.php" class="btn btn-primary">
                                    <i class="bi bi-shop"></i> Lanjut Belanja
                                </a>
                                <a href="orders.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-receipt"></i> Lihat Pesanan Saya
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="container my-5">
            <h4 class="text-center text-muted">Tidak ada pesanan baru.</h4>
        </div>
    <?php endif; ?>

    <?php require_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>