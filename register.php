<?php

require_once 'config.php';

$message = ""; // Variabel alert

if (isset($_POST['submit'])) {
    $name = strtolower(stripslashes(mysqli_real_escape_string($conn, $_POST['name'])));
    $email = strtolower(stripslashes(mysqli_real_escape_string($conn, $_POST['email'])));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $conf_password = $_POST['conf_password'];
    $role = $_POST['role'];

    // Cek apakah email sudah terdaftar
    $select_users = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('Query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
           <i class="bi bi-exclamation-circle"></i> User already exists!
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        if ($_POST['password'] !== $conf_password) {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle"></i> Confirm Password does not match!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')") or die('Query failed');
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> Registration Successful!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            header('Location:login.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="bg-light d-flex align-items-center vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg rounded-4 p-4">
                    <h2 class="text-center mb-4">Register</h2>

                    <!-- Alert Message -->
                    <?php if (!empty($message)) {
                        echo $message;
                    } ?>

                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Username</label>
                            <input type="text" name="name" class="form-control" id="name" required placeholder="Enter Your Username">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" required placeholder="Enter Your Email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" required placeholder="Enter Your Password">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" name="conf_password" class="form-control" id="confirm_password" required placeholder="Confirm Your Password">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Select Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option selected disabled>Choose role</option>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <button type="submit" name="submit" value="register" class="btn btn-primary w-100">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </form>

                    <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>