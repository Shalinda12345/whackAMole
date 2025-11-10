<?php
session_start();
require_once "../database.php"; // adjust the path

if (!isset($_SESSION['user'])) {
    // redirect to login if not logged in
    header("Location: ../login/login.php");
    exit;
}

$username = $_SESSION['user'];
$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;

// Save score in database
$sql = "INSERT INTO scores (username, score, createdAt) VALUES (?, ?, NOW())";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $username, $score);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    // Redirect to menu/home after saving
    header("Location: ../index.php");
    exit;
} else {
    echo "Error saving score: " . mysqli_error($conn);
}
