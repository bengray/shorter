<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shorter</title>
    <link rel="icon" type="image/png" href="">

    <!-- CSS -->
	<link href="css/style.css" rel="stylesheet">    

    <!-- JavaScript -->
  	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    
</head>

<body>
	
	<div id="form">
		<h1><a href="/">Shorter</a></h1>
	    <div class="page">
	        <div class="field">
	            <label class="label">Paste your long URL here:</label>
	            <div>
	                <input id="url" name="longURL" type="text" placeholder="http://"/>
	            </div>
	        </div>
	    </div>
		
		<div id="recaptcha"></div>
		
	    <div class="buttons">
	        <button class="btn" name="save" onclick="submit()">Make shorter!</button>
	    </div>
	    
		
		
	    <div class="shortURL">&nbsp;</div>
	</div> <!-- End form -->
	
<script type="text/javascript">

// Setting the api location 
var apiLocation = 'http://shorter.bendoylegray.com/api.php';
	
// Grab the JSON data being passed back from the api and storing it for use wherever we want.
$.getJSON(apiLocation, function(data){});

var sitekey = "6LcJ-QETAAAAAHGxopWuc3a8ZYYPDUMBWeSFTCDn"; // public key of a two-key process
var widgetId;
var onloadCallback = function() {
        widgetId = grecaptcha.render('recaptcha', {
          'sitekey' : sitekey,
          'theme' : 'light'
        });

      };
      
function submit() {
	var longURL = document.getElementById('url').value;
	
	$.ajax({
		type: "POST",   
        url: apiLocation,   
        data: {"name": 'urlSubmit', "url": longURL, "captchaResponse": grecaptcha.getResponse(widgetId)},
        async: false,
        dataType: 'json',
        success : function(data){ 
        	if(data.status == 'success') {
        		var shortURL = "http://shorter.bendoylegray.com/" + data.shortURL;
        		grecaptcha.reset(widgetId);
				$('.shortURL').html(function(){
					return 'Your shorter URL is: <input class="shortened" type="text" value='+shortURL+'></input>';
				});
        	} 
        	if(data.status == 'invalidData') {
	        	grecaptcha.reset(widgetId);
	        	alert('Sorry, we could not recognize the URL you entered.  Please try again (make sure it begins with http://)');
        	}
        	if(data.status == 'tooMany') {
	        	grecaptcha.reset(widgetId);
        		alert('You\'ve shortened too many URLs today. Please try again tomorrow');
        	}
        	if(data.isHuman == false) {
	        	$( ".shortURL" ).empty();
	        	alert('Go away, robot');
        	}
       	}
	});
}
	
</script>


<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
</body>

</html>