<?php
    
    require("../utils/utils.php");

    // Temp placeholder page so i can test lecture view stuff
    session_start();

    $body = <<<HTML
<form method="post" action="/389NGroupProject/pages/lecture/lecture-view.php">
    <input type="text" name="name" value="Professor" required /> <br />
    <input type="text" name="pdfName" placeholder="Name of PDF" required /> <br />
    <input type="submit" name="professorSubmit" /> <br />
</form>
HTML;

    echo generatePageWithPDF($body);

?>