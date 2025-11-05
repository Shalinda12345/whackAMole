<?php
session_start();
if(!isset($_SESSION["user"])){
    header("login/login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Banana Catcher</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div>
      <h1>Welcome to Banana Catcher</h1>
      <a href="logout.php">Logout</a>
    </div>
    
  </body>
</html>
