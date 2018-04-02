<?php
require_once("../utils/utils.php");




$body = <<<BODY
		<div id = "main">
		
		
		<div>
		<form action="#" method="post">
			<input type="submit" value="Input code" name="code">
		</form><br>
		<form action="../login/ProfessorLogin.php?" method="post">
			<input type="submit" value="Professor login" name="login">
		</form><br>
		</div>
		
		</div>
		
BODY;

echo renderPage("Home page", $body);


?>