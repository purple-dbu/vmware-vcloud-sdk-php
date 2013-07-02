<?php
/**
 * VMware vCloud SDK for PHP
 *
 * PHP version 5
 * *******************************************************
 * Copyright VMware, Inc. 2010-2012. All Rights Reserved.
 * *******************************************************
 *
 * @category    VMware
 * @package     VMware_VCloud_SDK
 * @subpackage  Samples
 * @author      Ecosystem Engineering
 * @disclaimer  this program is provided to you "as is" without
 *              warranties or conditions # of any kind, whether oral or written,
 *              express or implied. the author specifically # disclaims any implied
 *              warranties or conditions of merchantability, satisfactory # quality,
 *              non-infringement and fitness for a particular purpose.
 * @SDK version 5.1.0
 */

require_once dirname(__FILE__) . '/config.php';

/**
 * Sample for host preparing, unpreparing, enabling, disabling operations. 
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "a::";
$shorts .= "b::";
$shorts .= "c::";
$shorts .= "o::";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "host::",     //-a|--host   [required when not listing]
    "huser::",    //-b|--huser  [required when $op='prep']
    "hpswd::",    //-c|--hpswd  [required when $op='prep']
    "op::",       //-o|--op     [required when not listing]
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$hostName = null;
$hostUser = null;
$hostPswd = null;
$op = null;   // 'prep', 'unprep', 'en', 'dis' are supported
$list = null;

// loop through command arguments
foreach (array_keys($opts) as $opt) switch ($opt)
{
    case "s":
        $server = $opts['s'];
        break;
    case "server":
        $server = $opts['server'];
        break;

    case "u":
        $user = $opts['u'];
        break;
    case "user":
        $user = $opts['user'];
        break;

    case "p":
        $pswd = $opts['p'];
        break;
    case "pswd":
        $pswd = $opts['pswd'];
        break;

    case "a":
        $hostName = $opts['a'];
        break;
    case "host":
        $hostName = $opts['host'];
        break;

    case "b":
        $hostUser = $opts['b'];
        break;
    case "huser":
        $hostUser = $opts['huser'];
        break;

    case "c":
        $hostPswd = $opts['c'];
        break;
    case "hpswd":
        $hostPswd = $opts['hpswd'];
        break;

    case "o":
        $op = $opts['o'];
        break;
    case "op":
        $op = $opts['op'];
        break;

    case "l":
        $list = true;
        break;
    case "list":
        $list = true;
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd)) ||
    (true !== $list && (!isset($hostName) || !isset($op))) ||
    ('prep' == $op && (!isset($hostUser) || !isset($hostPswd))))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}
if ($op && !in_array($op, array('prep' , 'unprep', 'en', 'dis')))
{
    exit("$op is not supported, allowed operations are: 'prep' (for prepare),
          'unprep' (for unprepare), 'en' (for enable), 'dis' (for disable)\n");
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

// creates an SDK Extension object
$sdkExt = $service->createSDKExtensionObj();

// create an SDK host object
$hostRefs = $sdkExt->getHostRefs($hostName);
if (0 == count($hostRefs))
{
    if (isset($hostName))
    {
        exit("No host with name $hostName is found\n");
    }
    else
    {
        exit(0);
    }
}
if ($list)
{
    foreach ($hostRefs as $ref)
    {
        echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
             " name=" . $ref->get_name() . "\n";
    }
    exit(0);
}
$sdkHost = $service->createSDKObj($hostRefs[0]);

switch ($op) 
{
    case 'prep':   // prepare the host
        $sdkHost->prepare($hostUser, $hostPswd);
        break;
    case 'unprep': // unprepare the host  
        $sdkHost->unprepare();
        break;
    case 'en':     // enable the host
        $sdkHost->enable();
        break;
    case 'dis':    // disable the host
        $sdkHost->disable();
        break;
}


/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates preparing, unpreparing, enabling, disabling operations on a host.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php host.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>    [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>         [req] User name in the form user@organization\n";
    echo "                                         for the vCloud Director.\n";
    echo "     -p|--pswd <password>         [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--host <hostName>         [opt] Name of an existng host. Required when not listing\n";
    echo "     -b|--huser <username>        [opt] Username of the host. Required for preparing\n";
    echo "     -c|--hpswd <password>        [opt] Password of the host. Required for preparing\n";
    echo "     -o|--op <prep/unprep/en/dis> [opt] Operation on the host. Allows: prep, unprep, en, dis. Required when not listing\n";
    echo "     -l|--list                    [opt] List all hosts in vCloud Director.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php host.php -s 127.0.0.1 -u admin@Org -p password -a=host -b=user -c=pswd -o=prep // prepare a host\n";
    echo "     # php host.php -a=host -o=en     // using config.php to set login credentials, enable a host\n";
    echo "     # php host.php -a=host -o=dis    // disable a host\n";
    echo "     # php host.php -a=host -o=unprep // unprepare a host\n";
    echo "     # php host.php -l                // list all hosts in vCloud Director\n";
}
?>
