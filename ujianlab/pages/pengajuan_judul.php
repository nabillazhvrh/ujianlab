<?php
session_start();
include('../koneksi.php');

// Check if the user is a "Mahasiswa"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $user_id = $_SESSION['user_id'];

    $query = $conn->prepare("INSERT INTO judul_pengajuan (user_id, judul, status) VALUES (?, ?, 'Pending')");
    $query->bind_param("is", $user_id, $judul);

    if ($query->execute()) {
        echo "<p>Judul berhasil diajukan. Menunggu seleksi.</p>";
    } else {
        echo "<p>Terjadi kesalahan saat mengajukan judul. Coba lagi.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Judul</title>
</head>
<body>
    <h2>Pengajuan Judul</h2>
    <form method="POST">
        <label for="judul">Masukkan Judul:</label>
        <input type="text" id="judul" name="judul" required>
        <button type="submit">Ajukan Judul</button>
    </form>

     <!-- Tombol Kembali ke Dashboard Mahasiswa -->
     <form action="mahasiswa_dashboard.php" method="get" style="margin-top: 20px;">
        <button type="submit">Kembali ke Dashboard</button>
    </form>
</body>
</html>
