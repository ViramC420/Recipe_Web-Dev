<?php

require_once("config.php");

// Only allow logged-in employees to see this page.

if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

// Check to see if update hours form  has been submitted.

if (isset($_POST["update_hours"])) {
    echo "You submitted a form!";

    $pname = $_POST["pname"];
    
    if ($_POST["pname"] !== "" && $_POST["hours"] !== "") {
        $hours = floatval($_POST["hours"]);
        if ($hours > 0) {
            $db = get_db();
            $q = $db->prepare("UPDATE WorksOn NATURAL JOIN Project SET Hours = greatest(Hours, Hours + ?) WHERE eSSN = ? AND Pname = ?");
            $q->bindParam(1, $hours, PDO::PARAM_STR);
            $q->bindParam(2, $_SESSION["SSN"], PDO::PARAM_STR);
            $q->bindParam(3, $pname, PDO::PARAM_STR);
    
            if (!$q->execute()) {
                echo print_r($q->errorInfo(), true);
            }
        }
    }
    else {
        echo "Inputs cannot be empty";
    }

    var_dump($_POST);
}

$err = "";

if (isset($_POST["update_hours_exact"])) {
    echo "You submitted a form!";

    $pname = $_POST["pname"];
    
    if ($_POST["pname"] !== "" && $_POST["hours"] !== "") {
        $hours = floatval($_POST["hours"]);
        if ($hours > 0) {
            $db = get_db();
            $q = $db->prepare("UPDATE WorksOn NATURAL JOIN Project SET Hours = ? WHERE eSSN = ? AND Pname = ?");
            $q->bindParam(1, $hours, PDO::PARAM_STR);
            $q->bindParam(2, $_SESSION["SSN"], PDO::PARAM_STR);
            $q->bindParam(3, $pname, PDO::PARAM_STR);
    
            if (!$q->execute()) {
                echo print_r($q->errorInfo(), true);
            }
        }
        else {
            $err = "Hours for a project must be greater than zero";
        }
    }
    else {
        echo "Inputs cannot be empty";
    }

    var_dump($_POST);

}
//var_dump($_SESSION);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Company S24 example</title>
        <style>
.inputhours {
    width: 50px;
}
        </style>
    </head>
    <body>
        <h1>Company Database</h1>
        <h2>Welcome, <?= $_SESSION["Fname"] ?></h2>

        <a href="logout.php">Log out</a>
<?php

if ($err != "") {
    echo $err . "<br>";
}

$db = get_db();

$q = $db->prepare("SELECT Pname, Hours FROM Project NATURAL JOIN WorksOn WHERE eSSN = ?");
$q->bindParam(1, $_SESSION["SSN"], PDO::PARAM_STR);
$q->execute();
$projects = $q->fetchAll(PDO::FETCH_ASSOC);

function makeHoursTable($data, $showHeader = true) {
    $tableStr = "";

    $tableStr .= "<table>";

    foreach($data as $row) {
        if ($showHeader) {
            $tableStr .= "<tr>";
            foreach($row as $columnName => $columnValue) {
                $tableStr .= sprintf("<th>%s</th>", $columnName);
            }
            $tableStr .= "</tr>";
            $showHeader = false;
        }
        $tableStr .= "<tr>";
        foreach($row as $columnName => $columnValue) {
            if ($columnName == "Hours") {
                //  Old: manual form creation using HTML 
                /*
                $formStr = "<form method=\"POST\">";
                $formStr .= "<input name=\"pname\" type=\"hidden\" value=\"" . $row["Pname"] . "\">";
                $formStr .= "<input class=\"inputhours\" name=\"hours\" type=\"number\" required value=\"$columnValue\" step=\"0.01\" min=\"0.01\">";
                $formStr .= "<input name=\"update_hours_exact\" type=\"submit\" value=\"Update hours\">";
                $formStr .= "</form>";
                $tableStr .= "<td>$formStr</td>";
                */
                // New: form creation with PHP Form Builder class

                $form = new PhpFormBuilder();
                $form->set_att('method', 'post');
                $form->add_input('', array(
                    "type" =>  "number",
                    "min" => "0.01",
                    "step" => "0.01",
                    "required" => true,
                    "value" => $columnValue,
                    "class" => ["inputhours"]
                ), "hours");

                $form->add_input("Project name", array(
                    "type" => "hidden",
                    "value" => $row["Pname"]
                ), "pname");

                $form->add_input("Update hours", array(
                    "type" => "submit",
                    "value" => "Update hours"
                ), "update_hours_exact");

                $tableStr .= "<td>" . $form->build_form(false) . "</td>";
            }
            else {
                $tableStr .= sprintf("<td>%s</td>", $columnValue);
            }
            
        }
        $tableStr .= "</tr>";
    }

    $tableStr .= "</table>";

    return $tableStr;
}

echo makeHoursTable($projects);

$pnames = [];
foreach($projects as $proj) {
    $pnames []= $proj["Pname"];
}

var_dump($pnames);

?>

<h2>Update project hours</h2>

<form action="index.php" method="POST">
    <!-- <input name="pname" placeholder="Enter project name"> -->

    <select name="pname">
    <?php
    foreach($pnames as $pname) {
        //$optionElem = '<option value="' . $pname . '">' . $pname '</option>';
        //$optionElem = "<option>$pname</option>";

        $optionElem = "<option value=\"" . $pname . "\">";
        $optionElem .= $pname;
        $optionElem .= "</option>";
        echo $optionElem;
    }
    

    ?>
    </select>

    <input name="hours" type="number" required>
    <input name="when" type="datetime-local">
    <input name="update_hours" type="submit" value="Update hours">
</form>



<?php

// $q = $db->prepare("SELECT Dname as Department, CONCAT(Fname, ' ', Lname) as `Employee name` FROM Employee JOIN Department ON Employee.Dnumber = Department.Dnumber ORDER BY Dname, Lname");
// $q->execute();
// $rows = $q->fetchAll(PDO::FETCH_ASSOC);

// $employeeTable = makeTable($rows);

// echo $employeeTable;

/*
echo "<table>";

echo "<tr>";
echo "<th>Department</th>";
echo "<th>Employee name</th>";
echo "</tr>";

foreach($rows as $employee) {

    echo "<tr>";

    echo "<td>";
    echo $employee["Dname"];
    echo "</td>";

    echo "<td>";
    echo $employee["Fname"] . " " . $employee["Lname"];
    echo "</td>";

    echo "</tr>";
}

echo "</table>";
*/



?>
    </body>
</html>