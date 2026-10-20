<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "db.php";

$method = $_SERVER["REQUEST_METHOD"];
$action = $_GET["action"] ?? ($_POST["action"] ?? "");

if ($pdo === null) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection unavailable."]);
    exit;
}

if ($method === "GET") {
    $search = trim($_GET["search"] ?? "");
    try {
        if ($search !== "") {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE status = 'active' AND (name LIKE ? OR email LIKE ? OR course LIKE ?) ORDER BY id DESC");
            $wild = "%" . $search . "%";
            $stmt->execute([$wild, $wild, $wild]);
        } else {
            $stmt = $pdo->query("SELECT * FROM students WHERE status = 'active' ORDER BY id DESC");
        }
        $data = $stmt->fetchAll();
        http_response_code(200);
        echo json_encode(["status" => "success", "data" => $data]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}

if ($method === "POST") {
    $rawInput = file_get_contents("php://input");
    $jsonData = json_decode($rawInput, true);

    if (is_array($jsonData)) {
        $action = $jsonData["action"] ?? $action;
        $name = trim($jsonData["name"] ?? "");
        $email = trim($jsonData["email"] ?? "");
        $mobile = trim($jsonData["mobile"] ?? "");
        $course = trim($jsonData["course"] ?? "");
        $year = trim($jsonData["year"] ?? "");
        $gender = trim($jsonData["gender"] ?? "");
        $id = (int)($jsonData["id"] ?? 0);
    } else {
        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $mobile = trim($_POST["mobile"] ?? "");
        $course = trim($_POST["course"] ?? "");
        $year = trim($_POST["year"] ?? "");
        $gender = trim($_POST["gender"] ?? "");
        $id = (int)($_POST["id"] ?? 0);
    }

    if ($action === "create") {
        if ($name === "" || $email === "" || $mobile === "") {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Name, email and mobile are required."]);
            exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO students (name, email, mobile, course, year, gender, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$name, $email, $mobile, $course, $year, $gender]);
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Student record created successfully.", "id" => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }

    if ($action === "update") {
        if ($id <= 0 || $name === "" || $email === "") {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Valid ID, name and email required."]);
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, mobile = ?, course = ?, year = ?, gender = ? WHERE id = ?");
            $stmt->execute([$name, $email, $mobile, $course, $year, $gender, $id]);
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Student record updated successfully."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }

    if ($action === "delete") {
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Valid ID required for deletion."]);
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE students SET status = 'inactive' WHERE id = ?");
            $stmt->execute([$id]);
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Student record deleted successfully."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;
    }
}

http_response_code(400);
echo json_encode(["status" => "error", "message" => "Invalid API request action."]);
exit;
?>
