<?php
session_start();
require_once 'config.php';


$message = "";

if (isset($_POST['submit'])) {
    $email = strtolower(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password']; // Password tanpa hash dulu

    // Cek apakah email sudah terdaftar
    $select_users = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('Query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $row = mysqli_fetch_assoc($select_users);

        // Verifikasi password dengan password_verify()
        if (password_verify($password, $row['password'])) {
            // Set session sesuai role
            if ($row['role'] == 'admin') {
                $_SESSION['admin_name'] = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_id'] = $row['id'];
                header('Location: admin/dashboard.php');
                exit();
            } elseif ($row['role'] == 'user') {
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id'] = $row['id'];
                header('Location: user/home.php');
                exit();
            }
        } else {
            // Password salah
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle"></i> Incorrect password!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    } else {
        // Email tidak ditemukan
        $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
           <i class="bi bi-exclamation-circle"></i> User not found!
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="bg-light d-flex align-items-center vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg rounded-4 p-4">
                    <h2 class="text-center mb-4">Login Now</h2>

                    <!-- Alert Message -->
                    <?php if (!empty($message)) {
                        echo $message;
                    } ?>

                    <form action="" method="post">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" required placeholder="Enter Your Email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" required placeholder="Enter Your Password">
                        </div>
                        <button type="submit" name="submit" value="register" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-right"></i> Login
                        </button>
                    </form>

                    <p class="text-center mt-3">Don't have an account? <a href="register.php">Register Here</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>