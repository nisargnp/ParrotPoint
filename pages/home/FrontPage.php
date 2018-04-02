<?php
require_once("../utils/utils.php");




$body = <<<BODY
		
		<form action="#" method="post">
			<input type="submit" value="Register" name="register">
		</form><br>
		<form action="../login/ProfessorLogin.php?" method="post">
			<input type="submit" value="Register" name="register">
		</form><br>
		
BODY;

echo renderPage("Home page", $body);


?>