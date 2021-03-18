<?php
/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code registers a patient into the database.
*/

date_default_timezone_set("America/Toronto");
include "connect.php";

// retrieve parameters
$newUsername = filter_input(INPUT_POST, "newusername", FILTER_SANITIZE_SPECIAL_CHARS);
$newPassword = filter_input(INPUT_POST, "newpassword", FILTER_SANITIZE_SPECIAL_CHARS);
$dateBirth = filter_input(INPUT_POST, "datebirth", FILTER_SANITIZE_SPECIAL_CHARS);

/* 
    Source for how to form of a regular expresions: https://www.w3schools.com/php/filter_validate_regexp.asp
    Source for regular expression for hex colors: https://stackoverflow.com/questions/12837942/regex-for-matching-css-hex-colors
*/
$color = filter_input(INPUT_POST, "color", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/#([a-f0-9]{3}){1,2}\b/i")));

$dateValid = validateDate($dateBirth); // validate date parameter to be in date format

/* 
    https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format/19271434 
*/
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    
    return $d && $d->format($format) === $date;
}

// arrays to save errors
$errors = [];
$usernameErrors = [];
$passwordErrors = [];
$dateBirthErrors = [];
$colorErrors = [];
$databaseErrors = 0;

// validating username
if($newUsername === "" || $newUsername === null ){
    $usernameErrors[] = 0;
} else {
    // checking if the username already exists
    $cmd = "SELECT username FROM users WHERE username = ?";
    $stmt = $dbh->prepare($cmd);
    $success = $stmt->execute([$newUsername]);
    if($success){
        if($row = $stmt->fetch()){
            $usernameErrors[] = 1;
        }
    } else {
        $databaseErrors = 1;
    }
}

// validating password
if($newPassword === "" || $newPassword === null){
    $passwordErrors[] = 0;
} else {
    // cheking password length
    if (strlen($newPassword) < 6 || strlen($newPassword) > 40) {
        $passwordErrors[] = 1;
    }

    $upperFound = false;
    $lowerFound = false;
    $digitFound = false;
    $specialCharFound = false;
    

    for ($i = 0; $i < strlen($newPassword); $i++) {
        if (ctype_digit($newPassword[$i])) { // checking password digit present
            $digitFound = true;
        } else if (strpos("[\'^£$%&*()}{@#~?!><>,|=_+¬-]/", $newPassword[$i])){ // checking special char present
            $specialCharFound = true;
        } else {
            if (ctype_upper($newPassword[$i])) // checking upper case letter present
                $upperFound = true;
        
            if (ctype_lower($newPassword[$i])) // checking lower case letter present
                $lowerFound = true;
        }
    }

    // adding the appropriate errors based on previous checks
    if (!$upperFound) {
        $passwordErrors[] = 2;
    }
    if (!$lowerFound) {
        $passwordErrors[] = 3;
    }
    if (!$digitFound) {
        $passwordErrors[] = 4;
    }
    if (!$specialCharFound) {
        $passwordErrors[] = 5;
    }
}

// validating date
if($dateValid === false || $dateBirth === null ){
    $dateBirthErrors[] = 0;
} else{ // checking date is not in the future
    $today = strtotime(date("Y-m-d"));
    $birth = strtotime($dateBirth);
    if($birth>$today){
        $dateBirthErrors[] = 1;
    }
}

// validating color
if($color === false || $color === null){
    $colorErrors[] = 0;
} else if($color === "#ffffff" || $color === "#FFFFFF"){
    $colorErrors[] = 1;
}

//adding different errors to the main errors array
if(count($usernameErrors) !== 0){
    $errors ["username"] = $usernameErrors;
}

if(count($passwordErrors) !== 0){
    $errors ["password"] = $passwordErrors;
}

if(count($dateBirthErrors) !== 0){
    $errors ["date"] = $dateBirthErrors;
}

if(count($colorErrors) !== 0){
    $errors ["color"] = $colorErrors;
}

if($databaseErrors === 1){
    $errors ["database"] = 1;
}

if(count($errors) === 0){ // if there are no errors

    // register the patient into the system

    // create hashed password
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    $cmd = "INSERT INTO users(username, password, date_of_birth, favorite_color) 
    VALUES(?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $params = [$newUsername, $hash, $dateBirth, $color];
    $success = $stmt->execute($params);

    if($success){
        $errors = -1;
    } else {
        
        $databaseErrors = 1;
        $errors ["database"] = 1;
    }
}

echo json_encode($errors);









