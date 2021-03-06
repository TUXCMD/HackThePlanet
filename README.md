# HackThePlanet
Multi-threaded system that generates IP addresses at random, then checks for HTTP. If HTTP is accepted we record the header data and store into MongoDB for later searchable use.

## Why?
Because I'm cheap, and I don't feel like paying for [Shodan.io](https://developer.shodan.io/pricing).

## Requirements
PHP: pcntl();

PHP: MongoClient();

PHP: curl();

MongoDB

[GeoIP2-php](http://maxmind.github.io/GeoIP2-php/) - follow instructions on adding database to /usr/local/share/GeoIP/GeoIP2-ISP.mmdb

## Running
```/usr/bin/php hunter.php <workers>```

## REST API
Yes! This is happening! the api/ directory contains the Slim PHP framework files for the beginnings of our REST API.

## Making use and querying collected datas
Use the MongoDB shell
```$ mongo httphunter```

Count the number of results that match the string Microsoft-ISS (not case sensitive)
```
> db.results.find({"header": {$regex: /.*Microsoft-IIS.*/, $options: 'si'}}).count()
187
```

Count the number of results found with a 401 status code (Unauthorized, basic HTTP auth normally.)
```
> db.results.find({'status': 401}).count()
2032
```

Execute queries from the command line instead of using the Mongo shell
```
$ mongo httphunter --eval "printjson(db.results.find({status:200}, {ip:0, _id:0}).limit(1).toArray())"
MongoDB shell version: 2.4.9
connecting to: httphunter
[
	{
		"status" : 200,
		"header" : "HTTP/1.1 200 OK\r\nDate: Mon, 13 Jul 2015 18:17:01 GMT\r\nContent-Length: 1193\r\nContent-Type: text/html\r\nContent-Location: http://0.<REDACTED>.206.248/iisstart.htm\r\nLast-Modified: Fri, 21 Feb 2003 12:15:52 GMT\r\nAccept-Ranges: bytes\r\nETag: \"0ce1f9a2d9c21:276f\"\r\nServer: IIS\r\nX-Powered-By: WAF/2.0\r\nSet-Cookie: safedog-flow-item=2F3F1EF09C22C6F0A6DB702CB0F1E804; expires=Thur, 19-Aug-2151 21:28:17 GMT; domain=<REDACTED>; path=/\r\n\r\n",
		"0" : {
			"url" : "http://0.<REDACTED>.206.248/",
			"content_type" : "text/html",
			"http_code" : 200,
			"header_size" : 424,
			"request_size" : 100,
			"filetime" : -1,
			"ssl_verify_result" : 0,
			"redirect_count" : 0,
			"total_time" : 1.392957,
			"namelookup_time" : 0.000058,
			"connect_time" : 0.335482,
			"pretransfer_time" : 0.335657,
			"size_upload" : 0,
			"size_download" : 0,
			"speed_download" : 0,
			"speed_upload" : 0,
			"download_content_length" : 1193,
			"upload_content_length" : 0,
			"starttransfer_time" : 1.392885,
			"redirect_time" : 0,
			"redirect_url" : "",
			"primary_ip" : "<REDACTED>",
			"certinfo" : [ ],
			"primary_port" : 80,
			"local_ip" : "10.0.2.15",
			"local_port" : 41606
		},
		"found" : 1436811425
	}
]
```

Querying for found hosts with SSL support
```
> db.results.find({SSL: true}, {ip:1, status:1, SSL:1, _id:0}).count()
34
```

## Logging
*/var/log/httphunter.log*

To-Do list
----------
- [ ] Add TTL to each record entry to ensure that records do not become "stale" (the found key will be used for this since it's an epoch timestamp)
- [x] ~~Introduce SSL support~~
  - [x] ~~Store x509 generated cert data into the entry with the host that supports SSL~~
  - [ ] Function to check for SSL versions so they can be matched to attacks (do not actually attack hosts, just check version numbers and cross reference them with potential vulnerabilities
- [ ] Create jobs to search for common content management systems and fingerprint them
- [ ] Create pruning scripts to do routine maintenance on MongDB database
- [ ] Insert logic to allow for MongoDB clustered setup (for scale)
- [ ] Web dashboard to manage slaves when running in clustered mode
- [x] ~~Add GeoIP function that stores it with each entry in the MongoDB collection~~
- [ ] Create JSON based configuration system
- [ ] Make init scripts
- [ ] Add scan type to configuration of each scanning node (port number specification, timeouts, etc.)
- [ ] Clustered functionality
  - [ ] Heartbeat workers to display heatlh data
- [ ] Write system dashboard
    - [x] ~~Find a decent PHP web framework to use for dashboard~~
      - [x] ~~PHP Framework selected: [Slim](http://www.slimframework.com)~~
    - [ ] Cluster mode management in the dashboard
    - [ ] Cluster worker  management
    - [ ] Metrics page to include data on content management systems found, GeoIP metrics, historical graphs
    - [ ] Cloud provider support for deployment of Worker machines and DB nodes (scalability)
      - [ ] EC2
      - [ ] Digital Ocean
      - [ ] RackSpace
      - [ ] Linode
    - [ ] User management & Access controls
    - [ ] Export data feature
- [ ] REST API
  - [x] ~~Find framework for API (going with slim)~~
  - [ ] use access control function to govern API access and quotas
- [ ] Ansible
    - [ ] playbook for deploying DB nodes
    - [ ] playbook for deploying worker nodes
- [ ] Jobs to scan found hosts for other common services (SSH, MySQL, FTP, Telnet, etc.) periodically
- [ ] Install script
- [ ] Documentation
