<?php
/**
 * VMware vCloud SDK for PHP
 *
 * PHP version 5
 * *******************************************************
 * Copyright VMware, Inc. 2010-2013.  All Rights Reserved.
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
 * Sample for Creating, Getting, Updating and Deleting an edgeGateway.
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
    "org:",       //-a|--org       [required]
    "vdc:",       //-b|--vdc       [required]
    "extnet:",    //-c|--extnet    [required]
    "name:",      //-d|--name      [required]
    "certpath:",  //-e|--certpath  [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName= null;
$vdcName= null;
$extnetName= null;
$edgeGatewayName= null;
$certPath = null;
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

    case "v":
        $sdkversion = $opts['v'];
        break;
    case "sdkver":
        $sdkversion = $opts['sdkver'];
        break;

    case "a":
        $orgName = $opts['a'];
        break;
    case "org":
        $orgName = $opts['org'];
        break;

    case "b":
        $vdcName = $opts['b'];
        break;
    case "vdc":
        $vdcName = $opts['vdc'];
        break;

    case "c":
        $extnetName = $opts['c'];
        break;
    case "extnet":
        $extnetName = $opts['extnet'];
        break;

    case "d":
        $edgeGatewayName = $opts['d'];
        break;
    case "name":
        $edgeGatewayName = $opts['name'];
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
    (true !== $list && (!isset($orgName) || !isset($vdcName) || !isset($extnetName) || !isset($edgeGatewayName))))
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

    // create sdk admin object
    $sdkAdminObj = $service->createSDKAdminObj();

    // create admin org object
    $adminOrgRefs = $sdkAdminObj->getAdminOrgRefs($orgName);
    if(empty($adminOrgRefs))
    {
        exit("No admin org with name $orgName is found.");
    }
    $adminOrgRef = $adminOrgRefs[0];
    $adminOrgObj = $service->createSDKObj($adminOrgRef->get_href());

    // create admin vdc object
    $adminVdcRefs = $adminOrgObj->getAdminVdcRefs($vdcName);
    if(empty($adminVdcRefs))
    {
        exit("No admin vdc with name $vdcName is found.");
    }
    $adminVdcRef=$adminVdcRefs[0];
    $adminVdcObj=$service->createSDKObj($adminVdcRef->get_href());


    if (true === $list)
    {
        $refs = $adminVdcObj->getEdgeGatewayRefs();
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


    // create a reference to the external network
    $extensionobj=$service->createSDKExtensionObj();
    $extNetRefs=$extensionobj->getVMWExternalNetworks($extnetName);
    if (0 == count($extNetRefs))
    {
        exit("No external network with $extnetName is found!\n");
    }
    $extNetRef=$extNetRefs[0];
    $extnetObj=$service->createSDKObj($extNetRef->get_href());
    $configuration = $extnetObj->getVMWExternalNetwork()->getConfiguration();
    if(empty($configuration))
    {
        exit("$extnetName does n't have any configuration.");
    }
    $ipScopes = $configuration->getIpScopes();
    if(empty($ipScopes))
    {
        exit("$extnetName does n't have any IP Scopes.");
    }
    $ipScope = $ipScopes->getIpScope();
    $gateway = $ipScope[0]->getGateway();
    $netmask = $ipScope[0]->getNetmask();
    $ipRanges = $ipScope[0]->getIpRanges();
    if(empty($ipRanges))
    {
        exit("$extnetName does n't have any IP Ranges.");
    }
    $ipRange = $ipRanges->getIpRange();
    $startAddress = $ipRange[0]->getStartAddress();
    $endAddress = $ipRange[0]->getEndAddress();
    $extNetRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj($extNetRef->get_href());

    // create an edge Gateway
    echo "Creating an edgeGateway... \n";
    $edgeGatewayparams=createEdgeGatewayParams($edgeGatewayName, $extNetRef);
    $edgeGateway=$adminVdcObj->createEdgeGateways($edgeGatewayparams);
    $tasks = $edgeGateway->getTasks();
    if (!is_null($tasks))
    {
        $task = $tasks->getTask();
        if (sizeof($task) > 0)
        {
            print $task[0]->export();
            $task = $service->waitForTask($task[0]);
            print $task->export();
            if ($task->get_status() != 'success'){
                exit('Failed to create an edge Gateway: $edgeGatewayName.');
            }
        }
    }
    echo "\nCreated an edgeGateway with name: ".$edgeGateway->get_name()."\n";

    // create a reference to an edge Gateway
    $edgeGatewayRefs = $adminVdcObj->getEdgeGatewayRefs($edgeGatewayName);
    if (0 == count($edgeGatewayRefs))
    {
        exit("No an edgeGateway with Name $edgeGatewayName is found!\n");
    }
    $edgeGatewayRef = $edgeGatewayRefs[0];
    $edgeGatewayObj = $service->createSDKObj($edgeGatewayRef->get_href());
    $edgeGateway = $edgeGatewayObj->getEdgeGateway();
    $edgeGatewayConfig = $edgeGateway->getConfiguration();
    // update an edge Gateway
    echo "Updating an edge Gateway... \n";
    $edgeGatewayParams=updateEdgeGatewayParams($edgeGatewayName."_Updated", $edgeGatewayConfig);
    $task = $edgeGatewayObj->modify($edgeGatewayParams);
    print $task->export();
    $task = $service->waitForTask($task);
    print $task->export();
    if ($task->get_status() != 'success'){
        exit('Failed to update an edge Gateway.');
    }
    echo "\nUpdated an edge Gateway : " . $edgeGatewayName."_Updated" . "\n";

    // get an edge Gateway
    echo "\nGet an edge Gateway: \n";
    $name = $edgeGatewayName."_Updated";
    getEdgeGateway($name);

    // delete an edge Gateway
    echo "\nDeleting an edge Gateway...\n";
    deleteEdgeGateway($name);
    echo "Deleted an edge Gateway: $name\n";
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

/**
* create an edge Gateway
* @param String  $edgeGatewayName
* @param VMware_VCloud_API_ReferenceType  $extNetRef
*
* @return array VMware_VCloud_API_ReferenceType object array
*/
function createEdgeGatewayParams($edgeGatewayName, $extNetRef)
{
    global $gateway, $netmask, $startAddress, $endAddress, $adminVdcObj;

    $params=new VMware_VCloud_API_GatewayType();
    $params->set_Name($edgeGatewayName);

    $gatcon=new VMware_VCloud_API_GatewayConfigurationType();
    $gatcon->setGatewayBackingConfig("compact");
    $gatinter=new VMware_VCloud_API_GatewayInterfaceType();
    $gatinter->setDisplayName("gateway interface");

    $gatinter->setNetwork($extNetRef);
    $gatinter->setInterfaceType("uplink");
    $subnetparttype=new VMware_VCloud_API_SubnetParticipationType();
    $subnetparttype->setGateway($gateway);
    $subnetparttype->setNetmask($netmask);


    $ipRanges = new VMware_VCloud_API_IpRangesType();
    $ipRange = new VMware_VCloud_API_IpRangeType();
    $ipRange->setStartAddress($startAddress);
    $ipRange->setEndAddress($endAddress);
    $ipRanges->addIpRange($ipRange);
    $subnetparttype->setIpRanges($ipRanges);
    $gatinter->addSubnetParticipation($subnetparttype);
    $gatinter->setUseForDefaultRoute(true);

    $gatinfaces=new VMware_VCloud_API_GatewayInterfacesType();
    $gatinfaces->addGatewayInterface($gatinter);
    $gatcon->setGatewayInterfaces($gatinfaces);
    $gatcon->setHaEnabled(true);
    $gatcon->setUseDefaultRouteForDnsRelay(true);

    // Firewall Service
    $gatewayFeatures = new VMware_VCloud_API_GatewayFeaturesType();
    $firewallService = new VMware_VCloud_API_FirewallServiceType();
    $firewallService->setIsEnabled(true);
    $firewallService->setDefaultAction("drop");
    $firewallService->setLogDefaultAction(false);
    $gatewayFeatures->addNetworkService($firewallService);

    // NAT Service
    $natService = new VMware_VCloud_API_NatServiceType();
    $substr = substr(strrchr($endAddress, "."), 1);
    $intval = intval($substr)+3;
    $strpos = strpos($endAddress, $substr);
    $substring = substr($endAddress,0 , $strpos);
    $externalIp = $substring . $intval;
    $natService->setExternalIp($externalIp);
    $natService->setIsEnabled(false);
    $gatewayFeatures->addNetworkService($natService);

    // DHCP Service
    $dhcpService = new VMware_VCloud_API_DhcpServiceType();
    $dhcpService->setIpRange($ipRange);
    $dhcpService->setIsEnabled(true);
    $dhcpService->setPrimaryNameServer("r2");
    $dhcpService->setSubMask($netmask);
    $dhcpService->setDefaultLeaseTime(3600);
    $dhcpService->setMaxLeaseTime(7200);
    $gatewayFeatures->addNetworkService($dhcpService);

    // LoadBalancer Service
    $loadBalancer = new VMware_VCloud_API_LoadBalancerServiceType();

    $pool = new VMware_VCloud_API_LoadBalancerPoolType();
    $pool->setDescription("Pool Desc");
    $pool->setName("PoolName");
    $pool->setOperational(true);

    $lBPoolHealthCheck = new VMware_VCloud_API_LBPoolHealthCheckType();
    $lBPoolHealthCheck->setHealthThreshold("2");
    $lBPoolHealthCheck->setUnhealthThreshold("3");
    $lBPoolHealthCheck->setInterval("5");
    $lBPoolHealthCheck->setMode("HTTP");
    $lBPoolHealthCheck->setTimeout("15");

    $lBPoolMember = new VMware_VCloud_API_LBPoolMemberType();
    $substr = substr(strrchr($startAddress, "."), 1);
    $intval = intval($substr)+3;
    $strpos = strpos($startAddress, $substr);
    $substring = substr($startAddress,0 , $strpos);
    $lbPoolIp = $substring . $intval;
    $lBPoolMember->setIpAddress($lbPoolIp);
    $lBPoolMember->setWeight("1");

    $lBPoolServicePort = new VMware_VCloud_API_LBPoolServicePortType();
    $lBPoolServicePort->setIsEnabled(true);
    $lBPoolServicePort->setAlgorithm("ROUND_ROBIN");
    $lBPoolServicePort->setHealthCheckPort("80");
    $lBPoolServicePort->addHealthCheck($lBPoolHealthCheck);
    $lBPoolServicePort->setProtocol("HTTP");
    $lBPoolServicePort->setPort("80");

    $pool->addServicePort($lBPoolServicePort);

    $pool->addMember($lBPoolMember);
    $loadBalancer->addPool($pool);

    $loadBalancerVirtualServer = new VMware_VCloud_API_LoadBalancerVirtualServerType();
    $loadBalancerVirtualServer->setDescription("desc");
    $loadBalancerVirtualServer->setIsEnabled(true);
    $substr = substr(strrchr($startAddress, "."), 1);
    $intval = intval($substr)+4;
    $strpos = strpos($startAddress, $substr);
    $substring = substr($startAddress,0 , $strpos);
    $lbvirtualServerIp = $substring . $intval;
    $loadBalancerVirtualServer->setIpAddress($lbvirtualServerIp);
    $loadBalancerVirtualServer->setName("VirtualServerName");
    $loadBalancerVirtualServer->setPool("PoolName");
    $loadBalancerVirtualServer->setLogging(true);
    $loadBalancerVirtualServer->setInterface($extNetRef);

    $lBVirtualServerServiceProfile = new VMware_VCloud_API_LBVirtualServerServiceProfileType();
    $lBVirtualServerServiceProfile->setProtocol("HTTP");
    $lBVirtualServerServiceProfile->setPort("80");
    $lBVirtualServerServiceProfile->setIsEnabled(true);

    $lBPersistence = new VMware_VCloud_API_LBPersistenceType();
    $lBPersistence->setCookieMode("INSERT");
    $lBPersistence->setCookieName("CookieName");
    $lBPersistence->setMethod("COOKIE");
    $lBVirtualServerServiceProfile->setPersistence($lBPersistence);
    $loadBalancerVirtualServer->addServiceProfile($lBVirtualServerServiceProfile);

    $loadBalancer->addVirtualServer($loadBalancerVirtualServer);
    $loadBalancer->setIsEnabled(true);
    $gatewayFeatures->addNetworkService($loadBalancer);

    // Static Routing Service
    $staticRouting = new VMware_VCloud_API_StaticRoutingServiceType();
    $staticRouting->setIsEnabled(true);
    $staticRoute = new VMware_VCloud_API_StaticRouteType();
    $substr = substr(strrchr($endAddress, "."), 1);
    $intval = intval($substr)+2;
    $strpos = strpos($endAddress, $substr);
    $substring = substr($endAddress, 0, $strpos);
    $nextHopeIp = $substring . $intval;
    $staticRoute->setName("RouteName");
    $subStringArray = explode('.', $endAddress);
    $networkIp = $subStringArray[0] . '.' . $subStringArray[1] . ".2.0/24";
    $staticRoute->setNetwork($networkIp);
    $staticRoute->setNextHopIp($nextHopeIp);
    $staticRoute->setGatewayInterface($extNetRef);
    $staticRoute->setInterface("External");
    $staticRouting->addStaticRoute($staticRoute);
    $gatewayFeatures->addNetworkService($staticRouting);

    $gatcon->setEdgeGatewayServiceConfiguration($gatewayFeatures);
    $params->setConfiguration($gatcon);
    return $params;
}

