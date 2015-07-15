#!/usr/bin/php
<?php
/*
|
|
|     _______ _______ _______ ______        _______               __              
|    |   |   |_     _|_     _|   __ \______|   |   |.--.--.-----.|  |_.-----.----.
|    |       | |   |   |   | |    __/______|       ||  |  |     ||   _|  -__|   _|
|    |___|___| |___|   |___| |___|         |___|___||_____|__|__||____|_____|__|
|
|                      @xxxx[{::::::::::::::::::::::::::::::::::>
|	@dustyfresh | atxsec.com
|	@RootATX | rootatx.com
|
|
*/
error_reporting(1);
set_time_limit(0);
$workers = $argv[1] or die("Specify number of workers\n\t" . $argv[0] . " 5\n");
//fsockopen(localhost, 9050) or die("ERROR: TOR is not running!\n"); // uncomment to check for TOR
function connectScan(){ 
    $m = new MongoClient();
    $time = time();
    $out = "/var/log/httphunter.log";
    $file = fopen($out, 'a+') or die("Could not open log file for reading / writing\n");
    while(true){
        $ip = long2ip(rand(0, "4294967295"));
    	require_once "./sys/GeoIP/GeoIP.php";
	$curl = curl_init();
        curl_setopt_array($curl, array(
	    // CURLOPT_PROXY => "socks5://localhost:9050", // uncomment if you want to use TOR (slower) 
            CURLOPT_USERAGENT => md5(base64_encode(rand())),
            CURLOPT_HEADER => 1,
            CURLOPT_NOBODY => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 1.5,
            CURLOPT_URL => "http://$ip"
        ));
        if(curl_exec($curl)){
            $db = $m->httphunter;
            $collection = $db->results;
            $req_info = curl_getinfo($curl);
            $foundtime = time();
	    $sslcheck = fsockopen("$ip", 443, $errno, $errstr, 3);
	    if(!$sslcheck){
		$results = array("ip" => $ip, "status" => $req_info['http_code'], "header" => curl_exec($curl), $req_info, "SSL" => "false", "SSL_DATA" => "false", "found" => $foundtime, "GeoIP" => array("country" => $geoip_country, "state" => $geoip_state));
	    } else {
		$get_cert = stream_context_create (array("ssl" => array("capture_peer_cert" => true)));
		$connect_host = stream_socket_client("ssl://$ip:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get_cert);
		$ssl = stream_context_get_params($connect_host);
		$cert_info = json_encode(openssl_x509_parse($ssl["options"]["ssl"]["peer_certificate"]), true);
		$ssl_data = $cert_info;
		$results = array("ip" => $ip, "status" => $req_info['http_code'], "header" => curl_exec($curl), $req_info, "SSL" => true, "SSL_DATA" => $ssl_data, "found" => $foundtime, "GeoIP" => array("country" => $geoip_country, "state" => $geoip_state));
	    }
            if($req_info['http_code'] == 401){
                $collection->insert($results);
                $output = "[" . date(DATE_RFC2822) . "] - $ip - 401 AUTH\n";
                flock($file, LOCK_SH);
                fwrite($file, $output);
                flock($file, LOCK_UN);

            } elseif($req_info['http_code'] == 301){
                $collection->insert($results);
                $output = "[" . date(DATE_RFC2822) . "] - $ip - 301 REDIRECT\n";
                flock($file, LOCK_SH);
                fwrite($file, $output);
                flock($file, LOCK_UN);

            } else { 
		$collection->insert($results);
                $output = "[" . date(DATE_RFC2822) . "] - $ip - HTTP OK\n";
                flock($file, LOCK_SH);
                fwrite($file, $output);
                flock($file, LOCK_UN);

            }
        } 
    }
}

for ($i = 1; $i <= $workers; ++$i){ 
    $pid = pcntl_fork(); 

    if (!$pid){
        connectScan();
    }
}
