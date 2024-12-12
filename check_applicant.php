<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once "config.php";

$jobId = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
if (!$jobId) {
    echo "<p>Invalid Job ID. Please go back and select a valid job.</p>";
    exit;
}

try {
    $sql = "SELECT a.id AS application_id, r.name AS applicant_name, r.skills, r.experience, r.education, 
                   n.message AS decision_message
            FROM applications a
            INNER JOIN resumes r ON a.user_id = r.user_id
            LEFT JOIN notifications n ON a.user_id = n.user_id AND a.job_id = n.job_id
            WHERE a.job_id = :job_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['job_id' => $jobId]);
    $applicants = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p>Error fetching applicants: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Applicants</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Resume of Applicant</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="btn btn-secondary" href="admin_dashboard.php">Back</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Applicants for Job ID: <?php echo htmlspecialchars($jobId); ?></h1>

        <?php if ($applicants): ?>
            <div class="list-group mt-3">
                <?php foreach ($applicants as $applicant): ?>
                    <div class="list-group-item">
                        <h5 class="mb-1">Name: <?php echo htmlspecialchars($applicant['applicant_name']); ?></h5>
                        <p><strong>Skills:</strong> <?php echo nl2br(htmlspecialchars($applicant['skills'])); ?></p>
                        <p><strong>Experience:</strong> <?php echo nl2br(htmlspecialchars($applicant['experience'])); ?></p>
                        <p><strong>Education:</strong> <?php echo nl2br(htmlspecialchars($applicant['education'])); ?></p>

                        <?php if ($applicant['decision_message']): ?>
                           
                            <p class="text-success"><?php echo htmlspecialchars($applicant['decision_message']); ?></p>
                        <?php else: ?>
                            <a href="hire_or_reject.php?action=hire&application_id=<?php echo htmlspecialchars($applicant['application_id']); ?>&job_id=<?php echo htmlspecialchars($jobId); ?>" 
                               class="btn btn-success">Hire</a>
                            <a href="hire_or_reject.php?action=reject&application_id=<?php echo htmlspecialchars($applicant['application_id']); ?>&job_id=<?php echo htmlspecialchars($jobId); ?>" 
                               class="btn btn-danger">Reject</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-3">No applicants for this job yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
