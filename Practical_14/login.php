<?php
require_once "auth.php";
require_once "db.php";

$errorMessage = "";
$successMessage = "";

if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "logout") $successMessage = "Logged out successfully.";
    elseif ($_GET["msg"] === "unauthorized") $errorMessage = "Admin privileges required to access the dashboard.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $errorMessage = "Please enter email and password.";
    } elseif ($pdo === null) {
        $errorMessage = "Database is offline.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

                $log = $pdo->prepare("INSERT INTO audit_logs (user_email, action, details, ip_address) VALUES (?, 'ADMIN_LOGIN', 'Admin logged into dashboard', ?)");
                $log->execute([$user["email"], $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"]);

                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.html");
                }
                exit;
            } else {
                $errorMessage = "Invalid login credentials.";
            }
        } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 14 | Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <button id="hamburgerBtn" class="hamburger-btn" aria-label="toggle menu">☰ Menu</button>
    <?php require "menu.php"; ?>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<main>
<section>
    <h2 align="center">Sign In (Practical 14)</h2>
    <p align="center" style="margin-top: 5px;">Admin Account: <code>admin@studenthub.com</code> / <code>Admin@123</code></p>
    <br>

    <?php if ($successMessage != ""): ?>
        <div class="alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if ($errorMessage != ""): ?>
        <div class="alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Email Address</label>
        <input type="email" name="email" value="admin@studenthub.com" required>

        <label>Password</label>
        <input type="password" name="password" value="Admin@123" required>

        <br><br>
        <button type="submit">Sign In</button>
    </form>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
