<?php
/**
 * VMware vCloud SDK for PHP
 *
 * PHP version 5
 * *******************************************************
 * Copyright VMware, Inc. 2010-2013. All Rights Reserved.
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
 * @SDK version 5.5.0
 */

require_once dirname(__FILE__) . '/config.php';

/**
 * Sample for Creating, Getting, Updating and Deleting Network Pool.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "v:";

$shorts .= "a:";
$shorts .= "b:";
$shorts .= "c:";
$shorts .= "d:";
$shorts .= "e:";
$shorts .= "f:";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "type:",      //-a|--type      [required] Network Pool type
    "vim:",       //-b|--vim       [required] Name of an existing registered Vim server
    "pool:",      //-c|--pool      [required] Name of the network pool to be created
    "name:",      //-d|--name      [required] Name of port group
    "dvswitch:",  //-e|--dvswitch  [required] Name of dvSwitch
    "certpath:",  //-f|--certpath  [optional] local certificate path
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$poolName = null;
$portGroupName = null;
$netpooltype = null;
$dvSwitchName = null;
$portGroupType = null;
$certPath = null;

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

    case "v":
        $sdkversion = $opts['v'];
        break;
    case "sdkver":
        $sdkversion = $opts['sdkver'];
        break;

    case "a":
        $netpooltype = $opts['a'];
        break;
    case "type":
        $netpooltype = $opts['type'];
        break;

    case "b":
        $vimName = $opts['b'];
        break;
    case "vim":
        $vimName = $opts['vim'];
        break;

    case "c":
        $poolName = $opts['c'];
        break;
    case "pool":
        $poolName = $opts['pool'];
        break;

    case "d":
        $portGroupName = $opts['d'];
        break;
    case "name":
        $portGroupName = $opts['name'];
        break;

    case "e":
        $dvSwitchName = $opts['e'];
        break;
    case "dvswitch":
        $dvSwitchName = $opts['dvswitch'];
        break;

    case "f":
        $certPath = $opts['f'];
        break;
    case "certpath":
        $certPath = $opts['certpath'];
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd) || !isset($sdkversion)) ||
    !isset($netpooltype) || !isset($vimName) || !isset($poolName) || !isset($portGroupName) || !isset($dvSwitchName))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

$flag = true;
if (isset($certPath))
{
    $cert = file_get_contents($certPath);
    $data = openssl_x509_parse($cert);
    $encodeddata1 = base64_encode(serialize($data));

    // Split a server url by forward back slash
    $url = explode('/', $server);
    $url = end($url);

    // Creates and returns a stream context with below options supplied in options preset
    $context = stream_context_create();
    stream_context_set_option($context, 'ssl', 'capture_peer_cert', true);
    stream_context_set_option($context, 'ssl', 'verify_host', true);

    $encodeddata2 = null;
    if ($socket = stream_socket_client("ssl://$url:443/", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context))
    {
        if ($options = stream_context_get_options($context))
        {
            if (isset($options['ssl']) && isset($options['ssl']['peer_certificate']))
            {
                $x509_resource = $options['ssl']['peer_certificate'];
                $cert_arr = openssl_x509_parse($x509_resource);
                $encodeddata2 = base64_encode(serialize($cert_arr));
            }
        }
    }

    // compare two certificate as string
    if (strcmp($encodeddata1, $encodeddata2)==0)
    {
        echo "\n\nValidation of certificates is successful.\n\n";
        $flag=true;
    }
    else
    {
        echo "\n\nCertification Failed.\n";
        $flag=false;
    }
}

if ($flag==true)
{
    if (!isset($certPath))
    {
        echo "\n\nIgnoring the Certificate Validation --Fake certificate - DO NOT DO THIS IN PRODUCTION.\n\n";
    }
    // vCloud login
    $service = VMware_VCloud_SDK_Service::getService();
    $service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig, $sdkversion);

    echo "Login successfully.\n";

    // creates an SDK Extension object
    $sdkExt = $service->createSDKExtensionObj();

    // create references to the vim server
    $vimRefs = $sdkExt->getVimServerRefs($vimName);
    if (0 == count($vimRefs))
    {
        exit("No vim server with $vimName is found\n");
    }
    $vimRef = $vimRefs[0];
    $vimserverRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());

    if ($netpooltype== 'portgroup-backed')
    {
        $portGroupMoRef= getPortGroupMoRef($portGroupName, $vimName);
        // Add Portgroup-backed Network Pool
        echo "Adding Portgroup-backed Network Pool...\n";
        $pool = createPortGrpVMWNetworkPoolParams($vimserverRef, $portGroupMoRef, $poolName, $portGroupType);
        $VMWNetworkPool = $sdkExt->createVMWNetworkPool($pool);
        $task=$VMWNetworkPool->getTasks()->getTask();
        if (sizeof($task) > 0)
        {
            $service->waitForTask($task[0]);
        }
        echo "Added Portgroup-backed Network Pool : " . $VMWNetworkPool->get_name() . "\n";

        // create VMWNetworkpool object
        $refs = $sdkExt->getVMWNetworkPoolRefs($poolName);
        $ref = $refs[0];
        $vmwnetworkPoolob = $service->createSDKObj($ref);

        // Updated Portgroup-backed Network Pool type Network Pool
        echo "Updating Portgroup-backed Network Pool. \n";
        $pool = updatePortGrpVMWNetworkPoolParams($vimserverRef, $portGroupMoRef, $poolName . "_Updated", $portGroupType);
        $VMWNetworkPool = $vmwnetworkPoolob->modify($pool);
        $task=$VMWNetworkPool->getTasks()->getTask();
        if (sizeof($task) > 0)
        {
            $service->waitForTask($task[0]);
        }
        echo "Updated Portgroup-backed Network Pool : ".$VMWNetworkPool->get_name() . "_Updated\n";

        // Get Portgroup-backed Network Pool type Network Pool
        echo "Getting Portgroup-backed Network Pool...\n";
        $poolName = $poolName . "_Updated";
        getVMWNetworkPool($poolName);

        // Delete Portgroup-backed Network Pool type Network Pool
        echo "Deleting Portgroup-backed Network Pool...\n";
        deleteVMWNetworkPool($poolName);
        echo "Deleted Portgroup-backed Network Pool: ".$poolName."\n";
    }
    else if($netpooltype== 'vlan-backed')
    {
        $dvSwitchMoRef= getDvSwitchMoRef($dvSwitchName, $vimName);
        // Add Vlan-backed Network Pool
        echo "Adding Vlan-backed Network Pool...\n";
        $pool = createVlanVMWNetworkPoolParams($vimserverRef, $dvSwitchMoRef, $poolName);
        $VMWNetworkPool = $sdkExt->createVMWNetworkPool($pool);
        $task=$VMWNetworkPool->getTasks()->getTask();
        if (sizeof($task) > 0)
        {
            $service->waitForTask($task[0]);
        }
        echo "Added Vlan-backed Network Pool : ".$VMWNetworkPool->get_name() . "\n";

        // create VMWNetworkpool object
        $refs = $sdkExt->getVMWNetworkPoolRefs($poolName);
        $ref = $refs[0];
        $vmwnetworkPoolob = $service->createSDKObj($ref);

        // Updated Vlan-backed Network Pool type Network Pool
        echo "Update Vlan-backed Network Pool.\n";
        $pool = updateVlanVMWNetworkPoolParams($vimserverRef, $dvSwitchMoRef, $poolName . "_Updated");
        $VMWNetworkPool = $vmwnetworkPoolob->modify($pool);
        $task=$VMWNetworkPool->getTasks()->getTask();
        if (sizeof($task) > 0)
        {
            $service->waitForTask($task[0]);
        }
        echo "Updated Vlan-backed Network Pool : ".$VMWNetworkPool->get_name() . "_Updated\n";

        // Get Vlan-backed Network Pool type Network Pool
        echo "Get Vlan-backed Network Pool.\n";
        $poolName = $poolName."_Updated";
        getVMWNetworkPool($poolName);

        // Delete Vlan-backed Network Pool type Network Pool
        echo "Deleting Vlan-backed Network Pool...\n";
        deleteVMWNetworkPool($poolName);
        echo "Deleted Vlan-backed Network Pool: ".$poolName . "\n";
    }
    else if($netpooltype== 'isolation-backed')
    {
        $dvSwitchMoRef= getDvSwitchMoRef($dvSwitchName, $vimName);
        // Add Isolation-backed Network Pool
        echo "Adding Isolation-backed Network Pool. \n";
        $pool = createIsolationBakedVMWNetworkPoolParams($vimserverRef, $dvSwitchMoRef, $poolName);
        $VMWNetworkPool = $sdkExt->createVMWNetworkPool($pool);
        $task=$VMWNetworkPool->getTasks()->getTask();
        if (sizeof($task) > 0)
        {
            $service->waitForTask($task[0]);
        }
        echo "Added Isolation-backed Network Pool : ".$VMWNetworkPool->get_name() . "\n";

        // create VMWNetworkpool object
        $refs = $sdkExt->getVMWNetworkPoolRefs($poolName);
        $ref = $refs[0];
        $vmwnetworkPoolob = $service->createSDKObj($ref);

        // Updated Isolation-backed Network Pool type Network Pool
        echo "Updating Isolation-backed Network Pool. \n";
        $pool = updateIsolationBakedVMWNetworkPoolParams($vimserverRef, $dvSwitchMoRef, $poolName . "_Updated");
        $VMWNetworkPool = $vmwnetworkPoolob->modify($pool);
        $task=$VMWNetworkPool->getTasks()->getTask();
        if (sizeof($task) > 0)
        {
            $service->waitForTask($task[0]);
        }
        echo "Updated Isolation-backed Network Pool : ".$VMWNetworkPool->get_name() . "_Updated\n";

        // Get Isolation-backed Network Pool type Network Pool
        echo "Get Isolation-backed Network Pool. \n";
        $poolName = $poolName . "_Updated";
        getVMWNetworkPool($poolName);

        // Delete Isolation-backed Network Pool type Network Pool
        echo "Deleting Isolation-backed Network Pool.\n";
        deleteVMWNetworkPool($poolName);
        echo "Deleted Isolation-backed Network Pool : " . $poolName . "\n";
    }
    else
    {
        echo "you have selected wrong network pool type \n";
        echo "Please select one of these: 1. portgroup-backed 2. vlan-backed 3. isolation-backed \n";
    }
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

/**
 * Verify that one or more port groups are available in vSphere. The port groups must be available
 * on each ESX/ESXi host in the cluster, and each port group must use only a single VLAN. Port groups
 * with VLAN trunking are not supported.
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param String  $portGroupMoRef
 *
 * @return VMware_VCloud_API_Extension_PortGroupPoolType
 */
