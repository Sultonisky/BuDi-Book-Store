<?php

require_once '../config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('Location: ../login.php');
    exit; // Tambahkan exit agar kode berhenti setelah redirect
}


if (isset($_POST['edit_order_btn'])) {

    // validasi dan filter input
    $order_id = intval($_POST['edit_order_id']);
    $order_status = trim($_POST['edit_status']);

    // pastikan status hanya "pending" dan "completed"
    $allowed_status = ['pending', 'completed'];
    if (!in_array($order_status, $allowed_status)) {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> Invalid Status!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
        $stmt->bind_param('si', $order_status, $order_id);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> Order Status Changed Successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle"></i> Failed to update order status!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
        $stmt->close();
    }
}

// Cek apakah ada pencarian
if (isset($_POST['search_orders_btn'])) {
    $keyword = '%' . mysqli_real_escape_string($conn, $_POST['search_keyword']) . '%';
    $stmt = $conn->prepare("SELECT * FROM orders WHERE name LIKE ? OR number LIKE ? OR email LIKE ? OR method LIKE ? OR address LIKE ? OR placed_on LIKE ? OR payment_status LIKE ?");
    $stmt->bind_param("sssssss", $keyword, $keyword, $keyword, $keyword, $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();
} else {
    // Query default jika tidak ada pencarian
    $result = $conn->query("SELECT * FROM orders");
}

if (isset($_POST['delete_order_btn'])) {
    $delete_order_id = intval($_POST['delete_order_id']);

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param('i', $delete_order_id);

    if ($stmt->execute()) {
        $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> Order Deleted Successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle"></i> Failed to Delete Order!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    }
    $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>

    <div class="container mt-3">
        <h2 class="text-center fw-bold text-primary">Placed Orders</h2>
        <?php if (!empty($message)) {
            echo $message;
        } ?>

        <form action="" method="post">
            <div class="row justify-content-start">
                <div class="col-md-6 col-lg-4">
                    <div class="input-group mb-1">
                        <input type="text" class="form-control" name="search_keyword" placeholder="Search For...." aria-label="Search">
                        <button class="btn btn-outline-primary" name="search_orders_btn" type="submit">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <table class="table table-hover table-bordered mt-3 ">
            <thead>
                <tr class="table-primary text-center ">
                    <th scope="col">No</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Address</th>
                    <th scope="col">Products</th>
                    <th scope="col">Totals</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($result->num_rows > 0) {
                    while ($order = $result->fetch_assoc()) {
                ?>
                        <tr class="text-center">
                            <th scope="row"><?= $no++ ?></th>
                            <td><?= htmlspecialchars($order['name']) ?></td>
                            <td><?= $order['email'] ?></td>
                            <td><?= $order['address'] ?></td>
                            <td><?= $order['total_products'] ?></td>
                            <td>IDR. <?= number_format($order['total_price'], 0, ', ', '.') ?></td>
                            <td class="fw-bold <?= ($order['payment_status'] == 'completed') ? 'text-success' : 'text-danger' ?>">
                                <?= htmlspecialchars($order['payment_status']) ?>
                            </td>

                            <td>
                                <button type="button" class="btn btn-warning edit-order-btn btn-sm"
                                    data-id="<?= $order['id'] ?>"
                                    data-status="<?= $order['payment_status'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#editOrderModal">
                                    Update
                                </button>
                                <button type="button" class="btn btn-danger delete-order- btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $order['id'] ?>">
                                    Delete
                                </button>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#detailModal" class="btn btn-info btn-sm">
                                    Details
                                </button>

                                <!-- Modal Edit Product -->
                                <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="editOrderModalLabel">Update Status</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="post">
                                                    <input type="hidden" name="edit_order_id" id="edit_order_id"> <!-- ID Produk -->

                                                    <div class="mb-3">
                                                        <label for="edit_status" class="form-label text-start">Order Status</label>
                                                        <select class="form-select" name="edit_status" id="edit_status" aria-label="Default select example">
                                                            <option selected disabled>Select Payment Status</option>
                                                            <option value="pending">Pending</option>
                                                            <option value="completed">Completed</option>
                                                        </select>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="edit_order_btn" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal" tabindex="-1"
                                    aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">
                                                    Confirm Delete
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p> Are you Sure Want to Delete This Order Permanently?
                                                </p>
                                                <form action="" method="post">
                                                    <input type="hidden" name="delete_order_id" id="delete_order_id">
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">cancel</button>
                                                        <button type="submit" class="btn btn-danger" name="delete_order_btn">Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- modal details -->
                                <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h2 class="modal-title fs-5" id="detailModalLabel">Detail Order</h2>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form class="text-start">
                                                    <div class="mb-3">
                                                        <label for="user_id" class="form-label">User ID</label>
                                                        <input type="" disabled id="user_id" value="<?= $order['user_id'] ?>" class="form-control">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">User Name</label>
                                                        <input type="" disabled id="name" value="<?= htmlspecialchars($order['name']) ?>" class="form-control">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="number" class="form-label">Number</label>
                                                        <input type="" disabled id="number" value="<?= $order['number'] ?>" class="form-control">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">Email</label>
                                                        <input type="" disabled id="email" value="<?= $order['email'] ?>" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="method" class="form-label">Payment Method</label>
                                                        <input type="" disabled id="method" value="<?= $order['method'] ?>" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="address" class="form-label">Address</label>
                                                        <input type="" disabled id="address" value="<?= $order['address'] ?>" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="total_products" class="form-label">Total Products</label>
                                                        <input type="" disabled id="total_products" value="<?= $order['total_products'] ?>" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="total_price" class="form-label">Total Price</label>
                                                        <input type="" disabled id="total_price" value="<?= $order['total_price'] ?>" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="placed_on" class="form-label">Placed On</label>
                                                        <input type="" disabled id="placed_on" value="<?= $order['placed_on'] ?>" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="payment_status" class="form-label">Payment Status</label>
                                                        <input type="" disabled id="payment_status" value="<?= $order['payment_status'] ?>" class="form-control">
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="11" class="text-center text-muted">No Orders Placed</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js" defer></script>
</body>

</html>