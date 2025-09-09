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
    <link rel="stylesheet" href="assets/style.css"> <!-- Ini link ke file CSS -->
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
            $products = $conn->query("SELECT * FROM products WHERE seller_id = $user_id ORDER BY is_deleted ASC, created_at DESC");

            while ($row = $products->fetch_assoc()):
            ?>
                <div class="product">
                    <img src="<?= $row['image'] ?>" style="width: 100px;"><br>
                    <strong><?= $row['name'] ?></strong><br>
                    Harga: Rp<?= number_format($row['price'], 0, ',', '.') ?><br>
                    Stok: <?= $row['stock'] ?><br>
                    Deskripsi: <?= $row['description'] ?><br>
                    Status: <?= $row['is_deleted'] ? '<span style="color:red;">Dihapus</span>' : '<span style="color:green;">Aktif</span>' ?><br>

                    <?php if ($row['is_deleted']): ?>
                        <form action="restore_product.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit">Pulihkan</button>
                        </form>
                    <?php else: ?>
                        <form action="delete_product.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit">Hapus</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>