function createPortGrpVMWNetworkPoolParams($vimserverRef, $portGroupMoRef, $poolName, $portGroupType)
{
    // create references of portgroup
    $pgRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $pgRef->setVimServerRef($vimserverRef);
    $pgRef->setMoRef($portGroupMoRef);
    $pgRef->setVimObjectType($portGroupType);

    $pgRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
    $pgRefs->addVimObjectRef($pgRef);

    // create a port group type of network pool data object
    $pool = new VMware_VCloud_API_Extension_PortGroupPoolType();
    $pool->set_name($poolName);
    $pool->setPortGroupRefs($pgRefs);
    $pool->setVimServer($vimserverRef);
    return $pool;
}

/**
 * Verify that a range of VLAN IDs and a vSphere distributed switch are available in vSphere.
 * The VLAN IDs must be valid IDs that are configured in the physical switch to which the
 * ESX/ESXi servers are connected.
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param String  $dvSwitchName
 *
 * @return VMware_VCloud_API_Extension_VlanPoolType
 */
function createVlanVMWNetworkPoolParams($vimserverRef, $dvSwitchName, $poolName)
{
    // create references of VlanPool
    $pgRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $pgRef->setVimServerRef($vimserverRef);
    $pgRef->setMoRef($dvSwitchName);
    $pgRef->setVimObjectType('NETWORK');

    $numericrange=new VMware_VCloud_API_Extension_NumericRangeType();
    $numericrange->setStart(1);
    $numericrange->setEnd(10);

    // create a Vlan Pool type of network pool data object
    $pool = new VMware_VCloud_API_Extension_VlanPoolType();
    $pool->addVlanRange($numericrange);
    $pool->set_name($poolName);
    $pool->setVimSwitchRef($pgRef);
    return $pool;
}

