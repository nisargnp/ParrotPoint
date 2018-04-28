<?php
    
    require("../utils/utils.php");

    // Disable Caching since PDF.js fails sometimes because of it
    // have to do for whatever endpoint serves the PDFs

    // header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    // header("Pragma: no-cache"); // HTTP 1.0.
    // header("Expires: 0"); // Proxies.

    session_start();

    $sid = session_id();

    $valid = true;
    $code = "default-code";
    $name = "";
    $prof = "false";
    $start_polling = <<<HTML
        <li><a id='polling' onclick='startPolling()'>Start Polling</a></li>
HTML;
    $slider = <<<HTML
    <div id="switch-holder">
        <label class="switch">
            <input id="slider-input" type="checkbox" checked>
            <span class="slider round"></span>
        </label>
        <br />
        <label id="switch-label">Sync Slides</label>
    </div>
HTML;

    $slider_feature = "";
    $polling_option = ""; // empty for student, start/stop_polling for prof

    if (isset($_SESSION['studentUsername']) && isset($_SESSION['code'])) {
        $code = $_SESSION['code'];
        $name = $_SESSION['studentUsername'];

        if (isset($_SESSION['isProfessor'])) {
            $polling_option = "$start_polling";
            $prof = "true";
        }
        else {
            $slider_feature = $slider;
        }
    }
    else {
        $valid = false;
    }

    $body = <<<HTML

<!-- hacks -->
<script>
    var code = "$code";
    var name = "$name";
    var isProfessor = $prof;

    function tryAuth() {
        conn.send("auth-professor:$sid");
    }
</script>

<div id="left-panel">
    <center id="center-container">
        <canvas id="pdf_view"></canvas>
        <div style="width:80%; margin: 0px;">
            <label id="page-num">0</label>
            <button class="slide-control" onclick="decPage()"> < </button>
            <button class="slide-control" onclick="gotoMaster()"> o </button>
            <button class="slide-control" onclick="incPage()"> > </button>
            $slider_feature
        </div>
    </center>

    <div id="options-dropup" class="dropup">
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
            Dropup
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
            <!-- for the code have an inner expandable with code put in via PHP variable -->
            <li><a style="pointer-events: none; cursor: default;">Code: <b>$code</b></a></li>
            <!-- fixed -->
            <li><a id="download-button" href="#">Download</a></li>
            <!-- nothing for student -->
            $polling_option
            <li role="separator" class="divider"></li>
            <li><a href="/389NGroupProject/pages/home/FrontPage.php">Exit</a></li>
        </ul>
    </div>
</div>

<div id="right-panel">

    <div id="chat-box">
        <ul id="chat-box-list"></ul>
    </div>

    <textarea id="chat-text-box"></textarea>
    <button id="chat-send-btn" onclick="sendMessage(); return false;">></button>
</div>

<!-- Graph Popup -->
<div id="graph-popup">
    <div id="popup-info">
        <span id="popup-close" onclick='graphClose()'>&times;</span>
        <div id="popup-canvas-div"></div>
    </div>
</div>

HTML;

    if ($valid) {
        echo generatePageWithPDF($body, "Lecture View");
    }
    else {
        echo "bad form submit. replace this with redirect later. go thru name enter page for student, or professor dashboard for professor";
    }

?>