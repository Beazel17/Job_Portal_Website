<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once "config.php"; 
$sql = "SELECT * FROM jobs";
$result = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
    padding-top: 60px;
}

/* Navbar styles */
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

.navbar .title {
    color: #fff;
    font-size: 2.5rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.4);
}

.navbar .logout {
    color: #fff;
    font-size: 1.2rem;
    font-weight: bold;
    padding: 10px 20px;
    border: 2px solid #fff;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-right: 50px;
}

.navbar .logout:hover {
    background-color: #fff;
    color: #2c3e50;
    transform: scale(1.1);
}

.dashboard-container {
    margin-top: 120px;
    text-align: center;
    width: 90%;
    max-width: 1000px;
    border: 3px solid #4CAF50; 
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.dashboard-title {
    font-size: 3.5rem;
    margin-bottom: 40px;
    color: #fff;
    font-weight: bold;
    text-shadow: 3px 3px 12px rgba(0, 0, 0, 0.6);
}

.btn-add-job {
    background-color: #4CAF50;
    color: #fff;
    border: none;
    padding: 12px 24px;
    font-size: 1.4rem;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-add-job:hover {
    background-color: #45a049;
    transform: scale(1.05);
}
.job-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 40px;
    padding: 20px;
    border: 3px solid #4CAF50;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.job-card {
    background-color: #34495e;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.job-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
}

.modal-content {
    background-color: #2c3e50;
    color: #fff;
}

.modal-header, .modal-footer {
    border: none;
}

.job-title {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-decoration: underline;
}

.job-description {
    font-size: 16px;
    color: #b0b0b0;
    margin-bottom: 15px;
}

.job-location, .salary {
    font-size: 14px;
    color: #b0b0b0;
    margin-top: 5px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.job-location::before {
    content: "📍";
    margin-right: 5px;
}

.salary::before {
    content: "💰";
    margin-right: 5px;
}

@media (max-width: 768px) {
    .job-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .job-container {
        grid-template-columns: 1fr;
    }

    .navbar .title {
        font-size: 1.8rem;
    }

    .btn-add-job {
        font-size: 1.2rem;
    }

    .job-title {
        font-size: 20px;
    }
}
</style>

</head>
<body>

    <div class="navbar">
        <div class="title">Find Your Dream Applicant</div>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="dashboard-container">
        <h1 class="dashboard-title">Welcome to the Admin Dashboard</h1>
        
        <button class="btn-add-job" data-toggle="modal" data-target="#jobModal">Add Job</button>

        <div class="job-container">
    <?php
    if ($result->rowCount() > 0) {
       
        while ($job = $result->fetch()) {
           
            $jobId = $job['id'];
            $applicantSql = "SELECT * FROM applications WHERE job_id = :job_id";
            $stmt = $pdo->prepare($applicantSql);
            $stmt->execute(['job_id' => $jobId]);
            $applicants = $stmt->rowCount();

            echo '<div class="job-box">';
            echo '<div class="job-title">' . htmlspecialchars($job['job_title']) . '</div>';
            echo '<div class="job-description">' . nl2br(htmlspecialchars($job['job_description'])) . '</div>';
            echo '<div class="job-location">Location: ' . htmlspecialchars($job['job_location']) . '</div>';
            echo '<div class="salary">Salary: ₱' . number_format($job['min_salary'], 2) . ' - ₱' . number_format($job['max_salary'], 2) . '</div>';
            
            $btnClass = ($applicants > 0) ? 'btn-success' : 'btn-warning';
            $btnText = ($applicants > 0) ? 'Applicants Found' : 'No Applicants Yet';
            echo '<button class="btn ' . $btnClass . ' check-applicants-btn" data-job-id="' . $jobId . '" onclick="loadApplicants(this)">' . $btnText . '</button>';
            echo '</div>';
        }
    } else {
        echo '<p>No jobs available.</p>';
    }
    ?>
</div>

<script>
    function loadApplicants(button) {
        button.innerHTML = 'Loading...';
        button.classList.remove('btn-success', 'btn-warning');
        button.classList.add('btn-info'); 
        
        setTimeout(function() {
            const jobId = button.getAttribute('data-job-id');
            window.location.href = 'check_applicant.php?job_id=' + jobId;
        }, 2000); 
    }
</script>


    </div>

    <div class="modal fade" id="jobModal" tabindex="-1" role="dialog" aria-labelledby="jobModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalLabel">Create Job Wanted Applicant</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="jobForm">
                        <div class="form-group">
                            <label for="jobTitle">Job Title</label>
                            <input type="text" class="form-control" id="jobTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="jobDescription">Job Description</label>
                            <textarea class="form-control" id="jobDescription" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="jobLocation">Location</label>
                            <input type="text" class="form-control" id="jobLocation" required>
                        </div>
                        <div class="form-group">
                            <label for="minSalary">Minimum Salary</label>
                            <input type="number" class="form-control" id="minSalary" required>
                        </div>
                        <div class="form-group">
                            <label for="maxSalary">Maximum Salary</label>
                            <input type="number" class="form-control" id="maxSalary" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveJobBtn">Save Job</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
       document.getElementById("saveJobBtn").addEventListener("click", function() {
            const jobTitle = document.getElementById("jobTitle").value;
            const jobDescription = document.getElementById("jobDescription").value;
            const jobLocation = document.getElementById("jobLocation").value;
            const minSalary = document.getElementById("minSalary").value;
            const maxSalary = document.getElementById("maxSalary").value;

            if (jobTitle && jobDescription && jobLocation && minSalary && maxSalary) {
         
                $.ajax({
                    url: 'save_job.php',
                    type: 'POST',
                    data: {
                        jobTitle: jobTitle,
                        jobDescription: jobDescription,
                        jobLocation: jobLocation,
                        minSalary: minSalary,
                        maxSalary: maxSalary
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                          
                            window.location.href = 'admin_dashboard.php';
                        } else {
                            alert(data.message);
                        }
                    }
                });
            }
        });
        function loadApplicants(button) {
  
    button.innerHTML = 'Loading...';
    button.disabled = true; 
    setTimeout(function() {
       
        const jobId = button.getAttribute('data-job-id');

        window.location.href = 'check_applicant.php?job_id=' + jobId;
    }, 2000); 
}

    </script>

</body>
</html>
