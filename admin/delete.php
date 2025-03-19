<?php
require '../config.php'; // Pastikan file koneksi ke database ada
session_start(); // Tambahkan ini di awal

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Mencegah SQL Injection

    // Ambil nama file gambar
    $query = mysqli_query($conn, "SELECT image FROM products WHERE id = $id");
    $data = mysqli_fetch_assoc($query);
    $image_path = '../uploaded_img/' . $data['image'];

    // Hapus produk dari database
    $delete = mysqli_query($conn, "DELETE FROM products WHERE id = $id");

    if ($delete) {
        // Hapus gambar jika ada
        if (file_exists($image_path) && $data['image'] != '') {
            unlink($image_path);
        }
        $_SESSION['message'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> Product Deleted Successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle"></i> Product Failed to Delete!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    header("Location: products.php");
    exit();
} else {
    header("Location: products.php");
    exit();
}
