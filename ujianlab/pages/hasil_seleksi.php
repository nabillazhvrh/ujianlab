<?php
session_start();
include('../koneksi.php');

// Check if the user is a "Mahasiswa"
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Mahasiswa') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT judul, status FROM judul_pengajuan WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Seleksi Judul</title>
</head>
<body>
    <h2>Hasil Seleksi Judul</h2>
    <?php
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Judul</th><th>Status</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['judul'] . "</td><td>" . $row['status'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Belum ada judul yang diajukan.</p>";
    }
    ?>

    <!-- Tombol Kembali ke Dashboard Mahasiswa -->
    <form action="mahasiswa_dashboard.php" method="get" style="margin-top: 20px;">
        <button type="submit">Kembali ke Dashboard</button>
    </form>
</body>
</html>
