<?php
	require_once("../utils/utils.php");

	// if login session found, just redirect to dashboard
	session_start();
	if (isset($_SESSION['username'])) {
		header("Location: /389NGroupProject/pages/professor/dashboard.php");
	}

	$bottom_text = "";
	$db_connection = dbConnect(); // connect to the database

	// process registration
	if (isset($_POST["register"])) {
		$username = trim($_POST["professor_username"]);
		$name = trim($_POST["professor_name"]);
		$password = password_hash($_POST["professor_password"], PASSWORD_DEFAULT);

		// validate the fields 
		if (validateFields($name, $username)) {
			// insert the user into the table
			$results = dbQuery("insert into professors values ('$username', '$name', '$password')");

			$_SESSION['username'] = $username;
			header("Location: /389NGroupProject/pages/professor/dashboard.php");
		}
	}

	$body = <<<BODY
		<h1>Create your Account</h1>
		<form action="{$_SERVER['PHP_SELF']}" method="post">
			Name: <input type="text" name="professor_name"><br><br>
			Username: <input type="text" name="professor_username"><br><br>
			Password: <input type="password" name="professor_password"><br><br>

			<input type="submit" value="Register" name="register">
		</form><br>
		
		<strong>$bottom_text</strong>
BODY;
	
	echo renderPage("Create your Account", $body);


	$db_connection->close(); // close connection


	/*
	 * Helper function to validate fields.
	 *
	 * returns empty true if valid
	 * side-effect: modifies global bottom_text
	 */
	function validateFields($name, $username) {
		global $bottom_text;
		$isValid = true;

		if (strlen($name) < 1 || strlen($name) > 50) {
			$isValid = false;
			$bottom_text .= nl2br("Name must be between 1 and 50 characters\n");
		}
		if (strlen($username) < 1 || strlen($username) > 20) {
			$isValid = false;
			$bottom_text .= nl2br("Username must be between 1 and 20 characters\n");
		}
		if (userExistsInDB($username)) {
			$isValid = false;
			$bottom_text .= nl2br("Username exists in the database\n");
		}

		return $isValid;
	}




