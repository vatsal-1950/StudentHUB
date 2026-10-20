<?php
require_once "db.php";

$categoryFilter = trim($_GET["category"] ?? "");
$statusFilter = trim($_GET["status"] ?? "");
$events = [];
$successMessage = "";
$errorMessage = "";

if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "added") $successMessage = "Event with poster published successfully.";
    elseif ($_GET["msg"] === "updated") $successMessage = "Event details updated successfully.";
    elseif ($_GET["msg"] === "deleted") $successMessage = "Event and poster deleted successfully.";
    elseif ($_GET["msg"] === "error") $errorMessage = "Action could not be completed.";
}

if ($pdo !== null) {
    try {
        $sql = "SELECT * FROM events WHERE 1=1";
        $params = [];

        if ($categoryFilter !== "") {
            $sql .= " AND category = ?";
            $params[] = $categoryFilter;
        }

        if ($statusFilter !== "") {
            $sql .= " AND status = ?";
            $params[] = $statusFilter;
        }

        $sql .= " ORDER BY event_date ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();
    } catch (PDOException $e) {
        $errorMessage = "Database query error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 12 | Event Management CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <button id="hamburgerBtn" class="hamburger-btn" aria-label="toggle menu">☰ Menu</button>
    <nav class="ttt">
        <a href="home.html" class="m">Home</a>|
        <a href="about.html" class="m1">About</a>|
        <a href="manage_events.php" class="m4">Event CRUD</a>|
        <a href="login.html" class="m9">Login</a>|
        <a href="dashboard.html" class="m3">Dashboard</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="contact.html" class="m6">Contact</a>|
        <a href="faq.html" class="m7">FAQ</a>|
        <a href="feedback.html" class="m8">Feedback</a>
    </nav>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<main>
<section>
    <h2 align="center">Practical 12: Event Management CRUD</h2>
    <p align="center" style="margin-top: 5px;">Add, View, Edit, Delete Events with Poster Upload & File Validation</p>
    <br>

    <?php if ($successMessage != ""): ?>
        <div class="alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if ($errorMessage != ""): ?>
        <div class="alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <div style="text-align: center; margin: 15px auto;">
        <a href="add_event.php" class="vvv" style="font-size: 16px;">+ Create New Event (with Poster Upload)</a>
    </div>

    <form method="GET" action="manage_events.php" style="max-width: 650px; display: flex; gap: 10px;">
        <select name="category" style="flex: 1;">
            <option value="">All Categories</option>
            <option value="Workshop" <?php if ($categoryFilter === "Workshop") echo "selected"; ?>>Workshop</option>
            <option value="Competition" <?php if ($categoryFilter === "Competition") echo "selected"; ?>>Competition</option>
            <option value="Seminar" <?php if ($categoryFilter === "Seminar") echo "selected"; ?>>Seminar</option>
            <option value="Sports" <?php if ($categoryFilter === "Sports") echo "selected"; ?>>Sports</option>
        </select>
        <select name="status" style="flex: 1;">
            <option value="">All Statuses</option>
            <option value="upcoming" <?php if ($statusFilter === "upcoming") echo "selected"; ?>>Upcoming</option>
            <option value="completed" <?php if ($statusFilter === "completed") echo "selected"; ?>>Completed</option>
            <option value="cancelled" <?php if ($statusFilter === "cancelled") echo "selected"; ?>>Cancelled</option>
        </select>
        <button type="submit" style="margin-top: 5px;">Filter</button>
    </form>

    <div class="table-container">
        <h3 style="color: blue; text-align: center;">Total Events: <?php echo count($events); ?></h3>
        <?php if (count($events) > 0): ?>
            <table class="record-table">
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Event Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $ev): ?>
                        <tr>
                            <td style="text-align: center; width: 90px;">
                                <?php if (!empty($ev["poster_image"]) && file_exists(__DIR__ . "/uploads/" . $ev["poster_image"])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($ev["poster_image"], ENT_QUOTES, 'UTF-8'); ?>" alt="Poster" style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <span style="color: gray; font-size: 12px;">No Poster</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($ev["title"], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <br><small><?php echo htmlspecialchars($ev["description"] ?? "", ENT_QUOTES, 'UTF-8'); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($ev["category"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($ev["event_date"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($ev["venue"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <span style="font-weight: bold; color: <?php echo $ev['status'] === 'upcoming' ? 'green' : 'gray'; ?>;">
                                    <?php echo ucfirst($ev["status"]); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_event.php?id=<?php echo $ev['id']; ?>" class="vvv">Edit</a> |
                                <a href="delete_event.php?id=<?php echo $ev['id']; ?>" class="vvv" style="color: red;" onclick="return confirm('Delete this event?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin: 20px;">No events recorded.</p>
        <?php endif; ?>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
