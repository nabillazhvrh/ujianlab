<?php
session_start();
include('../koneksi.php');

// Cek jika pengguna adalah dosen
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Dosen') {
    header("Location: ../index.php");
    exit();
}

// Menangani pembaruan status seleksi judul
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['judul_id']) && isset($_POST['status'])) {
    $judul_id = $_POST['judul_id'];
    $status = $_POST['status'];

    // Update status seleksi judul
    $query = $conn->prepare("UPDATE judul_pengajuan SET status = ? WHERE id = ?");
    $query->bind_param("si", $status, $judul_id);

    if ($query->execute()) {
        echo "<p>Status seleksi judul berhasil diperbarui.</p>";
    } else {
        echo "<p>Gagal memperbarui status seleksi judul: " . $query->error . "</p>";
    }
}

// Menampilkan semua pengajuan judul dari mahasiswa
$query = "SELECT jp.id, jp.judul, u.username, u.role, jp.status
          FROM judul_pengajuan jp
          JOIN users u ON jp.user_id = u.id
          WHERE jp.status = 'Pending'";  // Menampilkan hanya yang statusnya 'Pending'

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>
</head>
<body>
    <h2>Dashboard Dosen</h2>
    <form action="logout.php" method="POST" style="text-align: right;">
        <input type="submit" value="Logout">
    </form>

    <h3>Pengajuan Judul Mahasiswa</h3>
    
    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>Judul Pengajuan</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>";
    
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['username']}</td> <!-- Nama mahasiswa (username) -->
                    <td>{$row['judul']}</td>
                    <td>{$row['status']}</td>
                    <td>
                        <form method='POST'>
                            <input type='hidden' name='judul_id' value='{$row['id']}'>
                            <select name='status'>
                                <option value='Disetujui' " . ($row['status'] == 'Disetujui' ? 'selected' : '') . ">Disetujui</option>
                                <option value='Ditolak' " . ($row['status'] == 'Ditolak' ? 'selected' : '') . ">Ditolak</option>
                            </select>
                            <button type='submit'>Update Status</button>
                        </form>
                    </td>
                </tr>";
            $no++;
        }
        echo "</table>";
    } else {
        echo "<p>Belum ada pengajuan judul yang perlu diseleksi.</p>";
    }
    ?>

</body>
</html>
