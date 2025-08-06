<?php
include 'includes/db.php';

if (isset($_POST['register'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check_query = "SELECT * FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error = "Email ini sudah terdaftar. Silakan gunakan email lain atau login.";
    } else {
        $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Pendaftaran gagal: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="assets/register.css">
    <link href="https://fonts.googleapis.com/css2?family=Cooper+BT&family=Baloo+Thambi&family=Roca+Two&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Register</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Nama" required><br>
            <input type="email" name="email" placeholder="Email" required><br>

            <input type="password" name="password" id="password" placeholder="Password" required>
            <div class="show-password-container">
                <input type="checkbox" id="show-password" onclick="togglePassword()">
                <label for="show-password">Tampilkan Password</label>
            </div>

            <button name="register">REGISTER</button>
        </form>
        <p class="login-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
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