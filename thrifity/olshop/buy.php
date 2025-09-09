<?php
include 'includes/db.php';
include 'includes/auth.php';

$user_id    = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$success = false;

if ($product_id > 0) {
    // Cek stok & pastikan produk belum dihapus
    $check = $conn->prepare("SELECT stock FROM products WHERE id = ? AND is_deleted = 0");
    $check->bind_param("i", $product_id);
    $check->execute();
    $result = $check->get_result();
    $product = $result->fetch_assoc();

    if ($product && $product['stock'] > 0) {
        // Masukkan transaksi
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, product_id, quantity, created_at) VALUES (?, ?, 1, NOW())");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();

        // Kurangi stok
        $update = $conn->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
        $update->bind_param("i", $product_id);
        $update->execute();

        $success = true;
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Beli Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <h2>Pembelian Berhasil!</h2>
            <p>Produk berhasil dibeli.</p>
        <?php else: ?>
            <h2>Gagal Membeli</h2>
            <p>Produk tidak valid atau terjadi kesalahan.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
    </div>
</body>
</html>
