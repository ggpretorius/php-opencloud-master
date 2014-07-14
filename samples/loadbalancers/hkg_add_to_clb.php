<?php
/**
 * (c)2012 Rackspace Hosting. See COPYING for license details
 *
 */
$start = time();

// require_once "php-opencloud.php";

/**
 * Relies upon environment variable settings — these are the same environment
 * variables that are used by python-novaclient. Just make sure that they're
 * set to the right values before running this test.
 */
/*define('AUTHURL', RACKSPACE_US);
define('USERNAME', $_ENV['OS_USERNAME']);
define('TENANT', $_ENV['OS_TENANT_NAME']);
define('APIKEY', $_ENV['NOVA_API_KEY']);
*/

require_once "/root/php-opencloud-master/lib/php-opencloud.php";

define('AUTHURL', 'https://hkg.identity.api.rackspacecloud.com/v2.0/');
//define('AUTHURL', 'https://ord.loadbalancers.api.rackspacecloud.com/v1.0/');
define('USERNAME', '<USERNAME>');
define('TENANT', '<TENANT>');
define('APIKEY', '<API_KEY>');


define('VOLUMENAME', 'SampleVolume');
define('VOLUMESIZE', 100);

//$fh = fopen('mytextfile.txt', 'w');

/**
 * numbers each step
 */
function step($msg,$p1=NULL,$p2=NULL,$p3=NULL) {
    global $STEPCOUNTER;
    printf("\nStep %d. %s\n", ++$STEPCOUNTER, sprintf($msg,$p1,$p2,$p3));
}
function info($msg,$p1=NULL,$p2=NULL,$p3=NULL,$p4=NULL,$p5=NULL) {
    printf("  %s\n", sprintf($msg,$p1,$p2,$p3,$p4,$p5));
}
define('TIMEFORMAT', 'r');

step('Authenticate');
$rackspace = new \OpenCloud\Rackspace(AUTHURL,
	array( 'username' => USERNAME,
		   'apiKey' => APIKEY ));

step('Connect to the Load Balancer Service');
$lbservice = $rackspace->LoadBalancerService('cloudLoadBalancers', 'HKG');

$mylb = $lbservice->LoadBalancer('LB_ID');

info('NAME: '.$mylb->name);

//fwrite($fh, $write); 

//Adding a node through  php-opencloud does not work!!!
//$mynodes = $mylb->addNode('31.2i22.179.62','80');
//$myresult = $mylb->AddNodes();
//print($myresult);

$myip = trim(`/sbin/ifconfig eth1 | grep "inet addr:" | sed s/addr:// | awk '{print $2;}'`);
info('MYIP: '.$myip);

$token = $rackspace->getToken();

$headers  =  array("X-Auth-Token: $token","Content-Type: application/json");
$content = "{\"nodes\": [{\"address\": \"".$myip."\",\"port\": 80,\"condition\": \"ENABLED\"}]}";
info('HEADERS:');
print_r(array_values($headers));
info("CONTENT: ".$content);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://ord.loadbalancers.api.rackspacecloud.com/v1.0/834457/loadbalancers/LB_ID/nodes");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 4);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

$data = curl_exec($ch);

if (curl_errno($ch)) {
 print curl_error($ch);
} else {
 curl_close($ch);
}

// $data contains the result of the post...
info("DATA: ".$data);

// list load balancers
$list = $lbservice->LoadBalancerList();
if ($list->Size()) {
	step('Load balancers:');
	while($lb = $list->Next()) {
		info('[%s] %s in %s', $lb->id, $lb->Name(), $lb->Region());
		info('  Status: [%s]', $lb->Status());
		
		// Nodes
		$list = $lb->NodeList();
		if ($list->Size() == 0)
			info('  No nodes');
		else {
			while($node = $list->Next()) {
				info('  Node: [%s] %s:%d %s/%s', 
					$node->Id(), $node->address, $node->port,
					$node->condition, $node->status);
			}
		}
		
		// NodeEvents
		//$list = $lb->NodeEventList();
		//if ($list->Size() == 0)
		//	info('  No node events');
		//else {
		//	while($event = $list->Next()) {
		//		info('  * Event: %s (%s)', 
		//			$event->detailedMessage, $event->author);
		//	}
		//}
		
		// SSL Termination
		//try {
		//	$ssl = $lb->SSLTermination();
		//	info('  SSL terminated');
		//} catch (OpenCloud\InstanceNotFound $e) {
		//	info('  No SSL termination');
		//}
		
		// Metadata
		$list = $lb->MetadataList();
		while($meta = $list->Next()) {
			info('  [Metadata #%s] %s=%s', 
				$meta->Id(), $meta->key, $meta->value);
		}
	}
}
else
	step('There are no load balancers');

step('DONE');
//fclose($fh);
exit;

function dot($obj) {
	info('...%s', $obj->Status());
}
