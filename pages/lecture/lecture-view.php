<?php
    
    require("../utils/utils.php");

    // Disable Caching since PDF.js fails sometimes because of it
    // have to do for whatever endpoint serves the PDFs

    // header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    // header("Pragma: no-cache"); // HTTP 1.0.
    // header("Expires: 0"); // Proxies.

    $start_polling = <<<HTML
<li><a onclick="nisargsStartFunction()"><b>Start Polling</b></a></li>
HTML;

    $stop_polling = <<<HTML
<li><a onclick="nisargsStopFunction()"><b>Stop Polling</b></a></li>
HTML;

    $polling_option = ""; // empty for student, start/stop_polling for prof

    $body = <<<HTML
<div id="left-panel">
    <center>
        <canvas id="pdf_view"></canvas>
        <div style="width:80%; margin: 0px;">
            <label id="page-num">0</label>
            <button class="slide-control" onclick="decPage()"> < </button>
            <button class="slide-control" onclick="gotoMaster()"> o </button>
            <button class="slide-control" onclick="incPage()"> > </button>
        </div>
    </center>

    <div id="options-dropup" class="dropup">
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
            Dropup
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
            <li><a onclick="console.log('code'); return false;">Lecture Code</a></li>
            <li><a onclick="#">Download</a></li>
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

HTML;

    echo generatePageWithPDF($body);

?>