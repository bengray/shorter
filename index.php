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

	    <div class="buttons">
	        <button class="btn" name="save" onclick="submit()">Make shorter!</button>
	    </div>

	    <div class="shortURL">&nbsp;</div>
	</div>
<div id="date"></div>
<script type="text/javascript">

// Setting the api location 
var apiLocation = 'http://shorter.bendoylegray.com/api.php';
	
// Grab the JSON data being passed back from the api and storing it for use wherever we want.
$.getJSON(apiLocation, function(data){});

function submit() {
	var longURL = document.getElementById('url').value;
	
	$.ajax({
		type: "POST",   
        url: apiLocation,   
        data: {"name": 'urlSubmit', "url": longURL},
        async: false,
        dataType: 'json',
        success : function(data){ 
        	if(data.status == 'success') {
        		var shortURL = "http://shorter.bendoylegray.com/" + data.shortURL;
				$('.shortURL').html(function(){
					return 'Your shorter URL is: <input class="shortened" type="text" value='+shortURL+'></input>';
				});
        	} 
        	if(data.status == 'invalidData') {
	        	alert('Sorry, we could not recognize the URL you entered.  Please try again (make sure it begins with http://)');
        	}
        	if(data.status == 'tooMany') {
        		alert('You\'ve shortened too many URLs today. Please try again tomorrow');
        	}
       	}
	});
}
//$("#date").text( (new Date).getFullYear() );	
</script>



</body>

</html>