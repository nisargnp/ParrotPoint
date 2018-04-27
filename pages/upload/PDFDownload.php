<?php
	require_once("../utils/utils.php");

	// "uploader" and "filename" must be given
	// will download file if "download" is set

	if (isset($_GET["uploader"]) && isset($_GET["filename"])) {

		$db_connection = dbConnect();
		
		$uploader = $db_connection->escape_string($_GET["uploader"]);
        $filename = $db_connection->escape_string($_GET["filename"]);

		$query = "select * from pdfs where uploader='$uploader' and filename='$filename'";
		$result = $db_connection->query($query);
		
		if ($result && $result->num_rows > 0) {

			$record = $result->fetch_array(MYSQLI_ASSOC);

			// no caching
			header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
			header("Pragma: no-cache"); // HTTP 1.0.
			header("Expires: 0"); // Proxies.

			header("Content-type: application/pdf");
		
			if (isset($_GET["download"])) {
				header("Content-Disposition: attachment; filename='$filename'");
			} else {
				header("Content-Disposition: inline");
			}
			
			echo $record["pdf"];
		
		} else {
			
			http_response_code(400);

		}

    } else {

		http_response_code(400);		

	}