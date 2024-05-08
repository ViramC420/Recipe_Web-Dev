<?php

session_start();

error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("display_errors", 1);

// Option 1: hardcode
// Option 2: store in a config file and read in from PHP
$db = new PDO(
    "mysql:host=localhost;dbname=projectfood", 
    "projectfood",                         
    "Mkez$1Lek",                      
    );

if (isset($_POST["UName"])) {
    $loginQuery = $db->prepare("select * from User where UName = :UName");
    $loginQuery->bindParam(":UName", $_POST["UName"], PDO::PARAM_STR);
    $loginQuery->execute();

    $loginResult = $loginQuery->fetchAll(PDO::FETCH_ASSOC);

    if (count($loginResult) == 0) {
        echo "Invalid login";
        $_SESSION["loggedin"] = false;
    }
    else {
        echo "Welcome, " . $loginResult[0]["UName"];
        $_SESSION["loggedin"] = true;
        $_SESSION["name"] = $loginResult[0]["UName"]; // . " " . $loginResult[0]["Lname"]
    }

    echo "<BR>";
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
    echo "<a href=\"loggedin.php\">User homepage</a><br>";
}

?>



<form method="POST" action="login.php">
    <input type="text" name="UName" placeholder="Username">
    <input type="submit" name="Login" value="Login">
</form>
