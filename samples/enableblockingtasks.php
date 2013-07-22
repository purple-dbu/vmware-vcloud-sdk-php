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
 * Enable or disable the blocking tasks.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "a::";
$shorts .= "b";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required] vCloud Director server IP/hostname
    "user:",      //-u|--user   [required] vCloud Director login username
    "pswd:",      //-p|--pswd   [required] vCloud Director login password
    "ops::",      //-a|--ops
    "dis",        //-b|--dis
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

// array of task operations to be enabled.
//$ops = array(VMware_VCloud_SDK_Extension_TaskOps::IMPORT_SINGLETON_TEMPLATE,
//             VMware_VCloud_SDK_Extension_TaskOps::VDC_UPDATE_TEMPLATE);
$ops = null;
$dis = null;
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
        $ops = explode(',', $opts['a']);
        break;
    case "ops":
        $ops = explode(',', $opts['ops']);
        break;

    case "b":
        $dis = true;
        break;
    case "dis":
        $dis = true;
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
    (!isset($ops) && !isset($dis) && !isset($list)))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

$sdkExt = $service->createSDKExtensionObj();

if (true === $list)
{
    $ebt = $sdkExt->getEnabledBlockingTaskOperations();
    echo $ebt->export() . "\n";
    exit(0);
}

$ops = (true === $dis)? null : $ops;
$tl = new VMware_VCloud_API_TaskOperationListType();
$tl->setOperation($ops);
$ntl = $sdkExt->updateEnabledBlockingTaskOperations($tl);

echo $ntl->export() . "\n";

/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates enabling or disabling blocking tasks.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php enableblockingtasks.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--ops <ops>            [opt] Enable blocking tasks for specified blocking task operations.\n";
    echo "                                     Blocking task operations are given in comma seperated string format.\n";
    echo "     -b|--dis                  [opt] Disable blocking task operations. When specified, -a will be ignored.\n";
    echo "     -l|--list                 [opt] List enabled blocking task operations.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php enableblockingtasks.php -s 127.0.0.1 -u admin@Org -p password // enable the default blocking task operations set by this sample\n";
    echo "     # php enableblockingtasks.php  // using config.php to set login credentials\n";
    echo "     # php enableblockingtasks.php -a=importSingletonTemplate  // enable one blocking task operation\n";
    echo "     # php enableblockingtasks.php -a=importSingletonTemplate,vdcUpdateTemplate // enable multiple blocking task operations\n";
    echo "     # php enableblockingtasks.php -b // disable all enabled blocking task operations\n";
    echo "     # php enableblockingtasks.php -l // list enabled blocking task operations\n\n";
}
?>
