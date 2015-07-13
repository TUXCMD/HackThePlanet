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
