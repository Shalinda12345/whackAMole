<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: ../index.php");
}
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
    // print_r($_POST);
    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];
        require_once "../database.php";
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($user) {
            if (password_verify($password, $user["password"])) {
                session_start();
                $_SESSION["user"] = $user["username"];
                header("Location: ../index.php");
                die();
            } else {
                // echo "<div>Password does not match</div>";
                array_push($errors, "Password does not match");
            }
        } else {
            // echo "<div>Email does not exists</div>";
            array_push($errors, "Email does not exists");
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



        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" placeholder="Enter Email: " name="email">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password: " name="password">
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login">
            </div>
        </form>
        <div>
            <p>Not registered yet <a href="../registration/registration.php">Register Here</a></p>
        </div>
    </div>
</body>

</html>