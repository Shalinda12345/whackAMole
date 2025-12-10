<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../login/login.php");
    exit();
}

require_once "../database.php";
$username = $_SESSION["user"];

// Get the highest score for this user
$user_query = $conn->prepare("SELECT MAX(score) AS highest_score FROM scores WHERE username = ?");
$user_query->bind_param("s", $username);
$user_query->execute();
$user_score = $user_query->get_result()->fetch_assoc()['highest_score'] ?? 0;

// Get top 3 users by their highest score
$top_query = $conn->query("SELECT username, MAX(score) AS highest_score FROM scores GROUP BY username ORDER BY highest_score DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="leaderboard.css" />
</head>

<body>
    <h1>Leaderboard</h1>

    <div class="box">
        <h2>Top 3 Players</h2>
        <?php
        $rank = 1;
        while ($row = $top_query->fetch_assoc()) {
            echo "
                <div class='topUserScore'>
                    <span class='name'>$rank. " . htmlspecialchars($row['username']) . "</span>
                    <span class='score'>" . htmlspecialchars($row['highest_score']) . "</span>
                </div>";

            $rank++;
        }
        ?>
    </div>

    <div class="userBox">
        <h2>Your Stats</h2>
        <p class="userName"><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
        <p class="userScore"><strong>Highest Score:</strong> <?= htmlspecialchars($user_score) ?></p>
    </div>

    <div>
        <button class="returnMenu">🔙</button>
    </div>
</body>

<script>
    let returnMenu = document.querySelector(".returnMenu");
    returnMenu.onclick = function() {
        window.location.href = "../index.php"; // ← change this
    };
</script>

</html>