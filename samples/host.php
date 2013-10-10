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
 * Sample for host preparing, unpreparing, enabling, disabling operations. 
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "v:";
$shorts .= "a::";
$shorts .= "b::";
$shorts .= "c::";
$shorts .= "o::";
$shorts .= "d:";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "host::",     //-a|--host      [required when not listing]
    "huser::",    //-b|--huser     [required when $op='prep']
    "hpswd::",    //-c|--hpswd     [required when $op='prep']
    "op::",       //-o|--op        [required when not listing]
    "certpath:",  //-d|--certpath  [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$hostName = null;
$hostUser = null;
$hostPswd = null;
$op = null;   // 'prep', 'unprep', 'en', 'dis' are supported
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
        $hostName = $opts['a'];
        break;
    case "host":
        $hostName = $opts['host'];
        break;

    case "b":
        $hostUser = $opts['b'];
        break;
    case "huser":
        $hostUser = $opts['huser'];
        break;

    case "c":
        $hostPswd = $opts['c'];
        break;
    case "hpswd":
        $hostPswd = $opts['hpswd'];
        break;

    case "o":
        $op = $opts['o'];
        break;
    case "op":
        $op = $opts['op'];
        break;

    case "d":
        $certPath = $opts['d'];
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
    (true !== $list && (!isset($hostName) || !isset($op))) ||
    ('prep' == $op && (!isset($hostUser) || !isset($hostPswd))))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}
if ($op && !in_array($op, array('prep' , 'unprep', 'en', 'dis')))
{
    exit("$op is not supported, allowed operations are: 'prep' (for prepare),
          'unprep' (for unprepare), 'en' (for enable), 'dis' (for disable)\n");
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

    // create an SDK host object
    $hostRefs = $sdkExt->getHostRefs($hostName);
    if (0 == count($hostRefs))
    {
        if (isset($hostName))
        {
            exit("No host with name $hostName is found\n");
        }
        else
        {
            exit(0);
        }
    }
    if ($list)
    {
        foreach ($hostRefs as $ref)
        {
            echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
                 " name=" . $ref->get_name() . "\n";
        }
        exit(0);
    }
    $sdkHost = $service->createSDKObj($hostRefs[0]);

    switch ($op) 
    {
        case 'prep':   // prepare the host
            print "Preparing a host:\n";
            $task = $sdkHost->prepare($hostUser, $hostPswd);
            $task = $service->waitForTask($task);
            if ($task->get_status() == 'success')
            {
                print "\nSuccessfully prepared a host.\n";
                return;
            }
            print "\nFailed preparing a host.\n";
            break;
        case 'unprep': // unprepare the host
            print "Unpreparing a host:\n";
            $task = $sdkHost->unprepare();
            $task = $service->waitForTask($task);
            if ($task->get_status() == 'success')
            {
                print "\nSuccessfully unprepared a host.\n";
                return;
            }
            print "\nFailed unpreparing a host.\n";
            break;
        case 'en':     // enable the host
            print "Enabling a host:\n";
            $task = $sdkHost->enable();
            $task = $service->waitForTask($task);
            if ($task->get_status() == 'success')
            {
                print "\nSuccessfully enabled a host.\n";
                return;
            }
            print "\nFailed enabling a host.\n";

            break;
        case 'dis':    // disable the host
            print "Disabling a host:\n";
            $task = $sdkHost->disable();
            $task = $service->waitForTask($task);
            if ($task->get_status() == 'success')
            {
                print "\nSuccessfully disabled a host.\n";
                return;
            }
            print "\nFailed disabling a host.\n";
            break;
    }
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
    echo "     This sample demonstrates preparing, unpreparing, enabling, disabling operations on a host.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php host.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--host <hostName>             [opt] Name of an existng host. Required when not listing\n";
    echo "     -b|--huser <username>            [opt] Username of the host. Required for preparing\n";
    echo "     -c|--hpswd <password>            [opt] Password of the host. Required for preparing\n";
    echo "     -o|--op <prep/unprep/en/dis>     [opt] Operation on the host. Allows: prep, unprep, en, dis. Required when not listing\n";
    echo "     -d|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List all hosts in vCloud Director.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php host.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a=host -b=user -c=pswd -o=prep // prepare a host\n";
    echo "     # php host.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a=host -b=user -c=pswd -o=prep -d certificatepath// prepare a host\n";
    echo "     # php host.php -a=host -o=en     // using config.php to set login credentials, enable a host\n";
    echo "     # php host.php -a=host -o=dis    // disable a host\n";
    echo "     # php host.php -a=host -o=unprep // unprepare a host\n";
    echo "     # php host.php -l                // list all hosts in vCloud Director\n";
}
?>
