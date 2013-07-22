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
 * Sample for registering, unregistering, enabling, disabling operations on a
 * Vim server. 
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b::";
$shorts .= "c::";
$shorts .= "d::";
$shorts .= "e::";
$shorts .= "f::";
$shorts .= "g::";
$shorts .= "h::";
$shorts .= "o::";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "vimn:",      //-a|--vimn   [required]
    "vims::",     //-b|--vims   [required when $op='reg']
    "vimu::",     //-c|--vimu   [required when $op='reg']
    "vimp::",     //-d|--vimp   [required when $op='reg']
    "vsmn::",     //-e|--vsmn
    "vsms::",     //-f|--vsms   [required when $op='reg']
    "vsmu::",     //-g|--vsmu   [required when $op='reg']
    "vsmp::",     //-h|--vsmp   [required when $op='reg']
    "op::",       //-o|--op     [required for register, unregister, enable, disable ops]
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

// vim server to be aregistered
$vimName = null;
$vimUrl = null;
$vimUser = null;
$vimPswd = null;

// vShield manager server to be registered
$vsmName = 'vsm_' . time();  // default
$vsmUrl = null;
$vsmUser = null;
$vsmPswd = null;

// operation supported: reg(ister), unreg(ister), en(able), dis(able)
$op = null;
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
        $vimName = $opts['a'];
        break;
    case "vimn":
        $vimName = $opts['vimn'];
        break;
        
    case "b":
        $vimUrl = $opts['b'];
        break;
    case "vims":
        $vimUrl = $opts['vims'];
        break;

    case "c":
        $vimUser = $opts['c'];
        break;
    case "vimu":
        $vimUser = $opts['vimu'];
        break;

    case "d":
        $vimPswd = $opts['d'];
        break;
    case "vimp":
        $vimPswd = $opts['vimp'];
        break;

    case "e":
        $vsmName = $opts['e'];
        break;
    case "vsmn":
        $vsmName = $opts['vsmn'];
        break;

    case "f":
        $vsmUrl = $opts['f'];
        break;
    case "vsms":
        $vsmUrl = $opts['vsms'];
        break;

    case "g":
        $vsmUser = $opts['g'];
        break;
    case "vsmu":
        $vsmUser = $opts['vsmu'];
        break;

    case "h":
        $vsmPswd = $opts['h'];
        break;
    case "vsmp":
        $vsmPswd = $opts['vsmp'];
        break;

    case "o":
        $op = $opts['o'];
        break;
    case "op":
        $op = $opts['op'];
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
    (true !== $list && (!isset($vimName) || !isset($op))) ||
    (('reg' == $op) && (!isset($vimUrl) || !isset($vimUser) || !isset($vimPswd)
        || !isset($vsmUrl) || !isset($vsmUser) || !isset($vsmPswd))))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}
if ($op && !in_array($op, array('reg' , 'unreg', 'en', 'dis')))
{
    exit("$op is not supported, allowed operations are: 'reg' (for register),
          'unreg' (for unregister), 'en' (for enable), 'dis' (for disable)\n");
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

// creates an SDK Extension object
$sdkExt = $service->createSDKExtensionObj();

if (true === $list)
{
    $refs = $sdkExt->getVimServerRefs();
    if (0 == count($refs))
    {
        exit(0);
    }
    foreach($refs as $ref)
    {
        echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
             " name=" . $ref->get_name() . "\n";
    }
    exit(0);
}

// for register Vim server operation
if ('reg' == $op)
{
    $vimServer = new VMware_VCloud_API_Extension_VimServerType();
    $vimServer->set_name($vimName);
    $vimServer->setUrl($vimUrl);
    $vimServer->setUsername($vimUser);
    $vimServer->setPassword($vimPswd);
    $vimServer->setIsEnabled(true);

    $vShieldManager = new VMware_VCloud_API_Extension_ShieldManagerType();
    $vShieldManager->set_name($vsmName);
    $vShieldManager->setUrl($vsmUrl);
    $vShieldManager->setUsername($vsmUser);
    $vShieldManager->setPassword($vsmPswd);

    $params = new VMware_VCloud_API_Extension_RegisterVimServerParamsType();
    $params->setVimServer($vimServer);
    $params->setShieldManager($vShieldManager);

    //echo "param:\n" . $params->export();
    $sdkExt->registerVimServer($params);
    exit(0); //exits for register operation
}

// Gets the reference of the Vim server specified. Needed by
// unregister and enable and disable operations.
$vimServerRefs = $sdkExt->getVimServerRefs($vimName);
if (0 == count($vimServerRefs)) {
    exit("No vim server with name $vimName is found.\n");
}
$vimServerRef = $vimServerRefs[0];
$sdkVimServer = $service->createSDKObj($vimServerRef);

switch ($op)
{
    case 'unreg':   // for unregister Vim server operation
        $sdkExt->unregisterVimServer($vimServerRef);
        break;
    case 'en':     // for enable Vim server operation
        $sdkVimServer->enable();
        break;
    case 'dis':    // for disable Vim server operation
        $sdkVimServer->disable();
        break;
}


/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates registering, unregistering, enabling, disabling operations on a vim server.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php vimserver.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>  [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>       [req] User name in the form user@organization\n";
    echo "                                       for the vCloud Director.\n";
    echo "     -p|--pswd <password>       [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vimn <vimName>        [req] Specifying the name of the vim server to be registered.\n";
    echo "     -b|--vims <vimUrl>         [opt] URL of the vim server. Required when op=reg.\n";
    echo "     -c|--vimu <vimUser>        [opt] Username of the vim server. Required when op=reg.\n";
    echo "     -d|--vimp <vimPswd>        [opt] password of the host. Required when op=reg.\n";
    echo "     -e|--vsmn <vsmName>        [opt] Name of the vShield Manager (VSM) to be registered.\n";
    echo "     -f|--vsms <vsmUrl>         [opt] URL of the VSM. Required when op=reg.\n";
    echo "     -g|--vsmu <vsmUser>        [opt] Username of the VSM. Required when op=reg.\n";
    echo "     -h|--vsmp <vsmPswd>        [opt] Password of the VSM. Required when op=reg.\n";
    echo "     -o|--op <reg/unreg/en/dis> [opt] Operation on the vim server. Allows: reg, unreg, en, dis\n";
    echo "     -l|--list                  [opt] List registered vim servers.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php vimserver.php -s 127.0.0.1 -u admin@Org -p password -o=reg -a vimName -b=https://<vim ip>:443 -c=vimUser -d=vimPswd -f=https://<vsm ip>:443 -g=vsmUser -h=vsmPswd\n";
    echo "     # php vimserver.php -a vimName -o=unreg // unregister the vim server; using config.php to set login credentials\n";
    echo "     # php vimserver.php -a vimName -o=dis   // disable the vim server\n";
    echo "     # php vimserver.php -a vimName -o=en    // enable the vim server\n";
    echo "     # php vimserver.php -l                  // list registered vim servers.\n\n";
}
?>
