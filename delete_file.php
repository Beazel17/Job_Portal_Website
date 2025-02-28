<?php
require 'config.php';

// Get POST request data
$data = json_decode(file_get_contents("php://input"), true);
$fileId = isset($data['id']) ? intval($data['id']) : 0;

if ($fileId > 0) {
    try {
        $query = "SELECT file_path FROM shared_files WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            $filePath = $file['file_path'];

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $deleteQuery = "DELETE FROM shared_files WHERE id = :id";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->bindParam(':id', $fileId, PDO::PARAM_INT);

            if ($deleteStmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => "Database deletion failed"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "File not found in database"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid file ID"]);
}
?>
