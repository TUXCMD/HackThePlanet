<html>
<title>HTTP-Hunter API</title>
<?php

require 'vendor/autoload.php';
$app = new \Slim\Slim();

$app->notFound(function () use ($app) {
	http_response_code(404);
	echo "<center>
		<img src='http://i.imgur.com/33vDXBr.png'><br/>
		<br/>404 not found - plz go away now
	</center>";
});

$app->get('/', function(){
	echo "<pre>" . 
		"
     _______ _______ _______ ______        _______               __              
    |   |   |_     _|_     _|   __ \______|   |   |.--.--.-----.|  |_.-----.----.
    |       | |   |   |   | |    __/______|       ||  |  |     ||   _|  -__|   _|
    |___|___| |___|   |___| |___|         |___|___||_____|__|__||____|_____|__|

                      @xxxx[{::::::::::::::::::::::::::::::::::>
	The API :)" .
		"</pre><br/>
<u><h3>Methods:</h3></u>
/version - check version [ SUPPORTED ]<br/>
/search/ip/{ip} - search for documents matching an IP address [ NOT SUPPORTED ]<br/>
/search/ssl/data/{string} - search for documents that match a string found in found in SSL_DATA (found x509 generated certificates)  [ NOT SUPPORTED ]<br/>
/search/ssl/{ip} - check if a specific IP that was found by HTTP-Hunter supports SSL, and returns the SSL_DATA record  [ NOT SUPPORTED ]<br>
/search/ssl - no argument, retrieve only the IP addresses and GeoIP data of ALL hosts that support SSL  [ NOT SUPPORTED ]<br/>
/search/countrycode/{code} - search by country code (US,GB,MX, etc.)  [ NOT SUPPORTED ]";
});

$app->get('/search/ip/:ip', function($ip){
	echo "This function will lookup the record for $ip";

});

$app->get('/version', function(){
	echo "beta-0.1";
});

$app->run();
