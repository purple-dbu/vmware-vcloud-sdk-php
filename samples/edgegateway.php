<?php
/**
 * VMware vCloud SDK for PHP
 *
 * PHP version 5
 * *******************************************************
 * Copyright VMware, Inc. 2010-2012.  All Rights Reserved.
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
 * Sample for Creating, Getting, Updating and Deleting an edgeGateway.
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
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "org:",       //-a|--org    [required]
    "vdc:",       //-b|--vdc    [required]
    "extnet:",    //-c|--extnet [required]
    "name:",      //-d|--name   [required]
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName= null;
$vdcName= null;
$extnetName= null;
$edgeGatewayName= null;

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

    case "l":
        $list = true;
        break;
    case "list":
        $list = true;
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd)) ||
    (true !== $list && (!isset($orgName) || !isset($vdcName) || !isset($extnetName) || !isset($edgeGatewayName))))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

// vCloud login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

// create sdk admin object
$sdkAdminObj=$service->createSDKAdminObj();

// create admin org object
$adminOrgRefs=$sdkAdminObj->getAdminOrgRefs($orgName);
$adminOrgRef=$adminOrgRefs[1];
$adminOrgObj=$service->createSDKObj($adminOrgRef->get_href());

// create admin vdc object
$adminVdcRefs=$adminOrgObj->getAdminVdcRefs($vdcName);
$adminVdcRef=$adminVdcRefs[2];
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
$extNetRef=$extNetRefs[1];
$extNetRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj($extNetRef->get_href());


    /**
     * create an edge Gateway
     * @param String  $edgeGatewayName
     * @param VMware_VCloud_API_ReferenceType  $extNetRef
     *
     * @return array VMware_VCloud_API_ReferenceType object array
     */
    function createEdgeGatewayParams($edgeGatewayName, $extNetRef)
    {
        $params=new VMware_VCloud_API_GatewayType();
        $params->set_Name($edgeGatewayName);

        $gatcon=new VMware_VCloud_API_GatewayConfigurationType();
        $gatcon->setGatewayBackingConfig("compact");
        $gatinter=new VMware_VCloud_API_GatewayInterfaceType();
        $gatinter->setDisplayName("gateway interface");

        $gatinter->setNetwork($extNetRef);
        $gatinter->setInterfaceType("uplink");
        $subnetparttype=new VMware_VCloud_API_SubnetParticipationType();
        $subnetparttype->setGateway("10.147.74.253");
        $subnetparttype->setNetmask("255.255.255.0");


        $ipRanges = new VMware_VCloud_API_IpRangesType();
        $ipRange = new VMware_VCloud_API_IpRangeType();
        $ipRange->setStartAddress("10.147.74.211");
        $ipRange->setEndAddress("10.147.74.240");
        $ipRanges->addIpRange($ipRange);
        $subnetparttype->setIpRanges($ipRanges);
        $gatinter->addSubnetParticipation($subnetparttype);
        $gatinter->setUseForDefaultRoute(true);

        $gatinfaces=new VMware_VCloud_API_GatewayInterfacesType();
        $gatinfaces->addGatewayInterface($gatinter);
        $gatcon->setGatewayInterfaces($gatinfaces);
        $gatcon->setHaEnabled(true);
        $gatcon->setUseDefaultRouteForDnsRelay(true);

        $gatewayFeatures = new VMware_VCloud_API_GatewayFeaturesType();
        $firewallService = new VMware_VCloud_API_FirewallServiceType();
        $firewallService->setIsEnabled(true);
        $firewallService->setDefaultAction("drop");
        $firewallService->setLogDefaultAction(false);

        $natService = new VMware_VCloud_API_NatServiceType();
        $natService->setExternalIp("10.147.74.218");
        $natService->setIsEnabled(false);
        $gatewayFeatures->addNetworkService($natService);

        $dhcpService = new VMware_VCloud_API_DhcpServiceType();
        $dhcpService->setIpRange($ipRange);
        $dhcpService->setIsEnabled(true);
        $dhcpService->setPrimaryNameServer("r2");
        $dhcpService->setSubMask("255.255.255.0");
        $dhcpService->setDefaultLeaseTime(3600);
        $dhcpService->setMaxLeaseTime(7200);
        $gatewayFeatures->addNetworkService($dhcpService);

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
        $lBPoolMember->setIpAddress("10.147.74.220");
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
        $loadBalancerVirtualServer->setIpAddress("10.147.74.222");
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

        $staticRouting = new VMware_VCloud_API_StaticRoutingServiceType();
        $staticRouting->setIsEnabled(true);
        $staticRoute = new VMware_VCloud_API_StaticRouteType();
        $staticRoute->setName("RouteName");
        $staticRoute->setNetwork("10.147.2.0/24");
        $staticRoute->setNextHopIp("10.147.74.235");
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
     * @param VMware_VCloud_API_ReferenceType  $extNetRef
     *
     * @return VMware_VCloud_API_TaskType
     */
    function updateEdgeGatewayParams($edgeGatewayName, $extNetRef)
    {
        $params=new VMware_VCloud_API_GatewayType();
        $params->set_Name($edgeGatewayName);

        $gatcon=new VMware_VCloud_API_GatewayConfigurationType();
        $gatcon->setGatewayBackingConfig("compact");
        $gatinter=new VMware_VCloud_API_GatewayInterfaceType();
        $gatinter->setDisplayName("gateway interface");

        $gatinter->setNetwork($extNetRef);
        $gatinter->setInterfaceType("uplink");
        $subnetparttype=new VMware_VCloud_API_SubnetParticipationType();
        $subnetparttype->setGateway("10.147.74.253");
        $subnetparttype->setNetmask("255.255.255.0");


        $ipRanges = new VMware_VCloud_API_IpRangesType();
        $ipRange = new VMware_VCloud_API_IpRangeType();
        $ipRange->setStartAddress("10.147.74.211");
        $ipRange->setEndAddress("10.147.74.240");
        $ipRanges->addIpRange($ipRange);
        $subnetparttype->setIpRanges($ipRanges);
        $gatinter->addSubnetParticipation($subnetparttype);
        $gatinter->setUseForDefaultRoute(true);

        $gatinfaces=new VMware_VCloud_API_GatewayInterfacesType();
        $gatinfaces->addGatewayInterface($gatinter);
        $gatcon->setGatewayInterfaces($gatinfaces);
        $gatcon->setHaEnabled(true);
        $gatcon->setUseDefaultRouteForDnsRelay(true);

        $gatewayFeatures = new VMware_VCloud_API_GatewayFeaturesType();
        $firewallService = new VMware_VCloud_API_FirewallServiceType();
        $firewallService->setIsEnabled(true);
        $firewallService->setDefaultAction("drop");
        $firewallService->setLogDefaultAction(false);

        $natService = new VMware_VCloud_API_NatServiceType();
        $natService->setExternalIp("10.147.74.218");
        $natService->setIsEnabled(false);
        $gatewayFeatures->addNetworkService($natService);

        $dhcpService = new VMware_VCloud_API_DhcpServiceType();
        $dhcpService->setIpRange($ipRange);
        $dhcpService->setIsEnabled(true);
        $dhcpService->setPrimaryNameServer("r2");
        $dhcpService->setSubMask("255.255.255.0");
        $dhcpService->setDefaultLeaseTime(3600);
        $dhcpService->setMaxLeaseTime(7200);
        $gatewayFeatures->addNetworkService($dhcpService);

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
        $lBPoolMember->setIpAddress("10.147.74.220");
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
        $loadBalancerVirtualServer->setIpAddress("10.147.74.222");
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

        $staticRouting = new VMware_VCloud_API_StaticRoutingServiceType();
        $staticRouting->setIsEnabled(true);
        $staticRoute = new VMware_VCloud_API_StaticRouteType();
        $staticRoute->setName("RouteName");
        $staticRoute->setNetwork("10.147.2.0/24");
        $staticRoute->setNextHopIp("10.147.74.235");
        $staticRoute->setGatewayInterface($extNetRef);
        $staticRoute->setInterface("External");
        $staticRouting->addStaticRoute($staticRoute);
        $gatewayFeatures->addNetworkService($staticRouting);

        $gatcon->setEdgeGatewayServiceConfiguration($gatewayFeatures);
        $params->setConfiguration($gatcon);
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


    // create an edge Gateway
    echo "Create an edgeGateway. \n";
    $edgeGatewayparams=createEdgeGatewayParams($edgeGatewayName, $extNetRef);
    $edgeGateway=$adminVdcObj->createEdgeGateways($edgeGatewayparams);
    echo "Created an edgeGateway : ".$edgeGateway->get_name()."\n";

    // create a reference to an edge Gateway
    $edgeGatewayRefs = $adminVdcObj->getEdgeGatewayRefs($edgeGatewayName);
    if (0 == count($edgeGatewayRefs))
    {
        exit("No an edgeGateway with Name $edgeGatewayName is found!\n");
    }
    $edgeGatewayRef = $edgeGatewayRefs[0];
    $edgeGatewayObj = $service->createSDKObj($edgeGatewayRef->get_href());

    // update an edge Gateway
    echo "Update an edge Gateway. \n";
    $edgeGatewayParams=updateEdgeGatewayParams($edgeGatewayName."_Updated", $extNetRef);
    $edgeGateway=$edgeGatewayObj->modify($edgeGatewayParams);
    echo "Updated an edge Gateway : ".$edgeGateway->get_name()."\n";

    // get an edge Gateway
    echo "Get an edge Gateway. \n";
    $name = $edgeGatewayName."_Updated";
    getEdgeGateway($name);

    // delete an edge Gateway
    echo "Delete an edge Gateway.\n";
    deleteEdgeGateway($name);


function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating, getting, updating and deleting an edgeGateway.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php edgegateway.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>   [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>        [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>        [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>          [req] Name of existing org.\n";
    echo "     -b|--vdc <vdcName>          [req] Name of existing vdc.\n";
    echo "     -c|--extnet <extnetName>    [req] Name of existing external network.\n";
    echo "     -d|--name <edgeGatewayName> [req] Name of edgeGateway to be created.\n";
    echo "     -l|--list                   [opt] List all edgeGateway.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php edgegateway.php -s 127.0.0.1 -u admin@Org -p password -a org -b vdc -c extnet -d name \n";
    echo "     # php edgegateway.php -a org -b vdc -c extnet -d name // using config.php to set login credentials\n";
    echo "     # php edgegateway.php -l// list all edgeGateway\n\n";
}
?>
