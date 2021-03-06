<!-- Needs: login page to set session var for the professor's username -->
<?php
	require_once("../utils/utils.php");
	session_start();

	$body = "";

	// process upload (download pdf into storage -> upload to db -> delete from storage)
	if (isset($_POST["upload"]) && isset($_SESSION['username'])) {

		// changing some stuff up
		// don't think we need to move the file before adding it to db
		// can add it straight to db from tmp storage

		$tmpFileName = $_FILES['uploaded_file']['tmp_name'];

		// check the file was uploaded via POST
		if (!file_exists($tmpFileName) || !is_uploaded_file($tmpFileName)) { // BUG: is triggered for valid pdfs (file permissions?)
			
			$body .= "ERROR: FILE NOT UPLOADED";

		} else {
			
			$db_connection = dbConnect();

			//TODO: check the validity of the file type

			// get necessary fields
			$filename = $db_connection->escape_string($_FILES['uploaded_file']['name']);
			$uploader = $_SESSION['username'];
			$pdf = $db_connection->escape_string(file_get_contents($tmpFileName));
			$code = substr(md5(microtime()),rand(0,26),5);

			try {

				// insert the pdf into the database
				if (strlen($filename) > 0 && strlen($pdf) > 0) { // TESTING: could modify/remove this check
					$results = dbQuery("insert into pdfs (filename, uploader, pdf, code) values ('$filename', '$uploader', '$pdf', '$code')");
				}

				$body .= "<h3>File upload complete</h3>"; // file upload success

			} catch (Exception $e) {

				$errmsg = $e->getMessage();
				$body .= "<h3>Error $errmsg</h3>"; // file upload success

			} finally {

				// delete the file from temporary storage
				// will delete the file even if query fails
				unlink($_FILES['uploaded_file']['tmp_name']);

			}

		}

		// unset temp name -- not sure if this does anything
		unset($tmpFileName);
	}

	$form = <<<HTML
	<div class = "centered">
    <div class = "upload-form" id = "uploader">
    <h1 class="replace-text">Select PDF</h1>
    <p>Choose a pdf to upload</p>
 
	<form action="{$_SERVER['PHP_SELF']}" enctype="multipart/form-data" method="post">
        <input type="file" name="uploaded_file" class = "labelUpload">
		<br><br><br><br>
		<input type="submit" id = "pickfilez" value="Upload" name="upload">
	</form>
	</div>
	</div>
	<br><br>
	<a href="/ParrotPoint/pages/professor/dashboard.php" class = "previous">&laquo; Back to dashboard</a>
HTML;
	$body = $form . $body;

	if (isset($_SESSION['username'])) {
		echo renderPage("PDF Upload", $body);
	} else {
		header("Location: /ParrotPoint/pages/login/ProfessorLogin.php");
	}
?>

