/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code defines the events for the components of
    the register form. It allows to validate input values
    and register a user into the database once the
    inputted values are valid.
*/
$(document).ready(
    /**
     * This is the main function that runs when the 
     * window is loaded, providing all of the described
     * functionality.
     */
    function () {
        $("#registerForm").submit(
            /**
             * Prevents default submission,
             * and uses ajax instead to submit
             * the form to register and obtain
             * the addecuate feedback.
             */
            function (event) {
                event.preventDefault(); //avoid form submission

                // reset form elements' field styling
                $("#newusername").css("background-color", "white");
                $("#usernameNotes").html("");

                $("newpassword").css("background-color", "white");

                $("#passwordNotes li").css("color", "white");

                $("#dateNotes").html("");

                $("#colorNotes").css("color", "white");

                // save inputted values into params
                let params = "newusername=" + $("#newusername").val() +
                    "&newpassword=" + $("#newpassword").val() +
                    "&datebirth=" + $("#datebirth").val() +
                    "&color=" + $("#color").val();


                // doing a POST fetch request with the stated parameters
                fetch("server/register.php", {
                        method: 'POST',
                        credentials: 'include',
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: params
                    })
                    .then(response => response.json())
                    .then(registered);
            });

        /**
         * Based on the response, it determines whether
         * there was an error. If so, it displays the
         * appropriate error messages. If not, then it
         * displays a successful registration message.
         * @param {Integer} response 
         */
        function registered(response) {
            if (response === -1) { // if there was no error
                $("#response").html("Registered Successfully").css(
                    "color", "#53b844"
                );
            } else { // if there was an error
                let message;
                if (response.hasOwnProperty('username')) { // check if there are username errors
                    for (let i = 0; i < response.username.length; i++) {
                        switch (response.username[i]) {
                            case 0: // if the parameter is missing
                                $("#newusername").css("background-color", "crimson");
                                break;
                            case 1: // if the username value is already taken
                                message = "username is already taken";
                                $("#usernameNotes").html(message).css("color", "crimson");
                                break;
                        }
                    }
                }

                if (response.hasOwnProperty('password')) { // check if there are password errors
                    for (let i = 0; i < response.password.length; i++) {
                        if (response.password[i] === 0) { // if the parameter is missing
                            $("#newpassword").css("background-color", "crimson");
                        } else { // note specific error from requirements list 
                            $("#passnote" + response.password[i]).css("color", "crimson");
                        }
                    }
                }


                if (response.hasOwnProperty('date')) { // check if there are date errors
                    for (let i = 0; i < response.date.length; i++) {
                        switch (response.date[i]) {
                            case 0: // if the parameter is missing or invalid
                                $("#datebirth").css("background-color", "crimson");
                                break;
                            case 1: // if the date specified is after today
                                $("#dateNotes").html(
                                    "We don't accept people born in the future").css(
                                    "color", "crimson"
                                );
                        }
                    }
                }

                if (response.hasOwnProperty('color')) { // check if there are color errors
                    for (let i = 0; i < response.color.length; i++) {
                        switch (response.color[i]) {
                            case 0: // if the parameter is missing or invalid
                                $("#color").css("background-color", "crimson");
                                break;
                            case 1: // if the user chose white (not valid)
                                $("#colorNotes").css("color", "crimson");
                        }
                    }
                }

                if (response.hasOwnProperty('database')) { // check if there was a database error
                    $("#response").html("There was a database error.").css( // display database error 
                        "color", "crimson"
                    );
                } else { // display generic non database error message 
                    $("#response").html("There are some invalid values.").css(
                        "color", "crimson"
                    );
                }


            }
        }
    });