<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

require_once "config.php";

$personal_email = $password = "";
$personal_email_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["otp"])) {
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
        $sql = "SELECT id, personal_email, password, first_login FROM users WHERE personal_email = :personal_email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":personal_email", $param_email, PDO::PARAM_STR);
            $param_email = $personal_email;

            if ($stmt->execute() && $stmt->rowCount() == 1) {
                if ($row = $stmt->fetch()) {
                    $id = $row["id"];
                    $personal_email = $row["personal_email"];
                    $hashed_password = $row["password"];
                    $first_login = $row["first_login"];

                    if (password_verify($password, $hashed_password)) {
                        $_SESSION["id"] = $id;
                        $_SESSION["personal_email"] = $personal_email;

                        if ($first_login == 1) {
                            $update = $pdo->prepare("UPDATE users SET first_login = 0 WHERE id = :id");
                            $update->execute([':id' => $id]);

                            $_SESSION["loggedin"] = true;
                            header("location: dashboard.php");
                            exit;
                        } else {
                            $otp = rand(100000, 999999);
                            $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                            $update = $pdo->prepare("UPDATE users SET otp = :otp, otp_expiry = :expiry WHERE id = :id");
                            $update->execute([
                                ":otp" => $otp,
                                ":expiry" => $expiry,
                                ":id" => $id
                            ]);

                            $_SESSION["show_otp"] = true;
                            $_SESSION["otp_code"] = $otp;
                            $_SESSION["otp_expires"] = $expiry;
                        }

                    } else {
                        $login_err = "Invalid email or password.";
                    }
                }
            } else {
                $login_err = "Invalid email or password.";
            }

            unset($stmt);
        }
    }

    unset($pdo);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["otp"])) {
    $entered_otp = implode('', $_POST["otp"]);
    $id = $_SESSION["id"];

    $stmt = $pdo->prepare("SELECT otp, otp_expiry FROM users WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $user = $stmt->fetch();

    if ($user) {
        if (new DateTime() > new DateTime($user["otp_expiry"])) {
            $login_err = "OTP has expired. Please login again.";
            session_destroy();
        } elseif ($entered_otp === $user["otp"]) {
            $_SESSION["loggedin"] = true;

            $clear = $pdo->prepare("UPDATE users SET otp = NULL, otp_expiry = NULL WHERE id = :id");
            $clear->execute([":id" => $id]);

            header("location: dashboard.php");
            exit;
        } else {
            $login_err = "Incorrect OTP.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login with OTP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <p>Enter your credentials.</p>

    <?php if (!empty($login_err)) echo '<div class="alert alert-danger">' . $login_err . '</div>'; ?>

    <?php if (!isset($_SESSION["show_otp"])): ?>
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
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </form>
    <?php else: ?>
    <form method="post">
        <div class="form-group otp-boxes d-flex justify-content-center">
            <input type="text" name="otp[]" maxlength="1" class="form-control" required>
            <input type="text" name="otp[]" maxlength="1" class="form-control" required>
            <input type="text" name="otp[]" maxlength="1" class="form-control" required>
            <input type="text" name="otp[]" maxlength="1" class="form-control" required>
            <input type="text" name="otp[]" maxlength="1" class="form-control" required>
            <input type="text" name="otp[]" maxlength="1" class="form-control" required>
        </div>
        <div class="form-group text-center">
            <input type="submit" class="btn btn-primary" value="Verify OTP">
        </div>
    </form>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION["show_otp"])): ?>
<div class="otp-popup" id="otpPopup">
    <strong>OTP:</strong> <span id="otpValue"><?php echo $_SESSION["otp_code"]; ?></span>
</div>
<script>
    window.addEventListener("load", function() {
        setTimeout(function() {
            document.getElementById("otpPopup").classList.add("show");
        }, 3000);
    });

    const otpInputs = document.querySelectorAll('.otp-boxes input');

    otpInputs[0].addEventListener('input', function(e) {
        const value = e.target.value;

        if (value.length > 1) {
            for (let i = 0; i < otpInputs.length; i++) {
                otpInputs[i].value = value[i] || '';
            }
        } else {
            if (value !== '') {
                otpInputs[1].focus();
            }
        }
    });

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            if (input.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === "Backspace" && input.value === '' && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });
</script>
<?php endif; ?>
</body>
</html>
