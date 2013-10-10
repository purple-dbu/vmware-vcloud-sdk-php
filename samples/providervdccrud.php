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
 * Should be system administrator.
 * Should contain atleast a vCenter with resource pools
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
$shorts .= "f:";
$shorts .= "g:";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "vim:",       //-a|--vim       [required]
    "pvdc:",      //-b|--pvdc      [required]
    "rp:",        //-c|--rp        [required]
    "stor:",      //-f|--stor      [required]
    "certpath:",  //-g|--certpath  [optional] local certificate path
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$pvdcName = null;
$rpMoRef = null;
$storageProfile = null;
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
        $rpMoRef = $opts['c'];
        break;
    case "rp":
        $rpMoRef = $opts['rp'];
        break;

    case "f":
        $storageProfile = $opts['f'];
        break;
    case "stor":
        $storageProfile = $opts['stor'];
        break;

    case "g":
        $certPath = $opts['g'];
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
    (true !== $list && (!isset($vimName) || !isset($pvdcName) ||
    !isset($storageProfile) || !isset($rpMoRef))))
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
    // vCloud login
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
    $vimserverRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vimRef->get_href());
    $vimServerOb = $service->createSDKObj($vimRef);
    $resourcePools = $vimServerOb->getResourcePools();
    if(empty($resourcePools))
    {
        exit("vCenter server do not contains any Resource Pool");
    }
    $dataStoreRefs = $resourcePools[0]->getDataStoreRefs();
    if (empty ($dataStoreRefs))
    {
        exit("vCenter server do not contains any Datastore VimObjects");
    }
    $vimObjectRef = $dataStoreRefs->getVimObjectRef();
    if (empty($vimObjectRef))
    {
        exit("vCenter server do not contains any Datastore"); 
    }
    $dsVimObject = $vimObjectRef[0];
    // Add provider vDC in vCloud Director
    echo "Adding provider vDC. \n";
    $providervDC = createProviderVdcParam($vimserverRef, $rpMoRef, $pvdcName, $storageProfile);
    $providervDC = $sdkExt->createProviderVdc($providervDC);
    $task=$providervDC->getTasks()->getTask();
    if (sizeof($task) > 0)
    {
        $service->waitForTask($task[0]);
    }
    echo "Added provider vDC : ".$providervDC->get_name() . "\n";

    // create provider vdc object
    $refs = $sdkExt->getVMWProviderVdcRefs($pvdcName);
    $ref = $refs[0];
    $vmwprovidervdcob = $service->createSDKObj($ref);

    // Updated provider vDC
    echo "Updating provider vDC. \n";
    $providervDC = updateProviderVdcParam($vimserverRef, $rpMoRef, $pvdcName . "_Updated", $dsVimObject);
    $providervDC = $vmwprovidervdcob->modify($providervDC);
    $tasks=$providervDC->getTasks();
    if (!is_null($tasks))
    {
        $task = $tasks->getTask();
        if (sizeof($task) > 0)
        {
            $service->waitForTask($task[0]);
        }
    }
    echo "Updated provider vDC : ".$providervDC->get_name() . "\n";

    // Get provider vDC
    echo "Get provider vDC: \n";
    getVMWProviderVdc($providervDC->get_name());

    // Delete provider vDC
    echo "Deleting provider vDC. \n";
    deleteVMWProviderVdc($providervDC->get_name());
    echo "Deleted provider vDC: ". $providervDC->get_name() . "\n";
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

/**
 * Create Provider Vdc
 *
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param string  $rpMoRef
 * @param string  $pvdcName
 * @param string  $storageProfile
 * @return VMware_VCloud_API_Extension_VMWProviderVdcType
 */
