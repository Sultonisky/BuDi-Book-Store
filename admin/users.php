<?php

require_once '../config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('Location: ../login.php');
    exit; // Tambahkan exit agar kode berhenti setelah redirect
}

if (isset($_POST['delete_user_btn'])) {
    $delete_user_id = intval($_POST['delete_user_id']);

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $delete_user_id);

    if ($stmt->execute()) {
        $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> User Deleted Successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle"></i> Failed to Delete User!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    }
    $stmt->close();
}

// Cek apakah ada pencarian
if (isset($_POST['search_users_btn'])) {
    $keyword = '%' . mysqli_real_escape_string($conn, $_POST['search_keyword']) . '%';
    $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? OR role LIKE ?");
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt->close();
} else {
    // Query default jika tidak ada pencarian
    $result = $conn->query("SELECT * FROM users");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>

    <?php require_once 'header.php'; ?>
    <div class="container mt-3">
        <h2 class="text-center fw-bold text-primary">Users</h2>
        <?php if (!empty($message)) {
            echo $message;
        } ?>

        <form action="" method="post">
            <div class="row justify-content-start">
                <div class="col-md-6 col-lg-4">
                    <div class="input-group mb-1">
                        <input type="text" class="form-control" name="search_keyword" placeholder="Search For...." aria-label="Search">
                        <button class="btn btn-outline-primary" name="search_users_btn" type="submit">Search</button>
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
                    <th scope="col">Role</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($result->num_rows > 0) {
                    while ($user = $result->fetch_assoc()) {
                ?>
                        <tr class="text-center">
                            <th scope="row"><?= $no++ ?></th>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= $user['email'] ?></td>
                            <td class="fw-bold <?= ($user['role'] == 'admin') ? 'text-success' : 'text-danger' ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </td>

                            <td>
                                <button type="button" class="btn btn-danger delete-user-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $user['id'] ?>">
                                    Delete
                                </button>

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
                                                <p> Are you Sure Want to Delete This User Permanently?
                                                </p>
                                                <form action="" method="post">
                                                    <input type="hidden" name="delete_user_id" id="delete_user_id">
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">cancel</button>
                                                        <button type="submit" class="btn btn-danger" name="delete_user_btn">Delete</button>
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
                        <td colspan="5" class="text-center text-muted">No Orders Placed</td>
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