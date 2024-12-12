<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch user data from session
$user_id = $_SESSION["id"];

// Include your database connection configuration
require_once "config.php";

// Fetch resume data from the database
$stmt = $pdo->prepare("SELECT name, phone, address, skills, experience, education FROM resumes WHERE user_id = ?");
$stmt->execute([$user_id]);
$resume = $stmt->fetch(PDO::FETCH_ASSOC);

$resume_exists = ($resume !== false);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile</title>
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
}

.navbar {
    background-color: #212121;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    padding: 10px 20px;
}

.navbar-brand {
    color: #fff;
    font-size: 1.8rem;
    font-weight: bold;
}

.navbar-nav .btn {
    color: white;
    text-transform: uppercase;
    margin-right: 10px;
}

.navbar-nav .btn:hover {
    background-color: #4CAF50;
    transform: scale(1.05);
}

.profile-container {
    text-align: center;
    margin-top: 100px;
    width: 90%;
    max-width: 900px;
}

.profile-title {
    font-size: 3rem;
    margin-bottom: 30px;
    color: #fff;
    font-weight: bold;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.6);
}

.profile-details {
    font-size: 1.3rem;
    color: #fff;
}

.card {
    background: rgba(0, 0, 0, 0.6);
    border: none;
    color: #fff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
}

.profile-text {
    font-size: 1.2rem;
    color: #4CAF50;
}

.bullet-point {
    margin-left: 20px;
    font-size: 1.1rem;
}

.add-button {
    background-color: #4CAF50;
    color: #fff;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}

.add-button:hover {
    background-color: #45a049;
}

@media (max-width: 768px) {
    .profile-title {
        font-size: 2.5rem;
    }

    .card {
        padding: 20px;
    }
}
/* Modal Styling */
.modal-content {
background-color: #2c3e50;
color: #fff;
border-radius: 10px;
box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
}

.modal-header {
background-color: #34495e;
border-bottom: 2px solid #4CAF50;
}

.modal-title {
font-size: 1.5rem;
font-weight: bold;
color: #fff;
}

.close {
color: #fff;
font-size: 1.5rem;
opacity: 1;
}

.close:hover, .close:focus {
color: #4CAF50;
text-decoration: none;
opacity: 0.7;
}

.modal-body {
padding: 20px;
}

.form-group {
margin-bottom: 1.5rem;
}

label {
font-size: 1.2rem;
font-weight: bold;
color: #fff;
}

textarea.form-control {
background-color: #34495e;
border: 1px solid #4CAF50;
color: #fff;
border-radius: 5px;
padding: 10px;
resize: vertical;
}

textarea.form-control:focus {
border-color: #4CAF50;
box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

button[type="submit"] {
background-color: #4CAF50;
color: white;
border: none;
padding: 10px 20px;
font-size: 1.1rem;
font-weight: bold;
border-radius: 5px;
cursor: pointer;
transition: all 0.3s ease;
}

button[type="submit"]:hover {
background-color: #45a049;
transform: scale(1.05);
}

/* Modal Overlay */
.modal-backdrop {
background-color: rgba(0, 0, 0, 0.5);
}

/* Ensure modal is centered */
.modal-dialog {
max-width: 600px;
}

</style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Find Your Dream Job</a>
            <div class="navbar-nav ml-auto">
                <a href="dashboard.php" class="btn btn-success">Go to Dashboard</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container profile-container">
        <h1 class="profile-title">Your Profile</h1>
        <div class="profile-details">
            <div class="card shadow-lg p-5 rounded">
                <h3 class="text-center mb-4">User Information</h3>
                <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION["personal_email"]); ?></p>
            </div>
        </div>

        <!-- Resume Section -->
        <div class="card mt-5 shadow-lg p-5 rounded">
            <h3 class="text-center mb-4">Your Resume</h3>

            <?php if ($resume_exists): ?>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($resume['name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($resume['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($resume['address']); ?></p>
                <h5>Skills:</h5>
                <ul>
                    <?php foreach (explode(",", $resume['skills']) as $skill): ?>
                        <li><?php echo htmlspecialchars($skill); ?></li>
                    <?php endforeach; ?>
                </ul>
                <h5>Work Experience:</h5>
                <p><?php echo nl2br(htmlspecialchars($resume['experience'])); ?></p>
                <h5>Education:</h5>
                <p><?php echo nl2br(htmlspecialchars($resume['education'])); ?></p>
                <div class="mt-4 text-center">
                    <a href="update_resume.php" class="btn btn-primary">Update Resume</a>
                </div>
            <?php else: ?>
                <p>No resume submitted yet.</p>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createResumeModal">Create Your Resume</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Resume Modal -->
    <?php if (!$resume_exists): ?>
    <div class="modal fade" id="createResumeModal" tabindex="-1" role="dialog" aria-labelledby="createResumeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createResumeModalLabel">Create Your Resume</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="submit_resume.php" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="skills">Skills (comma-separated)</label>
                            <input type="text" class="form-control" name="skills" required>
                        </div>

                        <div class="form-group">
                            <label for="experience">Work Experience</label>
                            <textarea class="form-control" name="experience" rows="5" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="education">Education</label>
                            <textarea class="form-control" name="education" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Save Resume</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
