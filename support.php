<?php

    function generatePage($body) {
        $page = <<<PAGE
            <!doctype html>
            <html>
                <head> 
                    <meta charset="utf-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

                </head>
                        
                <body>
                    <div>
                        $body
                    </div>
                </body>
            </html>
PAGE;

        return $page;
    }

    function generatePageWithPDF($body) {
        $page = <<<PAGE
<!doctype html>
<html>
    <head>
        <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous">
        </script>

        <script src="static/dist/pdf.js"></script>
        <script src="static/dist/pdf.worker.js"></script>

        <link rel="stylesheet" type="text/css" href="static/css/lecture-view.css">
    
    </head>
    <body>
        <div id="main">
            $body
        </div>

        <script type='text/javascript' src="static/js/lecture-view.js"></script>
    </body>

</html>
PAGE;

        return $page;
    }

?>