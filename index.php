<?php
session_start();
if(!isset($_SESSION["user"])){
    header("Location: login/login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Whack a Mole</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <?php
    if(isset($_POST["start"])){
      header("Location: game/index.php");
    }
    if(isset($_POST["leaderboard"])){
      header("Location: leaderboard/leaderboard.php");
    }
    ?>
    <div>
      <h1>Welcome to Banana Catcher</h1>
      <form action="index.php" method="post">
        <input type="submit" value="Start" name="start">
        <input type="submit" value="Leaderboard" name="leaderboard">
        <input type="submit" value="Settings" name="settings">
        <button><a href="logout.php">Logout</a></button>
      </form>
      
    </div>
    
  </body>
</html>
