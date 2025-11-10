<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../login/login.php");
    exit();
}
$username = $_SESSION["user"]; // The username from your DB or login
// var_dump($_SESSION["user"]);
// exit;
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
    <h1>Whack a Mole</h1>
    <h2 class="score">Score: <span>0</span></h2>
    <!-- 3 x 3 board-->
    <div class="board">
      <div class="hole"></div>
      <div class="hole"></div>
      <div class="hole"></div>
      <div class="hole"></div>
      <div class="hole"></div>
      <div class="hole"></div>
      <div class="hole"></div>
      <div class="hole"></div>
      <div class="hole"></div>
    </div>
    <div class="cursor"></div>

    <script>
  // Send PHP username to JS
  window.username = "<?php echo htmlspecialchars($username, ENT_QUOTES); ?>";
</script>
<script src="script.js"></script>

  </body>
</html>
