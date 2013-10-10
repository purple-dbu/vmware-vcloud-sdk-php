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
 * A vSphere port group is available. If the port group uses VLAN, it can use only a single VLAN.
 * Port groups with VLAN trunking are not supported
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
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "vim:",       //-a|--vim       [required] name of the registered vim server
    "net:",       //-b|--net       [required] name of the external network to be created
    "name:",      //-c|--name      [required] name of port group
    "fence:",     //-d|--fence     [required] fence mode
    "certpath:",  //-e|--certpath  [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);


// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$extNetName = null;
$portGroupName = null;
$fenceMode = null;
$list = null;
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
        $vimName = $opts['a'];
        break;
    case "vim":
        $vimName = $opts['vim'];
        break;

    case "b":
        $extName = $opts['b'];
        break;
    case "net":
        $extName = $opts['net'];
        break;

    case "c":
        $portGroupName = $opts['c'];
        break;
    case "name":
        $portGroupName = $opts['name'];
        break;

    case "d":
        $fenceMode = $opts['d'];
        break;
    case "fence":
        $fenceMode = $opts['fence'];
        break;

    case "e":
        $certPath = $opts['e'];
        break;
    case "certpath":
        $certPath = $opts['certpath'];
        break;

    case "l":
        $list = true;
        break;
    case "list":
        $list = true;
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd) || !isset($sdkversion)) ||
    ((true !== $list) && (!isset($vimName) || !isset($extName) ||
    !isset($portGroupName) ||  !isset($fenceMode))))
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
    // login
    $service = VMware_VCloud_SDK_Service::getService();
    $service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig, $sdkversion);

    // create an SDK Extension object
    $sdkExt = $service->createSDKExtensionObj();

    if (true === $list)
    {
        $refs = $sdkExt->getVMWExternalNetworkRefs();
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

    // create a reference to the vim server
    $vimRefs = $sdkExt->getVimServerRefs($vimName);
    if (0 == count($vimRefs))
    {
        exit("No vim server with $vimName is found!\n");
    }
    $vimRef = $vimRefs[0];
    $vimServerRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());
    $portGroupMoRef= getPortGroupMoRef($portGroupName, $vimName);

    // create an external network
    echo "Creating an external network...\n";
    $extnet=createExternalNetworkParams($vimServerRef, $portGroupMoRef, $extName, $fenceMode, $portGroupType);
    $extnetwork=$sdkExt->createVMWExternalNetwork($extnet);
    $task=$extnetwork->getTasks()->getTask();
    if (sizeof($task) > 0){
    $service->waitForTask($task[0]);
    }
    echo "Created an external network : ".$extnetwork->get_name()."\n";

    // create a reference to the External Network
    $VMWExtNetRefs = $sdkExt->getVMWExternalNetworkRefs($extName);
    if (0 == count($VMWExtNetRefs))
    {
        exit("No VMWExternalNetwork with $extName is found!\n");
    }
    $VMWExtNetRef = $VMWExtNetRefs[0];
    $VMWExtNetObj = $service->createSDKObj($VMWExtNetRef->get_href());

    // update an external network
    echo "\nUpdating an external network...\n";
    $extnet = updateExternalNetworkParams($vimServerRef, $portGroupMoRef, $extName."_Updated", $fenceMode, $portGroupType);
    $extnetwork = $VMWExtNetObj->modify($extnet);
    $tasks = $extnetwork->getTasks();
    if (!is_null($tasks))
    {
        $task = $tasks->getTask();
        if (sizeof($task) > 0)
        {
            $service->waitForTask($task[0]);
        }
    }
    echo "Updated an external network : ".$extnetwork->get_name() . "_Updated\n";


    // get an external network
    echo "Get an external network. \n";
    $extName = $extName."_Updated";
    getVMWExternalNetwork($extName);

    // delete an external network
    echo "\nDeleting an external network [$extName]...\n";
    deleteVMWExternalNetwork($extName);
    echo "Deleted an external network: $extName.\n";
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

/**
 * Update an External Network
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param String  $portGroupMoRef
 * @param String  $extName
 * @param String  $fenceMode
 *
 * @return VMware_VCloud_API_Extension_VMWExternalNetworkType
 */
