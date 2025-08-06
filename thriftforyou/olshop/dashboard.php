<?php
include 'includes/db.php';
include 'includes/auth.php';

// Ambil role user
$user_id = $_SESSION['user_id'];
$check = $conn->query("SELECT role FROM users WHERE id = $user_id");
$user = $check->fetch_assoc();

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

// Buat query berdasarkan role
if ($user['role'] === 'seller') {
    $query = "SELECT products.*, users.name AS seller_name 
              FROM products 
              JOIN users ON products.seller_id = users.id 
              WHERE products.name LIKE '%$search%' AND products.seller_id != $user_id";
} else {
    $query = "SELECT products.*, users.name AS seller_name 
              FROM products 
              JOIN users ON products.seller_id = users.id 
              WHERE products.name LIKE '%$search%'";
}

// Sorting
if ($sort === 'termurah') {
    $query .= " ORDER BY products.price ASC";
} elseif ($sort === 'termahal') {
    $query .= " ORDER BY products.price DESC";
} else {
    $query .= " ORDER BY products.created_at DESC";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

<!-- Navigasi -->
<nav class="nav">
    <a href="dashboard.php">Beranda</a>
    <a href="cart.php">Keranjang</a>
    <a href="profile.php">Profil</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
        <select name="sort">
            <option value="">Urutkan</option>
            <option value="termurah" <?= $sort === 'termurah' ? 'selected' : '' ?>>Harga Termurah</option>
            <option value="termahal" <?= $sort === 'termahal' ? 'selected' : '' ?>>Harga Termahal</option>
        </select>
        <button type="submit">Cari</button>
    </form>

    <h2>Daftar Produk</h2>

    <div class="product-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product-card">
            <img src="<?= $row['image'] ?>" alt="gambar">
            <strong><?= $row['name'] ?></strong><br>
            Rp<?= number_format($row['price'], 0, ',', '.') ?><br>
            <small><?= $row['description'] ?></small><br>
            <small>Stok: <?= $row['stock'] ?></small><br>
            <small>Penjual: <?= htmlspecialchars($row['seller_name']) ?></small><br><br>

            <?php if ($row['stock'] > 0): ?>
                <form action="buy.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                    <button type="submit">Beli</button>
                </form>

                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= $row['stock'] ?>" required>
                    <button type="submit">Add to Cart</button>
                </form>
            <?php else: ?>
                <p style="color:red;"><strong>Stok Habis</strong></p>
                <button disabled>Beli</button>
                <button disabled>Add to Cart</button>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>

</div>

</body>
</html>
