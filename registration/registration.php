<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require_once "../database.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="registration.css">
</head>

<body>

    <?php
    // Initialize variables
    $errors = [];
    $success = "";
    $usernameValue = $_POST['username'] ?? '';
    $emailValue = $_POST['email'] ?? '';

    // Handle Registration Submit
    if (isset($_POST['submit'])) {

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordRepeat = $_POST['repeat_password'];
        $otp = $_POST['otp'];

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Field Validation
        if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
            $errors[] = "All fields are required";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is not valid";
        }
        if (strlen($password) < 8) {
            $errors[] = "Password must be 8 characters long";
        }
        if (ctype_alpha($password)) {
            $errors[] = "Password contains only letters";
        }
        if (is_numeric($password)) {
            $errors[] = "Password contains only numbers";
        }
        if ($password !== $passwordRepeat) {
            $errors[] = "Passwords do not match";
        }

        // OTP Validation
        if (!isset($_SESSION['otp'])) {
            $errors[] = "Please request an OTP first.";
        }

        if (isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time'] > 300)) {
            unset($_SESSION['otp'], $_SESSION['otp_time']);
            $errors[] = "OTP expired. Request a new one.";
        } else {
            if (!isset($_SESSION["otp"]) || $otp !== $_SESSION["otp"]) {
                $errors[] = "OTP is wrong";
            }
        }

        // Check email duplication
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Email already exists!";
        }

        // Insert user if no errors
        if (count($errors) === 0) {
            $sql = "INSERT INTO users (username, email, password) VALUES (?,?,?)";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);

            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "sss", $username, $email, $passwordHash);
                mysqli_stmt_execute($stmt);

                $success = "✅ You are registered successfully! Redirecting to login page...";
                header("refresh:3; url=../login/login.php");
            } else {
                die("Something went wrong");
            }
        }
    }

    // Handle OTP Request
    if (isset($_POST['otpBtn'])) {

        $email = $_POST['email'];
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        if (empty($_POST['email'])) {
            $errors[] = "Please enter your email before requesting OTP.";
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email before requesting OTP.";
        } elseif (mysqli_num_rows($result) > 0) {
            $errors[] = "Email already exists!";
        }

        if (empty($errors)) {

            $otpCreate = createOTP();

            $_SESSION["otp"] = $otpCreate;
            $_SESSION['otp_time'] = time();

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'heshankoralagamage2002@gmail.com';
            $mail->Password = 'icdw hhch rjpm jfrr';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('heshankoralagamage2002@gmail.com');
            $mail->addAddress($_POST['email']);
            $mail->isHTML(true);

            $mail->Subject = "OTP for Your Email Verification";
            $mail->Body = "Your OTP is: <b>$otpCreate</b>";

            $mail->send();
        }
    }

    // OTP Generator Function
    function createOTP()
    {
        return (string)rand(100000, 999999);
    }
    ?>

    <!-- Message Box Section -->
    <div class="message-box">
        <?php
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        if (!empty($success)) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        ?>
    </div>

    <!-- Registration Form UI -->
    <div class="container">
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Full Name: "
                    value="<?= htmlspecialchars($usernameValue) ?>">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email: "
                    value="<?= htmlspecialchars($emailValue) ?>">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password: ">
            </div>
            <div class="form-group">
                <input type="password" name="repeat_password" placeholder="Re-enter Password: ">
            </div>
            <div class="otp">
                <div class="form-group-otp">
                    <input type="text" name="otp" placeholder="OTP">
                </div>
                <div class="form-btn-otp">
                    <input type="submit" value="Send OTP" name="otpBtn" id="otpBtn">
                    <span id="countdown" class="countdown"></span>
                </div>
            </div>
            <div class="form-btn">
                <input type="submit" value="Register" name="submit">
            </div>
        </form>
        <div>
            <p>Already registered? <a href="../login/login.php">Login Here</a></p>
        </div>
    </div>
</body>
<script>
    const otpBtn = document.getElementById("otpBtn");
    const countdownEl = document.getElementById("countdown");

    // If OTP was requested, start countdown from 60 sec
    <?php if (isset($_POST['otpBtn']) && empty($errors)) { ?>
        startCountdown(60);
    <?php } ?>

    function startCountdown(seconds) {
        otpBtn.disabled = true; // Disable button
        let timeLeft = seconds;

        const timer = setInterval(() => {
            countdownEl.innerHTML = `Wait ${timeLeft}s`;
            timeLeft--;

            if (timeLeft < 0) {
                clearInterval(timer);
                otpBtn.disabled = false; // Enable button again
                countdownEl.innerHTML = ""; // Clear text
            }
        }, 1000);
    }
</script>

</html>