<?php
require_once "auth.php";
requireAdmin();
require_once "db.php";

$message = "";
$user = getCurrentUser();

if (isset($_GET["toggle_student_id"]) && $pdo !== null) {
    $toggleId = (int)$_GET["toggle_student_id"];
    try {
        $curr = $pdo->prepare("SELECT status, name FROM students WHERE id = ?");
        $curr->execute([$toggleId]);
        $row = $curr->fetch();
        if ($row) {
            $newStatus = $row["status"] === "active" ? "inactive" : "active";
            $upd = $pdo->prepare("UPDATE students SET status = ? WHERE id = ?");
            $upd->execute([$newStatus, $toggleId]);

            $audit = $pdo->prepare("INSERT INTO audit_logs (user_email, action, details, ip_address) VALUES (?, 'STATUS_TOGGLE', ?, ?)");
            $audit->execute([$user["email"], "Toggled status for student {$row['name']} to $newStatus", $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"]);
            $message = "Status for {$row['name']} changed to $newStatus.";
        }
    } catch (PDOException $e) {
        $message = "Toggle error: " . $e->getMessage();
    }
}

$studentCount = 0;
$activeStudentCount = 0;
$eventCount = 0;
$registrationCount = 0;
$userCount = 0;
$latestRegistrations = [];
$auditLogs = [];
$studentList = [];

if ($pdo !== null) {
    try {
        $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $activeStudentCount = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn();
        $eventCount = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
        $registrationCount = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
        $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

        $regStmt = $pdo->query("
            SELECT r.id, s.name AS student_name, e.title AS event_title, r.registration_date, r.status
            FROM registrations r
            JOIN students s ON r.student_id = s.id
            JOIN events e ON r.event_id = e.id
            ORDER BY r.id DESC LIMIT 5
        ");
        $latestRegistrations = $regStmt->fetchAll();

        $auditStmt = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 5");
        $auditLogs = $auditStmt->fetchAll();

        $stuStmt = $pdo->query("SELECT id, name, email, course, status FROM students ORDER BY id DESC LIMIT 6");
        $studentList = $stuStmt->fetchAll();
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 14 | Admin Dashboard & RBAC</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stat-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin: 20px auto;
        }
        .stat-card {
            background-color: aliceblue;
            border: 2px solid blue;
            border-radius: 8px;
            padding: 15px;
            width: 200px;
            text-align: center;
        }
        .stat-card h3 {
            color: blue;
            margin-bottom: 5px;
            font-size: 16px;
        }
        .stat-card p {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
    </style>
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
    <h2 align="center">Practical 14: Dynamic Admin Dashboard & Audit Log</h2>
    <p align="center" style="margin-top: 5px;">Authenticated Admin: <strong><?php echo htmlspecialchars($user["email"], ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <br>

    <?php if ($message != ""): ?>
        <div class="alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="stat-grid">
        <div class="stat-card">
            <h3>Total Students</h3>
            <p><?php echo $studentCount; ?></p>
            <small><?php echo $activeStudentCount; ?> Active</small>
        </div>
        <div class="stat-card">
            <h3>Scheduled Events</h3>
            <p><?php echo $eventCount; ?></p>
            <small>Active Modules</small>
        </div>
        <div class="stat-card">
            <h3>Event Bookings</h3>
            <p><?php echo $registrationCount; ?></p>
            <small>Confirmed</small>
        </div>
        <div class="stat-card">
            <h3>System Accounts</h3>
            <p><?php echo $userCount; ?></p>
            <small>RBAC Users</small>
        </div>
    </div>

    <div class="table-container">
        <h3 style="color: blue;">Student Status Toggle (Intermediate Extension)</h3>
        <table class="record-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studentList as $st): ?>
                    <tr>
                        <td><?php echo $st["id"]; ?></td>
                        <td><?php echo htmlspecialchars($st["name"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($st["email"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($st["course"], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <strong style="color: <?php echo $st['status'] === 'active' ? 'green' : 'red'; ?>;">
                                <?php echo ucfirst($st["status"]); ?>
                            </strong>
                        </td>
                        <td>
                            <a href="admin_dashboard.php?toggle_student_id=<?php echo $st['id']; ?>" class="vvv">
                                Set to <?php echo $st["status"] === "active" ? "Inactive" : "Active"; ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <br>

    <div class="table-container">
        <h3 style="color: blue;">Latest Event Registrations (SQL Aggregation / JOIN)</h3>
        <?php if (count($latestRegistrations) > 0): ?>
            <table class="record-table">
                <thead>
                    <tr>
                        <th>Booking #</th>
                        <th>Student Name</th>
                        <th>Event Title</th>
                        <th>Registered Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestRegistrations as $lr): ?>
                        <tr>
                            <td><?php echo $lr["id"]; ?></td>
                            <td><strong><?php echo htmlspecialchars($lr["student_name"], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?php echo htmlspecialchars($lr["event_title"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($lr["registration_date"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span style="color: green; font-weight: bold;"><?php echo ucfirst($lr["status"]); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No recent registrations recorded.</p>
        <?php endif; ?>
    </div>

    <br>

    <div class="table-container">
        <h3 style="color: blue;">Recent Audit Logs (Traceability & Security)</h3>
        <?php if (count($auditLogs) > 0): ?>
            <table class="record-table">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auditLogs as $al): ?>
                        <tr>
                            <td><?php echo $al["id"]; ?></td>
                            <td><?php echo htmlspecialchars($al["user_email"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><strong><?php echo htmlspecialchars($al["action"], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?php echo htmlspecialchars($al["details"] ?? "", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($al["ip_address"] ?? "-", ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($al["created_at"], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No audit logs available.</p>
        <?php endif; ?>
    </div>

    <div class="action-links" style="margin-top: 25px;">
        <a href="logout.php" class="vvv" style="color: red;">Logout Admin Session</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
