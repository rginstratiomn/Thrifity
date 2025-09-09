<?php
include 'includes/db.php';
include 'includes/auth.php';

// Ambil role user
$user_id = $_SESSION['user_id'];
$check = $conn->query("SELECT role FROM users WHERE id = $user_id");
$user = $check->fetch_assoc();

$search = $_GET['search'] ?? '';
$sort   = $_GET['sort'] ?? '';

// --- Pagination setup ---
$limit = 50; // jumlah produk per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Buat query dasar berdasarkan role
if ($user['role'] === 'seller') {
    $baseQuery = "FROM products 
                  JOIN users ON products.seller_id = users.id 
                  WHERE products.name LIKE '%$search%' AND products.seller_id != $user_id AND products.is_deleted = 0";
} else {
    $baseQuery = "FROM products 
                  JOIN users ON products.seller_id = users.id 
                  WHERE products.name LIKE '%$search%' AND products.is_deleted = 0";
}

// Hitung total produk (untuk pagination)
$countQuery = "SELECT COUNT(*) as total " . $baseQuery;
$countResult = $conn->query($countQuery);
$totalProducts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $limit);

// Query produk sesuai page
$query = "SELECT products.*, users.name AS seller_name " . $baseQuery;

// Sorting
if ($sort === 'termurah') {
    $query .= " ORDER BY products.price ASC";
} elseif ($sort === 'termahal') {
    $query .= " ORDER BY products.price DESC";
} else {
    $query .= " ORDER BY products.created_at DESC";
}

$query .= " LIMIT $limit OFFSET $offset";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">Beranda</a>
        <a href="profile.php">Profil</a>
        <a href="cart.php">Cart</a>
    </div>

    <div class="container">
        <h2>Produk Tersedia</h2>

        <!-- Form pencarian & sortir -->
        <form method="GET" action="dashboard.php">
            <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
            <select name="sort">
                <option value="">Terbaru</option>
                <option value="termurah" <?= $sort === 'termurah' ? 'selected' : '' ?>>Harga Termurah</option>
                <option value="termahal" <?= $sort === 'termahal' ? 'selected' : '' ?>>Harga Termahal</option>
            </select>
            <button type="submit">Cari</button>
        </form>

        <div class="product-container">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <img src="<?= $row['image'] ?>" alt="gambar" style="width:100px;"><br>
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
                        <input type="number" name="quantity" value="1" min="1" max="<?= $row['stock'] ?>">
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

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>"
                   <?= $i == $page ? 'style="font-weight:bold;"' : '' ?>>
                   <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
