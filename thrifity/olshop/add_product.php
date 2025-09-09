<?php
include 'includes/db.php';
include 'includes/auth.php';

if ($_SESSION['role'] != 'seller') {
    echo "Akses hanya untuk seller!";
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $price = $_POST['price'];
    $desc  = $_POST['description'];
    $stock = $_POST['stock'];
    $image = '';

    // upload image
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $target_dir  = 'assets/img/';
        $image_name  = basename($_FILES['image']['name']);
        $target_path = $target_dir . time() . '_' . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image = $target_path;
        } else {
            $error = "Gagal mengupload gambar.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO products (seller_id, name, price, description, image, stock, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("isissi", $_SESSION['user_id'], $name, $price, $desc, $image, $stock);
        $stmt->execute();
        $success = "Produk berhasil ditambahkan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Tambah Produk</h2>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Nama produk" required><br>
        <input type="number" name="price" placeholder="Harga" required><br>
        <input type="number" name="stock" placeholder="Stok" required><br>
        <textarea name="description" placeholder="Deskripsi produk"></textarea><br>
        <input type="file" name="image"><br>
        <button type="submit">Tambah Produk</button>
    </form>

    <p><a href="dashboard.php">← Kembali ke Dashboard</a></p>
</div>
</body>
</html>
