<?php
    
    require("../utils/utils.php");

    // Temp placeholder page so i can test lecture view stuff
    session_start();

    $sid = session_id();

    $code = $rand = substr(md5(microtime()),rand(0,26),5);

    $body = <<<HTML
<form method="post" onsubmit="makeRoom();" action="/389NGroupProject/pages/lecture/lecture-view.php">
    <input type="text" name="name" value="Professor" required /> <br />
    <input type="text" name="pdfName" placeholder="Name of PDF" required /> <br />
    <input type="hidden" name="code" value="$code" />
    <input type="submit" name="professorSubmit" /> <br />
</form>

<script>
    var conn = new WebSocket('ws://localhost:3001');
    
    // do this before posting to the lecture view page
    function makeRoom() {
        // generate random code - for now...
        // add pdf name here later
        conn.send("make-room:$code:$sid");
    }
</script>
HTML;

    echo renderPage("Dashboard", $body);

?>