<?php
require_once "auth.php";
require_once "db.php";

$email = $_COOKIE["remember_user"] ?? "";
$password = "";
$errorMessage = "";
$successMessage = "";

if (isset($_GET["msg"])) {
    if ($_GET["msg"] === "logout") {
        $successMessage = "You have logged out successfully.";
    } elseif ($_GET["msg"] === "timeout") {
        $errorMessage = "Your session expired due to inactivity. Please log in again.";
    } elseif ($_GET["msg"] === "required") {
        $errorMessage = "Please log in to access your dashboard.";
    } elseif ($_GET["msg"] === "unauthorized") {
        $errorMessage = "Access restricted. You do not have permission for that area.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $remember = isset($_POST["remember"]);

    if ($email === "" || $password === "") {
        $errorMessage = "Please enter both email and password.";
    } elseif ($pdo === null) {
        $errorMessage = "Database is offline. Please start MySQL from XAMPP Control Panel.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["last_activity"] = time();

                $updateLogin = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateLogin->execute([$user["id"]]);

                $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_email, action, details, ip_address) VALUES (?, 'USER_LOGIN', 'Successful session login', ?)");
                $logStmt->execute([$user["email"], $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"]);

                if ($remember) {
                    setcookie("remember_user", $email, time() + (86400 * 30), "/");
                } else {
                    setcookie("remember_user", "", time() - 3600, "/");
                }

                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit;
            } else {
                $errorMessage = "Invalid email or password. Please try again.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Login query error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practical 10 | Secure Login</title>
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
        <a href="login.php" class="m9">Login</a>|
        <a href="student_dashboard.php" class="m3">Dashboard</a>|
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
    <h2 align="center">StudentHub Login (Practical 10)</h2>
    <p align="center" style="margin-top: 5px;">Secure Sessions, RBAC & Inactivity Timeout</p>
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

    <form action="login.php" method="POST">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter Password" required>

        <br>
        <input type="checkbox" id="remember" name="remember" value="1" <?php if (!empty($_COOKIE["remember_user"])) echo "checked"; ?>>
        <label for="remember" style="display:inline; font-weight:normal;">Remember Me</label>

        <br><br>
        <button type="submit">Login</button>

        <br><br>
        <p align="center">
            Don't have an account?
            <a href="register.php" class="vvv">Register Here</a>
        </p>
    </form>

    <div class="card" style="max-width: 550px; margin: 20px auto; display: block;">
        <h3>Default Demo Credentials:</h3>
        <p><strong>Admin Role:</strong> admin@studenthub.com | Password: <code>Admin@123</code></p>
        <p><strong>Student Role:</strong> student@studenthub.com | Password: <code>Student@123</code></p>
    </div>
</section>
</main>

<footer>
    <p>© 2026 StudentHub Portal | Web Development Frameworks</p>
</footer>

<script src="script.js"></script>
</body>
</html>
