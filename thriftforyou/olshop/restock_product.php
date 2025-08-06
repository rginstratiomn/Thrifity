<?php
include 'includes/db.php';
include 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $add_stock = (int)$_POST['add_stock'];
    $seller_id = $_SESSION['user_id'];

    // Pastikan produk memang milik seller ini
    $check = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
    $check->bind_param("ii", $product_id, $seller_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $conn->query("UPDATE products SET stock = stock + $add_stock WHERE id = $product_id");
        header("Location: profile.php");
        exit;
    } else {
        echo "Produk tidak ditemukan atau bukan milik Anda.";
    }
} else {
    echo "Metode tidak valid.";
}
?>
