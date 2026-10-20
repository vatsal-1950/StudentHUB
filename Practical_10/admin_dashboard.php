<?php
require_once "auth.php";
requireRole("admin");
require_once "db.php";

$studentCount = 0;
$userCount = 0;
$eventCount = 0;

if ($pdo !== null) {
    try {
        $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $eventCount = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Admin Control Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <button id="hamburgerBtn" class="hamburger-btn" aria-label="toggle menu">☰ Menu</button>
    <nav class="ttt">
        <a href="home.html" class="m">Home</a>|
        <a href="about.html" class="m1">About</a>|
        <a href="admin_dashboard.php" class="m3">Admin Panel</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="contact.html" class="m6">Contact</a>|
        <a href="logout.php" class="m9">Logout (Admin: <?php echo htmlspecialchars($_SESSION["name"] ?? "", ENT_QUOTES, 'UTF-8'); ?>)</a>
    </nav>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<div class="dashboard">
    <aside>
        <h2>Admin Controls</h2>
        <hr><br>
        <ul>
            <li><a href="admin_dashboard.php" class="vvv">Dashboard</a></li>
            <li><a href="events.html" class="vvv">Manage Events</a></li>
            <li><a href="logout.php" class="vvv" style="color: red;">Logout</a></li>
        </ul>
    </aside>

    <main>
        <h2>Administrator Dashboard</h2>
        <p style="margin: 8px 0;">Privilege Level: <strong>Admin (Full Access)</strong> | Session User: <strong><?php echo htmlspecialchars($_SESSION["email"] ?? "", ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <p style="color: green; margin-bottom: 20px;">Role-Based Access Control verified. Only users with role 'admin' can access this panel.</p>

        <div class="card-container">
            <div class="card">
                <h3>Total Students</h3>
                <p><?php echo $studentCount; ?> Registered</p>
            </div>
            <div class="card">
                <h3>System Users</h3>
                <p><?php echo $userCount; ?> Accounts</p>
            </div>
            <div class="card">
                <h3>Total Events</h3>
                <p><?php echo $eventCount; ?> Scheduled</p>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <a href="logout.php" class="vvv" style="color: red;">Logout from Admin Panel</a>
        </div>
    </main>
</div>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
