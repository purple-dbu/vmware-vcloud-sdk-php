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
 * Sample for deploying a vApp.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b:";
$shorts .= "c:";
$shorts .= "d";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "org:",       //-a|--org    [required]
    "vdc:",       //-b|--vdc    [required]
    "vapp:",      //-c|--vapp   [required]
    "on",         //-d|--on
);

$opts = getopt($shorts, $longs);
//var_dump($opts);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$vdcName = null;
$vAppName = null;
$powerOn = true;   // default to set to true
$deploymentLeaseSeconds = null;


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
        $vAppName = $opts['c'];
        break;
    case "vapp":
        $vAppName = $opts['vapp'];
        break;

    case "d":
        $powerOn = false;
        break;
    case "on":
        $powerOn = false;
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd)) ||
    !isset($orgName) || !isset($vdcName) || !isset($vAppName))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

// create an SDK Org object
$orgRefs = $service->getOrgRefs($orgName);
if (0 == count($orgRefs))
{
    exit("No organization with name $orgName is found\n");
}
$orgRef = $orgRefs[0];
$sdkOrg = $service->createSDKObj($orgRef);

// create an SDK vDC object
$vdcRefs = $sdkOrg->getVdcRefs($vdcName);
if (0 == count($vdcRefs))
{
    exit("No vDC with name $vdcName is found\n");
}
$vdcRef = $vdcRefs[0];
$sdkVdc = $service->createSDKObj($vdcRef);

// get a reference to a vApp in the vDC
$vAppRefs = $sdkVdc->getVAppRefs($vAppName);
if (!$vAppRefs)
{
    exit("No vApp with name $vAppName is found\n");
}
$vAppRef = $vAppRefs[0];
// create an SDK vApp object
$sdkVApp = $service->createSDKObj($vAppRef);

// create a VMware_VCloud_API_DeployVAppParamsType data object
$params = new VMware_VCloud_API_DeployVAppParamsType();
$params->set_powerOn($powerOn);
$params->set_deploymentLeaseSeconds($deploymentLeaseSeconds);

// deploy the vApp
$sdkVApp->deploy($params);


/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates deploying a vApp.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php deployvapp.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>        [req] Name of an existing organization in the vCloud Director.\n";
    echo "     -b|--vdc <vdcName>        [req] Name of an existing vDC in an organization.\n";
    echo "     -c|--vapp <vAppName>      [req] Name of an existing vApp (in power off state) to be deployed.\n";
    echo "     -d|--off                  [opt] Flag to indicate whether to power on or not after deployment.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php deployvapp.php -s 127.0.0.1 -u admin@Org -p password -a org -b vdc -c vapp\n";
    echo "     # php deployvapp.php -a org -b vdc -c vapp // using config.php to set login credentials\n\n";
}
?>
