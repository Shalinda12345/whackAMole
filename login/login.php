<?php
session_start();

if (isset($_SESSION["user"])) {
    header("Location: ../index.php");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require_once "../database.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <?php
    $errors = [];
    $success = "";
    $emailValue = $_POST['email'] ?? '';

    // Login Handling
    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $otp = $_POST['otp'];

        require_once "../database.php";
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

        if ($user) {
            if (password_verify($password, $user["password"])) {
                // OTP Not Requested
                if (!isset($_SESSION['otp'])) {
                    $errors[] = "Please request an OTP first.";
                }
                // OTP Expired
                if (isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time'] > 300)) {
                    unset($_SESSION['otp'], $_SESSION['otp_time']);
                    $errors[] = "OTP expired. Request a new one.";
                } else {
                    // OTP Wrong
                    if (!isset($_SESSION["otp"]) || $otp !== $_SESSION["otp"]) {
                        $errors[] = "OTP is wrong";
                    } else {
                        // Successful Login
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION["user"] = $user["username"];
                        $success = "✅ You have logged in successfully!";

                        echo "<script>
                                setTimeout(function(){
                                    window.location.href = '../index.php';
                                }, 3000);
                              </script>";
                    }
                }
            } else {
                $errors[] = "Password does not match";
            }
        } else {
            $errors[] = "Email does not exists";
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
        } else {
            $errors[] = "Email Does not exists!";
        }
    }

    function createOTP()
    {
        return (string)rand(100000, 999999);
    }
    ?>

    <!-- Message Box -->
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

    <div class="container">
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" placeholder="Enter Email: " name="email"
                    value="<?= htmlspecialchars($emailValue) ?>">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password: " name="password">
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
                <input type="submit" value="Login" name="login">
            </div>
        </form>
        <div>
            <p>Not registered yet?
                <a href="../registration/registration.php">Register Here</a>
            </p>
        </div>
    </div>

</body>

<script>
    const otpBtn = document.getElementById("otpBtn");
    const countdownEl = document.getElementById("countdown");

    <?php if (isset($_POST['otpBtn']) && empty($errors)) { ?>
        startCountdown(60);
    <?php } ?>

    function startCountdown(seconds) {
        otpBtn.disabled = true;
        let timeLeft = seconds;

        const timer = setInterval(() => {
            countdownEl.innerHTML = `Wait ${timeLeft}s`;
            timeLeft--;

            if (timeLeft < 0) {
                clearInterval(timer);
                otpBtn.disabled = false;
                countdownEl.innerHTML = "";
            }
        }, 1000);
    }
</script>

</html>