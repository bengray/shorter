<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/New_York'); 

include_once("dbconnect.php"); // Returns a $dbo

class shorterAPI {
	private function toBase($num, $b=62) {
	  $base='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	  $r = $num  % $b ;
	  $res = $base[$r];
	  $q = floor($num/$b);
	  while ($q) {
	    $r = $q % $b;
	    $q =floor($q/$b);
	    $res = $base[$r].$res;
	  }
	  return $res;
	}
	
	public function addURL($submittedURL, $dbo) {
		$rtn = array();
				
		if(filter_var($submittedURL, FILTER_VALIDATE_URL) == FALSE) {
			
			$rtn['status'] = 'invalidData';

		} else {

			$today = date('d-m-Y');
			$userIP = $_SERVER['REMOTE_ADDR'];

			// Count the number of times this IP has entered a URL today
			$countUser = $dbo->prepare("SELECT count(*) AS counted FROM urls WHERE dateSubmitted=:today AND userIP=:userIP");
			$countUser->execute(array(
				':today' => $today,
				':userIP'	=> $userIP
			));

			while($row = $countUser->fetchObject()) {
				$count = $row->counted;
			}

			if($count < 50) {
				// Go ahead and log the visitor's IP and timestamp this action
				// so we can limit the number of times during a 24 hour period they shorten a URL
				$logUser = $dbo->prepare('INSERT INTO urls (submittedURL,dateSubmitted,userIP) 
					VALUES (:submittedURL,:dateSubmitted,:userIP)');
				$logUser->execute(array(
					':dateSubmitted' => $today,
					':userIP'	=> $userIP,
					':submittedURL'  => $submittedURL
				));

				$newID = $dbo->lastInsertId();
				$shortURL = $this->toBase($newID);
				
				$insertShortURL = $dbo->prepare('UPDATE urls SET shortURL=:shortURL WHERE id=:newID');
				$insertShortURL->execute(array(
					':shortURL' => $shortURL,
					':newID'	=> $newID
				));

				$rtn['shortURL'] = $shortURL;
				$rtn['status'] = 'success';

			} else {

				$rtn['status'] = 'tooMany';
			
			}
			
		}

		header("Content-Type: application/json;charset=utf-8");
		print json_encode($rtn);
		
	}
}

$api = new shorterAPI();

if(!empty($_POST) && $_POST['name'] == 'urlSubmit') {
	$submittedURL = $_POST['url'];
	
	$api->addURL($submittedURL, $dbo);
} else {
	print "These are not the droids you're looking for...";
}


?>


