<?php
    
    require("support.php");

    // Disable Caching since PDF.js fails sometimes because of it
    // have to do for whatever endpoint serves the PDFs

    // header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    // header("Pragma: no-cache"); // HTTP 1.0.
    // header("Expires: 0"); // Proxies.

    $body = <<<BODY
<div style="width:72%; height:100%; border: 2px solid red; float: left;">

    <form onsubmit="return changePage()">
        <input type="number" name="page" value="1" id="pdfNumber">
        <input type="submit" value="Change Page" />
    </form>
    <br />

    <center>
        <canvas id="pdf_view" style="width: 75%; border: 2px solid black"></canvas>
        <div>
            <label> < </label>
            <label> o </label>
            <label> > </label>
        </div>
    </center>
</div>

<div style="width:25%; height:99%; border: 2px solid green; float: right; position: absolute; top: 1px; right: 1px;">

    <div id="chat-box" style="border: 1px dotted gray; width: 90%; height: 90%; position: absolute; top: 2px; left: 5%; overflow: scroll;">
        <ul id="chat-box-list">
            <li>ff?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
            <li>hey?</li>
            <li>hi</li>
        </ul>
    </div>
    <form onsubmit="sendMessage(); return false;">
        <input type="text" id="chat-text-box" style="position: absolute; width:75%; bottom: 1px; left: 5%;" />
        <input type="submit" id="chat-send-btn" style="position: absolute; bottom: 1px; right: 5%; width: 10%;" value=">" />
    </form>
</div>

BODY;

    echo generatePageWithPDF($body);

?>