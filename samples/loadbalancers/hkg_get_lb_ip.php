<?php
/**
 * (c)2012 Rackspace Hosting. See COPYING for license details
 *
 */
$start = time();

// require_once "php-opencloud.php";

/**
 * Relies upon environment variable settings âhese are the same environment
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
define('USERNAME', '<USRNAME>');
define('TENANT', '<TENANT>');
define('APIKEY', '<API_KEY>');


define('VOLUMENAME', 'SampleVolume');
define('VOLUMESIZE', 100);

//$fh = fopen('mytextfile.txt', 'w');

/**
 * numbers each step
 */
function info($msg,$p1=NULL,$p2=NULL,$p3=NULL,$p4=NULL,$p5=NULL) {
    printf("  %s\n", sprintf($msg,$p1,$p2,$p3,$p4,$p5));
}
define('TIMEFORMAT', 'r');

$rackspace = new \OpenCloud\Rackspace(AUTHURL,
        array( 'username' => USERNAME,
                   'apiKey' => APIKEY ));

$lbservice = $rackspace->LoadBalancerService('cloudLoadBalancers', 'HKG');

$mylb = $lbservice->LoadBalancer('LB_ID');

$test = $mylb->virtualIps;
$ip =   $test[0]->address;
printf($ip);
