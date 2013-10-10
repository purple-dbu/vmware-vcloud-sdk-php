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
 * This sample lists the tenant and the provider resources.
 *
 * @author Ecosystem Engineering
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "v:";
$shorts .= "c::";


$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "certpath::", //-c|--certpath  [optional] local certificate path
);

$opts = getopt($shorts, $longs);


// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

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

    case "c":
        $certPath = $opts['c'];
        break;
    case "certpath":
        $certPath = $opts['certpath'];
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

   /**
    * Inventory Sample.
    *
    * @throws VMware_VCloud_SDK_Exception
    */

    // org admin/users - lists the tenant resources
    tenantResourcesInventory();

    // system admin - lists the provider resources
    providerResourcesInventory();

}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

/**
 * Lists all the tenant resources. This method can be run by a sys admin/org
 * admin/org user. The tenant resources are Org, vdc, vdc network, media,
 * vapp, vm, vapptemplate, catalog etc.
 *
 * This method can be expensive in traversing the whole inventory. Instead
 * you can also use the query service to list all the objects. Refer to
 * {@link QueryAllvApps} or {@link QueryAllVms}
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function tenantResourcesInventory()
{
    global $service;
    echo "-------------------------------\n";
    echo "Tenant/Org Resources Inventory\n";
    echo "-------------------------------\n";
    $orgRefs=$service->getOrgRefs();
    foreach ($orgRefs as $orgRef)
    {
        echo "(Org) " . $orgRef->get_name() . "\n";
        $org = $service->createSDKObj($orgRef);
        $catalogRefs = $org->getCatalogRefs();
        if (!empty($catalogRefs))
        {
            foreach ($catalogRefs as $catalogRef)
            {
                echo "   (Catalog) " . $catalogRef->get_name() . "\n";
            }
        }
        $vdcRefs = $org->getVdcRefs();
        if (!empty ($vdcRefs))
        {
            foreach ($vdcRefs as $vdcRef)
            {
                echo "   (Vdc) " . $vdcRef->get_name() . "\n";
                try
                {
                    $vdc = $service->createSDKObj($vdcRef);
                    $mediaRefs = $vdc->getMediaRefs();
                    if (!empty ($mediaRefs))
                    {
                        foreach ($mediaRefs as $mediaRef)
                        {
                            echo "      (Media) " . $mediaRef->get_name() . "\n";
                        }
                    }
                    $vappRefs = $vdc->getVAppRefs();
                    if (!empty ($vappRefs))
                    {
                        foreach ($vappRefs as $vappRef)
                        {
                            echo "      (VApp) " . $vappRef->get_name() . "\n";
                            try
                            {
                                $vapp = $service->createSDKObj($vappRef);
                                $vmRefs = $vapp->getContainedVAppRefs();
                                foreach ($vmRefs as $vmRef)
                                {
                                    echo "         (VM) " . $vmRef->get_name() . "\n";
                                }
                            }
                            catch (Exception $e)
                            {
                                echo '         VM does not exist.';
                            }
                        }
                    }
                    $vAppTemplateRefs = $vdc->getVAppTemplateRefs();
                    if (!empty ($vAppTemplateRefs))
                    {
                        foreach ($vAppTemplateRefs as $vAppTemplateRef)
                        {
                            echo "      (VAppTemplate) " . $vAppTemplateRef->get_name() . "\n";
                            try
                            {
                                $vAppTemplate = $service->createSDKObj($vAppTemplateRef);
                                $vmRefs = $vAppTemplate->getVAppTemplate()->getChildren()->getVm();
                                foreach ($vmRefs as $vmRef)
                                {
                                    echo "         (VM) " . $vmRef->get_name() . "\n";
                                }
                                }
                            catch (Exception $e)
                            {
                                echo "      VAppTemplate is not set.\n";
                            }
                        }
                    }
                    $networkRefs = $vdc->getAvailableNetworkRefs();
                    if (!empty ($networkRefs))
                    {
                        foreach ($networkRefs as $networkRef)
                        {
                            echo "      (VdcNetwork) " . $networkRef->get_name() . "\n";
                        }
                    }
                }
                catch (Exception $e)
                {
                    echo "   Vdc does not exist.\n";
                }
            }
        }
    }
}

/**
 * Lists all the provider resources. This method requires sys admin
 * privileges. The provider resources can be provider vdc, external network,
 * network pool, host, datastore etc.
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function providerResourcesInventory()
{
    global $service;
    echo "----------------------------\n";
    echo "Provider Resources Inventory\n";
    echo "----------------------------\n";
    $adminExtenion = $service->createSDKExtensionObj();
    $vmwProvVdcRefs = $adminExtenion->getVMWProviderVdcRefs();
    foreach ($vmwProvVdcRefs as $vmwProvVdcRef)
    {
        echo "(ProviderVdc) " . $vmwProvVdcRef->get_name() . "\n";
    }
    $vmwExternalNetworkRefs = $adminExtenion->getVMWExternalNetworkRefs();
    foreach ($vmwExternalNetworkRefs as $vmwExternalNetworkRef)
    {
        echo "(ExternalNetwork) " . $vmwExternalNetworkRef->get_name() . "\n";
    }
    $vmwNetworkPoolRefs = $adminExtenion->getVMWNetworkPoolRefs();
    foreach ($vmwNetworkPoolRefs as $vmwNetworkPoolRef)
    {
        echo "(NetworkPool) " . $vmwNetworkPoolRef->get_name() . "\n";
    }
    $hostRefs = $adminExtenion->getHostRefs();
    foreach ($hostRefs as $hostRef)
    {
        echo "(Host) " . $hostRef->get_name() . "\n";
    }
    $datastoreRefs = $adminExtenion->getDatastoreRefs();
    foreach ($datastoreRefs as $datastoreRef)
    {
        echo "(Datastore) " . $datastoreRef->get_name() . "\n";
    }
}

/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample lists the tenant and the provider resources.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php inventory.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -c|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php inventory.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -c=certificatepath\n";
    echo "     # php inventory.php -c=certificatepath // using config.php to set login credentials\n\n";
}
?>
