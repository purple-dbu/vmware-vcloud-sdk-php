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
 * Sample for uploading an OVF as a vApp template to vDC.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b:";
$shorts .= "c::";
$shorts .= "d::";
$shorts .= "e::";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "org:",       //-a|--org    [required]
    "vdc:",       //-b|--vdc    [required]
    "temp::",     //-c|--temp   [required for uploading OVF]
    "disks::",    //-d|--disks  [required for uploading OVF]
    "ovf::",      //-e|--ovf    [required for uploading OVF]
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$vdcName = null;
$vAppTempName = null;
$disks = null;
$ovfDescriptorPath = null;
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
        $vdcName = $opts['b'];
        break;
    case "vdc":
        $vdcName = $opts['vdc'];
        break;

    case "c":
        $vAppTempName = $opts['c'];
        break;
    case "temp":
        $vAppTempName = $opts['temp'];
        break;

    case "d":
        $disks = explode(',', $opts['d']);
        break;
    case "disks":
        $disks = explode(',', $opts['disks']);
        break;

    case "e":
        $ovfDescriptorPath = $opts['e'];
        break;
    case "ovf":
        $ovfDescriptorPath = $opts['ovf'];
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
    !isset($orgName) || !isset($vdcName) ||
    ((true !== $list) && (!isset($vAppTempName) || !isset($disks) ||
      !isset($ovfDescriptorPath))))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

$orgRefs = $service->getOrgRefs($orgName);
if (0 == count($orgRefs))
{
    exit("No organization with name $orgName is found\n");
}
$orgRef = $orgRefs[0];
$sdkOrg = $service->createSDKObj($orgRef);

$vdcRefs = $sdkOrg->getVdcRefs($vdcName);
if (0 == count($vdcRefs))
{
    exit("No vDC with name $vdcName is found\n");
}
$vdcRef = $vdcRefs[0];
$sdkVdc = $service->createSDKObj($vdcRef);

if (true === $list)
{
    $refs = $sdkVdc->getVAppTemplateRefs();
    if (0 < count($refs))
    {
        foreach ($refs as $ref)
        {
            echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
                 " name=" . $ref->get_name() . "\n";
        }
    }
    exit(0);
}

// upload an OVF package to create a vAppTemplate in vCloud
$sdkVdc->uploadOVFAsVAppTemplate($vAppTempName, $ovfDescriptorPath, $disks);


/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates uploading an OVF as a vApp template to vDC.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php uploadvapptemplate.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>        [req] Name of the existing organization in the vCloud Director.\n";
    echo "     -b|--vdc <vdcName>        [req] Name of the existing vDC in the organization.\n";
    echo "     -c|--temp <tempName>      [opt] Name of the vApp template to be created. Required when do uploading OVF.\n";
    echo "     -d|--disks <disks>        [opt] Comma seperated disks path. Required when do uploading OVF.\n";
    echo "     -e|--ovf <ovf>            [opt] Path of the OVF file. Required when do uploading OVF.\n";
    echo "     -l|--list                 [opt] List vApp template in the vDC.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php uploadvapptemplate.php -s 127.0.0.1 -u admin@Org -p password -a org -b vdc -c=vapptemp -d=/tmp/disk1.vmdk -e=/tmp/test.ovf\n";
    echo "     # php uploadvapptemplate.php -a org -b vdc -c=vapptemp -d=/tmp/disk1.vmdk -e=/tmp/test.ovf // using config.php to set login credentials\n";
    echo "     # php uploadvapptemplate.php -a org -b vdc -c=vapptemp -d=/tmp/disk1.vmdk,/tmp/disk2.vmdk -e=/tmp/test.ovf //multiple virtual disks\n"; 
    echo "     # php uploadvapptemplate.php -a org -b vdc -l // list vApp template in the vDC\n\n";
}
?>
