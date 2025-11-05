<?php

$hostName = "localhost";
$dbUser = "root";
$password = "";
$dbName = "bananacatcher";
$conn = mysqli_connect($hostName, $dbUser, $password, $dbName);

if(!conn){
    die("Something went wrong")
}

?>