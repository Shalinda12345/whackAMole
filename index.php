<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login/login.php");
    exit();
}

if (isset($_POST["start"])) {
    header("Location: game/game.php");
    exit();
}
if (isset($_POST["leaderboard"])) {
    header("Location: leaderboard/leaderboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Whack a Mole - Jungle Adventure</title>
    <!-- <link rel="stylesheet" href="style.css" /> -->
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="jungle-container">
        <h1 class="title">🐹 Whack-a-Mole Jungle Adventure🪵</h1>
        <form action="index.php" method="post" class="menu">
            <audio id="mySound" src="assets/hover.mp3"></audio>
            <input type="submit" value="Start Game" name="start" class="btn start-btn" />
            <input type="submit" value="Leaderboard" name="leaderboard" class="btn leaderboard-btn" />
            <button type="button" class="btn mute-btn">Mute</button>
            <button type="button" class="btn logout-btn" onclick="window.location.href='logout.php'">Logout</button>
        </form>
    </div>
</body>
<script src="script.js"></script>

</html>