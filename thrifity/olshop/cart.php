<?php
include 'includes/db.php';
include 'includes/auth.php';

$user_id = $_SESSION['user_id'];

$query = "
    SELECT c.id as cart_id, p.name, p.price, p.image, c.quantity, p.id as product_id, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    AND p.is_deleted = 0
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">Beranda</a>
        <a href="profile.php">Profil</a>
        <a href="cart.php">Cart</a>
    </div>

    <div class="container">
        <h2>Keranjang Belanja</h2>

        <?php if ($result->num_rows > 0): ?>
            <form action="checkout.php" method="POST">
            <?php $total = 0; ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="product">
                    <img src="<?= $row['image'] ?>" style="width: 100px;"><br>
                    <?= $row['name'] ?> - Rp<?= number_format($row['price'], 0, ',', '.') ?><br>
                    Jumlah: <?= $row['quantity'] ?><br>
                    <input type="hidden" name="product_ids[]" value="<?= $row['product_id'] ?>">
                    <input type="hidden" name="quantities[]" value="<?= $row['quantity'] ?>">
                </div>
                <?php $total += $row['price'] * $row['quantity']; ?>
            <?php endwhile; ?>
            <strong>Total: Rp<?= number_format($total, 0, ',', '.') ?></strong><br><br>
            <button type="submit">Checkout</button>
            </form>
        <?php else: ?>
            <p>Keranjang kamu kosong.</p>
        <?php endif; ?>
    </div>
</body>
</html>
