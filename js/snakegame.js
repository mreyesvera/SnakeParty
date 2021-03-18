/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code defines the events for the components
    of the game menu option, which allows the user
    to play the snake game.
*/
$(document).ready(
    /**
     * This is the main function that runs when the 
     * window is loaded, providing all of the described
     * functionality.
     */
    function () {
        // starting head coordinates
        let snakeStartX = 240;
        let snakeStartY = 240;

        let squareSide = 20; // size of squares in canvas
        let gamePoints = 0;
        let snakeColor = $("#savedColor").html(); // set to user's favorite color
        let interval = 100; // sets snake movement speed 
        let middleOfChange = false; // avoids self crashing snake by pausing arrow input while in processing

        let currentFood; // food square

        let gameCanvas = document.getElementById("gameCanvas"); // game canvas where everything will be drawn
        let context = gameCanvas.getContext("2d"); // setting the context for the canvas
        let directionHeaded = "right"; // starting direction is right
        let gameStarted = false;
        let gameInterval;

        // creating and drawing the initial snake and food
        let mySnake = new Snake(snakeStartX, snakeStartY, squareSide, snakeColor);
        mySnake.createInitialSquares(5);
        mySnake.drawSquares(context);
        generateFood();

        $(window).keydown(
            /**
             * Event listener function that prevents the window
             * from scrolling up, down, right, or left based on
             * pressing keyboard arrows.
             * @param {Event} event 
             */
            function (event) {
                let key = event.key;
                if (key === "ArrowRight" || key === "ArrowLeft" || key === "ArrowUp" || key === "ArrowDown") {
                    event.preventDefault();
                }
            });

        $("#startGame").click(
            /**
             * Event listener function for when the user wants
             * to start the game.
             */
            function () {
                if (!gameStarted) { // allows to set the interval only once, at the beginning of the game
                    gameInterval = setInterval(
                        /**
                         * Interval function that moves the snake
                         * and does other actions based on the possible
                         * outcomes from the movement.
                         */
                        function () {
                            // if the snake ran into food
                            if (mySnake.x === currentFood.x && mySnake.y === currentFood.y) {
                                mySnake.moveSnake(directionHeaded, true); // move snake and add square

                                // add points to score and generate new food
                                gamePoints += 100;
                                $("#score").html(gamePoints);
                                generateFood();
                            } else { // if the snake didn't run into food
                                mySnake.moveSnake(directionHeaded, false); // move snake normally
                            }

                            if (snakeCrashed()) { // if the snake crashed, game ends
                                console.log("snake crashed");
                                clearInterval(gameInterval);
                                gameEnded();
                            } else { // if not, just update snake drawing
                                redrawSnake();
                            }

                            middleOfChange = false;

                        }, interval);

                    gameStarted = true;
                    $(this).css("display", "none"); //removing the button from view
                }
            })

        /**
         * Function that runs when the game ends.
         * It displays a message allowing the user to see 
         * final score, and play again or go back to the menu.
         * It also saves the score into the database.
         */
        function gameEnded() {
            $("#message").html(gamePoints); // display score in message

            // save score as a parameter
            let params = "score=" + gamePoints;

            // doing a POST fetch request with the stated parameter
            fetch("savescore.php", {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: params
                })
                .then(response => response.text())
                .then(scoreSaved);

            /**
             * Determines if based on the response there was an error,
             * and prints out messages accordingly.
             * @param {Integer} response 
             */
            function scoreSaved(response) {
                if (response === 2) { // if session is expired
                    window.location.href = "server/sessionexpired.php";
                } else {
                    $("#endGameContent").css("display", "block");
                    if (response === 1) { // if there was a database error
                        $("#completeMessage").html("Database Error");
                    } else if (response === 0) { // if the score was not valid
                        $("#completeMessage").html("Invalid Score Value");
                    }

                }
            }
        }


        $(document).keydown(keyPressed);

        /**
         * Event listener function for when the user presses a key.
         * Checks if the key pressed is one of the arrows,
         * and adjusts the directionHeaded accordingly.
         * @param {Event} event 
         */
        function keyPressed(event) {
            if (!middleOfChange) {
                middleOfChange = true; // indicating a change in direction is being done
                let key = event.key; // key pressed

                // changing the direction and making sure the user is not allowed
                // to go the total opposite direction
                if (key === "ArrowRight" && directionHeaded !== "left") {
                    directionHeaded = "right";

                } else if (key === "ArrowLeft" && directionHeaded != "right") {
                    directionHeaded = "left";

                } else if (key === "ArrowUp" && directionHeaded !== "down") {
                    directionHeaded = "up";

                } else if (key === "ArrowDown" && directionHeaded !== "up") {
                    directionHeaded = "down";
                }
            }
        }


        /**
         * Generates a new food square somewhere randomly
         * in the canvas.
         */
        function generateFood() {
            let validFood = true; // saves whether the food is not located where a snake square is

            // food square coordinates
            let x; 
            let y;

            // invalid square count
            let invalidCount;

            do {
                invalidCount = 0;

                // getting randome values within the canvas
                x = Math.floor(Math.random() * (gameCanvas.width / squareSide)) * squareSide;
                y = Math.floor(Math.random() * (gameCanvas.height / squareSide)) * squareSide;

                mySnake.squares.forEach(
                    /**
                     * Checks if the created coordinates match those
                     * of the provided square.
                     * @param {Square} square 
                     */
                    function (square) {
                        if (square.x === x && square.y === y) {
                            validFood = false;
                            invalidCount++;
                        }
                    })

                if (invalidCount === 0) { // if there was nothing invalid, set to true
                    validFood = true;
                }
            } while (!validFood);

            let color = getRandomColor();
            if (typeof currentFood === 'undefined') { // if currentFood hasn't been defined before
                currentFood = new Square(x, y, squareSide, color); // create the food square
            } else { 
                // change existing currentFood parameters
                currentFood.x = x;
                currentFood.y = y;
                currentFood.color = color;
            }
            currentFood.draw(context); // draw the food square
        }

        /**
         * Returns a random color in hsl format
         * with 50% saturation and 40% lighting
         * @returns created color
         */
        function getRandomColor() {
            // only changes the hue to avoid very light colors in a white background
            let color = "hsl(";
            color += Math.floor(Math.random() * 360)
            color += ", 50%, 40%)";

            return color;
        }

        /**
         * Updates snake drawing.
         */
        function redrawSnake() {
            context.clearRect(0, 0, gameCanvas.width, gameCanvas.height);
            currentFood.draw(context);
            mySnake.drawSquares(context);
        }

        /**
         * Determines whether the snake crashed or not.
         * @returns true if it crashed, false otherwise
         */
        function snakeCrashed() {
            // if the snake is at the limits it crashed
            if (mySnake.x < 0 || mySnake.x >= gameCanvas.width ||
                mySnake.y < 0 || mySnake.y >= gameCanvas.height) {
                return true;
            }

            // if two snake squares are at the same position, it crashed
            for (let i = 0; i < (mySnake.squares.length - 1); i++) {
                for (let j = i + 1; j < (mySnake.squares.length - 1); j++) {
                    if (mySnake.squares[i].x === mySnake.squares[j].x && mySnake.squares[i].y === mySnake.squares[j].y) {
                        return true;
                    }
                }
            }
            return false;
        }

        /**
         * This function creates a snake with defined
         * coordinate for the head square location,
         * side and color.
         * @param {Integer} x 
         * @param {Integer} y 
         * @param {Integer} side 
         * @param {Color} color 
         */
        function Snake(x, y, side, color) {
            this.x = x;
            this.y = y;
            this.squares = [];
            this.squareSide = side;
            this.amountSquares = 0;
            this.color = color;

            this.addSquare =
                /**
                 * Adds a square to the snake at a specified
                 * coordinate.
                 * @param {Integer} x 
                 * @param {Integer} y 
                 */
                function (x, y) {
                    this.squares.push(new Square(x, y, this.squareSide, this.color));
                    this.amountSquares++;
                }

            this.moveSnake =
                /**
                 * Moves the snake in a specific direction and
                 * adds a square to the snake if desired.
                 * @param {String} directionHeaded
                 * @param {Boolean} addSquare
                 */
                function (directionHeaded, addSquare) {
                    // determines coordinates of where the head will move
                    switch (directionHeaded) {
                        case "right":
                            this.x = this.squares[0].x + this.squareSide;
                            break;
                        case "left":
                            this.x = this.squares[0].x - this.squareSide;
                            break;
                        case "up":
                            this.y = this.squares[0].y - this.squareSide;
                            break;
                        case "down":
                            this.y = this.squares[0].y + this.squareSide;
                            break;
                    }

                    if (addSquare) { // add a square if stated 
                        this.addSquare(0, 0); //(coordinates are not important)
                    }

                    // setting each previous square to the one before it
                    // to give the appearance of movement
                    for (let i = this.amountSquares - 1; i > 0; i--) {
                        this.squares[i].x = this.squares[i - 1].x;
                        this.squares[i].y = this.squares[i - 1].y;
                    }

                    // set head movement to the previously obtained coordinates
                    this.squares[0].x = this.x;
                    this.squares[0].y = this.y;
                }

            this.createInitialSquares =
                /**
                 * Creates the initial snake with the
                 * defined amount of squares
                 * @param {Integer} amount 
                 */
                function (amount) {
                    for (let i = 0; i < amount; i++) {
                        this.addSquare(x - i * this.squareSide, y);
                    }
                }

            this.drawSquares =
                /**
                 * Draws the snake's squares in the provided context.
                 * @param {Context} context 
                 */
                function (context) {
                    for (let i = 0; i < this.squares.length; i++) {
                        (this.squares[i]).draw(context);
                    }
                }
        }

        /**
         * This function creates a Square
         * with defined x and y coordinates,
         * side length and color.
         * @param {Integer} x 
         * @param {Integer} y 
         * @param {Integer} side 
         * @param {Color} color 
         */
        function Square(x, y, side, color) {
            this.x = x;
            this.y = y;
            this.side = side;
            this.color = color;

            this.draw =
                /**
                 * Draws the square in a provided context
                 * @param {Context} context 
                 */
                function (context) {
                    context.fillStyle = this.color;
                    context.fillRect(this.x, this.y, this.side, this.side);
                }
        }
    });