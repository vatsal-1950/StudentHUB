<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeoutDuration = 900;

if (isset($_SESSION["last_activity"])) {
    if ((time() - $_SESSION["last_activity"]) > $timeoutDuration) {
        session_unset();
        session_destroy();
        header("Location: login.php?msg=timeout");
        exit;
    }
}
$_SESSION["last_activity"] = time();

function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php?msg=required");
        exit;
    }
}

function requireRole($requiredRole) {
    requireLogin();
    if ($_SESSION["role"] !== $requiredRole) {
        header("Location: login.php?msg=unauthorized");
        exit;
    }
}
?>
