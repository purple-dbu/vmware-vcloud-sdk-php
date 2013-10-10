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
 * Demonstrate using VMware vCloud Director query service for listing supported
 * query types, retrieving specified records or references.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "v:";

$shorts .= "a::";
$shorts .= "b::";
$shorts .= "c";
$shorts .= "d";
$shorts .= "e::";
$shorts .= "f::";
$shorts .= "g:";


$longs  = array(
    "server:",    //-s|--server    [required] vCloud Director server IP/hostname
    "user:",      //-u|--user      [required] vCloud Director login username
    "pswd:",      //-p|--pswd      [required] vCloud Director login password
    "sdkver:",    //-v|--sdkver    [required]
    "rec::",      //-a|--rec       [required if ref is not set] Query type for records
    "ref::",      //-b|--ref       [required if rec is not set] Query type for references
    "lrec",       //-c|--lrec
    "lref",       //-d|--lref
    "fields::",   //-e|--fields
    "filter::",   //-f|--filter
    "certpath:",  //-g|--certpath  [optional] local certificate path
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$format = "Records";
$query = null;
$fields = null;
$filter = null;
$lrec = null;
$lref = null;
$certPath = null;

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
        $queryRec = $opts['a'];
        break;
    case "rec":
        $queryRec = $opts['rec'];
        break;

    case "b":
        $queryRef = $opts['b'];
        $format = "References";
        break;
    case "ref":
        $queryRef = $opts['ref'];
        $format = "References";
        break;

    case "c":
        $lrec = true;
        break;
    case "lrec":
        $lrec = true;
        break;

    case "d":
        $lref = true;
        $format = "References";
        break;
    case "lref":
        $lref = true;
        $format = "References";
        break;

    case "e":
        $fields = $opts['e'];
        break;
    case "fields":
        $fields = $opts['fields'];
        break;

    case "f":
        $filter = $opts['f'];
        break;
    case "filter":
        $filter = $opts['filter'];
        break;

    case "g":
        $certPath = $opts['g'];
        break;
    case "certpath":
        $certPath = $opts['certpath'];
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd) || !isset($sdkversion)) ||
    (true !== $lrec && true !== $lref && 
    !isset($queryRec) && !isset($queryRef)))
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

    // create an SDK Query object
    $sdkQuery = VMware_VCloud_SDK_Query::getInstance($service);

    // get supported query types
    $supm = "getSupportedQuery$format" . "Types";
    $supports = VMware_VCloud_SDK_Query::$supm();

    // for listing supported query types options
    if (true === $lrec || true === $lref)
    {
        printSupportedTypes($supports, $format);
        exit(0);
    }

    // validate user input query type
    $type = isset($queryRec)? $queryRec : $queryRef;
    if (!in_array($type, $supports))
    {
        echo("[ERROR] $type is not a supported query type. ");
        printSupportedTypes($supports, $format);
        exit(1);
    }

    $qm = "query$format";
    $params = null;
    if (isset($fields) || isset($filter))
    {
        $params = new VMware_VCloud_SDK_Query_Params();
        $params->setFields($fields);
        $params->setFilter($filter);
    }
    $recsObj = $sdkQuery->$qm($type, $params);
    echo $recsObj->export() . "\n";
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

/**
 * Print the given supported query types
 */
function printSupportedTypes($types, $format)
{
    echo "Supported query type for " . strtolower($format) . " are:\n";
    foreach($types as $s)
    {
        echo(" -- " . $s . "\n");
    }
    echo "\n";
}

/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates VMware vCloud Director query service for\n";
    echo "     listing supported query types or retrieving records or references.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php query.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--rec <queryType>             [opt*] Query specified type in records format.\n";
    echo "     -b|--ref <queryType>             [opt*] Query specified type in references format.\n";
    echo "     -c|--lrec                        [opt*] Print supported query types for retrieving records.\n";
    echo "     -d|--lref                        [opt*] Print supported query types for retrieving references.\n";
    echo "     -e|--fields <fields>             [opt] Comma seperated fields of the records to be retrieved.\n";
    echo "     -f|--filter <filter>             [opt] Filter string in an FIQL format.\n";
    echo "     -g|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "  * requires to specify -a, -b, -c or -d\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php query.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a=organization -e=name,displayName\n";
    echo "     # php query.php -b=organization // using config.php to set login credentials\n";
    echo "     # php query.php -b=organization -g certificatepath// using config.php to set login credentials\n";
    echo "     # php query.php -c   // list all supported query types for retrieving records.\n";
    echo "     # php query.php -d   // list all supported query types for retrieving references.\n";
    echo "     # php query.php -a=vAppTemplate -e=name,ownerName,vdcName\n";
    echo "     # php query.php -a=vAppTemplate -e=name,ownerName,vdcName -f='ownerName==system;vdcName=VC%20Resources'\n";
    echo "     #        // returns query record(s) for vApp templates owned by 'system' and the vDC name\n";
    echo "     #        // is 'VC Resources', output name, ownerName, vdcName attributes.\n";
    echo "     # php query.php -b=vAppTemplate -f='ownerName==system;vdcName==VC20%Resources'\n";
    echo "     #        // returns query reference(s) for vApp template owned by 'system' and the vDC name\n";
    echo "     #        // is 'VC Resources'. Fields will be ignored if the query results do not have the specified fields.\n\n";
}
?>
