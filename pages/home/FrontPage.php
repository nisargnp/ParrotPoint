<?php
require_once("../utils/utils.php");


/*
 * TODO: Change code into a self reference for invalid/valid codes, add image logo for site
 *
 */

    $body = <<<BODY
    <div class = "join-view">
		<div class = "vertical-alignment-wrapper">
		<div class = "vertical-alignment-wrapper__center">
		
		<div>
		<div class ="logo-container">
		<div class = "logo center-block">
		</div>
		</div>
		<form action = "#" >
		<input type = "text" id = "inputSession" class = "username" name = "inputSession" placeholder="Session code">
		
		<button type = "submit" class = 'btn btn-greyscale' name = "codeInput">Enter</button>
		
        </form>
        <h4>or</h4>
        <form action="../login/ProfessorLogin.php?" method="post">
			<button type="submit" class = 'btn btn-white'>Login as Professor</button>
		</form>
        </div>
		</div>
		<div class = "vertical-alignment-wrapper__bottom">
		<div class = "email-info"></div> <!--work in progress-->
		<p class="info" >Email us any questions or concerns at <a href="mailto:example@email.com" style = "color:black">example@gmail.com</a> </p>
        </div>
		</div>
    </div>
BODY;

echo renderPage("Home page", $body);


?>