/**
 * Verify that a vSphere distributed switch is available.
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param String  $dvSwitchName
 *
 * @return VMware_VCloud_API_Extension_FencePoolType
 */
function createIsolationBakedVMWNetworkPoolParams($vimserverRef, $dvSwitchName, $poolName)
{
    // create references of portgroup
    $pgRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $pgRef->setVimServerRef($vimserverRef);
    $pgRef->setMoRef($dvSwitchName);
    $pgRef->setVimObjectType('NETWORK');

    // create a FencePool type of network pool data object
    $pool = new VMware_VCloud_API_Extension_FencePoolType();
    $pool->set_name($poolName);
    $pool->setFenceIdCount(5);
    $pool->setVimSwitchRef($pgRef);
    $pool->setVlanId(10);
    $pool->setUsedNetworksCount(2);
    return $pool;
}

/**
 * @param String  VMWNetworkPool name   $name
 * @return array VMware_VCloud_API_ReferenceType object array
 */
function getVMWNetworkPool($name)
{
    try
    {
        global $sdkExt;
        $refs = $sdkExt->getVMWNetworkPoolRefs($name);
        if (0 == count($refs))
        {
            exit(0);
        }
        foreach ($refs as $ref)
        {
            echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
            " name=" . $ref->get_name() . "\n";
        }
    }
    catch(Exception $e)
    {
        echo $e->getMessage() . "\n";
    }
}

