<?php
// (c)2012 Rackspace Hosting
// See COPYING for licensing information

// require_once "php-opencloud.php";
require_once "/home/promo/php-opencloud-master/lib/php-opencloud.php";

define('AUTHURL', 'https://lon.identity.api.rackspacecloud.com/v2.0/');
define('USERNAME', 'gpretorius');
define('TENANT', '10007226');
define('APIKEY', '2b579f6a2388c46cd17d17a94c0cab1b');

#print(AUTHURL);
print($_ENV['OS_USERNAME']);
//print(USERNAME);
//print(TENANT);
//print(APIKEY);

// establish our credentials
$connection = new \OpenCloud\Rackspace(AUTHURL,
	array( 'username' => USERNAME,
		   'apiKey' => APIKEY ));

// now, connect to the compute service
$compute = $connection->Compute('cloudServersOpenStack', 'LON');

// list all servers
print("ALL SERVERS:\n");
$slist = $compute->ServerList();
while($server = $slist->Next())
    printf("* %-20s %-10s (%s)\n", 
		$server->Name(), $server->status, $server->ip());

// list all servers named MODEL
print("\nALL SERVERS NAMED 'MODEL':\n");
$slist = $compute->ServerList(TRUE, array('name'=>'MODEL'));
while($server = $slist->Next())
    printf("* %s\n", $server->name);
