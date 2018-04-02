<?php
    
    require("support.php");

    // Disable Caching since PDF.js fails sometimes because of it
    // have to do for whatever endpoint serves the PDFs

    // header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    // header("Pragma: no-cache"); // HTTP 1.0.
    // header("Expires: 0"); // Proxies.

    $body = <<<HTML
<div id="left-panel">
    <center>
        <canvas id="pdf_view"></canvas>
        <div style="width:80%">
            <label id="page-num" style="float:left;">0</label>
            <label class="slide-control" onclick="decPage()"> < </label>
            <label class="slide-control" onclick="gotoMaster()"> o </label>
            <label class="slide-control" onclick="incPage()"> > </label>
        </div>
    </center>
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