function createExternalNetworkParams($vimServerRef, $portGroupMoRef, $extName, $fenceMode, $portGroupType)
{
    $vmPGRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $vmPGRef->setVimServerRef($vimServerRef);
    $vmPGRef->setMoRef($portGroupMoRef);
    $vmPGRef->setVimObjectType($portGroupType);

    // set IP range
    $ipRange = new VMware_VCloud_API_IpRangeType();
    $ipRange->setStartAddress('192.168.111.1');
    $ipRange->setEndAddress('192.168.111.19');

    $ipRanges = new VMware_VCloud_API_IpRangesType();
    $ipRanges->setIpRange(array($ipRange));

    // set network configuration
    $ipScope = new VMware_VCloud_API_IpScopeType();
    $ipScope->setIsInherited(true);
    $ipScope->setGateway('192.168.111.254');
    $ipScope->setNetmask('255.255.255.0');
    $ipScope->setDns1('1.2.3.4');
    $ipScope->setDnsSuffix('sample.vmware.com');
    $ipScope->setIpRanges($ipRanges);

    $ipscopes=new VMware_VCloud_API_IpScopesType();
    $ipscopes->addIpScope($ipScope);

    $config = new VMware_VCloud_API_NetworkConfigurationType();
    $config->setIpScopes($ipscopes);
    $config->setFenceMode($fenceMode);

    // create a external network data object
    $extNet = new VMware_VCloud_API_Extension_VMWExternalNetworkType();
    $extNet->set_name($extName);
    $extNet->setDescription('External network description');
    $extNet->setVimPortgroupRef($vmPGRef);
    $extNet->setConfiguration($config);
    return $extNet;
}

/**
 * Update an External Network
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param String  $portGroupMoRef
 * @param String  $extName
 * @param String  $fenceMode
 *
 * @return VMware_VCloud_API_Extension_VMWExternalNetworkType
 */
function updateExternalNetworkParams($vimServerRef, $portGroupMoRef, $extName, $fenceMode, $portGroupType)
{
    $vmPGRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $vmPGRef->setVimServerRef($vimServerRef);
    $vmPGRef->setMoRef($portGroupMoRef);
    $vmPGRef->setVimObjectType($portGroupType);

    // set IP range
    $ipRange = new VMware_VCloud_API_IpRangeType();
    $ipRange->setStartAddress('192.168.111.1');
    $ipRange->setEndAddress('192.168.111.19');

    $ipRanges = new VMware_VCloud_API_IpRangesType();
    $ipRanges->setIpRange(array($ipRange));

    // set network configuration
    $ipScope = new VMware_VCloud_API_IpScopeType();
    $ipScope->setIsInherited(true);
    $ipScope->setGateway('192.168.111.254');
    $ipScope->setNetmask('255.255.255.0');
    $ipScope->setDns1('1.2.3.4');
    $ipScope->setDnsSuffix('sample.vmware.com');
    $ipScope->setIpRanges($ipRanges);

    $ipscopes=new VMware_VCloud_API_IpScopesType();
    $ipscopes->addIpScope($ipScope);

    $config = new VMware_VCloud_API_NetworkConfigurationType();
    $config->setIpScopes($ipscopes);
    $config->setFenceMode($fenceMode);

    // create a external network data object
    $extNet = new VMware_VCloud_API_Extension_VMWExternalNetworkType();
    $extNet->set_name($extName);
    $extNet->setDescription('External network description');
    $extNet->setVimPortgroupRef($vmPGRef);
    $extNet->setConfiguration($config);
    return $extNet;
}

/**
 * @param String  External Network name   $name
 * @return array VMware_VCloud_API_ReferenceType object array
 */
function getVMWExternalNetwork($name)
{
    try
    {
        global $sdkExt;
        $refs = $sdkExt->getVMWExternalNetworkRefs($name);
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
 * @param String  VMWExternalNetwork Name  $name
 *
 */
function deleteVMWExternalNetwork($name)
{
    global $sdkExt,$service;
    $refs = $sdkExt->getVMWExternalNetworkRefs($name);
    $ref = $refs[0];
    $vmwexternalnetworkob = $service->createSDKObj($ref->get_href());
    $vmwexternalnetworkob->delete();
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
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating, updating, getting and deleting an external network.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php externalnetworkcrud.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>               [req] Name of a registered Vim server in the vCloud Director.\n";
    echo "     -b|--net <extName>               [req] Name of the external network to be created. Required for creating.\n";
    echo "     -c|--name <portGroupName>        [req] port group name. Required for creating.\n";
    echo "     -d|--fence <fencemode>           [req] Fence mode. Required for creating.\n";
    echo "     -e|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List all external networks in vCloud Director.";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php externalnetworkcrud.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b net -c name -d fence\n";
    echo "     # php externalnetworkcrud.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b net -c name -d fence -e certificatepath\n";
    echo "     # php externalnetworkcrud.php -a vim -b net -c name -d fence // using config.php to set login credentials\n";
    echo "     # php externalnetworkcrud.php -l\n\n";
}
?>
