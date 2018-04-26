<?php
    
    require("../utils/utils.php");

    // Temp placeholder page so i can test lecture view stuff
    session_start();

    // Code carried over from code entry with a post or something
    $code = "4cfe1";

    $body = <<<HTML
<form method="post" action="/389NGroupProject/pages/lecture/lecture-view.php">
    <input type="text" name="name" placeholder="Enter your Name" required /> <br />
    <input type="text" name="code" value="" placeholder="code" />
    <input type="submit" name="studentSubmit" /> <br />
</form>
HTML;

    echo renderPage("Choose Name", $body);

?>