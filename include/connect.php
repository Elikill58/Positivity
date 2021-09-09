<?php

if(session_status() == PHP_SESSION_NONE)
	session_start();

$isConnect = false;
if(isset($_SESSION["name"]) && isset($_SESSION["is_admin"])){
	$isConnect = true;
}
?>