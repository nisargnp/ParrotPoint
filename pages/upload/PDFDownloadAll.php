<?php

    print_r($_GET);

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

    $form = <<<HTML
        <form action="{$_SERVER['PHP_SELF']}" method="get">
            <select name="professor">
                $professor_options
            </select>
            <input type="submit" name="choose_professor" value="Choose Professor">
        </form>
HTML;

    echo renderPage("Download PDFs", $form . $bottom);
