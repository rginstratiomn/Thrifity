<?php
include 'includes/db.php';
include 'includes/auth.php';

$user_id = $_SESSION['user_id'];
$product_ids = $_POST['product_ids'] ?? [];
$quantities  = $_POST['quantities'] ?? [];

if (count($product_ids) !== count($quantities)) {
    echo "Data tidak valid.";
    exit;
}

for ($i = 0; $i < count($product_ids); $i++) {
    $pid = (int) $product_ids[$i];
    $qty = (int) $quantities[$i];

    // Cek apakah stok mencukupi
    $stmt_stock = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt_stock->bind_param("i", $pid);
    $stmt_stock->execute();
    $result_stock = $stmt_stock->get_result();
    $row_stock = $result_stock->fetch_assoc();

    if ($row_stock && $row_stock['stock'] >= $qty) {
        // Simpan ke transaksi
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iii", $user_id, $pid, $qty);
        $stmt->execute();

        // Update stok produk
        $stmt_upd = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt_upd->bind_param("ii", $qty, $pid);
        $stmt_upd->execute();

        // Hapus dari cart
        $stmt_del = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt_del->bind_param("ii", $user_id, $pid);
        $stmt_del->execute();
    } else {
        echo "Stok tidak mencukupi untuk produk ID: $pid";
        exit;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Checkout Berhasil!</h2>
        <p>Terima kasih telah berbelanja.</p>
        <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
    </div>
</body>
</html>

