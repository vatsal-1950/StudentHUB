<?php
$students = [];
$csvStudentFile = __DIR__ . "/students.csv";
if (file_exists($csvStudentFile)) {
    $handle = fopen($csvStudentFile, "r");
    if ($handle) {
        $first = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($first) {
                $first = false;
                continue;
            }
            if (count($row) >= 6) {
                $students[] = $row;
            }
        }
        fclose($handle);
    }
}

$contacts = [];
$csvContactFile = __DIR__ . "/contact.csv";
if (file_exists($csvContactFile)) {
    $handle = fopen($csvContactFile, "r");
    if ($handle) {
        $first = true;
        while (($row = fgetcsv($handle)) !== false) {
            if ($first) {
                $first = false;
                continue;
            }
            if (count($row) >= 3) {
                $contacts[] = $row;
            }
        }
        fclose($handle);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Stored Records</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <button id="hamburgerBtn" class="hamburger-btn" aria-label="toggle menu">☰ Menu</button>
    <nav class="ttt">
        <a href="home.html" class="m">Home</a>|
        <a href="about.html" class="m1">About</a>|
        <a href="register.php" class="m2">Register</a>|
        <a href="login.html" class="m9">Login</a>|
        <a href="dashboard.html" class="m3">Dashboard</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="contact.php" class="m6">Contact</a>|
        <a href="faq.html" class="m7">FAQ</a>|
        <a href="feedback.html" class="m8">Feedback</a>|
        <a href="view_records.php" class="m10">Records</a>
    </nav>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<main>
<section>
    <h2 align="center">Stored CSV/JSON Records</h2>
    <br>

    <h3 style="color: blue; text-align: center;">Student Registrations (Total: <?php echo count($students); ?>)</h3>
    <div class="table-container">
        <?php if (count($students) > 0): ?>
            <table class="record-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Gender</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($students as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row[0] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[1] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[2] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[3] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[4] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[5] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[6] ?? "-", ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin: 15px;">No student records found in CSV storage.</p>
        <?php endif; ?>
    </div>

    <br><br>

    <h3 style="color: blue; text-align: center;">Contact Messages (Total: <?php echo count($contacts); ?>)</h3>
    <div class="table-container">
        <?php if (count($contacts) > 0): ?>
            <table class="record-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $j = 1; foreach ($contacts as $row): ?>
                        <tr>
                            <td><?php echo $j++; ?></td>
                            <td><?php echo htmlspecialchars($row[0] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[1] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[2] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row[3] ?? "-", ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin: 15px;">No contact messages found in CSV storage.</p>
        <?php endif; ?>
    </div>

    <div class="action-links">
        <a href="register.php" class="vvv">Register New Student</a> |
        <a href="contact.php" class="vvv">Send New Message</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
