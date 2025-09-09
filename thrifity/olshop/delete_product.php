<?php
include 'includes/db.php';
include 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int) $_POST['product_id'];
    $stmt = $conn->prepare("UPDATE products SET is_deleted = 1 WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
}

header('Location: profile.php');
exit;
?>
