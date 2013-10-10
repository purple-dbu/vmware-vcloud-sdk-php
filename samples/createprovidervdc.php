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
 * Sample for creating provider vDC.
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
$shorts .= "h:";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "vim:",       //-a|--vim       [required]
    "pvdc:",      //-b|--pvdc      [required]
    "sp:",        //-c|--sp        [required]
    "rp:",        //-d|--rp        [required]
    "certpath:",  //-h|--certpath  [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$pvdcName = null;
$storageProfile = null;
$rpMoRef = null;
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
        $vimName = $opts['a'];
        break;
    case "vim":
        $vimName = $opts['vim'];
        break;
        
    case "b":
        $pvdcName = $opts['b'];
        break;
    case "pvdc":
        $pvdcName = $opts['pvdc'];
        break;

    case "c":
        $storageProfile = $opts['c'];
        break;
    case "sp":
        $storageProfile = $opts['sp'];
        break;

    case "d":
        $rpMoRef = $opts['d'];
        break;
    case "rp":
        $rpMoRef = $opts['rp'];
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
    (true !== $list && (!isset($vimName) || !isset($pvdcName) || !isset($storageProfile) ||
    !isset($rpMoRef))))
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

    // creates an SDK Extension object
    $sdkExt = $service->createSDKExtensionObj();

    if (true === $list)
    {
        $refs = $sdkExt->getVMWProviderVdcRefs();
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

    // create references of the vim server
    $vimRefs = $sdkExt->getVimServerRefs($vimName);
    if (0 == count($vimRefs))
    {
        exit("No vim server with $vimName is found\n");
    }
    $vimRef = $vimRefs[0];
    $vimRef1 = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());
    $vimRef2 = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());

    // set resource pool
    $vimObj2 = new VMware_VCloud_API_Extension_VimObjectRefType();
    $vimObj2->setVimServerRef($vimRef1);
    $vimObj2->setMoRef($rpMoRef);
    $vimObj2->setVimObjectType('RESOURCE_POOL');

    $rpRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
    $rpRefs->addVimObjectRef($vimObj2);

    // create a provider vDC data object
    $pvdc = new VMware_VCloud_API_Extension_VMWProviderVdcParamsType();
    $pvdc->set_name($pvdcName);
    $pvdc->setStorageProfile(array($storageProfile));
    $pvdc->setResourcePoolRefs($rpRefs);
    $pvdc->setIsEnabled(true);
    $pvdc->setVimServer(array($vimRef2));
    //echo $pvdc->export() . "\n";

    // create a provider vDC in vCloud Director
    try
    {
        echo "Creating Provider Vdc...\n";
        $sdkExt->createProviderVdc($pvdc);
        echo "Successfully Created Provider Vdc.\n";
    }
    catch(Exception $e)
    {
        echo $e->getMessage() . "\n";
    }
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating a provider vDC.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createprovidervdc.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>               [req] Name of a registered Vim server in the vCloud Director.\n";
    echo "     -b|--pvdc <pvdcName>             [req] Name of the provider vDC to be created.\n";
    echo "     -c|--sp <sp>                     [req] Name of Storage Profile.\n";
    echo "     -d|--rp <rp>                     [req] MoRef of a resource pool.\n";
    echo "     -h|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List all provider vDC.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b pvdc -c storageprofile -d resgroup-84\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b pvdc -c storageprofile -d resgroup-84 -h certificatepath\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b pvdc -c storageprofile -d resgroup-84\n";
    echo "     # php createprovidervdc.php -a vim -b pvdc -c storageprofile -d resgroup-84 // using config.php to set login credentials\n";
    echo "     # php createprovidervdc.php -l// list all provider vDC\n\n";
}
?>
