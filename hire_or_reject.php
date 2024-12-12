<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$applicationId = filter_input(INPUT_GET, 'application_id', FILTER_VALIDATE_INT);
$jobId = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);

if (!$action || !$applicationId || !$jobId) {
    echo "<p>Invalid request parameters. Please go back and try again.</p>";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM notifications WHERE user_id = :user_id AND job_id = :job_id");
    $stmt->execute(['user_id' => $_SESSION["user_id"], 'job_id' => $jobId]);
    $existingNotification = $stmt->fetch();

    if ($existingNotification) {
        echo "<p>The applicant has already been notified about this job. You cannot make another decision.</p>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT r.name, r.user_id, j.job_title, u.personal_email 
                           FROM applications a
                           INNER JOIN resumes r ON a.user_id = r.user_id
                           INNER JOIN jobs j ON a.job_id = j.id
                           INNER JOIN users u ON r.user_id = u.id
                           WHERE a.id = :application_id AND a.job_id = :job_id");
    $stmt->execute(['application_id' => $applicationId, 'job_id' => $jobId]);
    $applicant = $stmt->fetch();

    if (!$applicant) {
        echo "<p>Applicant not found. Please try again.</p>";
        exit;
    }

    $applicantName = htmlspecialchars($applicant['name']);
    $userId = $applicant['user_id'];
    $jobTitle = htmlspecialchars($applicant['job_title']);
    $email = htmlspecialchars($applicant['personal_email']);
    $adminName = "Group3"; 
    $adminEmail = "admin@group3.com"; 

    $notification = "";
    $subject = "";
    $message = "";
    $status = '';

    if ($action === "hire") {
        $notification = "Congratulations on being hired for the $jobTitle position.";
        $subject = "Congratulations on Your Employment Offer";
        $message = "
            Dear $applicantName,

            We are delighted to inform you that you have been selected for the position of $jobTitle.
            Please review the attached offer letter and let us know your acceptance.

            Best regards,
            $adminName
        ";
        $status = 'hired'; 
    } elseif ($action === "reject") {
        $notification = "You have not been selected for the $jobTitle position.";
        $subject = "Thank You for Your Application";
        $message = "
            Dear $applicantName,

            After careful consideration, we regret to inform you that you have not been selected for the position of $jobTitle. 
            Thank you for your interest.

            Best regards,
            $adminName
        ";
        $status = 'rejected';
    } else {
        echo "<p>Invalid action specified. Please try again.</p>";
        exit;
    }

    $stmt = $pdo->prepare("UPDATE applications SET status = :status WHERE id = :application_id");
    $stmt->execute(['status' => $status, 'application_id' => $applicationId]);

    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, job_id, message, created_at) VALUES (:user_id, :job_id, :message, NOW())");
    $stmt->execute(['user_id' => $userId, 'job_id' => $jobId, 'message' => $notification]);

    mail($email, $subject, $message, "From: $adminEmail");

    header("Location: check_applicant.php?job_id=$jobId");
    exit;

} catch (PDOException $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
