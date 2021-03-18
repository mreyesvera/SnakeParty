<?php
/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code retrives top scores from users (the parameter
    determines wheter its for the defined user or all users).
    These scores are then returned. If there is an error, then
    an error integer is sent back.
*/
session_start();
$access = isset($_SESSION["username"]);


if ($access) {
    include "connect.php";

    // parameter determines if all scores should be retrieved or just those of current user
    $values = filter_input(INPUT_POST, "values", FILTER_VALIDATE_INT);

    if ($values === null || $values === false) { // if the parameter is missing or invalid
        $result = 0;
    } else {
        if ($values === 0) { // retrieve only current user's top scores
            $cmd = "SELECT username, date, score FROM scores WHERE username=?
            ORDER BY score desc LIMIT 20";
            $stmt = $dbh->prepare($cmd);
            $params = [$_SESSION["username"]];
            $success = $stmt->execute($params);

            if ($success) { // if there was no database error
                $result = [];
                while($row = $stmt->fetch()){
                    $result[]=["username"=>$row["username"],
                    "date"=>$row["date"],
                    "score"=>$row["score"]];
                }
            } else { // if there was a database error
                $result = 2;
            }
        } else if($values === 1){ // retrieve all users' top scores
            $cmd = "SELECT username, date, score FROM scores
            ORDER BY score desc LIMIT 20";
            $stmt = $dbh->prepare($cmd);
            $params = [];
            $success = $stmt->execute($params);

            if ($success) { // if there was no database error
                $result = []; // array that will hold the associative arrays of scores
                while($row = $stmt->fetch()){
                    // adding current score to the array
                    $result[]=["username"=>$row["username"],
                    "date"=>$row["date"],
                    "score"=>$row["score"]];
                }
            } else { // if there was a database error
                $result = 2;
            }
        } else { // if the parameter was out of range
            $result = 1;
        }
    }
    echo json_encode($result); // return the result
} else {
    header("Location:edit.php"); // redirect to another page which will mention the expired session
}