function createProviderVdcParam($vimserverRef, $rpMoRef, $pvdcName, $storageProfile)
{
    // set resource pool
    $vimObj = new VMware_VCloud_API_Extension_VimObjectRefType();
    $vimObj->setVimServerRef($vimserverRef);
    $vimObj->setMoRef($rpMoRef);
    $vimObj->setVimObjectType('RESOURCE_POOL');

    $rpRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
    $rpRefs->addVimObjectRef($vimObj);

    // create a provider vDC data object
    $pvdc = new VMware_VCloud_API_Extension_VMWProviderVdcParamsType();
    $pvdc->set_name($pvdcName);

    $pvdc->addStorageProfile($storageProfile);
    $pvdc->setResourcePoolRefs($rpRefs);
    $pvdc->setIsEnabled(true);
    $pvdc->setVimServer(array($vimserverRef));
    return $pvdc;
}

/**
 * Update ProviderVdc
 *
 * @param VMware_VCloud_API_ReferenceType  $vimserverRef
 * @param string  $rpMoRef
 * @param string  $pvdcName
 * @param VMware_VCloud_API_Extension_VimObjectRefType  $dsVimObject
 * @return VMware_VCloud_API_Extension_VMWProviderVdcType
 */
function updateProviderVdcParam($vimserverRef, $rpMoRef, $pvdcName, $dsVimObject)
{
    // set resource pool
    $vimObj = new VMware_VCloud_API_Extension_VimObjectRefType();
    $vimObj->setVimServerRef($vimserverRef);
    $vimObj->setMoRef($rpMoRef);
    $vimObj->setVimObjectType('RESOURCE_POOL');

    $rpRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
    $rpRefs->addVimObjectRef($vimObj);

    $dsRefs = new VMware_VCloud_API_Extension_VimObjectRefsType();
    $dsRefs->addVimObjectRef($dsVimObject);

    // create a provider vDC data object
    $pvdc = new VMware_VCloud_API_Extension_VMWProviderVdcType();
    $pvdc->set_name($pvdcName);
    $pvdc->setResourcePoolRefs($rpRefs);
    $pvdc->setDataStoreRefs($dsRefs);
    $pvdc->setIsEnabled(true);
    $pvdc->setVimServer(array($vimserverRef));
    return $pvdc;
}

/**
 * @param String  VMWProviderVdc Name   $name
 * @return array VMware_VCloud_API_ReferenceType object array
 */
function getVMWProviderVdc($name)
{
    try
    {
        global $sdkExt;
        $refs = $sdkExt->getVMWProviderVdcRefs($name);
        if (0 == count($refs))
        {
            exit(0);
        }
        foreach ($refs as $ref)
        {
            echo "href=" . $ref->get_href() . " type=" . $ref->get_type() .
                 " name=" . $ref->get_name() . "\n";
        }
    }
    catch(Exception $e)
    {
        echo $e->getMessage() . "\n";
    }
}

/**
 * @param String  VMWProviderVdc Name  $name
 *
 */
function deleteVMWProviderVdc($name)
{
    global $sdkExt,$service;
    $refs = $sdkExt->getVMWProviderVdcRefs($name);
    $ref = $refs[0];
    $vmwnetworkPoolob = $service->createSDKObj($ref);
    $vmwnetworkPoolob->disable();
    $vmwnetworkPoolob->delete();
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
    echo "                                          for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>               [req] Name of a registered Vim server in the vCloud Director.\n";
    echo "     -b|--pvdc <pvdcName>             [req] Name of the provider vDC to be created.\n";
    echo "     -c|--rp <rp>                     [req] MoRef of a resource pool.\n";
    echo "     -f|--stor <storageprofile>       [opt] Name of storage profile.\n";
    echo "     -g|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "     -l|--list                        [opt] List all provider vDC.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b pvdc -c resgroup-84 -f storageprofile\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b pvdc -c resgroup-84 -f storageprofile -g certificatepath\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b pvdc -c resgroup-84 -f storageprofile\n";
    echo "     # php createprovidervdc.php -a vim -b pvdc -c resgroup-84 -f storageprofile// using config.php to set login credentials\n";
    echo "     # php createprovidervdc.php -l// list all provider vDC\n\n";
}
?>
