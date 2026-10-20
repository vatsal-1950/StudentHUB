<?php
require_once "auth.php";
$user = getCurrentUser();
?>
<nav class="ttt">
    <a href="home.html" class="m">Home</a>|
    <a href="about.html" class="m1">About</a>|
    <?php if ($user["role"] === "admin"): ?>
        <a href="admin_dashboard.php" class="m3" style="font-weight: bold; color: blue;">Admin Dashboard</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="logout.php" class="m9" style="color: red;">Logout (Admin: <?php echo htmlspecialchars($user["name"], ENT_QUOTES, 'UTF-8'); ?>)</a>
    <?php elseif ($user["role"] === "student"): ?>
        <a href="dashboard.html" class="m3">My Dashboard</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="contact.html" class="m6">Contact</a>|
        <a href="logout.php" class="m9" style="color: red;">Logout (<?php echo htmlspecialchars($user["name"], ENT_QUOTES, 'UTF-8'); ?>)</a>
    <?php else: ?>
        <a href="login.php" class="m9">Login</a>|
        <a href="admin_dashboard.php" class="m3">Admin Portal</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="contact.html" class="m6">Contact</a>|
        <a href="faq.html" class="m7">FAQ</a>
    <?php endif; ?>
</nav>
