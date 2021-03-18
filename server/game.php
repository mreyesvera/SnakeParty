<?php
/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code displays the game to the users if the session
    is set, or appropriate errors otherwise. 
*/

session_start();
$access = isset($_SESSION["username"]);

?>
<!DOCTYPE html>
<html>

<head>
    <title>SNAKE PARTY</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/game.css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/snakegame.js"></script>
</head>

<body>
    <?php
    if ($access) { // if the username is set in the session, display game
    ?>
        <!-- user's favorite color-->
        <div id="savedColor">
            <?= $_SESSION["favoriteColor"] ?>
        </div>
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
                <a href="menu.php"><input type="button" value="Menu"></a>
                <a href="logout.php"><input type="button" value="Log out"></a>
            </div>
        </header>
        <div id="currentScore">
            Score: <span id="score"></span>
        </div>
        <input id="startGame" type="button" value="START">
        <canvas id="gameCanvas" height='500' width='500'></canvas>


        <div id="instructions">
            Use your keyboard arrows to collect the food, which will
            give you points and increase your length.
            Keep on collecting the food and avoid crashing into
            the walls or yourself!
        </div>

        <div id="endGameContent">
            <div class="alignCenter">
                <span id="completeMessage">Congratulations <br><?= $_SESSION["username"]?> !
                <br>Score: <span id="message"></span></span>

                <a href="game.php"><input id="playAgain" type="button" value="Play again"></a>
                <a href="menu.php"><input id="backToMenu" type="button" value="Menu"></a>
            </div>
        </div>
    <?php
    } else { // display an expired session view
        include "sessionexpired.php";
    }
    ?>
</body>

</html>