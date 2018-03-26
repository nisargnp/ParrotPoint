// RUN `php example_wss/bin/chat-server.php` in the background 
// before opening these test files in XAMPP

var defaultPDFUrl = "/389NGroupProject/static/pdf/introHTML.pdf";

var pdfDoc = null;
var conn = null;

var pageRendering = false;
var pageNumPending = null;

var scale = 1.5;
var canvas = document.getElementById("pdf_view");
var context = canvas.getContext('2d');

// PDF Functions

function changePage() {

    var pageNum = parseInt($("#pdfNumber").val());
    if (pageNum) {
        generatePDF(pageNum);
        // propagate
        conn.send(pageNum);
    }
    // prevent form from refreshing
    return false;
}

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

function queueRenderPage(num) {
    if (pageRendering) {
        pageNumPending = num;
    } else {
        generatePDF(num);
    }
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
            if (!isNaN(e.data)) {
                console.log(e.data);
                queueRenderPage(parseInt(e.data));
            }
        };
    });

});

