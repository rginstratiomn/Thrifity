<?php
include 'includes/db.php';
include 'includes/auth.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Saya</title>
    <link rel="stylesheet" href="assets/profile.css"> <!-- Ini link ke file CSS -->
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">Beranda</a>
        <a href="profile.php">Profil</a>
        <a href="cart.php">Cart</a>
    </div>

    <div class="container profile-container">
        <h2>Profil Saya</h2>
        <p><strong>Nama:</strong> <?= $user['name'] ?></p>
        <p><strong>Email:</strong> <?= $user['email'] ?></p>
        <p><strong>Role:</strong> <?= $user['role'] ?></p>

        <form action="logout.php" method="POST">
            <button type="submit">Logout</button>
        </form>

        <hr>

        <?php if ($user['role'] === 'buyer'): ?>
            <form action="become_seller.php" method="POST">
                <button type="submit">Jadi Penjual</button>
            </form>
        <?php else: ?>
            <h3>Dashboard Penjual</h3>
            <a href="add_product.php"><button>Tambah Produk</button></a>

            <?php
            $products = $conn->query("SELECT * FROM products WHERE seller_id = $user_id");
            while ($row = $products->fetch_assoc()):
            ?>
                <div class="product">
                    <img src="<?= $row['image'] ?>" style="width: 100px;"><br>
                    <strong><?= $row['name'] ?></strong><br>
                    Harga: Rp<?= number_format($row['price'], 0, ',', '.') ?><br>
                    Stok: <?= $row['stock'] ?><br>
                    Deskripsi: <?= $row['description'] ?><br>

                    <form method="POST" action="restock_product.php">
                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                        <input type="number" name="add_stock" placeholder="Tambah stok" min="1" required>
                        <button type="submit">Restock</button>
                    </form>
                </div>

            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>
