<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/New_York'); 
header('Access-Control-Allow-Origin: *');

include_once("dbconnect.php"); // Returns a $dbo

include_once("classes.php");

$api = new shorterAPI();

if(!empty($_POST) && $_POST['name'] == 'urlSubmit') {
	
	$submittedURL = $_POST['url'];
	
	$api->addURL($submittedURL, $dbo);
	
} else {
	
	print "These are not the droids you're looking for...";
	
}


?>


