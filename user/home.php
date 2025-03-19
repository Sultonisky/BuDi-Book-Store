<?php
require_once '../config.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit; // Tambahkan exit agar kode berhenti setelah redirect
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_cart_btn'])) {
    // Amankan data yang diterima dari form
    $product_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['product_name']));
    $product_price = floatval($_POST['product_price']);
    $product_qty = intval($_POST['product_qty']);
    $product_image = mysqli_real_escape_string($conn, $_POST['product_image']); // Sanitasi input gambar

    // Cek apakah produk sudah ada di dalam cart
    $stmt = $conn->prepare("SELECT id FROM cart WHERE name = ? AND user_id = ? LIMIT 1");
    $stmt->bind_param("si", $product_name, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> Product already added to cart!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        // Tutup statement sebelum melakukan insert baru
        $stmt->close();

        // Masukkan produk ke dalam cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, name, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdis", $user_id, $product_name, $product_price, $product_qty, $product_image);
        $insert_success = $stmt->execute();

        if ($insert_success) {
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> Product added to cart successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle"></i> Product could not be added!
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
    <title>Home BuDi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>
    <div class="container-fluid">
        <?php if (!empty($message)) {
            echo $message;
        } ?>


        <!-- 🔹 Hero Section -->
        <header class="bg-dark text-white text-center py-5" style="background: url('../img/bg-store2.jpg') center/cover no-repeat; min-height: 50vh;">
            <div class="container">
                <h1 class="display-4 fw-bold">Welcome to <span class="fw-bold text-primary">Buku</span><span class="fw-bold text-warning">Digital</span></h1>
                <p class="lead fw-bold">Best products at the best prices.</p>
                <a href="shop.php" class="btn btn-primary btn-lg">Shop Now</a>
            </div>
        </header>

        <!-- 🔹 Product Section -->
        <section class="container py-5">
            <h2 class="text-center d-flex justify-content-center fw-bold">Our Products</h2>
            <div class="row justify-content-center mt-4 gap-4">
                <?php
                $select_products = mysqli_query($conn, "SELECT * FROM products") or die(mysqli_error($conn));
                if (mysqli_num_rows($select_products) > 0) {
                    while ($product = mysqli_fetch_assoc($select_products)) { ?>
                        <div class="col-md-3 d-flex">
                            <div class="card shadow-sm w-100">
                                <form action="" method="post">
                                    <img src="../uploaded_img/<?= $product['image']; ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']); ?>">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                                        <p class="text-primary fw-bold">IDR. <?= number_format($product['price'], 0, ',', '.'); ?></p>

                                        <!-- 🔹 Input Jumlah Pesanan -->
                                        <div class="mb-3">
                                            <label for="quantity_<?= $product['id']; ?>" class="form-label small">Quantity</label>
                                            <input type="number" name="product_qty" id="quantity_<?= $product['id']; ?>" class="form-control text-center" value="1" min="1" required>
                                        </div>

                                        <!-- 🔹 Hidden Input untuk Mengirim Data Produk -->
                                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']); ?>">
                                        <input type="hidden" name="product_price" value="<?= $product['price']; ?>">
                                        <input type="hidden" name="product_image" value="<?= $product['image']; ?>">

                                        <!-- 🔹 Tombol Add to Cart -->
                                        <button type="submit" name="add_cart_btn" class="btn btn-outline-primary">
                                            <i class="bi bi-cart"></i> Add to Cart
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                <?php }
                } ?>
            </div>
        </section>
    </div>


    <?php require_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>