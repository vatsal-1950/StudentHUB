<?php
require_once "db.php";

$search = trim($_GET["search"] ?? "");
$courseFilter = trim($_GET["course"] ?? "");
$yearFilter = trim($_GET["year"] ?? "");
$students = [];
$successMessage = "";
$errorMessage = "";

if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "added") $successMessage = "Student added successfully.";
    elseif ($_GET["msg"] === "updated") $successMessage = "Student details updated successfully.";
    elseif ($_GET["msg"] === "deleted") $successMessage = "Student record deleted successfully.";
    elseif ($_GET["msg"] === "error") $errorMessage = "Operation could not be completed.";
}

if ($pdo !== null) {
    try {
        $sql = "SELECT * FROM students WHERE status = 'active'";
        $params = [];

        if ($search !== "") {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR mobile LIKE ?)";
            $wildcard = "%" . $search . "%";
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        if ($courseFilter !== "") {
            $sql .= " AND course = ?";
            $params[] = $courseFilter;
        }

        if ($yearFilter !== "") {
            $sql .= " AND year = ?";
            $params[] = $yearFilter;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();
    } catch (PDOException $e) {
        $errorMessage = "Query error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 11 | Student Management CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StudentHub Portal</h1>
    <button id="hamburgerBtn" class="hamburger-btn" aria-label="toggle menu">☰ Menu</button>
    <nav class="ttt">
        <a href="home.html" class="m">Home</a>|
        <a href="about.html" class="m1">About</a>|
        <a href="students.php" class="m2">Students CRUD</a>|
        <a href="login.html" class="m9">Login</a>|
        <a href="dashboard.html" class="m3">Dashboard</a>|
        <a href="events.html" class="m4">Events</a>|
        <a href="profile.html" class="m5">Profile</a>|
        <a href="contact.html" class="m6">Contact</a>|
        <a href="faq.html" class="m7">FAQ</a>|
        <a href="feedback.html" class="m8">Feedback</a>
    </nav>
    <button id="themeButton" class="theme-btn">Light / Dark</button>
</header>

<main>
<section>
    <h2 align="center">Practical 11: Student Management CRUD</h2>
    <p align="center" style="margin-top: 5px;">Add, View, Edit, Soft Delete, Search, Filter & CSV Export</p>
    <br>

    <?php if ($successMessage != ""): ?>
        <div class="alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if ($errorMessage != ""): ?>
        <div class="alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <div style="text-align: center; margin: 15px auto;">
        <a href="add_student.php" class="vvv" style="margin-right: 15px;">+ Add New Student</a>
        <a href="export_csv.php" class="vvv" style="color: green;">Download CSV Export</a>
    </div>

    <form method="GET" action="students.php" style="max-width: 800px; display: flex; gap: 10px; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 200px;">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search name, email, or mobile...">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <select name="course">
                <option value="">All Courses</option>
                <option value="B.Tech Computer Engineering" <?php if ($courseFilter === "B.Tech Computer Engineering") echo "selected"; ?>>Computer Engineering</option>
                <option value="B.Tech Information Technology" <?php if ($courseFilter === "B.Tech Information Technology") echo "selected"; ?>>Information Technology</option>
                <option value="B.Tech Civil Engineering" <?php if ($courseFilter === "B.Tech Civil Engineering") echo "selected"; ?>>Civil Engineering</option>
                <option value="B.Tech Mechanical Engineering" <?php if ($courseFilter === "B.Tech Mechanical Engineering") echo "selected"; ?>>Mechanical Engineering</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 130px;">
            <select name="year">
                <option value="">All Years</option>
                <option value="First Year" <?php if ($yearFilter === "First Year") echo "selected"; ?>>First Year</option>
                <option value="Second Year" <?php if ($yearFilter === "Second Year") echo "selected"; ?>>Second Year</option>
                <option value="Third Year" <?php if ($yearFilter === "Third Year") echo "selected"; ?>>Third Year</option>
                <option value="Fourth Year" <?php if ($yearFilter === "Fourth Year") echo "selected"; ?>>Fourth Year</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 100px;">
            <button type="submit" style="margin-top: 5px; width: 100%;">Filter</button>
        </div>
    </form>

    <div class="table-container">
        <h3 style="color: blue; text-align: center;">Total Active Students: <?php echo count($students); ?></h3>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($s["name"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($s["email"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($s["mobile"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($s["course"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($s["year"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($s["gender"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a href="edit_student.php?id=<?php echo $s['id']; ?>" class="vvv">Edit</a> |
                                <a href="delete_student.php?id=<?php echo $s['id']; ?>" class="vvv" style="color: red;" onclick="return confirm('Are you sure you want to delete this student record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin: 20px;">No students match the criteria.</p>
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
