<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

require_once "config.php";

$personal_email = $password = "";
$personal_email_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["personal_email"]))) {
        $personal_email_err = "Please enter your email.";
    } else {
        $personal_email = trim($_POST["personal_email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($personal_email_err) && empty($password_err)) {
        $sql = "SELECT id, personal_email, password FROM users WHERE personal_email = :personal_email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":personal_email", $param_personal_email, PDO::PARAM_STR);

            $param_personal_email = trim($_POST["personal_email"]);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $personal_email = $row["personal_email"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            session_start();

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["personal_email"] = $personal_email;

                            header("location: dashboard.php");
                        } else {
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid email or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        background: linear-gradient(to bottom right, #4CAF50, #2c3e50);
        color: #fff;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
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
        font-size: 1.5rem;
        font-weight: bold;
        text-transform: uppercase;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .navbar-nav .nav-link {
        color: #fff !important;
        transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #4CAF50 !important;
    }

    .btn-login {
        background-color: #4CAF50;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        font-weight: bold;
    }

    .btn-login:hover {
        background-color: #3e8e41;
        transform: scale(1.05);
    }

    .title-container {
        text-align: center;
        margin-top: 120px;
    }

    .main-title {
        font-size: 3rem;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        margin-bottom: 20px;
        transition: transform 0.3s ease, text-shadow 0.3s ease;
    }

    .main-title:hover {
        transform: scale(1.1);
        text-shadow: 4px 4px 8px rgba(0, 0, 0, 0.9), 0 0 20px #4CAF50;
    }

    .typing-text {
        font-size: 1.2rem;
        font-weight: lighter;
        padding: 15px;
        border-radius: 5px;
        display: inline-block;
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }
</style>

</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php 
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="personal_email" class="form-control <?php echo (!empty($personal_email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $personal_email; ?>">
                <span class="invalid-feedback"><?php echo $personal_email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <div class="back-button">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
            <p>Don't have an account? <a href="register.php" style="color: #ffccbc;">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>
