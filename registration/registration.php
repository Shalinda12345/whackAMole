<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}
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
    $errors = [];
    $success = "";

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordRepeat = $_POST['repeat_password'];

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
            array_push($errors, "All fields are required");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
        }
        if (strlen($password) < 8) {
            array_push($errors, "Password must be 8 characters long");
        }
        if ($password !== $passwordRepeat) {
            array_push($errors, "Passwords do not match");
        }

        require_once "../database.php";
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            array_push($errors, "Email already exists!");
        }

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
    ?>

    <!-- Error/Success Messages ABOVE the container -->
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
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Full Name: ">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email: ">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password: ">
            </div>
            <div class="form-group">
                <input type="password" name="repeat_password" placeholder="Re-enter Password: ">
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

</html>