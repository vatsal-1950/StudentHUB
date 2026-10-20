<?php
$name = "";
$email = "";
$message = "";

$errors = [];
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $message = isset($_POST["message"]) ? trim($_POST["message"]) : "";

    if ($name == "") {
        $errors["name"] = "Name is required.";
    } elseif (strlen($name) < 2) {
        $errors["name"] = "Name must be at least 2 characters.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors["name"] = "Name can only contain letters and spaces.";
    }

    if ($email == "") {
        $errors["email"] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Please enter a valid email address.";
    }

    if ($message == "") {
        $errors["message"] = "Message cannot be empty.";
    } elseif (strlen($message) < 5) {
        $errors["message"] = "Message must be at least 5 characters.";
    }

    if (count($errors) == 0) {
        $cleanName = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
        $cleanEmail = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
        $cleanMessage = htmlspecialchars($message, ENT_QUOTES, "UTF-8");
        $submittedAt = date("Y-m-d H:i:s");

        $csvFile = __DIR__ . "/contact.csv";
        $csvExists = file_exists($csvFile) && filesize($csvFile) > 0;
        $csvHandle = fopen($csvFile, "a");
        if ($csvHandle) {
            if (!$csvExists) {
                fputcsv($csvHandle, ["Name", "Email", "Message", "Submitted_At"]);
            }
            fputcsv($csvHandle, [$cleanName, $cleanEmail, $cleanMessage, $submittedAt]);
            fclose($csvHandle);
        }

        $jsonFile = __DIR__ . "/contact.json";
        $contactList = [];
        if (file_exists($jsonFile)) {
            $jsonRaw = file_get_contents($jsonFile);
            if (!empty($jsonRaw)) {
                $decoded = json_decode($jsonRaw, true);
                if (is_array($decoded)) {
                    $contactList = $decoded;
                }
            }
        }
        $contactList[] = [
            "name" => $cleanName,
            "email" => $cleanEmail,
            "message" => $cleanMessage,
            "submitted_at" => $submittedAt
        ];
        file_put_contents($jsonFile, json_encode($contactList, JSON_PRETTY_PRINT), LOCK_EX);

        $successMessage = "Your message has been sent successfully and saved to CSV and JSON storage.";
        $name = "";
        $email = "";
        $message = "";
    } else {
        $errorMessage = "Please resolve the errors highlighted below and try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Contact</title>
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
    <h2 align="center">Contact Us (PHP Form Processing)</h2>
    <br>

    <?php if ($successMessage != ""): ?>
        <div class="alert-success">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage != ""): ?>
        <div class="alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <form action="contact.php" method="POST">
        <label for="contact_name">Name</label>
        <input type="text" id="contact_name" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Name">
        <?php if (isset($errors["name"])): ?>
            <span class="error"><?php echo $errors["name"]; ?></span>
        <?php endif; ?>

        <label for="contact_email">Email</label>
        <input type="email" id="contact_email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Email">
        <?php if (isset($errors["email"])): ?>
            <span class="error"><?php echo $errors["email"]; ?></span>
        <?php endif; ?>

        <label for="contact_message">Message</label>
        <textarea id="contact_message" name="message" rows="5" placeholder="Enter Message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></textarea>
        <?php if (isset($errors["message"])): ?>
            <span class="error"><?php echo $errors["message"]; ?></span>
        <?php endif; ?>

        <button type="submit">Send Message</button>
    </form>

    <div class="action-links">
        <a href="view_records.php" class="vvv">View All Stored Records</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal</p>
</footer>

<script src="script.js"></script>
</body>
</html>