/**
 * Update an edge Gateway
 * @param String  $edgeGatewayName
 * @param VMware_VCloud_API_GatewayConfigurationType  $edgeGatewayConfig
 *
 * @return VMware_VCloud_API_TaskType
 */
function updateEdgeGatewayParams($edgeGatewayName, $edgeGatewayConfig)
{

    $params=new VMware_VCloud_API_GatewayType();
    $params->set_Name($edgeGatewayName);
    $params->setDescription("updated desc");
    $params->setConfiguration($edgeGatewayConfig);
    return $params;
}

/**
 * @param String an edgeGateway Name   $name
 * @return array VMware_VCloud_API_ReferenceType object array
 */
function getEdgeGateway($name)
{
    try
    {
        global $adminVdcObj;
        $refs = $adminVdcObj->getEdgeGatewayRefs($name);
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
 * @param String an edgeGateway Name  $name
 *
 */
function deleteEdgeGateway($name)
{
    global $adminVdcObj,$service;
    $refs = $adminVdcObj->getEdgeGatewayRefs($name);
    $ref = $refs[0];
    $edgeGatewayObj = $service->createSDKObj($ref->get_href());
    $edgeGatewayObj->delete();
}

function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating, getting, updating and deleting an edgeGateway.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php edgegatewaycrud.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                           for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>               [req] Name of existing org.\n";
    echo "     -b|--vdc <vdcName>               [req] Name of existing vdc.\n";
    echo "     -c|--extnet <extnetName>         [req] Name of existing external network.\n";
    echo "     -d|--name <edgeGatewayName>      [req] Name of edgeGateway to be created.\n";
    echo "     -e|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List all edgeGateway.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php edgegatewaycrud.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b vdc -c extnet -d name \n";
    echo "     # php edgegatewaycrud.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b vdc -c extnet -d name -e certificatepath\n";
    echo "     # php edgegatewaycrud.php -a org -b vdc -c extnet -d name // using config.php to set login credentials\n";
    echo "     # php edgegatewaycrud.php -l// list all edgeGateway\n\n";
}
?>
