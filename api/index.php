<?php
require 'vendor/autoload.php';
$app = new \Slim\Slim();

$app->notFound(function () use ($app) {
	$app = \Slim\Slim::getInstance();
	$app->response->setStatus(404);
	echo "<body><center>
		<img src='http://i.imgur.com/33vDXBr.png'><br/>
		<br/>404 not found - plz go away now
	</center></body></html>";
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
/version - check version<br/>
/search/ip/{ip} - search for documents matching an IP address<br/>
/get/ips - get all IPs in the database. argument not required. Includes GeoIP data<br/>";
});

$app->get('/version', function(){
	$app = \Slim\Slim::getInstance();
	$app->response()->headers->set('Content-Type', 'application/json');
        print json_encode(array('version' => 'beta-0.1'), JSON_PRETTY_PRINT);
});

$app->get('/search/ip/:ip', function($ip){
        $app = \Slim\Slim::getInstance();
        $app->response()->headers->set('Content-Type', 'application/json');
	require_once 'inc/db.php';
	$cursor = $collection->find(array('ip' => "$ip"), array('_id' => 0));
	foreach($cursor as $result){
		print json_encode($result,JSON_PRETTY_PRINT);
	}
});

$app->get('/get/ips', function() use($app){
	$app->response()->headers->set('Content-Type', 'application/json');
	require_once 'inc/db.php';
	$cursor = $collection->find(array(), array('_id' => 0, 'ip' => 1, 'SSL' => 1, 'GeoIP' => 1));
	$out = array();
        foreach($cursor as $result){
                array_push($out, array($result['ip'] => $result['GeoIP'], 'SSL' => $result['SSL']));
	}
	print json_encode($out, JSON_PRETTY_PRINT);
});

$app->run();
