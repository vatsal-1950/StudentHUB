<?php
require_once "db.php";
$connected = ($pdo !== null);
$tables = [];

if ($connected) {
    try {
        $tablesQuery = $pdo->query("SHOW TABLES");
        $rawTables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

        foreach ($rawTables as $tableName) {
            $countStmt = $pdo->query("SELECT COUNT(*) AS total FROM `$tableName`");
            $countRow = $countStmt->fetch();
            $tables[$tableName] = $countRow["total"] ?? 0;
        }
    } catch (PDOException $e) {
        $queryError = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 8 | Database Connectivity</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <button id="hamburgerBtn" class="hamburger-btn" aria-label="toggle menu">☰ Menu</button>
    <nav class="ttt">
        <a href="home.html" class="m">Home</a>|
        <a href="about.html" class="m1">About</a>|
        <a href="register.html" class="m2">Register</a>|
        <a href="login.html" class="m9">Login</a>|
        <a href="dashboard.html" class="m3">Dashboard</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="contact.html" class="m6">Contact</a>|
        <a href="faq.html" class="m7">FAQ</a>|
        <a href="feedback.html" class="m8">Feedback</a>|
        <a href="test_db.php" class="m10">DB Status</a>
    </nav>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<main>
<section>
    <h2 align="center">Practical 8: Database Connection & Schema Verification</h2>
    <br>

    <?php if ($connected): ?>
        <div class="alert-success">
            Database connection established successfully with MySQL database: <strong>studenthub_db</strong>
        </div>

        <div class="table-container">
            <h3 style="color: blue; text-align: center;">Schema Tables & Records Count</h3>
            <table class="record-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Table Name</th>
                        <th>Total Records</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($tables as $name => $count): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><strong><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?php echo $count; ?></td>
                            <td style="color: green; font-weight: bold;">Ready</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert-danger">
            Database connection failed.<br>
            Error: <?php echo htmlspecialchars($dbError ?? "Unable to connect to MySQL host: localhost.", ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <div class="card" style="max-width: 650px; margin: 20px auto; display: block;">
            <h3>Setup Instructions:</h3>
            <ol style="margin-left: 20px; line-height: 28px;">
                <li>Open XAMPP Control Panel and start <strong>Apache</strong> and <strong>MySQL</strong>.</li>
                <li>Open phpMyAdmin at <strong>http://localhost/phpmyadmin/</strong></li>
                <li>Import <strong>schema.sql</strong> located in this directory.</li>
                <li>Refresh this page to verify the connection.</li>
            </ol>
        </div>
    <?php endif; ?>

    <div class="action-links">
        <a href="home.html" class="vvv">Return to Home</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
