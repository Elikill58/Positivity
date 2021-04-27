<?php
session_start();
if(isset($_SESSION['name'])) {
	unset($_SESSION);
    session_destroy();
}
header("Location: ./connection");
die();
?>