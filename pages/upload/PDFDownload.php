<?php
	require_once("../utils/utils.php");

	$db_connection = dbConnect();
	//testing code to generate pdf - downloads into temp folder on apache server
	$query = "select * from pdfs where filename='spec.pdf'";
	$results = $db_connection->query($query);
	$record = $results->fetch_array(MYSQLI_ASSOC);
	file_put_contents('temp_pdf_storage/filename.pdf', $record["pdf"]);