<?php
	require_once("../utils/utils.php");
	session_start();

	$body = ""; 
	$db_connection = dbConnect();

	// process upload (download pdf into storage -> upload to db -> delete from storage)
	if (isset($_POST["upload"])) {
		$serverUploadDirectory = 'temp_pdf_storage'; // temp storage 

		$tmpFileName = $_FILES['uploaded_file']['tmp_name'];
		$serverFileName = $serverUploadDirectory."/".$_FILES['uploaded_file']['name'];

		// check the file was uploaded via POST
		if (!is_uploaded_file($tmpFileName)) // BUG: is triggered for valid pdfs (file permissions?)
			$body .= "File upload failed";
		else {
			// TODO: delete this
			$body .= <<<BODY
				<h3>Information about uploaded file</h3>
				<p>
					name: {$_FILES['uploaded_file']['name']} <br>
					tmp_name: {$_FILES['uploaded_file']['tmp_name']} <br>
					size: {$_FILES['uploaded_file']['size']} <br>
					type: {$_FILES['uploaded_file']['type']} <br>
				</p>
BODY;
			
			//TODO: check the validity of the file type
			
			// copy file from temporary location to server location
			if (!move_uploaded_file($tmpFileName, $serverFileName))
				$body .= "File upload failed"; // file upload failure
			else {
				$body .= "File upload complete"; // file upload success

				// get necessary fields
				$filename = $_FILES['uploaded_file']['name'];
				$uploader = ""; // TODO: fill this with the proper value (session -> professor will be logged in)
				$pdf = $db_connection->real_escape_string(file_get_contents($serverFileName));

				// insert the pdf into the database
				if (strlen($filename) > 0 && strlen($pdf) > 0) { // TESTING: could modify/remove this check
					$results = dbQuery("insert into pdfs (filename, uploader, pdf) values ('$filename', '$uploader', '$pdf')");
				}

				// delete the file from temporary storage
				// POSSIBLE: move this before the query to delete the file in case the query fails
				unlink($serverFileName);
			}

		


		}

		// unset temp name -- not sure if this does anything
		unset($tmpFileName);
		unset($serverFileName);
	}
	else {
		$body = <<<BODY
		<form action="{$_SERVER['PHP_SELF']}" enctype="multipart/form-data" method="post">
			<strong>Select a PDF to upload: </strong><br>
			<input type="file" name="uploaded_file"><br><br>

			<input type="submit" value="Upload" name="upload">
	    </form>
BODY;
	}

	echo renderPage("PDF Upload", $body);
?>