/**
 * Update params for portgroup-backed network pool
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param String  $portGroupMoRef
 *
 * @return VMware_VCloud_API_Extension_PortGroupPoolType
 */
function updatePortGrpVMWNetworkPoolParams($vimserverRef, $portGroupMoRef, $poolName, $portGroupType)
{
    // create references of portgroup
    $pgRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $pgRef->setVimServerRef($vimserverRef);
    $pgRef->setMoRef($portGroupMoRef);
    $pgRef->setVimObjectType($portGroupType);
    $pgRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
    $pgRefs->addVimObjectRef($pgRef);

    // create a port group type of network pool data object
    $pool = new VMware_VCloud_API_Extension_PortGroupPoolType();
    $pool->set_name($poolName);
    $pool->setPortGroupRefs($pgRefs);
    $pool->setVimServer($vimserverRef);
    return $pool;
}

/**
 * Update params for vlan-backed network pool
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param String  $dvSwitchName
 *
 * @return VMware_VCloud_API_Extension_VlanPoolType
 */
function updateVlanVMWNetworkPoolParams($vimserverRef, $dvSwitchName, $poolName)
{
    // create references of VlanPool
    $pgRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $pgRef->setVimServerRef($vimserverRef);
    $pgRef->setMoRef($dvSwitchName);
    $pgRef->setVimObjectType('NETWORK');

    $numericrange=new VMware_VCloud_API_Extension_NumericRangeType();
    $numericrange->setStart(2);
    $numericrange->setEnd(12);

    // create a Vlan Pool type of network pool data object
    $pool = new VMware_VCloud_API_Extension_VlanPoolType();
    $pool->addVlanRange($numericrange);
    $pool->set_name($poolName);
    $pool->setVimSwitchRef($pgRef);
    return $pool;
}

