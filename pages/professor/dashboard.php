<?php
    
    require("../utils/utils.php");

    session_start();

    $professorName = isset($_SESSION['username']) ? $_SESSION['username'] : "";
    
    if (isset($_SESSION['username']) && isset($_POST["code"])) {
        $_SESSION['studentUsername'] = $professorName; // shouldn't this be professor_name?
        $_SESSION['code'] = $_POST["code"];
        $_SESSION['isProfessor'] = true;
        header("Location: /ParrotPoint/pages/lecture/lecture-view.php");
    }

    // query DB to get available PDFs for professor
    $db_connection = dbConnect();
    $query = "SELECT filename FROM pdfs WHERE uploader=\"$professorName\"";
    $result = $db_connection->query($query);

    $pdfNames = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) { 
        $pdfNames[] = $row["filename"];
    }
    natcasesort($pdfNames);
    $pdfNames = array_unique($pdfNames);

    $pdf_options = "";
    foreach ($pdfNames as $pdf) {
        $pdf_options .= "<option value='$pdf'>$pdf</option>";
    }

    $sid = session_id();

    $code = $rand = substr(md5(microtime()),rand(0,26),5);

    $body = <<<HTML
<div class ="nav-top">
    <div class = "nameBox">Welcome back $professorName</div>
    <div class = "settingInfo">
        <div class = "btn-group">
            <button type="button" class="btnRed btn-danger btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Settings 
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="logout.php?">Log Out</a></li>
            </ul>
        </div>
    </div>
</div>
<div class = "container">
    <div class = "dashboard-top">
        <h2>Start a New Lecture</h2>
    </div>
    <form id="form" method="post" action="{$_SERVER['PHP_SELF']}" >
        <select id="pdf-selector" class = "pdf-selector"  name="pdf-selector">
            $pdf_options
        </select>
        <input type="hidden" name="code" value="$code" />
        <button  class = "button button1" onclick="makeRoom();" value="Start lecture" >Start Lecture</button> <br />
    </form>
    <br />
    <br />
    <a class = "dashPdf" href="/ParrotPoint/pages/upload/PDFUpload.php"><button class = "pdfButton">PDF Upload</button></a>
</div>
<script>
    var conn = new WebSocket(WEBSOCKET_ADDR);

    conn.onmessage = function(e) {
        console.log(e.data);

        let [header] = e.data.split(":", 1);

        // redirect after confirmation
        if (header == "created-room") {
            document.getElementById("form").submit();
        }
    }
    
    
    // do this before posting to the lecture view page
    function makeRoom() {
        let pdfName = document.getElementById("pdf-selector").value;
        conn.send("make-room:$code:$sid:$professorName:" + pdfName);
        return false;
    }
</script>
HTML;

    if (isset($_SESSION['username'])) {
        echo renderPage("Dashboard", $body);
    } else {
        header("Location: /ParrotPoint/pages/login/ProfessorLogin.php");
    }

?>