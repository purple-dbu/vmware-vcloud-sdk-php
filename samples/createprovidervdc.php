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
 * Sample for creating provider vDC.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b:";
$shorts .= "c:";
$shorts .= "d:";
$shorts .= "e::";
$shorts .= "f::";
$shorts .= "g::";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "vim:",       //-a|--vim    [required]
    "pvdc:",      //-b|--pvdc   [required]
    "ds:",        //-c|--ds     [required]
    "rp:",        //-d|--rp     [required]
    "cpu::",      //-e|--cpu  comma seperated value in Units, Allocation, Total, Used order
    "mem::",      //-f|--mem  comma seperated value in Units, Allocation, Total, Used order
    "stor::",     //-g|--stor comma seperated value in Units, Allocation, Total, Used order
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$pvdcName = null;
$dsMoRef = null;
$rpMoRef = null;
$cpuQut = array('MHz', 200, 1024, 0);  //in Units, Allocation, Total, Used order
$memQut = array('MB', 100, 512, 0);    //in Units, Allocation, Total, Used order
$storQut = array('MB', 1000, 10000, 0);//in Units, Allocation, Total, Used order
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
        $vimName = $opts['a'];
        break;
    case "vim":
        $vimName = $opts['vim'];
        break;
        
    case "b":
        $pvdcName = $opts['b'];
        break;
    case "pvdc":
        $pvdcName = $opts['pvdc'];
        break;

    case "c":
        $dsMoRef = $opts['c'];
        break;
    case "ds":
        $dsMoRef = $opts['ds'];
        break;

    case "d":
        $rpMoRef = $opts['d'];
        break;
    case "rp":
        $rpMoRef = $opts['rp'];
        break;

    case "e":
        $cpuQut = explode(',', $opts['e']);
        break;
    case "cpu":
        $cpuQut = explode(',', $opts['cpu']);
        break;

    case "f":
        $memQut = explode(',', $opts['f']);
        break;
    case "mem":
        $memQut = explode(',', $opts['mem']);
        break;

    case "g":
        $storQut = explode(',', $opts['g']);
        break;
    case "stor":
        $storQut = explode(',', $opts['stor']);
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
    (true !== $list && (!isset($vimName) || !isset($pvdcName) || !isset($dsMoRef) ||
    !isset($rpMoRef))))
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

if (true === $list)
{
    $refs = $sdkExt->getVMWProviderVdcRefs();
    if (0 == count($refs))
    {
        exit(0);
    }
    foreach ($refs as $ref)
    {
        echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
             " name=" . $ref->get_name() . "\n";
    }
    exit(0);
}

// create references of the vim server
$vimRefs = $sdkExt->getVimServerRefs($vimName);
if (0 == count($vimRefs))
{
    exit("No vim server with $vimName is found\n");
}
$vimRef = $vimRefs[0];
$vimRef1 = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());
$vimRef2 = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());

// set cpu capacity
$cpu = new VMware_VCloud_API_ProviderVdcCapacityType();
$cpu->setUnits($cpuQut[0]);
$cpu->setAllocation($cpuQut[1]);
$cpu->setTotal($cpuQut[2]);
$cpu->setUsed($cpuQut[3]);

// set memory capacity
$mem = new VMware_VCloud_API_ProviderVdcCapacityType();
$mem->setUnits($memQut[0]);
$mem->setAllocation($memQut[1]);
$mem->setTotal($memQut[2]);
$mem->setUsed($memQut[3]);

// set compute capacity for the provider vDC
$compCap = new VMware_VCloud_API_RootComputeCapacityType();
$compCap->setCpu($cpu);
$compCap->setMemory($mem);
$compCap->setIsElastic(false);
$compCap->setIsHA(false);

// set storage capacity for the provider vDC
$storCap = new VMware_VCloud_API_ProviderVdcCapacityType();
$storCap->setUnits($storQut[0]);
$storCap->setAllocation($storQut[1]);
$storCap->setTotal($storQut[2]);
$storCap->setUsed($storQut[3]);

// set data store
$vimObj = new VMware_VCloud_API_Extension_VimObjectRefType();
$vimObj->setVimServerRef($vimRef1);
$vimObj->setMoRef($dsMoRef);
$vimObj->setVimObjectType('DATASTORE');

$dsRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
$dsRefs->addVimObjectRef($vimObj);

// set resource pool
$vimObj2 = new VMware_VCloud_API_Extension_VimObjectRefType();
$vimObj2->setVimServerRef($vimRef1);
$vimObj2->setMoRef($rpMoRef);
$vimObj2->setVimObjectType('RESOURCE_POOL');

$rpRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
$rpRefs->addVimObjectRef($vimObj2);

// create a provider vDC data object
$pvdc = new VMware_VCloud_API_Extension_VMWProviderVdcType();
$pvdc->set_name($pvdcName);
$pvdc->setComputeCapacity($compCap);  // adding cpu and mem capacity
$pvdc->setStorageCapacity($storCap);  // adding storage capacity
$pvdc->setDataStoreRefs($dsRefs);
$pvdc->setResourcePoolRefs($rpRefs);
$pvdc->setIsEnabled(true);
$pvdc->setVimServer(array($vimRef2));
//echo $pvdc->export() . "\n";

// create a provider vDC in vCloud Director
try
{
    $sdkExt->createVMWProviderVdc($pvdc);
}
catch(Exception $e)
{
    echo $e->getMessage() . "\n";
}

function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating a provider vDC.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createprovidervdc.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>        [req] Name of a registered Vim server in the vCloud Director.\n";
    echo "     -b|--pvdc <pvdcName>      [req] Name of the provider vDC to be created.\n";
    echo "     -c|--ds <ds>              [req] MoRef of a datastore.\n";
    echo "     -d|--rp <rp>              [req] MoRef of a resource pool.\n";
    echo "     -e|--cpu <cpu>            [opt] CPU settings: comma seperated value in Units, Allocation, Total, Used order.\n";
    echo "     -f|--mem <mem>            [opt] Memory settings: comma seperated value in Units, Allocation, Total, Used order.\n";
    echo "     -g|--stor <stor>          [opt] Storage settings: comma seperated value in Units, Allocation, Total, Used order.\n";
    echo "     -l|--list                 [opt] List all provider vDC.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -a vim -b pvdc -c datastore-5 -d resgroup-84\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -a vim -b pvdc -c datastore-5 -d resgroup-84 -e=\"'MHz', 200, 1024, 0\"\n";
    echo "     # php createprovidervdc.php -a vim -b pvdc -c datastore-5 -d resgroup-84 // using config.php to set login credentials\n";
    echo "     # php createprovidervdc.php -l// list all provider vDC\n\n";
}
?>
