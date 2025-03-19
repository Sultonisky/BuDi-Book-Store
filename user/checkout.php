<?php

require_once '../config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('Location: ../login.php');
    exit();
}

if (isset($_POST['order_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = intval($_POST['number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $placed_on = date('d-M-Y');

    $cart_total = 0;
    $cart_items = [];

    $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_items = $cart_query->get_result();

    if ($cart_items->num_rows > 0) {
        while ($cart_item = $cart_items->fetch_assoc()) {
            $cart_products[] = $cart_item['name'] . " (x" . $cart_item['quantity'] . ")";
            $cart_total += $cart_item['price'] * $cart_item['quantity'];
        }
    }

    $total_products = implode(', ', $cart_products);
    $total_price = $cart_total;

    $order_query = $conn->prepare("SELECT * FROM orders WHERE name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ? AND placed_on = ?");
    $order_query->bind_param("sisssssi", $name, $number, $email, $payment_method, $address, $total_products, $total_price, $placed_on);
    $order_query->execute();
    $existing_order = $order_query->get_result();

    if ($cart_total == 0) {
        $message = '<p class="text-center text-muted mt-4">Your cart is empty.</p>';
    } else {
        if ($existing_order->num_rows > 0) {
            $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> This Order Already Exists!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            $insert_order = $conn->prepare("INSERT INTO orders (user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_order->bind_param("isissssis", $user_id, $name, $number, $email, $payment_method, $address, $total_products, $total_price, $placed_on);
            $insert_success = $insert_order->execute();

            if ($insert_success) {
                // Simpan informasi pesanan ke session
                $_SESSION['insert_success'] = true;
                $_SESSION['placed_on'] = $placed_on;
                $_SESSION['total_products'] = $total_products;
                $_SESSION['total_price'] = $total_price;
                $_SESSION['payment_method'] = $payment_method;

                $cleart_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                $cleart_cart->bind_param("i", $user_id);
                $cleart_cart->execute();
                $cleart_cart->close();

                header('Location: orders.php');
                exit();
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle"></i> Orders Could not be Placed!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
            $insert_order->close();
        }
    }
    $order_query->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout BuDi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>

    <div class="container my-3">
        <h2 class="text-center fw-bold text-primary mb-4">Checkout</h2>

        <div class="row">
            <!-- Form Data Pembeli -->
            <div class="col-md-6">
                <h4>Billing Details</h4>

                <?php
                $grand_total = 0;
                $cart_items_data = [];

                $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $select_cart->bind_param("i", $user_id);
                $select_cart->execute();
                $cart_items = $select_cart->get_result();

                if ($cart_items->num_rows > 0) {
                    while ($cart_item = $cart_items->fetch_assoc()) {
                        $cart_items_data[] = $cart_item;
                        $grand_total += $cart_item['price'] * $cart_item['quantity'];
                    }
                    $shipping = ($grand_total > 0) ? $grand_total * 0.01 : 0;

                ?>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="COD">Cash on Delivery</option>
                            </select>
                        </div>

            </div>

            <!-- Ringkasan Pesanan -->
            <div class="col-md-6">
                <h4>Order Summary</h4>
                <div class="border p-3">
                    <ul class="list-group mb-3">
                        <?php foreach ($cart_items_data as $cart_item) : ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= htmlspecialchars($cart_item['name']); ?> ( x<?= $cart_item['quantity']; ?> )</span>
                                <strong>IDR <?= number_format($cart_item['price'] * $cart_item['quantity'], 0, ',', '.'); ?></strong>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Shipping</span>
                            <strong>IDR <?= number_format($shipping, 0, ',', '.'); ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-primary text-white">
                            <strong>Total</strong>
                            <strong>IDR <?= number_format($grand_total + $shipping, 0, ',', '.'); ?></strong>
                        </li>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="shop.php" class="btn btn-outline-primary">Back to Shop</a>
                        <button type="submit" class="btn btn-success" name="order_btn">Proceed to Checkout</button>
                    </div>
                </div>
            <?php
                } else {
            ?>
                <div class="d-flex flex-column align-items-center justify-content-center mt-5">
                    <div class="card text-center border-0 shadow-sm p-4" style="max-width: 400px;">
                        <div class="card-body">
                            <i class="bi bi-cart-x text-danger" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Your cart is empty</h5>
                            <p class="text-muted">Add some products to your cart before checking out.</p>
                            <a href="shop.php" class="btn btn-primary">
                                <i class="bi bi-shop"></i> Back to Shop
                            </a>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>
            </form>
            </div>
        </div>
    </div>



    <?php require_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>