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
 * Sample for uploading an OVF as a vApp template to vDC.
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
$shorts .= "d:";
$shorts .= "e:";
$shorts .= "f::";
$shorts .= "g::";
$shorts .= "h::";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "org:",       //-a|--org       [required]
    "vdc:",       //-b|--vdc       [required]
    "catalog:",   //-c|--catalog   [required]
    "temp:",      //-d|--temp      [required for creating vAppTemplate]
    "ovf:",       //-e|--ovf       [required for uploading OVF]
    "des::",      //-f|--des       [optional]
    "manifest::", //-g|--manifest  [optional]
    "certpath::", //-h|--certpath  [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$vdcName = null;
$catalogName = null;
$vAppTempName = null;
$ovfDescriptorPath = null;
$description = null;
$manifestRequired = null;
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
        $catalogName = $opts['c'];
        break;
    case "catalog":
        $catalogName = $opts['catalog'];
        break;

    case "d":
        $vAppTempName = $opts['d'];
        break;
    case "temp":
        $vAppTempName = $opts['temp'];
        break;

    case "e":
        $ovfDescriptorPath = $opts['e'];
        break;
    case "ovf":
        $ovfDescriptorPath = $opts['ovf'];
        break;

    case "f":
        $description = $opts['f'];
        break;
    case "des":
        $description = $opts['des'];
        break;

    case "g":
        $manifestRequired = $opts['g'];
        break;
    case "manifest":
        $manifestRequired = $opts['manifest'];
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
    !isset($orgName) || !isset($vdcName) || !isset($catalogName) ||
    ((true !== $list) && (!isset($vAppTempName) || !isset($ovfDescriptorPath))))
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
    $vdcrefs= $sdkVdc->getVdcStorageProfileRefs();
    $vdcStorageProfileRef = $vdcrefs[0];
    // Get a reference to the Catalog from org.
    $catRefs = $sdkOrg->getCatalogRefs($catalogName);
    $catalogRef=$catRefs[0];

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
    echo "Uploading a vAppTemplate...\n";
    $sdkVdc->uploadOVFAsVAppTemplate($vAppTempName, $ovfDescriptorPath, $description, $manifestRequired, $vdcStorageProfileRef, $catalogRef);
    echo "\nUploaded a vAppTemplate.\n";
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
    echo "     This sample demonstrates uploading an OVF as a vApp template to vDC.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php uploadvapptemplate.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                                    for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>               [req] Name of the existing organization in the vCloud Director.\n";
    echo "     -b|--vdc <vdcName>               [req] Name of the existing vDC in the organization.\n";
    echo "     -c|--catalog <catalogName>       [req] Name of the existing catalog in the organization.\n";
    echo "     -d|--temp <tempName>             [req] Name of the vApp template to be created. Required when do uploading OVF.\n";
    echo "     -e|--ovf <ovf>                   [req] Path of the OVF file. Required when do uploading OVF.\n";
    echo "     -f|--des <description>           [opt] Description of the vApp template to be created.\n";
    echo "     -g|--manifest <manifest>         [opt] A flag indicates the manifest file is
                                                      required or not.\n";
    echo "     -h|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List vApp template in the vDC.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php uploadvapptemplate.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b vdc -c catalog -d=vapptemp -e 'E:/ovf/dsl-with-tools.ovf' -f='vappTemp description' -g='true' ";
    echo "     # php uploadvapptemplate.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b vdc -c catalog -d=vapptemp -e 'E:/ovf/dsl-with-tools.ovf' -f='vappTemp description' -g='true' -h='certificatepath'\n";
    echo "     # php uploadvapptemplate.php -a org -b vdc -c catalog -d=vapptemp -e 'E:/ovf/dsl-with-tools.ovf' // using config.php to set login credentials\n";
    echo "     # php uploadvapptemplate.php -a org -b vdc -c catalog -l // list vApp template in the vDC\n\n";
}
?>
