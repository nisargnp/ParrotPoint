
$(function() {

    generatePDF("introHTML.pdf", parseInt($("#pdfNumber").val()));

    $("#pdfNumber").on("input", function() {
        var pageNum = parseInt($(this).val());
        if (pageNum) {
            generatePDF("introHTML.pdf", pageNum);
        }
    });

});

function generatePDF(pdfName, pdfPage) {
    PDFJS.getDocument(pdfName)
        .then(function(pdf) {
            return pdf.getPage(pdfPage);
        })
        .then(function(page) {

            var scale = 1.5;
            var viewport = page.getViewport(scale);
            var canvas = document.getElementById("pdf_view");
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            var renderContext = {
                canvasContext: context,
                viewport: viewport
            }
            page.render(renderContext);

        });
}
