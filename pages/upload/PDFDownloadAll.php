<?php
    require_once("../utils/utils.php");

    $db_connection = dbConnect();

    $query = "select uploader from pdfs";
    $result = $db_connection->query($query);

    $professors = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) { 
        $professors[] = $row["uploader"];
    }
    natcasesort($professors);
    $professors = array_unique($professors);

    $professor_options = "";
    foreach ($professors as $professor) {
        $professor_options .= "<option value='$professor'>$professor</option>";
    }

    $pdf_list = "<ul>";
    if (isset($_GET["choose_professor"])) {
        
        $professor = $_GET["professor"];

        $query = "select filename from pdfs where uploader='$professor'";
        $result = $db_connection->query($query);

        $pdfs = array();
        while($row = $result->fetch_array(MYSQLI_ASSOC)) { 
            $pdfs[] = $row["filename"];
        }
        natcasesort($pdfs);

        foreach ($pdfs as $pdf) {

            $style = "style='color:white;'";
            $href  = "href='PDFDownload.php?uploader=$professor&filename=$pdf&download'";
            $pdf_list .= "<li $style ><a $style $href>$pdf</a></li>";
            
        }

    }
    $pdf_list .= "</ul>";

    $form = <<<HTML
    <div class = "centered">
    <div class = "upload-form" id = "uploader">
    <h1 >Download Form</h1>
    <p>Pick a professor to download all their past pdfs.</p>
 
        <form action="{$_SERVER['PHP_SELF']}" method="get">
            <select name="professor">
            <option value='Default'>Default</option> 
                $professor_options
            </select>
            <br><br><br><br><br><br><br>
            <input type="submit" id = "pickfilez" name="choose_professor" value="Choose Professor">
        </form>
</div>
</div>
HTML;

    $backButton = <<<HTML
    <br><br>
    <a href="/389NGroupProject/pages/home/FrontPage.php" class = "previous">&laquo; Back to home</a>
    <br><br><br>
HTML;

    echo renderPage("Download PDFs", $form . $backButton. $pdf_list);
