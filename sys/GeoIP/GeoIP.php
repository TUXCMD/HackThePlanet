<?php
if(!$ip){
	die("IP is not set, GeoIP cannot work :(\n");
}
require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;
$reader = new Reader('/usr/local/share/GeoIP/GeoLite2-City.mmdb');
$record = $reader->city($ip);
$geoip_country = $record->country->isoCode; // 'US'
$geoip_state = $record->mostSpecificSubdivision->name; // 'Minnesota'
