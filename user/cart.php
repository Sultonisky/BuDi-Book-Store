<?php

require_once '../config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('Location: ../login.php');
}

// 🔹 Menambah Quantity
if (isset($_POST['increase_qty'])) {
    $cart_id = intval($_POST['cart_id']);
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->close();

    header("Location: cart.php");
}

// 🔹 Mengurangi Quantity
if (isset($_POST['decrease_qty'])) {
    $cart_id = intval($_POST['cart_id']);

    // Cek jumlah sebelum dikurangi
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row['quantity'] > 1) {
            // Kurangi quantity
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE id = ?");
            $stmt->bind_param("i", $cart_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Jika quantity = 1, hapus item
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->bind_param("i", $cart_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: cart.php");
}

// 🔹 Menghapus Item dari Cart
if (isset($_POST['remove_cart_btn'])) {
    $cart_id = intval($_POST['cart_id']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->close();

    header("Location: cart.php");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart BuDi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>

    <!-- 🔹 Cart Section -->
    <section class="container py-5">
        <h2 class="text-center fw-bold">Shopping Cart</h2>

        <?php
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_items = $stmt->get_result();

        if ($cart_items->num_rows > 0) {
            $total_price = 0;
        ?>
            <div class="row justify-content-center mt-4">
                <?php while ($item = $cart_items->fetch_assoc()) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $total_price += $subtotal;
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="row g-0">
                                <div class="col-4 d-flex align-items-center">
                                    <img src="../uploaded_img/<?= $item['image']; ?>" class="img-fluid rounded" alt="<?= $item['name']; ?>">
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($item['name']); ?></h5>
                                        <p class="text-primary fw-bold">IDR <?= number_format($item['price'], 0, ',', '.'); ?></p>

                                        <!-- Form Update Quantity -->
                                        <form action="" method="post" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="cart_id" value="<?= $item['id']; ?>">

                                            <button type="submit" name="decrease_qty" class="btn btn-outline-secondary btn-sm">-</button>

                                            <input type="text" name="new_qty" value="<?= $item['quantity']; ?>" class="form-control text-center" style="width: 50px;" readonly>

                                            <button type="submit" name="increase_qty" class="btn btn-outline-secondary btn-sm">+</button>
                                        </form>

                                        <p class="mt-2">Subtotal: <span class="fw-bold">IDR <?= number_format($subtotal, 0, ',', '.'); ?></span></p>

                                        <!-- Remove Button -->
                                        <form action="" method="post">
                                            <input type="hidden" name="cart_id" value="<?= $item['id']; ?>">
                                            <button type="submit" name="remove_cart_btn" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="text-center mt-4">
                <h4>Total: <span class="text-primary fw-bold">IDR <?= number_format($total_price, 0, ',', '.'); ?></span></h4>
                <a href="shop.php" class="btn btn-outline-primary mt-2">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-success mt-2">Proceed to Checkout</a>
            </div>

        <?php } else { ?>

            <<div class="d-flex flex-column align-items-center justify-content-center">
                <div class="card text-center border-0 shadow-sm" style="max-width: 400px;">
                    <div class="card-body">
                        <i class="bi bi-cart-x text-danger" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Your cart is empty</h5>
                        <p class="text-muted">Add some products to your cart before checking out.</p>
                        <a href="shop.php" class="btn btn-primary">
                            Back to Shop
                        </a>
                    </div>
                </div>
                </div>
            <?php } ?>
    </section>




    <?php require_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>