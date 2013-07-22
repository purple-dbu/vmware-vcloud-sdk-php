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
 * Sample for creating a port group type of network pool.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b:";
$shorts .= "c:";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "vim:",       //-a|--vim    [required]
    "pool:",      //-b|--pool   [required]
    "net:",       //-c|--net    [required]
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$poolName = null;
$netMoRef = null;

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
        $vimName = $opts['a'];
        break;
    case "vim":
        $vimName = $opts['vim'];
        break;
        
    case "b":
        $poolName = $opts['b'];
        break;
    case "pool":
        $poolName = $opts['pool'];
        break;

    case "c":
        $netMoRef = $opts['c'];
        break;
    case "net":
        $netMoRef = $opts['net'];
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd)) ||
    !isset($vimName) || !isset($poolName) || !isset($netMoRef))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

// creates an SDK Extension object
$sdkExt = $service->createSDKExtensionObj();

// create references to the vim server
$vimRefs = $sdkExt->getVimServerRefs($vimName);
if (0 == count($vimRefs))
{
    exit("No vim server with $vimName is found\n");
}
$vimRef = $vimRefs[0];
$vimRef1 = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());
$vimRef2 = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());

// create references of portgroup
$pgRef = new VMware_VCloud_API_Extension_VimObjectRefType();
$pgRef->setVimServerRef($vimRef1);
$pgRef->setMoRef($netMoRef);
$pgRef->setVimObjectType('NETWORK');
$pgRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
$pgRefs->addVimObjectRef($pgRef);

// create a port group type of network pool data object
$pool = new VMware_VCloud_API_Extension_PortGroupPoolType();
$pool->set_name($poolName);
$pool->setPortGroupRefs($pgRefs);
$pool->setVimServer($vimRef2);

// create a network pool in the vCloud Director
$sdkExt->createVMWNetworkPool($pool);


function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating a port group type of network pool.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createnetpool.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>       [req] Name of an existing registered Vim server in the vCloud Director.\n";
    echo "     -b|--pool <poolName>     [req] Name of the network pool to be created.\n";
    echo "     -c|--net <moref>         [req] Vim port group MoRef.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createnetpool.php -s 127.0.0.1 -u admin@Org -p password -a vim -b pool -c network-19\n";
    echo "     # php createnetpool.php -a vim -b pool -c network-19 // using config.php to set login credentials\n\n";
}
?>
