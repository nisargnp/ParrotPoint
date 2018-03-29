<?php
	require_once("utils/utils.php");
	session_start();
	$bottom_text = "";
	$db_connection = dbConnect();

	if (isset($_POST["login"])) {
		$username = $_POST["username"];
		$password = $_POST["password"];

		// validate the login provided
		if (!validateFields($username, $password)) {
			$bottom_text = "No such user/password combination exists.";
		}
		else {
			// TODO: success redirect
			
		}
	}

	$body = <<<BODY
		<h1>Professor Sign In</h1>
		<form action="{$_SERVER['PHP_SELF']}" method="post">
			Username: <input type="text" name="username"><br><br>
			Password: <input type="password" name="password"><br><br>

			<input type="submit" value="Log In" name="login">
		</form>
		$bottom_text
BODY;

	echo renderPage("Professor Sign In", $body);

	/*
	 * Helper function to validate login fields.
	 *
	 * returns true if no errors, and false if any errors 
	 */
	function validateFields($username, $password) {
		global $db_connection;
		$isValid = true;
        $results = $db_connection->query("select * from users where username='$username'");

        if ($results->num_rows === 0) {
            $isValid = false;
        } 
        else {
        	$results->data_seek(0); // assumes usernames are unique
			$record = $results->fetch_array(MYSQLI_ASSOC);
			$realPasswordHash = $record["password"];

	      	if (!password_verify($password, $realPasswordHash)) {
	      		$isValid = false;
	      	}
        }

        $results->close();
		return $isValid;
	}