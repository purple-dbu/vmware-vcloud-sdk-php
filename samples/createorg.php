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
 * Sample for creating an organization
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a::";
$shorts .= "b::";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "org::",      //-a|--org    [required for creating]
    "full::",     //-b|--full   [required for creating]
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);
//var_dump($opts);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$fullName = null;
// The following are hard-coded to simplify command line options
$description = "Org description";
$isEnabled = true;
$canPublishCatalogs = true;
$ldapMode = 'NONE';
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
        $fullName = $opts['b'];
        break;
    case "full":
        $fullName = $opts['full'];
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
    ((true !== $list) && (!isset($orgName) || !isset($fullName))))
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
    $orgRefs = $service->getOrgRefs();
    if (0 < count($orgRefs))
    {
        foreach ($orgRefs as $ref)
        {
            echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
                 " name=" . $ref->get_name() . "\n";
        }
    }
    exit(0);
}

// create an SDK Admin object
$sdkAdmin = $service->createSDKAdminObj();

// create an AdminOrgType data object
$gSettings = new VMware_VCloud_API_OrgGeneralSettingsType();
$gSettings->setCanPublishCatalogs($canPublishCatalogs);

$lSettings = new VMware_VCloud_API_OrgLdapSettingsType();
$lSettings->setOrgLdapMode($ldapMode);

$settings = new VMware_VCloud_API_OrgSettingsType();
$settings->setOrgGeneralSettings($gSettings);
$settings->setOrgLdapSettings($lSettings);

$adminOrgObj = new VMware_VCloud_API_AdminOrgType();
$adminOrgObj->set_name($orgName); // name is required
$adminOrgObj->setFullName($fullName);  // FillName is required
$adminOrgObj->setDescription($description); // Description is optioanl
$adminOrgObj->setSettings($settings); // Settings is required
$adminOrgObj->setIsEnabled($isEnabled);
//echo $adminOrgObj->export() . "\n";

// create an administrative organization in vCloud Director.
$sdkAdmin->createOrganization($adminOrgObj);


/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating a new organization in vCloud Director.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createorg.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>        [opt] Name of the organization to be created in the vCloud Director. Required when creating.\n";
    echo "     -b|--full <fullName>      [opt] Full name of the organization to be created. Required when creating.\n";
    echo "     -l|--list                 [opt] List all organizations in vCloud Director\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createorg.php -s 127.0.0.1 -u admin@Org -p password -a=org -b=full\n";
    echo "     # php createorg.php -a=org -b=full // using config.php to set login credentials\n";
    echo "     # php createorg.php -l // list all organizations in vCloud Director\n\n";
}
?>
