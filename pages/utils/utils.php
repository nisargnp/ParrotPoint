<?php
    require_once("dbInfo.php");

//-------------HTML/Page Rendering Functions----------------------
     
    /*
     * Generates the page body
     * TODO: add styling
     *
     * returns string containing html for the page
     */ 
    function renderPage($title, $body) {
        $page = <<<BODY
            <!doctype html>
            <head>
                <meta charset="utf-8" /> 
                <!-- for responsive page -->
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
                <title>{$title}</title>
            </head>

            <body>
                <div>
                    {$body}
                    <script src="bootstrap/jquery-3.2.1.min.js"></script>
                    <script src="bootstrap/js/bootstrap.min.js"></script>
                </div>
            </body>
BODY;
        return $page;
    }


// -------------Database Interaction Functions----------------
     

    /*
     * Attempts to connect to the db using the credentials from dbInfo.php
     *
     * returns mysqli object on success, dies on failure
     */
    function dbConnect() {
        global $db_host;
        global $db_user;
        global $db_password;
        global $db_database;

        $db_connection = new mysqli($db_host, $db_user, $db_password, $db_database);

        // failure to connect
        if ($db_connection->connect_error) { 
            // die($db_connection->connect_error);
            die("Registration is not available at the current time.");
        }

        return $db_connection;
    }

    /*
     * Queries the database. The return depends on the expect_record parameter.
     *
     * param query - a string for the database query
     *
     * returns the results array of the query, or nothing
     */
    function dbQuery($query) {
        global $db_connection;
        $results = $db_connection->query($query);   

        if (!$results) {
            die ("DB Query Failed: ".$db_connection->error);
        }

        return $results; 
    }


    /*
     * Makes a select query using the primary key
     *
     * returns true if the results are not empty.
     */
    function userExistsInDB($user) {
        global $db_connection;
        $success = false;
        $results = $db_connection->query("select * from users where username='$user'");

        if ($results->num_rows !== 0) {
            $success = true;
        }

        $results->close();
        return $success;
    }


