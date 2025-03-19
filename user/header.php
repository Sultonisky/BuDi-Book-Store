<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-light shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold text-primary" href="home.php">
            Buku<span class="fw-bold text-warning">Digital</span>
        </a>

        <!-- Toggle Button (Mobile) -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto fw-semibold">
                <li class="nav-item"><a class="nav-link text-primary" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="contact.php">Contact</a></li>
                <li class="nav-item"><a class="nav-link text-primary" href="order_list.php">Orders</a></li>
            </ul>

            <!-- Search, Cart, User -->
            <div class="d-flex align-items-center gap-3">
                <!-- Search -->
                <a href="search_page.php" class="btn btn-outline-primary">
                    <i class="bi bi-search"></i>
                </a>

                <!-- Cart -->
                <?php
                $select_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id' ") or die(mysqli_error($conn));
                $cart_rows = mysqli_num_rows($select_cart);
                ?>
                <a href="cart.php" class="btn btn-outline-primary position-relative">
                    <i class="bi bi-cart"></i>
                    <?php if ($cart_rows > 0) : ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $cart_rows; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?= $_SESSION['user_name']; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="dropdown-item text-primary">
                            <i class="bi bi-person"></i> <?= $_SESSION['user_name']; ?>
                        </li>
                        <li class="dropdown-item text-primary">
                            <i class="bi bi-envelope"></i> <?= $_SESSION['user_email']; ?>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="../logout.php">
                                <i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>