/**
 * Update params for isolation-backed network pool
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param String  $dvSwitchName
 *
 * @return VMware_VCloud_API_Extension_FencePoolType
 */
function updateIsolationBakedVMWNetworkPoolParams($vimserverRef, $dvSwitchName, $poolName)
{
    // create references of FencePool
    $pgRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $pgRef->setVimServerRef($vimserverRef);
    $pgRef->setMoRef($dvSwitchName);
    $pgRef->setVimObjectType('NETWORK');

    // create a FencePool type of network pool data object
    $pool = new VMware_VCloud_API_Extension_FencePoolType();
    $pool->set_name($poolName);
    $pool->setFenceIdCount(5);
    $pool->setVimSwitchRef($pgRef);
    $pool->setVlanId(10);
    $pool->setUsedNetworksCount(2);
    return $pool;
}

/**
 * @param String  VMWNetworkPool name  $name
 *
 */
function deleteVMWNetworkPool($name)
{
    global $sdkExt,$service;
    $refs = $sdkExt->getVMWNetworkPoolRefs($name);
    $ref = $refs[0];
    $vmwnetworkPoolob = $service->createSDKObj($ref);
    $vmwnetworkPoolob->delete();
}

/**
 * Get port group moref.
 * @param String  port group Name  $portGroupName
 * @param String  vim server Name  $vimName
 * @return port group moref.
 *
 */
function getPortGroupMoRef($portGroupName, $vimName)
{
    global $service, $portGroupType;
    $type = 'portgroup';
    $records= $service->queryRecordsByType($type);
    foreach ($records as $record)
    {
        if($record->get_name()==$portGroupName && $record->get_vcName()==$vimName)
        {
            $portGroupType= $record->get_portgroupType();
            return $record->get_moref();
        }
    }
}

/**
 * Get dv Switch moref.
 * @param String  dvSwitch Name  $dvSwitchName
 * @param String  vim server Name  $vimName
 * @return dvSwitch moref.
 *
 */
function getDvSwitchMoRef($dvSwitchName, $vimName)
{
    global $service;
    $type = 'dvSwitch';
    $records= $service->queryRecordsByType($type);
    foreach ($records as $record)
    {
        if($record->get_name()==$dvSwitchName && $record->get_vcName()==$vimName)
        {
            return $record->get_moref();
        }
    }
}

function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating, updating, getting and deleting all type of network pool.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php networkpoolcrud.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                            for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--type <netpooltype>          [req] Network Pool type as 1. portgroup-backed 2. vlan-backed 3. isolation-backed.\n";
    echo "     -b|--vim <vimName>               [req] Name of an existing registered Vim server in the vCloud Director.\n";
    echo "     -c|--pool <poolName>             [req] Name of the network pool to be created.\n";
    echo "     -d|--name <portGroupName>        [req] Name of the port group.\n";
    echo "     -e|--dvswitch <dvSwitchName>     [req] Name of the dvSwitch.\n";
    echo "     -f|--certpath <certificatepath>  [opt] Local certificate's full path.\n";

    echo "\n";
    echo "  [Examples]\n";
    echo "     # php networkpoolcrud.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a portgroup-backed -b vim -c pool -d name -e dvswitch\n";
    echo "     # php networkpoolcrud.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a portgroup-backed -b vim -c pool -d name -e dvswitch -f certificatepath\n";
    echo "     # php networkpoolcrud.php -a portgroup-backed -b vim -c pool -d name -e dvswitch// using config.php to set login credentials\n\n";
}
?>
