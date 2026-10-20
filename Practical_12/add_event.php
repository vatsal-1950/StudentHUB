<?php
require_once "db.php";

$title = "";
$description = "";
$eventDate = "";
$venue = "";
$category = "";
$status = "upcoming";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $status = trim($_POST["status"] ?? "upcoming");
    $posterName = null;

    if ($title === "" || $eventDate === "" || $venue === "" || $category === "") {
        $errorMessage = "Please fill in all required fields.";
    } else {
        if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] === UPLOAD_ERR_OK) {
            $file = $_FILES["poster"];
            $allowedExtensions = ["jpg", "jpeg", "png", "webp"];
            $allowedMimes = ["image/jpeg", "image/png", "image/webp"];
            $fileInfo = pathinfo($file["name"]);
            $ext = strtolower($fileInfo["extension"] ?? "");
            $mime = mime_content_type($file["tmp_name"]);
            $maxSize = 2 * 1024 * 1024;

            if (!in_array($ext, $allowedExtensions) || !in_array($mime, $allowedMimes)) {
                $errorMessage = "Invalid image type. Only JPG, PNG, and WEBP files are allowed.";
            } elseif ($file["size"] > $maxSize) {
                $errorMessage = "File size exceeds 2MB limit.";
            } else {
                $posterName = uniqid("poster_", true) . "." . $ext;
                $targetDir = __DIR__ . "/uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                if (!move_uploaded_file($file["tmp_name"], $targetDir . $posterName)) {
                    $errorMessage = "Failed to upload poster image.";
                    $posterName = null;
                }
            }
        }

        if ($errorMessage === "") {
            try {
                $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, venue, category, poster_image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $eventDate, $venue, $category, $posterName, $status]);
                header("Location: manage_events.php?msg=added");
                exit;
            } catch (PDOException $e) {
                $errorMessage = "Database insertion error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 12 | Add Event</title>
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
    <h2 align="center">Create Event with Poster Upload</h2>
    <br>

    <?php if ($errorMessage != ""): ?>
        <div class="alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="add_event.php" enctype="multipart/form-data">
        <label>Event Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Description</label>
        <textarea name="description" rows="3"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>

        <label>Event Date</label>
        <input type="date" name="event_date" value="<?php echo htmlspecialchars($eventDate, ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Venue</label>
        <input type="text" name="venue" value="<?php echo htmlspecialchars($venue, ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Category</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Workshop">Workshop</option>
            <option value="Competition">Competition</option>
            <option value="Seminar">Seminar</option>
            <option value="Sports">Sports</option>
        </select>

        <label>Status</label>
        <select name="status">
            <option value="upcoming">Upcoming</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <label>Upload Event Poster (JPG/PNG/WEBP, Max 2MB)</label>
        <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">

        <br><br>
        <button type="submit">Publish Event</button>
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
