<?php
session_start();
if(isset($_SESSION["user"])){
    header("Location: ../index.php");
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
    <div class="container">
        <?php 
        // print_r($_POST);
        if(isset($_POST['submit'])){
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $passwordRepeat = $_POST['repeat_password'];

            $passwordHash= password_hash($password, PASSWORD_DEFAULT);

            $errors = array();
            
            if(empty($username) OR empty($email) OR empty($password) OR empty($passwordRepeat)){
                array_push($errors, "All fields are required");
            }
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                array_push($errors, "Email is not valid");
            }
            if(strlen($password)< 8){
                array_push($errors, "Password must be 8 characters long");
            }
            if($password!==$passwordRepeat){
                array_push($errors, "Passwords does not match");
            }

            require_once "../database.php";
            $sql = "SELECT * FROM users WHERE email = '$email' ";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if($rowCount>0){
                array_push($errors, "Email already exists!");
            }
            if(count($errors)>0){
                foreach($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }else{
                
                $sql = "INSERT INTO users (username, email, password) VALUES (?,?,?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if($prepareStmt){
                    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div>You are registered Successfully</div>";
                }else{
                    die("something went wrong");
                }
            }
        }
        ?>
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
            <div class="form-group">
                <input type="submit" value="Registration" name="submit">
            </div>
        </form>
        <div><p>Already registered <a href="../login/login.php">Login Here</a></p></div>
    </div>
</body>
</html>