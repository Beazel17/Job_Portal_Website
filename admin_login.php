<?php

$adminemail = $password = "";
$adminemail_err = $password_err = $login_err = "";

$correct_email = "admin@group3.com";
$correct_password = "group3final";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["adminemail"]))) {
        $adminemail_err = "Please enter email.";
    } else {
        $adminemail = trim($_POST["adminemail"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

  
    if (empty($adminemail_err) && empty($password_err)) {
     
        if ($adminemail == $correct_email && $password == $correct_password) {
          
            session_start();

            $_SESSION["loggedin"] = true;
            $_SESSION["adminemail"] = $adminemail;

            header("location: admin_dashboard.php");
            exit(); 
        } else {
            $login_err = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom right, #4CAF50, #2c3e50);
            color: #fff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            box-sizing: border-box;
        }

        .wrapper {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            background-color: #f5f5f5;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            background-color: #fff;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 12px 20px;
            font-size: 1.2rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .invalid-feedback {
            color: #e74c3c;
            font-size: 0.9rem;
            display: block;
            margin-top: 5px;
        }

        .back-button {
            margin-top: 20px;
        }

        .back-button a {
            color: #fff;
            font-size: 1rem;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #333;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button a:hover {
            background-color: #555;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .wrapper {
                width: 90%;
                padding: 20px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .btn-primary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Admin Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="adminemail" class="form-control <?php echo (!empty($adminemail_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $adminemail; ?>">
                <span class="invalid-feedback"><?php echo $adminemail_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn-primary" value="Login">
            </div>
            <span class="invalid-feedback"><?php echo $login_err; ?></span>
        </form>
    </div>
    <div class="back-button">
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>
