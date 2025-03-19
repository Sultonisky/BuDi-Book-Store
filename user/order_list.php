<?php

require_once '../config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('Location: ../login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders BuDi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .badge-status {
            font-size: 0.85rem;
            padding: 0.4em 0.8em;
        }
    </style>
</head>

<body>

    <?php require_once 'header.php'; ?>

    <div class="container my-4">
        <h2 class="text-center fw-bold text-primary mb-4">Orders Placed</h2>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Placed on</th>
                                <th>Address</th>
                                <th>Payment</th>
                                <th>Product</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($order = $result->fetch_assoc()) {
                                    $status_class = ($order['payment_status'] == 'pending') ? 'badge bg-warning text-dark badge-status' : 'badge bg-success badge-status';
                            ?>
                                    <tr>
                                        <td>#<?= $no++; ?></td>
                                        <td><?= htmlspecialchars($order['name']) ?></td>
                                        <td><?= htmlspecialchars($order['placed_on']) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($order['address']) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($order['method']) ?></td>
                                        <td><?= htmlspecialchars($order['total_products']) ?></td>
                                        <td>IDR <?= number_format($order['total_price'], 0, ',', '.'); ?></td>
                                        <td><span class="<?= $status_class ?>"><?= htmlspecialchars($order['payment_status']) ?></span></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="8" class="text-muted text-center py-3">Belum ada pesanan.</td></tr>';
                            }
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-4">
                    <a href="shop.php" class="btn btn-outline-primary px-4">
                        <i class="bi bi-shop"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>