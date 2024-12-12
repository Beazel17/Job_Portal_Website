<?php
// Include database configuration
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $education = $_POST['education'];

    // Convert skills from string to array, then back to a string with commas
    $skills_array = array_map('trim', explode(',', $skills));
    $skills_string = implode(', ', $skills_array);

    // Prepare SQL insert query
    $sql = "INSERT INTO resumes (user_id, name, phone, address, skills, experience, education) 
            VALUES (:user_id, :name, :phone, :address, :skills, :experience, :education)";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind parameters
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
        $stmt->bindParam(":address", $address, PDO::PARAM_STR);
        $stmt->bindParam(":skills", $skills_string, PDO::PARAM_STR);
        $stmt->bindParam(":experience", $experience, PDO::PARAM_STR);
        $stmt->bindParam(":education", $education, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            header("location: profile.php");
            exit;
        } else {
            echo "Error saving resume.";
        }
    }
    unset($stmt);
}
unset($pdo);
?>
