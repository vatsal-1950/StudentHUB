<?php
require_once "db.php";

$name = "";
$email = "";
$mobile = "";
$course = "";
$year = "";
$gender = "";
$errors = [];
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $gender = trim($_POST["gender"] ?? "");

    if ($name === "") $errors["name"] = "Name is required.";
    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors["email"] = "Valid email is required.";
    if ($mobile === "" || !preg_match("/^[0-9]{10}$/", $mobile)) $errors["mobile"] = "10-digit mobile is required.";
    if ($course === "") $errors["course"] = "Course is required.";
    if ($year === "") $errors["year"] = "Year is required.";
    if ($gender === "") $errors["gender"] = "Gender is required.";

    if (count($errors) === 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (name, email, mobile, course, year, gender, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$name, $email, $mobile, $course, $year, $gender]);
            header("Location: students.php?msg=added");
            exit;
        } catch (PDOException $e) {
            $errorMessage = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 11 | Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <nav class="ttt">
        <a href="students.php" class="m">Students List</a>|
        <a href="home.html" class="m1">Home</a>
    </nav>
</header>

<main>
<section>
    <h2 align="center">Add New Student Record</h2>
    <br>

    <?php if ($errorMessage != ""): ?>
        <div class="alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="add_student.php">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required>
        <?php if (isset($errors["name"])) echo "<span class='error'>{$errors['name']}</span>"; ?>

        <label>Email Address</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
        <?php if (isset($errors["email"])) echo "<span class='error'>{$errors['email']}</span>"; ?>

        <label>Mobile Number</label>
        <input type="tel" name="mobile" value="<?php echo htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8'); ?>" required>
        <?php if (isset($errors["mobile"])) echo "<span class='error'>{$errors['mobile']}</span>"; ?>

        <label>Course</label>
        <select name="course" required>
            <option value="">Select Course</option>
            <option value="B.Tech Computer Engineering">B.Tech Computer Engineering</option>
            <option value="B.Tech Information Technology">B.Tech Information Technology</option>
            <option value="B.Tech Civil Engineering">B.Tech Civil Engineering</option>
            <option value="B.Tech Mechanical Engineering">B.Tech Mechanical Engineering</option>
        </select>
        <?php if (isset($errors["course"])) echo "<span class='error'>{$errors['course']}</span>"; ?>

        <label>Year</label>
        <select name="year" required>
            <option value="">Select Year</option>
            <option value="First Year">First Year</option>
            <option value="Second Year">Second Year</option>
            <option value="Third Year">Third Year</option>
            <option value="Fourth Year">Fourth Year</option>
        </select>
        <?php if (isset($errors["year"])) echo "<span class='error'>{$errors['year']}</span>"; ?>

        <label>Gender</label>
        <input type="radio" name="gender" value="Male" required> Male
        <input type="radio" name="gender" value="Female"> Female
        <input type="radio" name="gender" value="Other"> Other
        <?php if (isset($errors["gender"])) echo "<span class='error'>{$errors['gender']}</span>"; ?>

        <br><br>
        <button type="submit">Save Student Record</button>
    </form>

    <div class="action-links">
        <a href="students.php" class="vvv">Cancel and Return</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>
</body>
</html>
