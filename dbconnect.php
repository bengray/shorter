<?php
$host_name = "shorter.db";
$database = "shortener"; 
$username = "bgray"; 
$password = "qht8ntKU:-o!:"; 

try {
	$dbo = new PDO('mysql:host='.$host_name.';dbname='.$database, $username, $password);
} catch(PDOException $e) {
	print "ERROR!" . $e->getMessage() . "<br />";
	die();
}
?>
