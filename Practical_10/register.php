<?php
require_once "db.php";

$name = "";
$email = "";
$mobile = "";
$course = "";
$year = "";
$gender = "";
$password = "";
$confirm = "";
$terms = "";

$errors = [];
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $mobile = isset($_POST["mobile"]) ? trim($_POST["mobile"]) : "";
    $course = isset($_POST["course"]) ? trim($_POST["course"]) : "";
    $year = isset($_POST["year"]) ? trim($_POST["year"]) : "";
    $gender = isset($_POST["gender"]) ? trim($_POST["gender"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $confirm = isset($_POST["confirm"]) ? $_POST["confirm"] : "";
    $terms = isset($_POST["terms"]) ? $_POST["terms"] : "";

    if ($name == "") {
        $errors["name"] = "Full name is required.";
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

    if ($mobile == "") {
        $errors["mobile"] = "Mobile number is required.";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors["mobile"] = "Mobile number must be exactly 10 digits.";
    }

    if ($course == "" || $course == "Select Course") {
        $errors["course"] = "Please select a valid course.";
    }

    if ($year == "" || $year == "Select Year") {
        $errors["year"] = "Please select a valid year.";
    }

    if ($gender == "") {
        $errors["gender"] = "Please select gender.";
    }

    if ($password == "") {
        $errors["password"] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors["password"] = "Password must be at least 8 characters.";
    }

    if ($confirm == "") {
        $errors["confirm"] = "Confirm password is required.";
    } elseif ($password !== $confirm) {
        $errors["confirm"] = "Passwords do not match.";
    }

    if ($terms == "") {
        $errors["terms"] = "You must agree to the Terms & Conditions.";
    }

    if (count($errors) == 0) {
        if ($pdo === null) {
            $errorMessage = "Database connection error. Please ensure MySQL is running in XAMPP.";
        } else {
            try {
                $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $checkStmt->execute([$email]);

                if ($checkStmt->fetch()) {
                    $errors["email"] = "This email is already registered. Please login or use a different email.";
                    $errorMessage = "Duplicate email detected. Registration cannot proceed.";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $insertUser = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
                    $insertUser->execute([$name, $email, $hashedPassword]);

                    $insertStudent = $pdo->prepare("INSERT INTO students (name, email, mobile, course, year, gender) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertStudent->execute([$name, $email, $mobile, $course, $year, $gender]);

                    $ip = $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";
                    $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_email, action, details, ip_address) VALUES (?, 'USER_REGISTRATION', 'New user registered via Practical 9', ?)");
                    $logStmt->execute([$email, $ip]);

                    $successMessage = "User registration successful! Details inserted securely with password hashing.";
                    $name = "";
                    $email = "";
                    $mobile = "";
                    $course = "";
                    $year = "";
                    $gender = "";
                    $password = "";
                    $confirm = "";
                    $terms = "";
                }
            } catch (PDOException $e) {
                $errorMessage = "Database query error: " . $e->getMessage();
            }
        }
    } else {
        $errorMessage = "Please resolve the highlighted validation errors and try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 9 | Secure Registration</title>
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
        <a href="contact.html" class="m6">Contact</a>|
        <a href="faq.html" class="m7">FAQ</a>|
        <a href="feedback.html" class="m8">Feedback</a>|
        <a href="view_users.php" class="m10">Users</a>
    </nav>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<main>
<section>
    <h2 align="center">Practical 9: Secure User Registration</h2>
    <p align="center" style="margin-top: 5px;">Database Insert with Duplicate Email Check & Password Hashing</p>
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

    <form action="register.php" method="POST">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter your full name">
        <?php if (isset($errors["name"])): ?>
            <span class="error"><?php echo $errors["name"]; ?></span>
        <?php endif; ?>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter your email">
        <?php if (isset($errors["email"])): ?>
            <span class="error"><?php echo $errors["email"]; ?></span>
        <?php endif; ?>

        <label for="mobile">Mobile Number</label>
        <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter 10-digit mobile number">
        <?php if (isset($errors["mobile"])): ?>
            <span class="error"><?php echo $errors["mobile"]; ?></span>
        <?php endif; ?>

        <label for="course">Course</label>
        <select id="course" name="course">
            <option value="">Select Course</option>
            <option value="B.Tech Computer Engineering" <?php if ($course == "B.Tech Computer Engineering") echo "selected"; ?>>B.Tech Computer Engineering</option>
            <option value="B.Tech Information Technology" <?php if ($course == "B.Tech Information Technology") echo "selected"; ?>>B.Tech Information Technology</option>
            <option value="B.Tech Civil Engineering" <?php if ($course == "B.Tech Civil Engineering") echo "selected"; ?>>B.Tech Civil Engineering</option>
            <option value="B.Tech Mechanical Engineering" <?php if ($course == "B.Tech Mechanical Engineering") echo "selected"; ?>>B.Tech Mechanical Engineering</option>
        </select>
        <?php if (isset($errors["course"])): ?>
            <span class="error"><?php echo $errors["course"]; ?></span>
        <?php endif; ?>

        <label for="year">Year</label>
        <select id="year" name="year">
            <option value="">Select Year</option>
            <option value="First Year" <?php if ($year == "First Year") echo "selected"; ?>>First Year</option>
            <option value="Second Year" <?php if ($year == "Second Year") echo "selected"; ?>>Second Year</option>
            <option value="Third Year" <?php if ($year == "Third Year") echo "selected"; ?>>Third Year</option>
            <option value="Fourth Year" <?php if ($year == "Fourth Year") echo "selected"; ?>>Fourth Year</option>
        </select>
        <?php if (isset($errors["year"])): ?>
            <span class="error"><?php echo $errors["year"]; ?></span>
        <?php endif; ?>

        <label>Gender</label>
        <input type="radio" name="gender" value="Male" <?php if ($gender == "Male") echo "checked"; ?>> Male
        <input type="radio" name="gender" value="Female" <?php if ($gender == "Female") echo "checked"; ?>> Female
        <input type="radio" name="gender" value="Other" <?php if ($gender == "Other") echo "checked"; ?>> Other
        <?php if (isset($errors["gender"])): ?>
            <span class="error"><?php echo $errors["gender"]; ?></span>
        <?php endif; ?>

        <br><br>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create Password (min 8 chars)">
        <?php if (isset($errors["password"])): ?>
            <span class="error"><?php echo $errors["password"]; ?></span>
        <?php endif; ?>

        <label for="confirm">Confirm Password</label>
        <input type="password" id="confirm" name="confirm" placeholder="Confirm Password">
        <?php if (isset($errors["confirm"])): ?>
            <span class="error"><?php echo $errors["confirm"]; ?></span>
        <?php endif; ?>

        <br>
        <input type="checkbox" id="terms" name="terms" value="yes" <?php if ($terms == "yes") echo "checked"; ?>>
        <label for="terms" style="display:inline; font-weight:normal;">I agree to the Terms & Conditions</label>
        <?php if (isset($errors["terms"])): ?>
            <span class="error"><?php echo $errors["terms"]; ?></span>
        <?php endif; ?>

        <br><br>
        <button type="submit">Create Account</button>
    </form>

    <div class="action-links">
        <a href="view_users.php" class="vvv">View Registered Users</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
