<nav class="navbar navbar-expand-lg bg-primary shadow-lg" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../dashboard_admin.php">
            Budi - <span class="fw-bold text-warning">Admin Panel</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse nav-bar" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-bold" href="contact.php">Messages</a></li>
            </ul>

            <!-- Account Box -->
            <div class="dropdown ms-lg-3 order-lg-last">
                <button class="btn btn-light dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> <?= $_SESSION['admin_name']; ?>
                </button>
                <ul class="dropdown-menu bg-white dropdown-menu-end" aria-labelledby="accountDropdown">
                    <li class="dropdown-item fw-semibold text-primary"><i class="bi bi-person"></i> <?= $_SESSION['admin_name']; ?></li>
                    <li class="dropdown-item fw-semibold text-primary"><i class="bi bi-envelope"></i> <?= $_SESSION['admin_email']; ?></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item fw-semibold text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>