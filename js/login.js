/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code defines the events for the components of 
    the log in form. It allows to validate input values and 
    only allow submission then. 
*/
$(document).ready(
    /**
     * This is the main function that runs when the 
     * window is loaded, providing all of the described
     * functionality.
     */
    function () {
        let validUsername = false;
        let validPassword = false;

        $("#username").keyup(
            /**
             * Event handler for username input to validate
             * user input.
             */
            function validateUsername() {
                if ($(this).val() === "") {
                    vaidUsername = false;
                    $("#usernameError").html("- missing").css("color", "crimson");
                    $(this).css("background-color", "crimson");
                } else {
                    validUsername = true;
                    $("#usernameError").html("");
                    $(this).css("background-color", "white");
                }
            });

        $("#password").keyup(
            /**
             * Event handler for password input to validate
             * user input.
             */
            function validatePassword() {
                if ($(this).val() === "") {
                    vaidPassword = false;
                    $("#passwordError").html("- missing").css("color", "crimson");
                    $(this).css("background-color", "crimson");
                } else {
                    validPassword = true;
                    $("#passwordError").html("");
                    $(this).css("background-color", "white");
                }
            });

        $("input[type='submit']").click(
            /**
             * Event handler for the submit button.
             * Validates the username and password
             * and prevents submission if invalid. 
             * @param {Event} event 
             */
            function login(event) {
                $("#username").keyup();
                $("#password").keyup();
                if (!validUsername || !validPassword) {
                    event.preventDefault();
                }
            });
    });