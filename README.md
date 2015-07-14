# HTTP-Hunter
Multi-threaded system that generates IP addresses at random, then checks for HTTP. If HTTP is accepted we record the header data and store into MongoDB for later searchable use.

## Why?
Because I'm cheap, and I don't feel like paying for [Shodan.io](https://developer.shodan.io/pricing).

## Requirements
PHP: pcntl();

PHP: MongoClient();

PHP: curl();

MongoDB

## Running
```/usr/bin/php hunter.php <workers>```

## Making use and querying collected datas
Use the MongoDB shell
```$ mongo httphunter```

```
> db.results.find({"header": {$regex: /.*Microsoft-IIS.*/, $options: 'si'}}).count()
187
```

```
> db.results.find({'status': 401}).count()
2032
```

Execute queries from the command line
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

## Logging
*/var/log/httphunter.log*

To-Do list
----------
- [ ] Create cache of IP ranges to not scan (government, law enforcement, certain countries, etc.)
- [ ] Add TTL to each record entry to ensure that records do not become "stale"
- [ ] Introduce SSL support
  - [ ] Function to check for SSL versions so they can be matched to attacks (do not actually attack hosts, just check version numbers and cross reference them with potential vulnerabilities
- [ ] Create jobs to search for common content management systems and fingerprint them
- [ ] Create pruning scripts to do routine maintenance on MongDB database
- [ ] Insert logic to allow for MongoDB clustered setup (for scale)
- [ ] Web dashboard to manage slaves when running in clustered mode
- [ ] Add GeoIP function that stores it with each entry in the MongoDB collection
- [ ] Create JSON based configuration system
- [ ] Make init scripts
- [ ] Add scan type to configuration of each scanning node (port number specification, timeouts, etc.)
- [ ] Clustered functionality
- [ ] Write system dashboard
    - [ ] Find a decent PHP web framework to use for this (CodeIgniter, Cake, Lavarel, Slim, etc.)
      - [ ] PHP Framework selected: ____
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
      - [ ] use access control function to govern API access and quotas
- [ ] Ansible
    - [ ] playbook for deploying DB nodes
    - [ ] playbook for deploying worker nodes
- [ ] Jobs to scan found hosts for other common services (SSH, MySQL, FTP, Telnet, etc.) periodically
