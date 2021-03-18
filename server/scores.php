<?php
/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code displays the scores if the username is 
    set in the session. If not it displays a session 
    expired view
*/


session_start();
$access = isset($_SESSION["username"]);

?><!DOCTYPE html>
<html>

<head>
    <title>SNAKE PARTY</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/scores.css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/scores.js"></script>
</head>

<body>
<?php
    if ($access) { // if the username is set
    ?>
<header>
        <div id="titleLogo">
            <img id="smallLogo" src="../img/SnakeParty.png" >
            <div id="title">
                <h3>SNAKE PARTY</h3>
            </div>
        </div>

        <div id="usernameDisplay">
            <h3><?= $_SESSION["username"]?></h3>
        </div>

        <div id="accountButtons">
            <a href="menu.php"><input type="button" value="Menu"></a>
            <a href="logout.php"><input type="button" value="Log out"></a>
        </div>
    </header>

    <div id="options">
        <input id="myScores" type="button" value="My Scores">
        <input id="allScores" type="button" value="All Scores">
    </div>
    <table id="scores"></table>

    <?php
    } else {
        include "sessionexpired.php";
    }
    ?>
</body>

</html>