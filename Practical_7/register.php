<?php
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
        $cleanName = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
        $cleanEmail = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
        $cleanMobile = htmlspecialchars($mobile, ENT_QUOTES, "UTF-8");
        $cleanCourse = htmlspecialchars($course, ENT_QUOTES, "UTF-8");
        $cleanYear = htmlspecialchars($year, ENT_QUOTES, "UTF-8");
        $cleanGender = htmlspecialchars($gender, ENT_QUOTES, "UTF-8");
        $registeredAt = date("Y-m-d H:i:s");

        $csvFile = __DIR__ . "/students.csv";
        $csvExists = file_exists($csvFile) && filesize($csvFile) > 0;
        $csvHandle = fopen($csvFile, "a");
        if ($csvHandle) {
            if (!$csvExists) {
                fputcsv($csvHandle, ["Name", "Email", "Mobile", "Course", "Year", "Gender", "Registered_At"]);
            }
            fputcsv($csvHandle, [$cleanName, $cleanEmail, $cleanMobile, $cleanCourse, $cleanYear, $cleanGender, $registeredAt]);
            fclose($csvHandle);
        }

        $jsonFile = __DIR__ . "/students.json";
        $studentList = [];
        if (file_exists($jsonFile)) {
            $jsonRaw = file_get_contents($jsonFile);
            if (!empty($jsonRaw)) {
                $decoded = json_decode($jsonRaw, true);
                if (is_array($decoded)) {
                    $studentList = $decoded;
                }
            }
        }
        $studentList[] = [
            "name" => $cleanName,
            "email" => $cleanEmail,
            "mobile" => $cleanMobile,
            "course" => $cleanCourse,
            "year" => $cleanYear,
            "gender" => $cleanGender,
            "registered_at" => $registeredAt
        ];
        file_put_contents($jsonFile, json_encode($studentList, JSON_PRETTY_PRINT), LOCK_EX);

        $successMessage = "Student registration completed successfully! Data has been stored in CSV and JSON.";
        $name = "";
        $email = "";
        $mobile = "";
        $course = "";
        $year = "";
        $gender = "";
        $password = "";
        $confirm = "";
        $terms = "";
    } else {
        $errorMessage = "Please resolve the errors highlighted below and submit again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Register</title>
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
    <h2 align="center">Student Registration (PHP Form Processing)</h2>
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
        <button type="submit">Register</button>
    </form>

    <div class="action-links">
        <a href="view_records.php" class="vvv">View All Stored Records</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
