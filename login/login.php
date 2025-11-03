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
        if(isset($_POST["login"])){
            $username = $_POST["username"];
            $password = $_POST["password"];
        }


        ?>

        <form action="login.php">
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
    </div>
</body>
</html>