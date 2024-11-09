<?php
session_start();
include('../koneksi.php');

// Cek jika pengguna adalah staff prodi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    header("Location: ../index.php");
    exit();
}

// Menangani pembaruan status pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_id']) && isset($_POST['status'])) {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['status'];

    // Update status pembayaran
    $query = $conn->prepare("UPDATE pembayaran SET status = ? WHERE id = ?");
    $query->bind_param("si", $status, $payment_id);

    if ($query->execute()) {
        echo "<p>Status pembayaran berhasil diperbarui.</p>";
    } else {
        echo "<p>Gagal memperbarui status pembayaran.</p>";
    }
}

// Menangani upload surat pengantar pembimbing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['surat_pengantar']) && isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];  // ID Pembayaran
    $file_name = $_FILES['surat_pengantar']['name'];  // Nama file surat pengantar
    $file_tmp = $_FILES['surat_pengantar']['tmp_name'];  // Tempat file sementara
    $file_error = $_FILES['surat_pengantar']['error'];  // Cek error saat upload

    if ($file_error === 0) {
        // Tentukan path penyimpanan file surat pengantar
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_new_name = uniqid('surat_', true) . '.' . $file_ext;
        $file_destination = "../uploads/" . $file_new_name;

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($file_tmp, $file_destination)) {
            // Menyimpan data surat pengantar ke database
            $query = $conn->prepare("INSERT INTO surat_pengantar (payment_id, file_surat) VALUES (?, ?)");
            $query->bind_param("is", $payment_id, $file_new_name);

            if ($query->execute()) {
                echo "<p>Surat pengantar berhasil diunggah.</p>";
            } else {
                echo "<p>Gagal mengunggah surat pengantar.</p>";
            }
        } else {
            echo "<p>Gagal mengunggah surat pengantar.</p>";
        }
    } else {
        echo "<p>Terjadi kesalahan saat mengunggah file.</p>";
    }
}

// Menampilkan data mahasiswa yang telah mengajukan pembayaran dan statusnya 'Dipending'
$query = "
    SELECT p.id, u.username, p.amount, p.proof, p.status, p.tanggal_pembayaran
    FROM pembayaran p
    JOIN users u ON p.user_id = u.id
    WHERE p.status = 'Pending'";

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff Prodi</title>
</head>
<body>
    <h2>Dashboard Staff Prodi</h2>
    <form action="logout.php" method="POST" style="text-align: right;">
        <input type="submit" value="Logout">
    </form>

    <h3>Rekapitulasi Pembayaran Mahasiswa</h3>
    <table border="1">
        <tr>
            <th>No</th>
            <th>Username</th>
            <th>Jumlah Pembayaran</th>
            <th>Bukti Pembayaran</th>
            <th>Status Pembayaran</th>
            <th>Tanggal Pembayaran</th>
            <th>Surat Pengantar Pembimbing</th>
            <th>Aksi</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['amount']}</td>
                    <td><a href='../uploads/{$row['proof']}' target='_blank'>Lihat Bukti</a></td>
                    <td>{$row['status']}</td>
                    <td>{$row['tanggal_pembayaran']}</td>
                    <td>";

                    // Cek apakah sudah ada surat pengantar yang diupload
                    $query_surat = $conn->prepare("SELECT file_surat FROM surat_pengantar WHERE payment_id = ?");
                    $query_surat->bind_param("i", $row['id']);
                    $query_surat->execute();
                    $result_surat = $query_surat->get_result();

                    if ($result_surat->num_rows > 0) {
                        $surat = $result_surat->fetch_assoc();
                        echo "<a href='../uploads/{$surat['file_surat']}' target='_blank'>Lihat Surat</a>";
                    } else {
                        echo "Belum ada surat pengantar.";
                    }

                echo "</td>
                    <td>
                        <form method='POST'>
                            <input type='hidden' name='payment_id' value='{$row['id']}'>
                            <select name='status'>
                                <option value='Pending' " . ($row['status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                                <option value='Diterima' " . ($row['status'] == 'Diterima' ? 'selected' : '') . ">Diterima</option>
                                <option value='Ditolak' " . ($row['status'] == 'Ditolak' ? 'selected' : '') . ">Ditolak</option>
                            </select>
                            <button type='submit'>Update Status</button>
                        </form><br>

                        <form action='staff_dashboard.php' method='POST' enctype='multipart/form-data'>
                            <input type='hidden' name='payment_id' value='{$row['id']}'>
                            <label for='surat_pengantar'>Upload Surat Pengantar</label>
                            <input type='file' name='surat_pengantar' required><br>
                            <button type='submit'>Upload Surat</button>
                        </form>
                    </td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='8'>Tidak ada mahasiswa yang status pembayarannya dipending.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
