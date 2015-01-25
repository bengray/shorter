<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/New_York'); 
header('Access-Control-Allow-Origin: *');

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
	
	private function logUser($userIP, $today, $dbo) {
		$logUser = $dbo->prepare('INSERT INTO users (dateSubmitted,userIP) 
			VALUES (:dateSubmitted,:userIP)');
		$logUser->execute(array(
			':userIP'	=> $userIP,
			':dateSubmitted'  => $today
		));
		
		if($logUser) { 
			return true ; 
		} else { 
			return false; 
		}
	}
	
	private function validateURL($submittedURL, $dbo) {
		$validate = filter_var($submittedURL, FILTER_VALIDATE_URL);
		
		return $validate;
	}
	
	private function getCount($today, $userIP, $dbo) {
		$countUser = $dbo->prepare("SELECT count(*) AS counted FROM users WHERE dateSubmitted=:today AND userIP=:userIP");
		$countUser->execute(array(
			':today' => $today,
			':userIP'	=> $userIP
		));

		while($row = $countUser->fetchObject()) {
			$count = $row->counted;
		}
		
		return $count;
	}
	
	private function checkUrlExists($submittedURL, $dbo) {
		$checkDB = $dbo->prepare("SELECT count(*) AS count FROM urls WHERE submittedURL=:submittedURL");
		$checkDB->execute(array(
			':submittedURL'	=> $submittedURL
		));

		while($row = $checkDB->fetchObject()) {
			$count = $row->count;
		}
		
		if($count == 0) {
			return false;
		} else {
			return true;
		}
				
	}
	
	private function getShortURL($submittedURL, $dbo) {
		$getShortURL = $dbo->prepare("SELECT shortURL FROM urls WHERE submittedURL=:submittedURL");
		$getShortURL->execute(array(
			':submittedURL'	=> $submittedURL
		));
		
		while($row = $getShortURL->fetchObject()) {
			$shortURL = $row->shortURL;
		}
		
		return $shortURL;
		
	}
	
	public function addURL($submittedURL, $dbo) {
		$rtn = array();
		
		$validURL = $this->validateURL($submittedURL, $dbo);
				
		if($validURL) {
			
			$today = date('d-m-Y');
			$userIP = $_SERVER['REMOTE_ADDR'];			
			
			// See how many times this IP has submitted a URL today
			$count = $this->getCount($today, $userIP, $dbo);
			
			if($count < 100) {
				
				// Go ahead and log the visitor's IP and timestamp this action
				// so we can limit the number of times during a 24 hour period they shorten a URL
				$userLogged = $this->logUser($userIP, $today, $dbo);
				
				// Check to see if the submitted URl already exists in the database
				$URLExists = $this->checkUrlExists($submittedURL, $dbo);
				
				if($URLExists) {
					
					$shortURL = $this->getShortURL($submittedURL, $dbo);
					
				} else {
					
					// If you're at this point in the code, it's because the URL doesn't exist in the database, add it now
					$insertLongURL = $dbo->prepare("INSERT INTO urls (submittedURL) VALUES (:submittedURL)");
					$insertLongURL->execute(array(
						':submittedURL'	=> $submittedURL
					));
					
					// Grab the id of the URL you just put into the database, we're
					// going to use the id to create a unique short url
					$newID = $dbo->lastInsertId();
					$shortURL = $this->toBase($newID);
					
					// Now that we have made the id into a short URL, go back and add it in next to the long URL
					$insertShortURL = $dbo->prepare('UPDATE urls SET shortURL=:shortURL WHERE id=:newID');
					$insertShortURL->execute(array(
						':shortURL' => $shortURL,
						':newID'	=> $newID
					));
				}
				

				$rtn['shortURL'] = $shortURL;
				$rtn['status'] = 'success';

			} else {

				$rtn['status'] = 'tooMany';
			
			}
			
		} else {
			
			$rtn['status'] = 'invalidData';
			
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


