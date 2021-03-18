<?php
/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code allows the user to edit account information
    if the session is set. If not, it displays an appropriate
    session expired message.
*/

session_start();
$access = isset($_SESSION["username"]);

if ($access) { // if the session username is set
    date_default_timezone_set("America/Toronto");
    include "connect.php";

    // retrieve parameters
    $dateBirth = filter_input(INPUT_POST, "datebirth", FILTER_SANITIZE_SPECIAL_CHARS);

    /* 
        Source for how to form of a regular expresions: https://www.w3schools.com/php/filter_validate_regexp.asp
        Source for regular expression for hex colors: https://stackoverflow.com/questions/12837942/regex-for-matching-css-hex-colors
    */
    $color = filter_input(INPUT_POST, "color", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/#([a-f0-9]{3}){1,2}\b/i")));

    /* 
        https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format/19271434 
    */
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.

        return $d && $d->format($format) === $date;
    }

    $dateValid = validateDate($dateBirth); // check if the date is in a valid format

    // create arrays to save errors
    $errors = [];
    $dateBirthErrors = [];
    $colorErrors = [];
    $databaseErrors = 0;

    // validate date
    if ($dateValid === false || $dateBirth === null) {
        $dateBirthErrors[] = 0;
    } else { // check the date is not in the future
        $today = strtotime(date("Y-m-d"));
        $birth = strtotime($dateBirth);
        if ($birth > $today) {
            $dateBirthErrors[] = 1;
        }
    }

    // validate color
    if ($color === false || $color === null) {
        $colorErrors[] = 0;
    } else if ($color === "#ffffff" || $color === "#FFFFFF") { // check that the color is not white
        $colorErrors[] = 1;
    }

    // add appropriate errors to the main errors array
    if (count($dateBirthErrors) !== 0) {
        $errors["date"] = $dateBirthErrors;
    }

    if (count($colorErrors) !== 0) {
        $errors["color"] = $colorErrors;
    }

    if ($databaseErrors === 1) {
        $errors["database"] = 1;
    }

    // if there are no errors
    if (count($errors) === 0) {
        // update the user's account
        $cmd = "UPDATE users SET date_of_birth = ?, favorite_color = ? WHERE username = ?";
        $stmt = $dbh->prepare($cmd);
        $params = [$dateBirth, $color, $_SESSION["username"]];
        $success = $stmt->execute($params);

        if ($success) {
            $errors = -1;
            $_SESSION["favoriteColor"] = $color;
            $_SESSION["dateBirth"] = $dateBirth;
        } else {

            $databaseErrors = 1;
            $errors["database"] = 1;
        }
    }

    echo json_encode($errors); // return errors
} else { // if the session is not set
    header("Location:edit.php");
}
