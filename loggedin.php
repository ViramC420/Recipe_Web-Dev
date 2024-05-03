<?php
session_start();

if ($_SESSION["loggedin"] == false) {
    header("Location: login.php");
}

echo $_SESSION["name"];
?>

<a href="logout.php">Log out</a>