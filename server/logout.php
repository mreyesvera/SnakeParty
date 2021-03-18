<?php
/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code logs the user out of the application.
*/

session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html>

<head>
    <title>TODO supply a title</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/logout.css">
</head>

<body>
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
            <div class="message">You have logged out.</div>
            <a href="../index.html"><input type="button" value="Log in"></a>
        </div>
    </div>
</body>

</html>