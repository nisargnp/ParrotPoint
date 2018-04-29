<?php
require_once("../utils/utils.php");

session_start();

function generateCodeForm($bool) {
	$validClass = $bool ? "username" : "usernameInvalid";
	$placeholder = $bool ? "Enter Code" : "Invalid Code!";
	$btnMessage = $bool ? "Enter" : "Try Again";
	return <<<HTML
	<div class = "join-view">
		<div class = "vertical-alignment-wrapper">
		<div class = "vertical-alignment-wrapper__center">
		
		<div>
		<div class ="logo-container">
		<img src="http://cultofthepartyparrot.com/parrots/hd/parrot.gif">
		</div>
		
		<form id="studentForm" action = "{$_SERVER['PHP_SELF']}" method = "post" >
		<input type = "text" id = "inputSession" class = "$validClass" name = "inputSession" placeholder="$placeholder">
		<input type="hidden" id="hiddenResult" name="hiddenResult" value=""/>
		<button onclick="checkCode();" class = 'btn btn-greyscale' name = "codeInput">$btnMessage</button>
		</form>

		<h4>or</h4>
        <form action="../upload/PDFDownloadAll.php" method="post">
			<button type="submit" class = 'btn btn-white'>Download PDFs</button>
		</form>
		
        <h4>or</h4>
        <form action="../login/ProfessorLogin.php?" method="post">
			<button type="submit" class = 'btn btn-white'>Login as Professor</button>
		</form>

        </div>
		</div>
		<div class = "vertical-alignment-wrapper__bottom">
		<div class = "email-info"></div> <!--work in progress-->
		<p class="info" >Email us at <a href="mailto:example@email.com" style = "color:black">example@gmail.com</a> </p>
		</div>
		</div>
	</div>

	<script>
		var conn = new WebSocket('ws://localhost:3001');

		conn.onmessage = function(e) {
			console.log(e.data);

			let [header, resp] = e.data.split(":", 2);

			// redirect after confirmation
			if (header == "validate-response" && resp != undefined) {
				if (resp == "true")
					document.getElementById('hiddenResult').value = "true";
				else
					document.getElementById('hiddenResult').value = "false";

				conn.close();
				document.getElementById("studentForm").submit();
			}
		}

		function checkCode() {
			let code = document.getElementById("inputSession").value;
			conn.send("validate-code:" + code);

			return false;
		}
	</script>
HTML;
}

if(isset($_POST['usernameButton'])){ //Code correct and entered a username
    $_SESSION['studentUsername'] = $_POST['studentName'];
    header('Location: ../lecture/lecture-view.php?'); // Redirects to the lecture view for students
}
if (isset($_POST['inputSession']) && isset($_POST['hiddenResult'])) {
	$code = $_POST['inputSession'];
	$_SESSION['code'] = $code;
    if ($_POST['hiddenResult'] == "true") {
        $body = <<<HTML
    <div class = "join-view">
		<div class = "vertical-alignment-wrapper">
		<div class = "vertical-alignment-wrapper__center">
		
		<div>
		<div class ="logo-container">
		<img src="http://cultofthepartyparrot.com/parrots/hd/parrot.gif">
		</div>
		<form action = "{$_SERVER['PHP_SELF']}" method = "post">
		<input type = "text" id = "inputSession" class = "username" name = "studentName" placeholder="Enter username">
		
		<button type = "submit" class = 'btn btn-greyscale' name = "usernameButton">Enter</button>
		
		</form>
		
		<h4>or</h4>
        <form action="../upload/PDFDownloadAll.php" method="post">
			<button type="submit" class = 'btn btn-white'>Download PDFs</button>
		</form>

        <h4>or</h4>
        <form action="../login/ProfessorLogin.php?" method="post">
			<button type="submit" class = 'btn btn-white'>Login as Professor</button>
		</form>
		
        </div>
		</div>
		<div class = "vertical-alignment-wrapper__bottom">
		<div class = "email-info"></div> <!--work in progress-->
		<p class="info" >Email us at <a href="mailto:example@email.com" style = "color:black">example@gmail.com</a> </p>
        </div>
		</div>
    </div>
HTML;
    } else {
		$body = generateCodeForm(false);
    }
}
else {
    $body = generateCodeForm(true);
}

echo renderPage("Home page", $body);


?>