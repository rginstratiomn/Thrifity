<?php
session_start();
include 'includes/db.php';

$error = '';

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Cooper+BT&family=Baloo+Thambi&family=Roca+Two&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <div class="show-password-container">
                <input type="checkbox" id="show-password" onclick="togglePassword()">
                <label for="show-password">Tampilkan Password</label>
            </div>
            
            <button name="login">LOGIN</button>
        </form>
        <p class="register-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
    <script>
        function togglePassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</body>
</html>