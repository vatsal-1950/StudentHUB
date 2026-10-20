<?php
require_once "db.php";

$id = (int)($_GET["id"] ?? 0);
$student = null;
$errorMessage = "";

if ($pdo !== null && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
}

if (!$student) {
    header("Location: students.php?msg=error");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $gender = trim($_POST["gender"] ?? "");

    if ($name === "" || $email === "" || $mobile === "") {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $update = $pdo->prepare("UPDATE students SET name = ?, email = ?, mobile = ?, course = ?, year = ?, gender = ? WHERE id = ?");
            $update->execute([$name, $email, $mobile, $course, $year, $gender, $id]);
            header("Location: students.php?msg=updated");
            exit;
        } catch (PDOException $e) {
            $errorMessage = "Update error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 11 | Edit Student</title>
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
    <h2 align="center">Edit Student Record (#<?php echo $id; ?>)</h2>
    <br>

    <?php if ($errorMessage != ""): ?>
        <div class="alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_student.php?id=<?php echo $id; ?>">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Email Address</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Mobile Number</label>
        <input type="tel" name="mobile" value="<?php echo htmlspecialchars($student['mobile'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Course</label>
        <select name="course" required>
            <option value="B.Tech Computer Engineering" <?php if ($student['course'] === 'B.Tech Computer Engineering') echo 'selected'; ?>>B.Tech Computer Engineering</option>
            <option value="B.Tech Information Technology" <?php if ($student['course'] === 'B.Tech Information Technology') echo 'selected'; ?>>B.Tech Information Technology</option>
            <option value="B.Tech Civil Engineering" <?php if ($student['course'] === 'B.Tech Civil Engineering') echo 'selected'; ?>>B.Tech Civil Engineering</option>
            <option value="B.Tech Mechanical Engineering" <?php if ($student['course'] === 'B.Tech Mechanical Engineering') echo 'selected'; ?>>B.Tech Mechanical Engineering</option>
        </select>

        <label>Year</label>
        <select name="year" required>
            <option value="First Year" <?php if ($student['year'] === 'First Year') echo 'selected'; ?>>First Year</option>
            <option value="Second Year" <?php if ($student['year'] === 'Second Year') echo 'selected'; ?>>Second Year</option>
            <option value="Third Year" <?php if ($student['year'] === 'Third Year') echo 'selected'; ?>>Third Year</option>
            <option value="Fourth Year" <?php if ($student['year'] === 'Fourth Year') echo 'selected'; ?>>Fourth Year</option>
        </select>

        <label>Gender</label>
        <input type="radio" name="gender" value="Male" <?php if ($student['gender'] === 'Male') echo 'checked'; ?>> Male
        <input type="radio" name="gender" value="Female" <?php if ($student['gender'] === 'Female') echo 'checked'; ?>> Female
        <input type="radio" name="gender" value="Other" <?php if ($student['gender'] === 'Other') echo 'checked'; ?>> Other

        <br><br>
        <button type="submit">Update Record</button>
    </form>

    <div class="action-links">
        <a href="students.php" class="vvv">Cancel</a>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>
</body>
</html>
