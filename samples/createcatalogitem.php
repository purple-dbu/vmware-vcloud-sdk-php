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
 * Sample for adding a vApp template or a media as a catalog item to a catalog.
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
$shorts .= "d::";
$shorts .= "e::";
$shorts .= "f::";
$shorts .= "g::";
$shorts .= "h:";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "org:",       //-a|--org       [required]
    "vdc:",       //-b|--vdc       [required when creating catalog item]
    "cat:",       //-c|--cat       [required]
    "temp::",     //-d|--temp      [required when creating vapp template catalog item]
    "item::",     //-e|--item      [required when creating catalog item]
    "desc::",     //-f|--desc
    "media::",    //-g|--media     [required when creating media catalog item]
    "certpath:",  //-h|--certpath  [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$vdcName = null;
$vAppTempName = null;
$catName = null;
$catItemName = null;
$description = "Catalog item description";
$mediaName = null;
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
        $catName = $opts['c'];
        break;
    case "cat":
        $catName = $opts['cat'];
        break;

    case "d":
        $vAppTempName = $opts['d'];
        break;
    case "temp":
        $vAppTempName = $opts['temp'];
        break;

    case "e":
        $catItemName = $opts['e'];
        break;
    case "item":
        $catItemName = $opts['item'];
        break;

    case "f":
        $description = $opts['f'];
        break;
    case "desc":
        $description = $opts['desc'];
        break;

    case "g":
        $mediaName = $opts['g'];
        break;
    case "media":
        $mediaName = $opts['media'];
        break;

    case "h":
        $certPath = $opts['h'];
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
    !isset($orgName) || (!isset($vdcName) || !isset($catName) ||
    ((true !== $list) && (!isset($catItemName) ||
    (!isset($vAppTempName) && (!isset($mediaName)))))))
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

    // create an SDK catalog object
    $catRefs = $sdkOrg->getCatalogRefs($catName);
    if (0 == count($catRefs))
    {
        exit("No catalog with name $catName is found\n");
    }
    $catRef = $catRefs[0];
    $sdkCat = $service->createSDKObj($catRef);

    if (true === $list)
    {
        $refs = $sdkCat->getCatalogItemRefs();
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

    // get a reference to a vAppTemplate or a media in the vDC
    if (isset($vAppTempName))
    {
        $mediaName = $vAppTempName;
        $refs = $sdkVdc->getVAppTemplateRefs($vAppTempName);
    }
    else if (isset($mediaName))
    {
        $refs = $sdkVdc->getMediaRefs($mediaName);
    }

    if (0 == count($refs))
    {
        exit("No vAppTemplate or media with name $mediaName is found.\n");
    }
    $ref = $refs[0];
    // create a ReferenceType object to be added to CatalogItemType object 
    $ref = VMware_VCloud_SDK_Helper::createReferenceTypeObj($ref->get_href());

    // create a catalog item to be added.
    $catItem = new VMware_VCloud_API_CatalogItemType();
    $catItem->set_name($catItemName);
    $catItem->setDescription($description);
    $catItem->setEntity($ref);

    // Add the catalog item to the catalog
    echo "Creating catalog item...\n";
    $sdkCat->addCatalogItem($catItem);
    echo "Created catalog item.\n";
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
    echo "     This sample demonstrates adding a vApp template or a media to a catalog.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createcatalogitem.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                            for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>               [req] Name of an existing organization in the vCloud Director.\n";
    echo "     -b|--vdc <vdcName>               [req] Name of an existing vDC in the organization. Required for creating catalog item\n";
    echo "     -c|--cat <catName>               [req] Name of an existing catalog in the organization.\n";
    echo "     -d|--temp <tempName>             [opt] Name of an existing vApp template to be added. Required for creating catalog item\n";
    echo "     -e|--item <itemName>             [opt] Name of the catalog item to be created. Required for creating catalog item\n";
    echo "     -f|--desc <description>          [opt] Description of the catalog item to be created.\n";
    echo "     -h|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List catalog items in the catalog\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createcatalogitem.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b=vdc -c catlog -d=vapptemp -e=item -f=description\n";
    echo "     # php createcatalogitem.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b=vdc -c catlog -d=vapptemp -e=item -f=description -h certificatepath\n";
    echo "     # php createcatalogitem.php -a org -b=vdc -c catlog -d=vapptemp -e=item -f=description// using config.php to set login credentials\n";
    echo "     # php createcatalogitem.php -a org -b=vdc -c catlog -g=media -e=item -f=description //adding a media to the catalog\n";
    echo "     # php createcatalogitem.php -a org -c catlog -l // list catalog items in the catalog\n\n";
}
?>
