<?php
include 'includes/db.php';
include 'includes/auth.php';

$user_id = $_SESSION['user_id'];
$product_id = (int) $_POST['product_id'];
$quantity_to_add = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1; // default 1

// Ambil stok
$stock_result = $conn->query("SELECT stock FROM products WHERE id = $product_id");
$product = $stock_result->fetch_assoc();
$stock = (int) $product['stock'];

// Cek jumlah saat ini di cart
$cart_result = $conn->query("SELECT quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id");
$cart = $cart_result->fetch_assoc();
$current_qty = $cart ? (int)$cart['quantity'] : 0;

// Hitung total yang akan ada
$new_qty = $current_qty + $quantity_to_add;

// Bandingkan dengan stok
if ($new_qty > $stock) {
    $new_qty = $stock;
}

// Update atau insert
if ($cart) {
    // Update
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $new_qty, $user_id, $product_id);
} else {
    // Insert
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $new_qty);
}

$stmt->execute();

// Redirect balik ke dashboard
header("Location: dashboard.php");
exit;


