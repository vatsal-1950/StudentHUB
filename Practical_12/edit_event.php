<?php
require_once "db.php";

$id = (int)($_GET["id"] ?? 0);
$event = null;
$errorMessage = "";

if ($pdo !== null && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
}

if (!$event) {
    header("Location: manage_events.php?msg=error");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $status = trim($_POST["status"] ?? "upcoming");
    $posterName = $event["poster_image"];

    if ($title === "" || $eventDate === "" || $venue === "" || $category === "") {
        $errorMessage = "Please fill in all required fields.";
    } else {
        if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] === UPLOAD_ERR_OK) {
            $file = $_FILES["poster"];
            $allowedExtensions = ["jpg", "jpeg", "png", "webp"];
            $fileInfo = pathinfo($file["name"]);
            $ext = strtolower($fileInfo["extension"] ?? "");

            if (!in_array($ext, $allowedExtensions)) {
                $errorMessage = "Invalid file type. Allowed: JPG, PNG, WEBP.";
            } elseif ($file["size"] > 2 * 1024 * 1024) {
                $errorMessage = "File size exceeds 2MB.";
            } else {
                $newPoster = uniqid("poster_", true) . "." . $ext;
                if (move_uploaded_file($file["tmp_name"], __DIR__ . "/uploads/" . $newPoster)) {
                    if (!empty($event["poster_image"]) && file_exists(__DIR__ . "/uploads/" . $event["poster_image"])) {
                        unlink(__DIR__ . "/uploads/" . $event["poster_image"]);
                    }
                    $posterName = $newPoster;
                }
            }
        }

        if ($errorMessage === "") {
            try {
                $update = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, venue = ?, category = ?, poster_image = ?, status = ? WHERE id = ?");
                $update->execute([$title, $description, $eventDate, $venue, $category, $posterName, $status, $id]);
                header("Location: manage_events.php?msg=updated");
                exit;
            } catch (PDOException $e) {
                $errorMessage = "Update error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 12 | Edit Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <nav class="ttt">
        <a href="manage_events.php" class="m">Events List</a>|
        <a href="home.html" class="m1">Home</a>
    </nav>
</header>

<main>
<section>
    <h2 align="center">Edit Event (#<?php echo $id; ?>)</h2>
    <br>

    <?php if ($errorMessage != ""): ?>
        <div class="alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_event.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <label>Event Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Description</label>
        <textarea name="description" rows="3"><?php echo htmlspecialchars($event['description'] ?? "", ENT_QUOTES, 'UTF-8'); ?></textarea>

        <label>Event Date</label>
        <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Venue</label>
        <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Category</label>
        <select name="category" required>
            <option value="Workshop" <?php if ($event['category'] === 'Workshop') echo 'selected'; ?>>Workshop</option>
            <option value="Competition" <?php if ($event['category'] === 'Competition') echo 'selected'; ?>>Competition</option>
            <option value="Seminar" <?php if ($event['category'] === 'Seminar') echo 'selected'; ?>>Seminar</option>
            <option value="Sports" <?php if ($event['category'] === 'Sports') echo 'selected'; ?>>Sports</option>
        </select>

        <label>Status</label>
        <select name="status">
            <option value="upcoming" <?php if ($event['status'] === 'upcoming') echo 'selected'; ?>>Upcoming</option>
            <option value="completed" <?php if ($event['status'] === 'completed') echo 'selected'; ?>>Completed</option>
            <option value="cancelled" <?php if ($event['status'] === 'cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>

        <label>Replace Poster (optional)</label>
        <?php if (!empty($event["poster_image"])): ?>
            <p style="margin: 5px 0;">Current: <strong><?php echo htmlspecialchars($event["poster_image"], ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <?php endif; ?>
        <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">

        <br><br>
        <button type="submit">Update Event</button>
    </form>

    <div class="action-links">
        <a href="manage_events.php" class="vvv">Cancel</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>
</body>
</html>
