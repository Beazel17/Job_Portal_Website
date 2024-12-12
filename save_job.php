<?php

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobTitle = $_POST['jobTitle'];
    $jobDescription = $_POST['jobDescription'];
    $jobLocation = $_POST['jobLocation'];
    $minSalary = $_POST['minSalary'];
    $maxSalary = $_POST['maxSalary'];

    try {
        $sql = "INSERT INTO jobs (job_title, job_description, job_location, min_salary, max_salary) 
                VALUES (:jobTitle, :jobDescription, :jobLocation, :minSalary, :maxSalary)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':jobTitle' => $jobTitle,
            ':jobDescription' => $jobDescription,
            ':jobLocation' => $jobLocation,
            ':minSalary' => $minSalary,
            ':maxSalary' => $maxSalary
        ]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Could not save the job.']);
    }
}
?>
