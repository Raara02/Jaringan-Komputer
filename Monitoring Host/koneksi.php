<?php
$host = "localhost";
$user = "root";         // ubah jika berbeda
$pass = "";             // ubah jika pakai password
$db   = "monitoring_website";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
