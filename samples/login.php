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
 * Log in to vCloud Director.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "v:";
$shorts .= "c:";

$longs  = array(
    "server:",    //-s|--server    [required] vCloud Director server IP/hostname
    "user:",      //-u|--user      [required] vCloud Director login username
    "pswd:",      //-p|--pswd      [required] vCloud Director login password
    "sdkver:",    //-v|--sdkver    [required]
    "certpath:",  //-c|--certpath  [optional] local certificate path
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

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

    case "c":
        $certpath = $opts['c'];
        break;
    case "certpath":
        $certpath = $opts['certpath'];
        break;
}

// parameters validation
if (!isset($server) || !isset($user) || !isset($pswd) || !isset($sdkversion))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

$flag = true;
if (isset($certpath))
{
    $cert = file_get_contents($certpath);
    $data = openssl_x509_parse($cert);
    $encodeddata1 = base64_encode(serialize($data));
    // Split a server url by '/'
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

    // compare two certificates as string
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
    if (count($opts)<=3)
    {
        echo "\n\nIgnoring the Certificate Validation --Fake certificate - DO NOT DO THIS IN PRODUCTION.\n\n";
    }
    // login
    $service = VMware_VCloud_SDK_Service::getService();
    $service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig, $sdkversion);

    // verify logged in by print out name and href of all the organizations
    $orgRefs = $service->getOrgRefs();
    if (!empty($orgRefs))
    {
        foreach ($orgRefs as $ref)
        {
            echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
                 " name=" . $ref->get_name() . "\n";
        }
    }
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
    echo "     This sample demonstrates logging into VMware vCloud Director with Valid or Fake certificate.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php login.php -s <server> -u <username> -p <password> -v <sdkversion>\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                            for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "     -c|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php login.php -s 127.0.0.1 -u admin@Org -p password -v 5.5\n";
    echo "     # php login.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -c certificatepath\n";
    echo "     # php login.php  // using config.php to set login credentials\n\n";
}
?>
