
<?php
// includes/db.php
$servername = "localhost";
$username = "root"; // Username default XAMPP
$password = "";     // Password default XAMPP (biasanya kosong)
$dbname = "olshop_db"; // Nama database yang baru Anda buat

// Buat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
// echo "Koneksi berhasil!"; // Anda bisa mengaktifkan ini sementara untuk memastikan koneksi berhasil
?>