<?php
include 'includes/db.php';
include 'includes/auth.php';

$user_id = $_SESSION['user_id'];
$conn->query("UPDATE users SET role = 'seller' WHERE id = $user_id");

header("Location: profile.php");
exit;
?>
