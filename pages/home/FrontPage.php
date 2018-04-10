<?php
require_once("../utils/utils.php");

session_start();

if(isset($_POST['usernameButton'])){ //Code correct and entered a username
    $_SESSION['studentUsername'] = $_POST['studentName'];
    header('Location: ../lecture/lecture-view.php?'); // Redirects to the lecture view for students
}
if (isset($_POST['codeInput'])){
    $code = $_POST['inputSession'];
    if (codeValid($code)) { // Todo: add function into utils
        $body = <<<BODY
    <div class = "join-view">
		<div class = "vertical-alignment-wrapper">
		<div class = "vertical-alignment-wrapper__center">
		
		<div>
		<div class ="logo-container">
		<div class = "logo center-block">
		</div>
		</div>
		<form action = "{$_SERVER['PHP_SELF']}" method = "post">
		<input type = "text" id = "inputSession" class = "username" name = "studentName" placeholder="Enter username">
		
		<button type = "submit" class = 'btn btn-greyscale' name = "usernameButton">Enter</button>
		
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
    }else {
        $body = <<<BODY
    <div class = "join-view">
		<div class = "vertical-alignment-wrapper">
		<div class = "vertical-alignment-wrapper__center">
		
		<div>
		<div class ="logo-container">
		<div class = "logo center-block">
		</div>
		</div>
		<form action = "{$_SERVER['PHP_SELF']}" method = "post" >
		<input type = "text" id = "inputSession" class = "usernameInvalid" name = "inputSession" placeholder="Invalid Code">
		
		<button type = "submit" class = 'btn btn-greyscale' name = "codeInput">Try Again</button>
		
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
    }
}
else {
    $body = <<<BODY
    <div class = "join-view">
		<div class = "vertical-alignment-wrapper">
		<div class = "vertical-alignment-wrapper__center">
		
		<div>
		<div class ="logo-container">
		<!--div class = "logo center-block"-->
		<img src="http://cultofthepartyparrot.com/parrots/hd/parrot.gif">
		</div>
		</div>
		<form action = "{$_SERVER['PHP_SELF']}" method = "post">
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
}

echo renderPage("Home page", $body);


?>