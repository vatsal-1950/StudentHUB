<?php
require_once "db.php";

$id = (int)($_GET["id"] ?? 0);

if ($pdo !== null && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT poster_image FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch();

        if ($event && !empty($event["poster_image"])) {
            $posterPath = __DIR__ . "/uploads/" . $event["poster_image"];
            if (file_exists($posterPath)) {
                unlink($posterPath);
            }
        }

        $del = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $del->execute([$id]);

        header("Location: manage_events.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        header("Location: manage_events.php?msg=error");
        exit;
    }
}

header("Location: manage_events.php");
exit;
?>
