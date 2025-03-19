<?php

require_once '../config.php';
session_start();

$message = ""; // Variabel alert

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('Location: ../login.php');
    exit; // Tambahkan exit agar kode berhenti setelah redirect
}

// Add Product
if (isset($_POST['add_btn'])) {

    $product_name = ucwords(trim(htmlspecialchars(mysqli_real_escape_string($conn, $_POST['product_name']), ENT_QUOTES, 'UTF-8')));
    $price = floatval($_POST['price']); // Pastikan harga berupa angka
    $image_name = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp']; // Format yang diizinkan
    $image_new_name = uniqid() . '.' . $image_ext; // Hindari nama duplikat
    $image_folder = '../uploaded_img/' . $image_new_name;

    // Cek apakah produk sudah ada
    $stmt = $conn->prepare("SELECT name FROM products WHERE name = ?");
    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> Product Name already exists!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        // Validasi ukuran dan format gambar sebelum insert
        if (!in_array(strtolower($image_ext), $allowed_ext)) {
            $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> Invalid image format! Only JPG, JPEG, PNG, WEBP allowed.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } elseif ($image_size > 2000000) {
            $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> Image Too Large! Max 2MB.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            // Masukkan ke database
            $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $product_name, $price, $image_new_name);
            $insert_success = $stmt->execute();

            if ($insert_success) {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> Product Added Successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle"></i> Product Could not be Added!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
    }
    $stmt->close();
}

// Edit Product
if (isset($_POST['edit_btn'])) {

    $edit_id = intval($_POST['edit_id']);
    $edit_product_name = ucwords(trim(htmlspecialchars(mysqli_real_escape_string($conn, $_POST['edit_product_name']), ENT_QUOTES, 'UTF-8')));
    $edit_price = floatval($_POST['edit_price']);
    $image_name = $_FILES['edit_image']['name'];
    $image_size = $_FILES['edit_image']['size'];
    $image_tmp_name = $_FILES['edit_image']['tmp_name'];
    $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp']; // Format yang diizinkan
    $image_new_name = uniqid() . '.' . $image_ext;
    $image_folder = '../uploaded_img/' . $image_new_name;

    // Cek apakah produk dengan nama yang sama sudah ada (kecuali produk yang sedang diedit)
    $stmt = $conn->prepare("SELECT id FROM products WHERE name = ? AND id != ?");
    $stmt->bind_param("si", $edit_product_name, $edit_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> Product Name already exists!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        // Periksa apakah ada gambar baru yang diupload
        if (!empty($image_name)) {
            if (!in_array(strtolower($image_ext), $allowed_ext)) {
                $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> Invalid image format! Only JPG, JPEG, PNG, WEBP allowed.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } elseif ($image_size > 2000000) {
                $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> Image Too Large! Max 2MB.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                // Hapus gambar lama sebelum mengganti dengan yang baru
                $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_assoc();
                $old_image_path = '../uploaded_img/' . $data['image'];

                if (file_exists($old_image_path) && !empty($data['image'])) {
                    unlink($old_image_path); // Hapus gambar lama
                }

                // Update dengan gambar baru
                $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ? WHERE id = ?");
                $stmt->bind_param("sdsi", $edit_product_name, $edit_price, $image_new_name, $edit_id);
                $update_success = $stmt->execute();

                if ($update_success) {
                    move_uploaded_file($image_tmp_name, $image_folder);
                    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Product Updated Successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                } else {
                    $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle"></i> Failed to Update Product!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
            }
        } else {
            // Jika tidak ada gambar baru, update hanya name & price
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
            $stmt->bind_param("sdi", $edit_product_name, $edit_price, $edit_id);
            $update_success = $stmt->execute();

            if ($update_success) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> Product Updated Successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle"></i> Failed to Update Product!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
    }
    $stmt->close();
}

// Cek apakah ada pencarian
if (isset($_POST['search_product_btn'])) {
    $keyword = '%' . mysqli_real_escape_string($conn, $_POST['search_keyword']) . '%';
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR price LIKE ? OR image LIKE ?");
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();
} else {
    // Query default jika tidak ada pencarian
    $result = $conn->query("SELECT * FROM products");
}

if (isset($_POST['delete_btn'])) {
    $delete_id = $_POST['delete_btn'];
    mysqli_query($conn, "DELETE * FROM products WHERE id = '$delete_id'") or die('query failed');
    header('Location:products.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Products</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>



    <div class="container mt-3">
        <h2 class="text-center fw-bold text-primary">Products</h2>
        <?php if (!empty($message)) {
            echo $message;
        } ?>

        <?php if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']); // Hapus pesan setelah ditampilkan 
        } ?>


        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Add Product
        </button>
        <form action="" method="post">
            <div class="row justify-content-start">
                <div class="col-md-6 col-lg-4">
                    <div class="input-group mb-1">
                        <input type="text" class="form-control" name="search_keyword" placeholder="Search For...." aria-label="Search">
                        <button class="btn btn-outline-primary" name="search_product_btn" type="submit">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <table class="table table-hover mt-3 table-bordered">
            <thead>
                <tr class="table-primary text-center">
                    <th scope="col">No</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Image</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($result->num_rows > 0) {
                    while ($product = $result->fetch_assoc()) {
                ?>
                        <tr class="text-center">
                            <th scope="row"><?= $no++ ?></th>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td>IDR.<?= number_format($product['price'], 0, ', ', '.') ?></td>
                            <td><img src="../uploaded_img/<?= htmlspecialchars($product['image']) ?>" height="100" width="100" style="object-fit: contain; border-radius: 5px;" alt="product image"></td>
                            <td>
                                <button type="button" class="btn btn-warning edit-btn"
                                    data-id="<?= $product['id'] ?>"
                                    data-name="<?= htmlspecialchars($product['name']) ?>"
                                    data-price="<?= $product['price'] ?>"
                                    data-image="../uploaded_img/<?= $product['image'] ?>"
                                    data-bs-toggle="modal" data-bs-target="#editModal">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $product['id'] ?>">
                                    Hapus
                                </button>
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal<?= $product['id'] ?>" tabindex="-1"
                                    aria-labelledby="deleteModalLabel<?= $product['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel<?= $product['id'] ?>">
                                                    Confirm Delete
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you Sure Want to Delete <b><?= htmlspecialchars($product['name']) ?></b> Permanently?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">cancel</button>
                                                <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-danger">Delete</a>
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
                        <td colspan="5" class="text-center text-muted">No products available</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="ProductName" class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" id="ProductName" aria-describedby="emailHelp">
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Product Price</label>
                            <input type="number" name="price" class="form-control" id="price" aria-describedby="emailHelp">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" name="image" class="form-control" id="image" aria-describedby="emailHelp" accept="image/jpg, image/jpeg, image/png">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_btn" class="btn btn-primary">Add Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Product -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="edit_id" id="edit_id"> <!-- ID Produk -->

                        <div class="mb-3 text-center">
                            <img id="edit_preview" src="" alt="Product Image" class="img-fluid rounded" style="max-height: 150px;">
                        </div>

                        <div class="mb-3">
                            <label for="edit_product_name" class="form-label">Product Name</label>
                            <input type="text" name="edit_product_name" class="form-control" id="edit_product_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Product Price</label>
                            <input type="number" name="edit_price" class="form-control" id="edit_price" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Change Image</label>
                            <input type="file" name="edit_image" class="form-control" id="edit_image" accept="image/jpg, image/jpeg, image/png, image/webp">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit_btn" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js" defer></script>
</body>

</html>