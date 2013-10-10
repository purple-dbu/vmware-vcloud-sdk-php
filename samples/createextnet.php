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
 * Sample for creating an external network.
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
$shorts .= "d::";
$shorts .= "e::";
$shorts .= "f::";
$shorts .= "g::";
$shorts .= "h::";
$shorts .= "i::";
$shorts .= "j::";
$shorts .= "k::";
$shorts .= "m:";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server   [required]
    "user:",      //-u|--user     [required]
    "pswd:",      //-p|--pswd     [required]
    "sdkver:",    //-v|--sdkver   [required]
    "vim::",      //-a|--vim      [required for creating] name of the registered vim server
    "net::",      //-b|--net      [required for creating] name of the external network to be created
    "netmoref::", //-c|--netmoref [required for creating] vim port group MoRef
    "gw::",       //-d|--gw       [required for creating] gateway
    "mask::",     //-e|--mask     [required for creating] netmask
    "dns1::",     //-f|--dns1     [required for creating] 1st DNS
    "dns2::",     //-g|--dns2                2nd DNS 
    "suf::",      //-h|--suf                 DNS suffix
    "start::",    //-i|--start    [required for creating] start IP for IpRangeType
    "end::",      //-j|--end      [required for creating] end IP for IpRangeType
    "fence::",    //-k|--fence    [required for creating] fence mode
    "certpath:",  //-m|--certpath [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);


// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$extNetName = null;
$netMoRef = null;
$gateway = null;
$netmask = null;
$dns1 = null;
$dns2 = null;
$dnsSuffix = null;
$startAddr = null;
$endAddr = null;
$fenceMode = null;
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
        $extName = $opts['b'];
        break;
    case "net":
        $extName = $opts['net'];
        break;

    case "c":
        $netMoRef = $opts['c'];
        break;
    case "netmoref":
        $netMoRef = $opts['netmoref'];
        break;

    case "d":
        $gateway = $opts['d'];
        break;
    case "gw":
        $gateway = $opts['gw'];
        break;

    case "e":
        $netmask = $opts['e'];
        break;
    case "mask":
        $netmask = $opts['mask'];
        break;

    case "f":
        $dns1 = $opts['f'];
        break;
    case "dns1":
        $dns1 = $opts['dns1'];
        break;

    case "g":
        $dns2 = $opts['g'];
        break;
    case "dns2":
        $dns2 = $opts['dns2'];
        break;

    case "h":
        $dnsSuffix = $opts['h'];
        break;
    case "suf":
        $dnsSuffix = $opts['suf'];
        break;

    case "i":
        $startAddr = $opts['i'];
        break;
    case "start":
        $startAddr = $opts['start'];
        break;

    case "j":
        $endAddr = $opts['j'];
        break;
    case "end":
        $endAddr = $opts['end'];
        break;

    case "k":
        $fenceMode = $opts['k'];
        break;
    case "fence":
        $fenceMode = $opts['fence'];
        break;

    case "m":
        $certPath = $opts['m'];
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
    ((true !== $list) && (!isset($vimName) || !isset($extName) ||
    !isset($netMoRef) || !isset($gateway) || !isset($netmask) ||
    !isset($startAddr) || !isset($endAddr) ||
    !isset($fenceMode))))
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

    // create an SDK Extension object
    $sdkExt = $service->createSDKExtensionObj();

    if (true === $list)
    {
        $refs = $sdkExt->getVMWExternalNetworkRefs();
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

    // create a reference to the vim server
    $vimRefs = $sdkExt->getVimServerRefs($vimName);
    if (0 == count($vimRefs))
    {
        exit("No vim server with $vimName is found!\n");
    }
    $vimRef = $vimRefs[0];
    $vimRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());

    // create a reference to the portgroup
    $vmPGRef = new VMware_VCloud_API_Extension_VimObjectRefType();
    $vmPGRef->setVimServerRef($vimRef);
    $vmPGRef->setMoRef($netMoRef);
    $vmPGRef->setVimObjectType('DV_PORTGROUP');

    // set IP range
    $ipRange = new VMware_VCloud_API_IpRangeType();
    $ipRange->setStartAddress($startAddr);
    $ipRange->setEndAddress($endAddr);

    $ipRanges = new VMware_VCloud_API_IpRangesType();
    $ipRanges->setIpRange(array($ipRange));

    // set network configuration
    $ipScope = new VMware_VCloud_API_IpScopeType();
    $ipScope->setIsInherited(false);
    $ipScope->setGateway($gateway);
    $ipScope->setNetmask($netmask);
    $ipScope->setDns1($dns1);
    $ipScope->setDns2($dns2);
    $ipScope->setDnsSuffix($dnsSuffix);
    $ipScope->setIpRanges($ipRanges);

    $config = new VMware_VCloud_API_NetworkConfigurationType();
    $config->setIpScope($ipScope);
    $config->setFenceMode($fenceMode);

    // create a external network data object
    $extNet = new VMware_VCloud_API_Extension_VMWExternalNetworkType();
    $extNet->set_name($extName);
    $extNet->setDescription('External network description');
    $extNet->setVimPortgroupRef($vmPGRef);
    $extNet->setConfiguration($config);
    //echo $extNet->export() . "\n";

    // create an external network
    $sdkExt->createVMWExternalNetwork($extNet);
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
    echo "     This sample demonstrates creating an external network.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createextnet.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>               [opt] Name of a registered Vim server in the vCloud Director. Required for creating.\n";
    echo "     -b|--net <netName>               [opt] Name of the external network to be created. Required for creating.\n";
    echo "     -c|--netmoref <netmoref>         [opt] Vim portgroup MoRef. Required for creating.\n";
    echo "     -d|--gw  <gateway>               [opt] Gateway. Required for creating.\n";
    echo "     -e|--mask <netmask>              [opt] Netmask. Required for creating.\n";
    echo "     -f|--dns1 <dns1>                 [opt] The first DNS.\n";
    echo "     -g|--dns2 <dns2>                 [opt] The second DNS.\n";
    echo "     -h|--suf <suffix>                [opt] DNS suffix.\n";
    echo "     -i|--start  <startaddr>          [opt] Start IP for IpRangeType. Required for creating.\n";
    echo "     -j|--end <endaddr>               [opt] End start IP for IpRangeType. Required for creating.\n";
    echo "     -k|--fence <fencemode>           [opt] Fence mode. Required for creating.\n";
    echo "     -m|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List all external networks in vCloud Director.";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createextnet.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a=vim -b=net -c=dvportgroup-37 -d=gw -e=mask -f=dns1 -g=dns2 -h=suffix -i=start -j=end -k=fence\n";
    echo "     # php createextnet.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a=vim -b=net -c=dvportgroup-37 -d=gw -e=mask -f=dns1 -g=dns2 -h=suffix -i=start -j=end -k=fence -m certificatepath\n";
    echo "     # php createextnet.php -a=vim -b=net -c=dvportgroup-37 -d=gw -e=mask -f=dns1 -g=dns2 -h=suffix -i=start -j=end -k=fence // using config.php to set login credentials\n";
    echo "     # php createextnet.php -l\n\n";
}
?>
