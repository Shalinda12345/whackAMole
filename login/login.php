<?php
session_start();
if(isset($_SESSION["user"])){
    header("Location: ../index.html");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <?php
        print_r($_POST);
        if(isset($_POST["login"])){
            $username = $_POST["username"];
            $password = $_POST["password"];
            require_once "../database.php";
            $sql = "SELECT * FROM users WHERE username = '$username'";
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if($user){
                if(password_verify($password, $user["password"])){
                    session_start();
                    $_SESSION["user"] = "yes";
                    header("Location: ../index.php");
                    die();
                }else{
                    echo "<div>Password does not match </div>";
                }
            }else{
                echo "<div>Username does not exists</div>";
            }
        }
        ?>

        <form action="login.php" method="post">
            <div class="form-group">
                <input type="text" placeholder="Enter Username: " name="username" />
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter Password: " name="password" />
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" />
            </div>
        </form>
        <div><p>Not Registered yet <a href="../registration/registration.php">Register here</a></p></div>
    </div>
</body>
</html>