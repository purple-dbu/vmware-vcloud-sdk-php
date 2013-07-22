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
 * Sample for creating a catalog.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b::";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "org:",       //-a|--org    [required]
    "cat::",      //-b|--cat    [required when creating]
    "list"        //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$catName = null;
$items = null;
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
        $catName = $opts['b'];
        break;
    case "cat":
        $catName = $opts['cat'];
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
   ((true !== $list) && !isset($catName)))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}
// end of parameter validation

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
    $sdkOrg = $service->createSDKObj($orgRefs[0]->get_href());
    $catRefs = $sdkOrg->getCatalogRefs();
    if (0 < count($catRefs))
    {
        foreach($catRefs as $ref)
        {
            echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
                 " name=" . $ref->get_name() . "\n";
        }
    }
    exit(0);
}

// create an SDK object for the entry point of administrator operations
$sdkAdmin = $service->createSDKAdminObj();

// create an SDK object for the specified organization
$adminOrgRefs = $sdkAdmin->getAdminOrgRefs($orgName);
if (0 == count($adminOrgRefs))
{
    exit("No organization with $orgName is found\n");
}
$adminOrgRef = $adminOrgRefs[0];
$sdkAdminOrg = $service->createSDKObj($adminOrgRef);

// create a catalog data object
$cat = new VMware_VCloud_API_AdminCatalogType();
// set the new catalog name
$cat->set_name($catName);
// set the catalog item(s) to be added. In this sample, it is empty.
$cat->setCatalogItems($items);

// create a new catalog
$sdkAdminOrg->createCatalog($cat);


/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating a new empty catalog in the vDC.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createcatalog.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>        [req] Name of an existing organization in the vCloud Director.\n";
    echo "     -b|--cat <catName>        [opt] Name of the catalog to be created in the organization. Required for creating.\n";
    echo "     -l|--list                 [opt] List all the catalog in the organization\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createcatalog.php -s 127.0.0.1 -u admin@Org -p password -a org -b=cat\n";
    echo "     # php createcatalog.php -a org -b=cat // using config.php to set login credentials\n";
    echo "     # php createcatalog.php -a org -l\n\n";
}
?>
