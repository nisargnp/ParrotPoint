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
        $page = <<<HTML
            <!doctype html>
            <html>
            <head>
                <meta charset="utf-8" /> 

                <!-- for responsive page -->
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />

                <link rel="stylesheet" type="text/css" href="/389NGroupProject/static/dist/bootstrap/css/bootstrap.min.css">
                <link rel="stylesheet" type="text/css" href="/389NGroupProject/static/css/Default.css">

                <script type='text/javascript' src="/389NGroupProject/static/js/websocket-addr.js"></script>

                <title>{$title}</title>
            </head>

            <body>
                <div ng-view>
                    {$body}
                </div>
                    <script type='text/javascript' src="/389NGroupProject/static/dist/bootstrap/jquery-3.2.1.min.js"></script>
                    <script type='text/javascript' src="/389NGroupProject/static/dist/bootstrap/js/bootstrap.min.js"></script>
            </body>
</html>
HTML;
        return $page;
    }

    /*
     * Generate page body with PDF library included
     */

    function generatePageWithPDF($body, $title) {
        $page = <<<HTML
<!doctype html>
    <html>
        <head>
            <script type='text/javascript' src="/389NGroupProject/static/dist/pdf.js"></script>
            <script type='text/javascript' src="/389NGroupProject/static/dist/pdf.worker.js"></script>
            <script type='text/javascript' src="/389NGroupProject/static/dist/Chart.bundle.min.js"></script>
            <script type='text/javascript' src="/389NGroupProject/static/js/websocket-addr.js"></script>

            <link rel="stylesheet" type="text/css" href="/389NGroupProject/static/dist/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="/389NGroupProject/static/css/lecture-view.css">
        
            <title>$title</title>
        </head>
        <body>
            <div id="main">
                $body
            </div>

            <script type='text/javascript' src="/389NGroupProject/static/dist/bootstrap/jquery-3.2.1.min.js"></script>
            <script type='text/javascript' src="/389NGroupProject/static/dist/bootstrap/js/bootstrap.min.js"></script>
            <script type='text/javascript' src="/389NGroupProject/static/dist/download.js"></script>
            <script type='text/javascript' src="/389NGroupProject/static/js/lecture-view.js"></script>

        </body>

    </html>
HTML;

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
        $results = $db_connection->query("select * from professors where username='$user'");

        if ($results->num_rows !== 0) {
            $success = true;
        }

        $results->close();
        return $success;
    }


