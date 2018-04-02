// RUN `php websocket_server/bin/server.php` in the background 
// before opening these test files in XAMPP

var defaultPDFUrl = "static/pdf/introHTML.pdf";

var pdfDoc = null;
var conn = null;

var pageRendering = false;
var pageNumPending = null;
var incPage = decPage = gotoLocal = gotoMaster = setMasterPage = null;

var scale = 1.5;
var canvas = document.getElementById("pdf_view");
var context = canvas.getContext('2d');

// PDF Functions

function makePageManager(curr) {
    let master = parseInt(curr);
    let local = master;

    let inc = function() {
        local += 1;
        queueRenderPage(local);
    };

    let dec = function() {
        local -= 1;
        queueRenderPage(local);
    };

    let gotoLocal = function() {
        queueRenderPage(local);
    }

    let gotoMaster = function() {
        queueRenderPage(master);
    }

    let setMaster = function(newMaster) {
        master = parseInt(newMaster);
    }    

    return [inc, dec, gotoLocal, gotoMaster, setMaster];
}

/*
function changePage(pn) {
    let pageNum = parseInt(pn);

    if (pageNum) {
        generatePDF(pageNum);
        // propagate
        // conn.send(pageNum);
    }
    // prevent form from refreshing
    return false;
}
*/

function generatePDF(pdfPage) {
    if (pdfDoc != null) {
        pageRendering = true;

        pdfDoc.getPage(pdfPage).then(
            function(page) {
                var viewport = page.getViewport(scale);
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                var renderContext = {
                    canvasContext: context,
                    viewport: viewport
                }

                var renderTask = page.render(renderContext);

                // Wait for rendering to finish
                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        // New page rendering is pending
                        generatePDF(pageNumPending);
                        pageNumPending = null;
                    }
                });

                // set input element's new value
                $("#pdfNumber").val(pdfPage);

            });
    }
}

function queueRenderPage(n) {
    let num = parseInt(n);

    // set page number label
    let pnl = document.getElementById("page-num");
    pnl.innerText = num;

    if (pageRendering) {
        pageNumPending = num;
    } else {
        generatePDF(num);
    }
}

// Chat functions

function updateScroll() {
    let element = document.getElementById("chat-box");
    element.scrollTop = element.scrollHeight;
}


var addToChatBox = function(msg, sender = "YOU") {
    let cbl = document.getElementById("chat-box-list");
    let nc = document.createElement("li");

    let s = document.createElement("b");
    s.appendChild(document.createTextNode(sender));

    let text = document.createTextNode(": " + msg);

    nc.appendChild(s);
    nc.appendChild(text);
    cbl.appendChild(nc);

    updateScroll();
}

var sendMessage = function() {
    let inputBox = document.getElementById("chat-text-box");
    let msg = inputBox.value;
    // add to chat box
    addToChatBox(msg);

    // send message
    conn.send("chat:" + msg);

    // reset input
    inputBox.value = "";

    return false;
}

$(function() {
    // Initially download PDF
    PDFJS.getDocument(defaultPDFUrl).then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;

        // Setup Web Socket
        conn = new WebSocket('ws://localhost:3001');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            let [header] = e.data.split(":", 1);

            if (header == "chat") {
                let [_, sender, message] = e.data.split(":", 3);
                addToChatBox(message, sender);
            } 
            else {
                if (!isNaN(e.data)) {
                    let pageNum = parseInt(e.data);

                    if (gotoMaster == null) {
                        [incPage, decPage, gotoLocal, gotoMaster, setMasterPage] = makePageManager(pageNum);
                        queueRenderPage(pageNum);
                    }
                    else {
                        setMasterPage(pageNum);
                    }
                    console.log(e.data);
                }
            }
        };
    });

    // correct scroll for chat box
    updateScroll();

    // set chat textbox / button handlers
    document.getElementById("chat-text-box").addEventListener("keydown", function(e) {
        if (e.keyCode == 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
            return false;
        }
    });

});

/*
TODO:
- instructor version
- data structures for storing connected users and their names
- style.... haow
*/

