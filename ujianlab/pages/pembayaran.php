<?php
session_start();
include('../koneksi.php');

// Check if the user is a "Mahasiswa"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $proof = $_FILES['proof']['name'];
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($proof);

    if (move_uploaded_file($_FILES['proof']['tmp_name'], $target_file)) {
        $query = $conn->prepare("INSERT INTO pembayaran (user_id, amount, proof, status) VALUES (?, ?, ?, 'Pending')");
        $query->bind_param("ids", $user_id, $amount, $proof);

        if ($query->execute()) {
            echo "<p>Pembayaran berhasil diajukan. Menunggu konfirmasi.</p>";
        } else {
            echo "<p>Terjadi kesalahan saat mengajukan pembayaran.</p>";
        }
    } else {
        echo "<p>Gagal mengunggah bukti pembayaran.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
</head>
<body>
    <h2>Pembayaran</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="amount">Jumlah Pembayaran:</label>
        <input type="number" id="amount" name="amount" required>
        
        <label for="proof">Unggah Bukti Pembayaran:</label>
        <input type="file" id="proof" name="proof" accept="image/*" required>
        
        <button type="submit">Kirim Pembayaran</button>
    </form>

      <!-- Tombol Kembali ke Dashboard Mahasiswa -->
      <form action="mahasiswa_dashboard.php" method="get" style="margin-top: 20px;">
        <button type="submit">Kembali ke Dashboard</button>
    </form>

</body>
</html>
