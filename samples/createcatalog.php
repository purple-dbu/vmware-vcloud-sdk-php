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
 * Sample for creating a catalog.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "v:";

$shorts .= "a:";
$shorts .= "b::";
$shorts .= "c:";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "org:",       //-a|--org       [required]
    "cat::",      //-b|--cat       [required when creating]
    "certpath:",  //-c|--certpath  [optional] local certificate path
    "list"        //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$catName = null;
$items = null;
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
        $catName = $opts['b'];
        break;
    case "cat":
        $catName = $opts['cat'];
        break;

    case "c":
        $certPath = $opts['c'];
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
   ((true !== $list) && !isset($catName)))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}
// end of parameter validation

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
    $adminVdcRefs = $sdkAdminOrg->getAdminVdcRefs();
    if (0 == count($adminVdcRefs))
    {
        exit("No adminvdc found\n");
    }
    $sdkAdminVdc = $service->createSDKObj($adminVdcRefs[0]);
    $VdcStorageProfiles = $sdkAdminVdc->getAdminVdcStorageProfileRefs();
    if (0 == count($VdcStorageProfiles))
    {
        exit("No VdcStorageProfiles found\n");
    }
    $catStorProf = new VMware_VCloud_API_CatalogStorageProfilesType();
    $catStorProf->setVdcStorageProfile($VdcStorageProfiles);

    // create a catalog data object
    $cat = new VMware_VCloud_API_AdminCatalogType();
    // set the new catalog name
    $cat->set_name($catName);
    // set the catalog item(s) to be added. In this sample, it is empty.
    $cat->setCatalogItems($items);
    $cat->setCatalogStorageProfiles($catStorProf);
    echo "Creating catalog: " . $catName . "\n";
    // create a new catalog
    $sdkAdminOrg->createCatalog($cat);
    echo "Successfully created catalog: " . $catName . "\n";
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

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
    echo "     # php createcatalog.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                            for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>               [req] Name of an existing organization in the vCloud Director.\n";
    echo "     -b|--cat <catName>               [opt] Name of the catalog to be created in the organization. Required for creating.\n";
    echo "     -c|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List all the catalog in the organization\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createcatalog.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b=cat\n";
    echo "     # php createcatalog.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b=cat -c certificatepath\n";
    echo "     # php createcatalog.php -a org -b=cat // using config.php to set login credentials\n";
    echo "     # php createcatalog.php -a org -l\n\n";
}
?>
