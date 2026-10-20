<?php
require_once "auth.php";
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <button id="hamburgerBtn" class="hamburger-btn" aria-label="toggle menu">☰ Menu</button>
    <nav class="ttt">
        <a href="home.html" class="m">Home</a>|
        <a href="about.html" class="m1">About</a>|
        <a href="student_dashboard.php" class="m3">Dashboard</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="contact.html" class="m6">Contact</a>|
        <a href="faq.html" class="m7">FAQ</a>|
        <a href="logout.php" class="m9">Logout (<?php echo htmlspecialchars($_SESSION["name"] ?? "User", ENT_QUOTES, 'UTF-8'); ?>)</a>
    </nav>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<div class="dashboard">
    <aside>
        <h2>Student Menu</h2>
        <hr><br>
        <ul>
            <li><a href="student_dashboard.php" class="vvv">Dashboard</a></li>
            <li><a href="profile.html" class="vvv">My Profile</a></li>
            <li><a href="events.html" class="vvv">Events</a></li>
            <li><a href="logout.php" class="vvv" style="color: red;">Logout</a></li>
        </ul>
    </aside>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["name"] ?? "Student", ENT_QUOTES, 'UTF-8'); ?>!</h2>
        <p style="margin: 8px 0;">Role: <strong><?php echo htmlspecialchars($_SESSION["role"] ?? "student", ENT_QUOTES, 'UTF-8'); ?></strong> | Email: <strong><?php echo htmlspecialchars($_SESSION["email"] ?? "", ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <p style="color: green; margin-bottom: 20px;">Authenticated via secure PHP session. Auto-logout occurs after 15 minutes of inactivity.</p>

        <div class="card-container">
            <div class="card">
                <h3>Enrolled Courses</h3>
                <p>6 Active Courses</p>
            </div>
            <div class="card">
                <h3>Upcoming Events</h3>
                <p>5 Campus Activities</p>
            </div>
            <div class="card">
                <h3>Attendance</h3>
                <p>94% Aggregate</p>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <a href="logout.php" class="vvv" style="color: red;">Click here to safely terminate session (Logout)</a>
        </div>
    </main>
</div>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
