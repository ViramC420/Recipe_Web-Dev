<?php

session_start();

function get_db() {

    $user = "projectfood";
    $pass = "Mkez$1Lek";
    $dbname = "projectfood";
    $connstr = "mysql:host=localhost;dbname=$dbname";

    $db = new PDO($connstr, $user, $pass, array());

    return $db;
}

require_once("tablemaker.php");
require_once("FormBuilder.php");

?>