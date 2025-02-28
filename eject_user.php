<?php
require 'config.php';

// Get POST request data
$data = json_decode(file_get_contents("php://input"), true);
$userId = isset($data['user_id']) ? intval($data['user_id']) : 0;

if ($userId > 0) {
    try {
        $query = "UPDATE users SET status = 'ejected' WHERE id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update user status"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid user ID"]);
}
?>
