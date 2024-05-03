<?php 

session_start();
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("display_erros", 1);

$conn = new PDO(
    "mysql:host=localhost;dbname=projectfood",  // change dbname
    "projectfood",                         // change username
    "Mkez$1Lek",                      // change password
    $options);

if (isset($_POST["UName"])) {
    $loginQuery = $conn->prepare("select * from User where UName = :UName");
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
        $_SESSION["name"] = $loginResult[0]["UName"];
    }

    echo "<BR>";
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
    echo "<a href=\"loggedin.php\">User homepage</a><br>";
}

?>



<form method="POST" action="login.php">
    <input type="text" name="UName" placeholder="Login Name">
    <input type="submit" name="Login" value="Login">
</form>