<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$user_email = htmlspecialchars($_SESSION["personal_email"]);

define('DB_SERVER', 'localhost');
define('DB_NAME', 'job_application');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

if (isset($_POST['apply'])) {
    $job_id = $_POST['job_id'];

    $sql = "SELECT * FROM applications WHERE user_id = :user_id AND job_id = :job_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->bindValue(':job_id', $job_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // If already applied, check status
        $application = $stmt->fetch(PDO::FETCH_ASSOC);
        $status = $application['status']; // Fetch status from the applications table

        if ($status == 'pending') {
            header("Location: dashboard.php?message=application_pending");
        } elseif ($status == 'hired') {
            header("Location: dashboard.php?message=application_hired");
        } else {
            header("Location: dashboard.php?message=application_rejected");
        }
    } else {
        $sql = "INSERT INTO applications (user_id, job_id) VALUES (:user_id, :job_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':job_id', $job_id);
        $stmt->execute();

        header("Location: dashboard.php?message=application_success");
    }
    exit;
}

$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

$sql = "SELECT * FROM jobs WHERE job_title LIKE :searchTerm ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':searchTerm', '%' . $searchTerm . '%');
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Dream Job - Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    padding-top: 60px;
}

.navbar {
    background: linear-gradient(to right, #4CAF50, #2c3e50);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar .navbar-brand {
    color: #fff;
    font-size: 2rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
}

.navbar .navbar-nav .btn {
    color: white;
    font-size: 1.2rem;
    font-weight: bold;
    padding: 12px 20px;
    border: 2px solid #fff;
    border-radius: 25px;
    transition: all 0.3s ease;
    margin-left: 15px;
}

.navbar .navbar-nav .btn:hover {
    background-color: #fff;
    color: #2c3e50;
    transform: scale(1.1);
}

.dashboard-container {
    text-align: center;
    margin-top: 120px;
    width: 90%;
    max-width: 900px;
}

.dashboard-title {
    font-size: 3rem;
    margin-bottom: 30px;
    font-weight: 700;
}

.dashboard-description {
    font-size: 1.2rem;
    color: #fff;
    margin-bottom: 40px;
}

.job-card {
    background-color: #fff;
    color: #333;
    padding: 25px;
    margin: 20px 0;
    border-radius: 10px;
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.job-card:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
}

.job-title {
    font-size: 1.8rem;
    font-weight: 600;
}

.job-location, .job-salary {
    font-size: 1.1rem;
}

.job-description {
    font-size: 1rem;
    margin-top: 10px;
    line-height: 1.5;
}

.job-card button {
    font-size: 1.1rem;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: #fff;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.job-card button:hover {
    background-color: #2c3e50;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .dashboard-title {
        font-size: 2rem;
    }

    .navbar .navbar-brand {
        font-size: 1.5rem;
        letter-spacing: 1px;
    }

    .navbar .navbar-nav .btn {
        font-size: 1rem;
    }

    .job-card {
        padding: 15px;
    }

    .job-title {
        font-size: 1.6rem;
    }
}
.highlight {
            background-color: gray; 
        }
</style>
   
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Find Your Dream Job</a>
            <div class="navbar-nav ml-auto">
                <a href="emails.php" class="btn btn-success">Emails</a>
                <a href="profile.php" class="btn btn-success">Go to Profile</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <h1 class="dashboard-title">Welcome, <?php echo $user_email; ?>!</h1>
        <p class="dashboard-description">"The right job is out there, and with patience, perseverance, and a positive mindset, you'll find the one that matches your skills, passion, and goals."</p>

        <input type="text" id="search" class="form-control" placeholder="Search Jobs..." onkeyup="searchJobs()">

        <h2 class="mt-4">Available Jobs</h2>

        <div id="job-list">
            <?php
                if ($jobs) {
                    foreach ($jobs as $job) {
                      
                        $sql = "SELECT status FROM applications WHERE user_id = :user_id AND job_id = :job_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':user_id', $user_id);
                        $stmt->bindValue(':job_id', $job['id']);
                        $stmt->execute();
                        $application = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($application) {
                            $status = $application['status'];
                        } else {
                            $status = 'none'; 
                        }

                        $highlightedTitle = str_ireplace($searchTerm, "<span class='highlight'>" . $searchTerm . "</span>", htmlspecialchars($job['job_title']));
                        
                        $buttonClass = 'btn-primary';
                        $buttonText = 'Apply';
                        $buttonDisabled = '';
                        $statusClass = '';
                        
                        if ($status == 'pending') {
                            $buttonClass = 'btn-warning';
                            $buttonText = 'Application in Process';
                            $statusClass = 'bg-warning text-dark';
                            $buttonDisabled = 'disabled';
                        } elseif ($status == 'hired') {
                            $buttonClass = 'btn-success';
                            $buttonText = 'Hired';
                            $statusClass = 'bg-success text-white';
                            $buttonDisabled = 'disabled';
                        } elseif ($status == 'rejected') {
                            $buttonClass = 'btn-danger';
                            $buttonText = 'Rejected';
                            $statusClass = 'bg-danger text-white';
                            $buttonDisabled = 'disabled';
                        }

                        echo '<div class="job-card ' . $statusClass . '">';
                        echo '<h3 class="job-title">' . $highlightedTitle . '</h3>';
                        echo '<p class="job-location"><strong>Location:</strong> ' . htmlspecialchars($job['job_location']) . '</p>';
                        echo '<p class="job-salary"><strong>Salary:</strong> Php ' . number_format($job['min_salary']) . ' - Php ' . number_format($job['max_salary']) . '</p>';
                        echo '<p class="job-description">' . nl2br(htmlspecialchars($job['job_description'])) . '</p>';
                        echo '<form method="POST" action="">
                                <input type="hidden" name="job_id" value="' . $job['id'] . '">
                                <button type="submit" name="apply" class="btn ' . $buttonClass . '" ' . $buttonDisabled . '>' . $buttonText . '</button>
                              </form>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No jobs available.</p>';
                }
            ?>
        </div>
    </div>

    <script>
        function searchJobs() {
            let searchTerm = document.getElementById("search").value;
            $.ajax({
                url: "dashboard.php", 
                type: "GET",
                data: { search: searchTerm },
                success: function(response) {
                    let jobList = $(response).find('#job-list').html();
                    $('#job-list').html(jobList);
                }
            });
        }
    </script>
</body>
</html>
