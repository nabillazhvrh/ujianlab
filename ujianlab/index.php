<?php
session_start();
include('koneksi.php');

// Check if user is already logged in
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Mahasiswa') {
        header("Location: pages/mahasiswa_dashboard.php");
    } elseif ($_SESSION['role'] === 'Staff') {
        header("Location: pages/staff_dashboard.php");
    } elseif ($_SESSION['role'] === 'Dosen') {
        header("Location: pages/dosen_dashboard.php");
    }
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'Mahasiswa') {
            header("Location: pages/mahasiswa_dashboard.php");
        } elseif ($user['role'] === 'Staff') {
            header("Location: pages/staff_dashboard.php");
        } elseif ($user['role'] === 'Dosen') {
            header("Location: pages/dosen_dashboard.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Kolom kiri untuk gambar/logo -->
        <div class="left-column">
            <img src="unilak.jpeg" alt="unilak.jpeg" class="unilak.jpeg">
        </div>

        <!-- Kolom kanan untuk form login -->
        <div class="right-column">
            <div class="login-box">
                <h2>login</h2>
                <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                <form action="index.php" method="POST">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="login-btn">Login</button>
                    <div class="footer-links">
                        <a href="#">Forgot Password?</a> | 
                        <a href="#">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
