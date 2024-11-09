<?php
session_start();
include('../koneksi.php');

// Check if the user is a "Mahasiswa"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Mahasiswa') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Dashboard Mahasiswa</h2>

        <!-- Logout Button -->
        <form action="logout.php" method="post" style="text-align: right;">
            <input type="submit" value="Logout">
        </form><br>

        <!-- Section for Mahasiswa Actions -->
        <div class="actions">
            <form action="pengajuan_judul.php" method="get">
                <input type="submit" value="Pengajuan Judul">
            </form> <br>
            <form action="hasil_seleksi.php" method="get">
                <input type="submit" value="Lihat Hasil Seleksi Judul">
            </form> <br>
            <form action="pembayaran.php" method="get">
                <input type="submit" value="Pembayaran">
            </form>
        </div>
    </div>
</body>
</html>
