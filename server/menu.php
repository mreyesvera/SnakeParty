<?php
/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code verifies username and password aprameters and
    displays the user with the main menu if
    the values are correct or the session was already set, 
    if not it shows a session expired view.
*/
session_start();

if (!isset($_SESSION["username"])) {
    // retrieve user credentials
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

    if ( // if the parameters are empty
        $username === "" || $username === null ||
        $password === "" || $password === null
    ) {
        $result = -1;
    } else {
        include "connect.php";

        $command = "SELECT date_of_birth, password, favorite_color FROM
        users WHERE username = ?";
        $stmt = $dbh->prepare($command);
        $params = [$username];
        $success = $stmt->execute($params);


        if (!$success) { // if there was a database error
            $result = -2;
        } else {
            if ($row = $stmt->fetch()) {
                if (password_verify($password, $row["password"])) { // check if the passwords match
                    $result = 1;

                    // set up user information into the session
                    $_SESSION["username"] = $username;
                    $_SESSION["dateBirth"] = $row["date_of_birth"];
                    $_SESSION["favoriteColor"] = $row["favorite_color"];
                } else {
                    $result = -4;
                }
            } else { // if there was user with that username found
                $result = -3;
            }
        }
    }

    if ($result !== 1) { // if there was an error
        session_unset();
        session_destroy();
    }
} else { // if the session was set from before
    $result = 1;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>SNAKE PARTY</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/menu.css">
</head>

<body>

    <?php
    if ($result === 1) { // if the session username is set
    ?>
        <header>
            <div id="titleLogo">
                <img id="smallLogo" src="../img/SnakeParty.png">
                <div id="title">
                    <h3>SNAKE PARTY</h3>
                </div>
            </div>

            <div id="usernameDisplay">
                <h3><?= $_SESSION["username"] ?></h3>
            </div>

            <div id="accountButtons">
                <a href="edit.php"><input type="button" value="Edit Account"></a>
                <a href="logout.php"><input type="button" value="Log out"></a>
            </div>
        </header>

        <div class="mainContainer">
            <div id="menuButtons">
                <a href="game.php"><input type="button" value="PLAY"></a>
                <a href="scores.php"><input type="button" value="SCORES"></a>
            </div>
        </div>
    <?php
    } else if ($result === -2) {  // if there was a database error
    ?>
        <header>
            <div id="titleLogo">
                <img id="smallLogo" src="../img/SnakeParty.png">
                <div id="title">
                    <h3>SNAKE PARTY</h3>
                </div>
            </div>
        </header>

        <div class="mainContainer">
            <div id="menuButtons">
                <div class="errorMessage">There was an error connecting to the database. Please try again.</div>
                <a href="../index.html"><input type="button" value="Log in"></a>
            </div>
        </div>

    <?php
    } else { // if the parameters were invalid
    ?>
        <header>
            <div id="titleLogo">
                <img id="smallLogo" src="../img/SnakeParty.png">
                <div id="title">
                    <h3>SNAKE PARTY</h3>
                </div>
            </div>
        </header>

        <div class="mainContainer">
            <div id="menuButtons">
                <div class="errorMessage">The information sent was invalid</div>
                <a href="../index.html"><input type="button" value="Log in"></a>
            </div>
        </div>

    <?php
    }
    ?>

</body>

</html>