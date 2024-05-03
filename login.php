<?php

require_once("config.php");

if (isset($_POST["login"])) {
    echo "Log in attempt made through form<br>";

    $ssn = $_POST["UName"];
    $password = $_POST["password"];

    $db = get_db();

    // SQL Injection potential: a reason not to use PHP's string expansion with double quotes.
    //$ssn = '107-06-5768';
    //$ssn = "' OR '1' = '1";

    //SELECT Dnumber, SupervisorSSN FROM Employee WHERE SSN = '' OR '1' = '1'
    // SELECT Dnumber, SupervisorSSN FROM Employee WHERE SSN = '$ssn'

    $verify = $db->prepare("SELECT Password FROM User WHERE UName = ?");
    $verify->bindParam(1, $ssn, PDO::PARAM_STR);

    if (!$verify->execute()) {
        print_r($verify->errorInfo());
    }

    $verifyResults = $verify->fetchAll(PDO::FETCH_ASSOC);

    $loginError = false;

    // Part 1: we know that the employee UName is valid. 
    if (count($verifyResults) == 1) {

        if (password_verify($password, $verifyResults[0]["Password"])) {

            $q = $db->prepare("SELECT UName FROM Employee WHERE UName = ?");
            $q->bindParam(1, $ssn, PDO::PARAM_STR);
        
            if (!$q->execute()) {
                print_r($q->errorInfo());
            }
            else {
                echo "Query successful...<br>";
            }
        
            $rows = $q->fetchAll(PDO::FETCH_ASSOC);
        
            if (count($rows) == 1) {
                $_SESSION["logged_in"] = true;
                $_SESSION["UName"] = $ssn;
                $_SESSION["Dnumber"] = $rows[0]["Dnumber"];
                $_SESSION["SupervisorUName"] = $rows[0]["SupervisorUName"];
                $_SESSION["Fname"] = $rows[0]["Fname"];
                $_SESSION["Lname"] = $rows[0]["Lname"];
                header("Location: index.php");
            }
            else {
                $loginError = false;
            }
        }
        else {
            $loginError = true;
        }
    }
    else {
        $loginError = true;
    }

    if ($loginError) {
        echo "Invalid credentials<br>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>ProjectFood Login</title>
    </head>
    <body>
        <h1>ProjectFood Login</h1>
        <form action="login.php" method="POST">
            <input type="text" name="UName" placeholder="User UName">
            <input type="password" name="password">
            <input type="submit" name="login" value="Log in">
        </form>
    </body>
</html>