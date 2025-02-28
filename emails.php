<?php
session_start();
require_once "config.php"; 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["id"])) {
  
    header("location: login.php");
    exit;
}

$userId = $_SESSION["id"];

try {
    $stmt = $pdo->prepare("SELECT n.message, n.created_at, j.job_title
                           FROM notifications n
                           INNER JOIN jobs j ON n.job_id = j.id
                           WHERE n.user_id = :user_id
                           ORDER BY n.created_at DESC");
    $stmt->execute(['user_id' => $userId]);
    $notifications = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p>Error fetching notifications: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emails - Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background: linear-gradient(to bottom right, #4CAF50, #2c3e50);
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 80px;
}

.navbar {
    background: linear-gradient(to right, #4CAF50, #2c3e50);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar .navbar-brand {
    color: #fff;
    font-size: 2rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4);
    text-decoration: none;
}

.navbar .btn-secondary {
    color: #fff;
    font-size: 1rem;
    font-weight: bold;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.navbar .btn-secondary:hover {
    background-color: #fff;
    color: #2c3e50;
}

.container {
    margin-top: 80px;
    text-align: center;
    width: 90%;
    max-width: 1000px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    background: rgba(0, 0, 0, 0.5);
}

.container h1 {
    font-size: 2.5rem;
    color: #fff;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
    margin-bottom: 20px;
}

.list-group {
    margin-top: 20px;
}

.list-group-item {
    background-color: #34495e;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.list-group-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.list-group-item h5 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    text-transform: capitalize;
}

.list-group-item p {
    font-size: 1rem;
    margin-bottom: 10px;
    line-height: 1.5;
}

/* Button styles */
.btn-success, .btn-danger {
    color: #fff;
    padding: 10px 20px;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-success {
    background-color: #28a745;
}

.btn-danger {
    background-color: #dc3545;
}

.btn-success:hover {
    background-color: #218838;
    transform: scale(1.05);
}

.btn-danger:hover {
    background-color: #c82333;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .navbar .navbar-brand {
        font-size: 1.5rem;
    }

    .container h1 {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .navbar .navbar-brand {
        font-size: 1.2rem;
    }

    .container h1 {
        font-size: 1.5rem;
    }

    .list-group-item h5 {
        font-size: 1.2rem;
    }

    .btn-success, .btn-danger {
        font-size: 0.9rem;
    }
}

</style>










</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Look for Emails</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="btn btn-secondary" href="dashboard.php">Back</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
    <h1>Your Notifications</h1>

    <?php if ($notifications): ?>
        <div class="list-group mt-3">
            <?php foreach ($notifications as $notification): ?>
                <div class="list-group-item <?php echo (strpos($notification['message'], 'uploaded a file') !== false) ? 'bg-info text-white' : ''; ?>">
                    <h5 class="mb-1"><?php echo htmlspecialchars($notification['job_title']); ?> - Notification</h5>
                    <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                    <p><small>Received on: <?php echo htmlspecialchars($notification['created_at']); ?></small></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="mt-3">You have no notifications at the moment.</p>
    <?php endif; ?>
</div>

</body>
</html>
