<?php
    
    require("support.php");

    // Disable Caching since PDF.js fails sometimes because of it
    // have to do for whatever endpoint serves the PDFs

    // header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    // header("Pragma: no-cache"); // HTTP 1.0.
    // header("Expires: 0"); // Proxies.

    $body = <<<BODY
<div id="left-panel">

    <center>
        <canvas id="pdf_view"></canvas>
        <div>
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

    <form onsubmit="sendMessage(); return false;">
        <input type="text" id="chat-text-box" />
        <input type="submit" id="chat-send-btn" value=">" />
    </form>
</div>

BODY;

    echo generatePageWithPDF($body);

?>