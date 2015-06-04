PHP Memory Cacher 
=================
#Key-value storage in memory  
[![Build Status](https://travis-ci.org/jamm/Memory.png)](https://travis-ci.org/jamm/Memory)  
As a storage can be used:

 * [APC](http://pecl.php.net/package/APC)
 * [Redis](http://redis.io)
 * [Couchbase](http://www.couchbase.com)
 * [Memcache](http://pecl.php.net/package/memcache)
 * [Shared memory](http://php.net/manual/en/book.shmop.php)
 
All storage objects have one interface, so you can switch them without changing the working code.

#Features:
+ Tags for keys
+ "Dog-pile" ("cache miss storm") and "race condition" effects are excluded
+ Lock, unlock or acquire key just by one command
+ Auto Unlocker - any locked key will be automatically unlocked (on exit from function or script)
+ You can select keys via callback-function (Map)
+ One interface for all storages - you can change storage without changing your code
+ Increment() method can work with arrays, strings and numeric values
+ MultiAccess class can be used for any resource, to create an access model *one write multiple read*

#Usage:
See [demo.php](https://github.com/jamm/Memory/blob/master/demo.php) to get examples of code.  

You can use MemoryObjects (RedisObject, CouchbaseObject, APCObject, MemcacheObject, SHMObject) as usual key-value storage: get/set/delete.    
What for this library was designed is to provide additional features, such as Tags or "dog-pile" effect avoidance.  

In all storages race conditions are excluded, but you can also lock keys to avoid race conditions in your algorithm:  
for example, see this code:

    $value = $mem->read('key');    
    if (some_condition()) $mem->save('key', $value . 'append');

If this code will be executed by 2 scripts simultaneously, 'append' of one script will be lost.  
To avoid it, you can lock key:    
  
	if ($mem->lock_key('key', $au))  
	{
		if (some_condition()) $mem->save('key', $value . 'append');
	}
  
or acquire:  
 
	if ($mem->acquire_key('key', $au))  
	{
		if (some_condition()) $mem->save('key', $value . 'append');
	}
  
Difference between these methods is what they will do when key is locked by another process: lock_key() will just return 'false', 
acquire_key() will wait until key will not be unlocked (maximum time of waiting declared in code).  

All 'locks' here are *soft*. It means keys aren't locked for write or read, but you can check, if key is 'locked' or not, and what to do with this - is decision of your script.    
It was designed to avoid dead-locks and unnecessary queues of clients which waits for access the key.

Example in code:

	if ($mem->lock_key('key', $au))  
	{
		if (some_condition()) $mem->save('key', $value . 'append');
	}
	else
	{
		// key is not hard-locked actually
		$mem->del('key'); // we can do this
		// but we can use 'locks' to manage multi-process interactions properly and easy (see previous code examples)
	}

To avoid the "Dog-pile" effect ("cache miss storm", "cache stampede"), we can use second argument of method read() - when time of expiration is near, we can try to lock key, and if key was locked - update value.   
See example in [demo.php](https://github.com/jamm/Memory/blob/master/demo.php).    

#Requirements:
You can use each storage separately, requirements are individually for storages

###PHP version: 5.3+

##For APCObject:
[APC](http://pecl.php.net/package/APC) should be installed, and this setting should be added in php.ini (or apc.ini if you use it)

+ apc.slam_defense = Off
+ __recommended:__ apc.user_ttl = 0

##For Memcache:
[Memcache](http://pecl.php.net/package/memcache) or [Memcached](http://pecl.php.net/package/memcached) PHP extension should be installed.  
Memcache is not the fastest and most secure storage, so use it only when necessary. [Read more](http://code.google.com/p/memcached/wiki/WhyNotMemcached)

##For Redis:
[Redis](http://redis.io) server should be installed (in debian/ubuntu: "apt-get install redis-server").  
Supported version is 2.6.9 and below.
Also, [phpredis](https://github.com/nicolasff/phpredis) (if installed) can be used as client library - just use `PhpRedisObject` instead of default `RedisObject`.

##For Couchbase:
Installed [Couchbase PHP SDK](http://www.couchbase.com/develop/php/current)    
[Couchbase Server 2.0](http://www.couchbase.com/download)  
Use few nodes if you need fault-tolerance.  

##For SHMObject and MultiAccess:
PHP should support shm-functions and msg-functions (--enable-shmop --enable-sysvsem --enable-sysvshm --enable-sysvmsg)  
Should be used only in specific cases (e.g. mutexes), or when other extensions can not be installed.

#Storages comparison:
**APC** is a very fast and easy to use storage, use it when RAM on app-server is enough for caching.  
If APC can not be used for caching data, or you just like Redis - use **Redis**.    
Use **Couchbase** if you need fault-tolerant and very easy scalable cluster.   
Redis, Couchbase and **Memcache(d)** can be used for cross-process communication. Also, data in Redis and Couchbase storages will be restored even after server reboot.     
If you can't install any third-party packages, you can use **Shared Memory** - but your PHP should be compiled with support of shmop-functions.  

Tests:
=====

	<?php
	namespace Jamm\Memory\Tests;
	
	header('Content-type: text/plain; charset=utf-8');
	
	$testRedisObject = new TestMemoryObject(new \Jamm\Memory\RedisObject('test'));
	$testRedisObject->RunTests();
	$testRedisServer = new TestRedisServer();
	$testRedisServer->RunTests();

	$printer = new \Jamm\Tester\ResultsPrinter();	
	$printer->addTests($testRedisObject->getTests());
	$printer->addTests($testRedisServer->getTests());
	$printer->printResultsLine();
	$printer->printFailedTests();

***
_Look at the comments in demo.php for additional info. Ask, what you want to see commented._

License: [MIT](http://en.wikipedia.org/wiki/MIT_License)
