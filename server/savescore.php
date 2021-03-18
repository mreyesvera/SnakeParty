<?php
/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code saves the sent score into the database if
    the username is set, if not it shows a session expired view.
    A code is returned based on the different paths.
*/

session_start();
$access = isset($_SESSION["username"]);

if ($access) { // if the session username is set
    date_default_timezone_set("America/Toronto");
    include "connect.php";

    // retrieve score
    $score = filter_input(INPUT_POST, "score", FILTER_VALIDATE_INT);

    if ($score === null || $score === false || $score<0) { // if the score is invalid
        $error = 0;
    } else { // save score into database
        $cmd = "INSERT INTO scores (date, score, username)
        VALUES (?, ?, ?)";
        $stmt = $dbh->prepare($cmd);
        $params = [date("Y-m-d"), $score, $_SESSION["username"]];
        $success = $stmt->execute($params);

        if ($success) {
            $error = -1;
        } else {
            $error = 1;
        }
    }
    echo $error;
} else {
    header("Location:edit.php");
}
