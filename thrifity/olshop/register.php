<?php
include 'includes/db.php';

if (isset($_POST['register'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: login.php");
        exit;
    } else {
        echo "Pendaftaran gagal: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="assets/register.css">
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
            <br>
            <input type="checkbox" onclick="togglePassword()"> Tampilkan Password
            <br>

            <button name="register">Register</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    
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

