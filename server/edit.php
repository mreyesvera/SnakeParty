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

?>
<!DOCTYPE html>
<html>

<head>
    <title>SNAKE PARTY</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/edit.css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/edit.js"></script>
    <style>
        <?php
        if ($access) {
        ?>.mainContainer {
            background-color: white;
            color: black;
        }

        <?php
        }
        ?>
    </style>
</head>

<body>
    <?php
    if ($access) { // if session is set then the form is displayed
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
                <a href="menu.php"><input type="button" value="Menu"></a>
                <a href="logout.php"><input type="button" value="Log out"></a>
            </div>
        </header>

        <form class="mainContainer" id="registerForm" method="POST" action="savechanges.php">
            <div class="fields">
                <h4>Edit Account Details</h4>

                <div class="dateField">
                    <label name="dateBirthLabel" for="datebirth">DATE OF BIRTH</label>
                    <input id="datebirth" type="date" placeholder="dd-mm-yyyy" name="datebirth" required value="<?= $_SESSION["dateBirth"] ?>">
                    <div id="dateNotes"></div>
                </div>

                <div class="colorField">
                    <label name="colorLabel" for="color">FAVORITE COLOR:</label>
                    <input id="color" type="color" name="color" required value="<?= $_SESSION["favoriteColor"] ?>">
                    <div id="colorNotes">*You can't choose white</div>
                </div>

                <div>
                    <span id="response"></span>
                </div>

                <div class="submitField">
                    <input type="submit" value="Save">
                </div>
            </div>
        </form>

    <?php
    } else { // if session is not set, an expired session display is presented
        include "sessionexpired.php";
    }
    ?>
</body>

</html>