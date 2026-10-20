<?php
require_once "db.php";

$id = (int)($_GET["id"] ?? 0);

if ($pdo !== null && $id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE students SET status = 'inactive' WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: students.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        header("Location: students.php?msg=error");
        exit;
    }
}

header("Location: students.php");
exit;
?>
