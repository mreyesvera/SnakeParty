/*
    Author: Silvia Mariana Reyesvera Quijano - 000813686 
    Date: December 7, 2020

    This code defines the events for the components
    of the scores menu option, which allows to view
    whether just the user top scores, or all top scores.
*/
$(document).ready(
    /**
     * This is the main function that runs when the 
     * window is loaded, providing all of the described
     * functionality.
     */
    function () {
        // add event listeners to both score option buttons
        $("#myScores").click(myScores);
        $("#allScores").click(allScores);

        // display user scores
        $("#myScores").click();


        /**
         * Event listener function for the myScores button.
         * Retrieves through ajax, the list of the top 20 user scores
         * from this user, ordered by score. 
         */
        function myScores() {
            let params = "values=0"; // param of 0 to identify only this user scores required

            // doing a POST fetch request with the stated parameters
            fetch("viewscores.php", {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: params
                })
                .then(response => response.json())
                .then(viewTable);
        }

        /**
         * Event listener function for the allScores button.
         * Retrieves through ajax, the list of the top 20 user scores
         * from all users, ordered by score.
         */
        function allScores() {
            let params = "values=1"; // param of 1 to identify all user scores required

            // doing a POST fetch request with the stated parameters
            fetch("viewscores.php", {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: params
                })
                .then(response => response.json())
                .then(viewTable);
        }

        /**
         * Receives an array of objects with the information
         * of the respective users, or an integer if there is
         * an error. If there is no error, it formats the
         * information into the content of a table and displays it.
         * @param {Array} response 
         */
        function viewTable(response) {
            if (response === 0 || response === 1 || response === 2) { // if there was an error
                $("#scores").html("<tbody><tr><td>Invalid Action</td></tr></tbody>");
            } else {
                // format information as table content
                let tableContent = "<tbody>";

                for (let i = 0; i < response.length; i++) {
                    tableContent += "<tr>";
                    tableContent += "<td>" + response[i].username + "</td>";
                    tableContent += "<td>" + response[i].date + "</td>";
                    tableContent += "<td>" + response[i].score + "</td>";
                    tableContent += "</tr>";
                }
                tableContent += "</tbody>";

                $("#scores").html(tableContent); // display content
            }
        }
    });