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
 * Sample for creating a vDC in an organization.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b::";
$shorts .= "c::";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "org:",       //-a|--org    [required]
    "pvdc::",     //-b|--pvdc   [required for creating]
    "vdc::",      //-c|--vdc    [required for creating]
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$pvdcName = null;
$vdcName = null;
// The following are hard-coded for simplifying command line options.
$description = "vDC description";
$allocationModel = 'AllocationPool';
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
        $orgName = $opts['a'];
        break;
    case "org":
        $orgName = $opts['org'];
        break;
        
    case "b":
        $pvdcName = $opts['b'];
        break;
    case "pvdc":
        $pvdcName = $opts['pvdc'];
        break;

    case "c":
        $vdcName = $opts['c'];
        break;
    case "vdc":
        $vdcName = $opts['vdc'];
        break;

    case "l":
        $list = true;
        break;
    case "list":
        $list = true;
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd)) || !isset($orgName) ||
    ((true !== $list) && (!isset($pvdcName) || !isset($vdcName))))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

if (true === $list)
{
    $orgRefs = $service->getOrgRefs($orgName);
    if (0 == count($orgRefs))
    {
        exit("No organization with $orgName is found\n");
    }
    $org = $service->get($orgRefs[0]->get_href());
    $links = VMware_VCloud_SDK_Helper::getContainedLinks('vdc', 'down', $org,
                                                         $method='getLink');
    if (0 < count($links))
    {
        foreach ($links as $ref)
        {
            echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
                 " name=" . $ref->get_name() . "\n";
        }
    }
    exit(0);
}

// create an SDK Admin object
$sdkAdmin = $service->createSDKAdminObj();

// get references to administrative organization entities
$adminOrgRefs = $sdkAdmin->getAdminOrgRefs($orgName);
if (0 == count($adminOrgRefs))
{
    exit("No organization with $orgName is found\n");
}
$adminOrgRef = $adminOrgRefs[0];
// create SDK AdminOrg object
$sdkAdminOrg = $service->createSDKObj($adminOrgRef);

// get references to the provide vDC
$providerVdcRefs = $sdkAdmin->getProviderVdcRefs($pvdcName);
if (0 == count($providerVdcRefs))
{
    exit("No provider vDC with $pvdcName is found\n");
}
$providerVdcRef = $providerVdcRefs[0];

// create cpu
$cpu = new VMware_VCloud_API_CapacityWithUsageType();
$cpu->setUnits('MHz');
$cpu->setAllocated(200);
$cpu->setLimit(200);

// create memory
$mem = new VMware_VCloud_API_CapacityWithUsageType();
$mem->setUnits('MB');
$mem->setAllocated(200);
$mem->setLimit(200);

// configure compute capacity
$cc = new VMware_VCloud_API_ComputeCapacityType();
$cc->setCpu($cpu);
$cc->setMemory($mem);

// configure storage capacity
$sc = new VMware_VCloud_API_CapacityWithUsageType();
$sc->setUnits('MB');
$sc->setAllocated(218);
$sc->setLimit(500);

// create a VMware_VCloud_API_AdminVdcType data object
$vdc = new VMware_VCloud_API_AdminVdcType();
$vdc->set_name($vdcName);
$vdc->setDescription($description);
$vdc->setAllocationModel($allocationModel);
$vdc->setComputeCapacity($cc);
$vdc->setStorageCapacity($sc);
$vdc->setNicQuota(0);
$vdc->setNetworkQuota(0);
$vdc->setIsEnabled(true);
$vdc->setIsThinProvision(true);
$vdc->setProviderVdcReference($providerVdcRef);

// create a vDC in an organization
$sdkAdminOrg->createAdminVdc($vdc);

/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating a vDC in an organization.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createvdc.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>        [req] Name of an existing organization in vCloud Director.\n";
    echo "     -b|--pvdc <pvdcName>      [opt] Name of an existing provider vDC in the organization. Required for creating.\n";
    echo "     -c|--vdc <vdcName>        [opt] Name of the vDC to be created in the organization. Required for creating.\n";
    echo "     -l|--list                 [opt] List all vDC in the organization\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createvdc.php -s 127.0.0.1 -u admin@Org -p password -a org -b=pvdc -c=vdc\n";
    echo "     # php createvdc.php -a org -b=pvdc -c=vdc // using config.php to set login credentials\n";
    echo "     # php createvdc.php -a org -l // list all vDC in the organization\n\n";
}
?>
