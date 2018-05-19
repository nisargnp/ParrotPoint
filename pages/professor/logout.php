<?php

require("../utils/utils.php");

session_start();
if(isset($_SESSION['username'])){
    unset($_SESSION['username']);
}
header("Location: /ParrotPoint/pages/home/FrontPage.php?");

?>