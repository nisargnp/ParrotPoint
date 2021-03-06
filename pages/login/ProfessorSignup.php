<?php
	require_once("../utils/utils.php");

	// if login session found, just redirect to dashboard
	session_start();
	if (isset($_SESSION['username'])) {
		header("Location: /ParrotPoint/pages/professor/dashboard.php");
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
			header("Location: /ParrotPoint/pages/professor/dashboard.php");
		}
	}

	$body = <<<HTML
		<div class="join-view vertical-alignment-wrapper vertical-alignment-wrapper__center">
			<h1>Create Your Account</h1>
			<form action="{$_SERVER['PHP_SELF']}" method="post">
				<h4>Name:</h4>
				<input type="text" name="professor_name">

				<h4>Username:</h4> 
				<input type="text" name="professor_username">

				<h4>Password:</h4>
				<input type="password" name="professor_password">

				<br><br>
				<input type="submit" value="Register" name="register" class="btn">
			</form>

			<br>
			<strong>$bottom_text</strong>
		</div>
HTML;
	
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




