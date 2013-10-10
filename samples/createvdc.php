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
 * Sample for creating a vDC in an organization.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "v:";

$shorts .= "a:";
$shorts .= "b::";
$shorts .= "c::";
$shorts .= "d:";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "org:",       //-a|--org       [required]
    "pvdc::",     //-b|--pvdc      [required for creating]
    "vdc::",      //-c|--vdc       [required for creating]
    "certpath:",  //-d|--certpath  [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$pvdcName = null;
$vdcName = null;
$certPath = null;
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

    case "d":
        $certPath = $opts['d'];
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
if ((!isset($server) || !isset($user) || !isset($pswd) || !isset($sdkversion)) || !isset($orgName) ||
    ((true !== $list) && (!isset($pvdcName) || !isset($vdcName))))
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
    $providerVdc = $service->createSDKObj($providerVdcRef);
    $pvdcStorageProfRefs = $providerVdc->getProviderVdcStorageProfileRefs();
    if (0 == count($pvdcStorageProfRefs))
    {
        exit("No provider vDC storage profile is found\n");
    }
    $ProviderVdcStorageProfileRef = $pvdcStorageProfRefs[0];

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

    $VdcStorageProfiles = new VMware_VCloud_API_VdcStorageProfileParamsType();
    $VdcStorageProfiles->setDefault(true);
    $VdcStorageProfiles->setEnabled(true);
    $VdcStorageProfiles->setProviderVdcStorageProfile($ProviderVdcStorageProfileRef);
    $VdcStorageProfiles->setLimit(100000);
    $VdcStorageProfiles->setUnits('MB');

    // create a VMware_VCloud_API_AdminVdcType data object
    $vdc = new VMware_VCloud_API_CreateVdcParamsType();
    $vdc->set_name($vdcName);
    $vdc->setDescription($description);
    $vdc->setAllocationModel($allocationModel);
    $vdc->setComputeCapacity($cc);
    $vdc->setVdcStorageProfile(array($VdcStorageProfiles));
    $vdc->setNicQuota(0);
    $vdc->setNetworkQuota(0);
    $vdc->setIsEnabled(true);
    $vdc->setIsThinProvision(true);
    $vdc->setUsesFastProvisioning(true);
    $vdc->setProviderVdcReference($providerVdcRef);

    // create a vDC in an organization
    echo "Creating org vDC...\n";
    $adminOrgVdc = $sdkAdminOrg->createAdminOrgVdc($vdc);
    $tasks = $adminOrgVdc->getTasks();
    if (!is_null($tasks))
    {
        $task = $tasks->getTask();
        if (sizeof($task) > 0)
        {
            $task = $service->waitForTask($task[0]);
            if ($task->get_status() != 'success')
                exit("Failed to create org vdc.\n");
        }
    }
    echo "Successfully created org vDC.\n";
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

// log out
$service->logout();
echo "logged out.\n";

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
    echo "     # php createvdc.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>               [req] Name of an existing organization in vCloud Director.\n";
    echo "     -b|--pvdc <pvdcName>             [opt] Name of an existing provider vDC in the organization. Required for creating.\n";
    echo "     -c|--vdc <vdcName>               [opt] Name of the vDC to be created in the organization. Required for creating.\n";
    echo "     -d|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List all vDC in the organization\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createvdc.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b=pvdc -c=vdc\n";
    echo "     # php createvdc.php -a org -b=pvdc -c=vdc // using config.php to set login credentials\n";
    echo "     # php createvdc.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b=pvdc -c=vdc -d certificatepath\n";
    echo "     # php createvdc.php -a org -l // list all vDC in the organization\n\n";
}
?>
