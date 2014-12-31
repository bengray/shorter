<?php
include_once "dbconnect.php"; // Returns a $dbo

$id = $_GET['id'];

$getURL = $dbo->prepare('SELECT submittedURL FROM urls WHERE shortURL=:id');
$getURL->execute(array(
	':id' => $id
));

$count = $getURL->rowCount();

while($row = $getURL->fetchObject()) {
	
	$url = $row->submittedURL;
	
}

if ($count > 0) {
	
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ". $url );
	exit;
	
} else {
	
    header("Location: http://shorter.bendoylegray.com/404.php");
    
}

?>