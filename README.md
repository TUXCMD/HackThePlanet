# HTTP-Hunter
Multi-threaded system that generates IP addresses at random, then checks for HTTP. If HTTP is accepted we record the header data and store into MongoDB for later searchable use.

## Requirements
PHP: pcntl();

PHP: MongoClient();

PHP: curl();

MongoDB

## Running
```/usr/bin/php hunter.php <workers>```

## Making use and querying collected datas
```$ mongo```

```> db.results.find({"header": {$regex: /.*Microsoft-IIS.*/, $options: 'si'}}).count()
187```

```> db.results.find({'status': 401}).count()
2032```

## Logging
*/var/log/httphunter.log*

To-Do list
----------
- [ ] Create cache of IP ranges to not scan (government, law enforcement, certain countries, etc.)
- [ ] Add TTL to each record entry to ensure that records do not become "stale"
- [ ] Introduce SSL support
  - [ ] Function to check for SSL versions so they can be matched to attacks (do not actually attack hosts, just check version numbers and cross reference them with potential vulnerabilities
- [ ] Create jobs to search for common content management systems and fingerprint them
- [ ] Add web interface to search, modify, and or delete entries in the system
- [ ] Create pruning scripts to do routine maintenance on MongDB database
- [ ] Insert logic to allow for MongoDB clustered setup (for scale)
- [ ] Web dashboard to manage slaves when running in clustered mode
- [ ] Add GeoIP function that stores it with each entry in the MongoDB collection
- [ ] Create JSON based configuration system
- [ ] Make init scripts

