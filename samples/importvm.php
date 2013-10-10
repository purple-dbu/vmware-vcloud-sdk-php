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
 * Sample for importing vm from vSphere to vDC as vApp or vApp template. 
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
$shorts .= "i:";
$shorts .= "j:";

$longs  = array(
    "server:",    //-s|--server    [required] vCloud Director server IP/hostname
    "user:",      //-u|--user      [required] vCloud Director login username
    "pswd:",      //-p|--pswd      [required] vCloud Director login password
    "sdkver:",    //-v|--sdkver    [required]
    "vim:",       //-a|--vim       [required]
    "org:",       //-b|--org       [required]
    "vdc:",       //-c|--vdc       [required]
    "vapp:",      //-d|--vapp      [required]
    "moref:",     //-e|--moref     [required]
    "vm::",       //-f|--vm
    "cat::",      //-g|--cat       [required when import is set as 'template']
    "desc::",     //-h|--desc
    "import:",    //-i|--import    [required] allows: vapp, template
    "certpath:",  //-j|--certpath  [optional] local certificate path
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$orgName = null;
$vdcName = null;
$vAppName = null;
$moref = null;
$import = null;
$catName = null;
$certPath = null;
$vmName = "vm_" . time();   // default
$description = "vApp imported from Vm.";  // default

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
        $orgName = $opts['b'];
        break;
    case "org":
        $orgName = $opts['org'];
        break;

    case "c":
        $vdcName = $opts['c'];
        break;
    case "vdc":
        $vdcName = $opts['vdc'];
        break;

    case "d":
        $vAppName = $opts['d'];
        break;
    case "vapp":
        $vAppName = $opts['vapp'];
        break;

    case "e":
        $moref = $opts['e'];
        break;
    case "moref":
        $moref = $opts['moref'];
        break;

    case "f":
        $vmName = $opts['f'];
        break;
    case "vm":
        $vmName = $opts['vm'];
        break;

    case "g":
        $catName = $opts['g'];
        break;
    case "cat":
        $catName = $opts['cat'];
        break;

    case "h":
        $description = $opts['h'];
        break;
    case "desc":
        $description = $opts['desc'];
        break;

    case "i":
        $import = $opts['i'];
        break;
    case "import":
        $import = $opts['import'];
        break;

    case "j":
        $certPath = $opts['j'];
        break;
    case "certpath":
        $certPath = $opts['certpath'];
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd) || !isset($sdkversion)) ||
    (!isset($vimName) || !isset($orgName) || !isset($vdcName) ||
     !isset($vAppName) || !isset($moref) || !isset($import)) ||
    ('template' == $import && !isset($catName)))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}
if (!in_array($import, array('vapp', 'template')))
{
    exit("$import is not supported, allowed import value are 'vapp', 'template'");
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

    // create an SDK Vim Server object
    $vimRefs = $sdkExt->getVimServerRefs($vimName);
    if (0 == count($vimRefs))
    {
        exit("No vim server with name $vimName is found\n");
    }
    $vimRef = $vimRefs[0];
    $sdkVimServer = $service->createSDKObj($vimRef);

    // get reference of the vDC where to import
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
    $vdcRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vdcRef->get_href());

    // ops
    switch ($import)
    {
        case 'vapp':
            $params = new VMware_VCloud_API_Extension_ImportVmAsVAppParamsType();
            $params->set_name($vAppName);
            $params->setDescription($description);
            $params->setVmName($vmName);
            $params->setVmMoRef($moref);
            $params->setVdc($vdcRef);
            echo "Importing a VM from vSphere to a vDC as a vApp...\n";
            $sdkVimServer->importVmAsVApp($params);
            echo "Successfully imported a VM from vSphere to a vDC as a vApp.\n";
            break;
        case 'template':
            // get catalog reference
            $catRefs = $sdkOrg->getCatalogRefs($catName);
            if (0 == count($catRefs))
            {
                exit("No catalog with name $catName is found\n");
            }
            $catRef = $catRefs[0];
            $catRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj(
                                                              $catRef->get_href());

            $params = new VMware_VCloud_API_Extension_ImportVmAsVAppTemplateParamsType();
            $params->set_name($vAppName);
            $params->setDescription($description);
            $params->setVmName($vmName);
            $params->setVmMoRef($moref);
            $params->setVdc($vdcRef);
            $params->setCatalog($catRef);
            echo "Importing a VM from vSphere to a vDC as a vApp template...\n";
            $sdkVimServer->importVmAsVAppTemplate($params);
            echo "Successfully imported a VM from vSphere to a vDC as a vApp template.\n";
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
    echo "     This sample demonstrates creating a new organization in vCloud Director.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php importvm.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>   [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>        [req] User name in the form user@organization\n";
    echo "                                        for the vCloud Director.\n";
    echo "     -p|--pswd <password>        [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>    [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>          [req] Name of a registered vim server in vCloud Director.\n";
    echo "     -b|--org <orgName>          [req] Name of an existing organization in vCloud Director.\n";
    echo "     -c|--vdc <vdcName>          [req] Name of an existing vDC in the organization.\n";
    echo "     -d|--vapp <vappName>        [req] Name of the vApp or vApp template to be created.\n";
    echo "     -e|--moref <moref>          [req] MoRef of the vm or template in vSphere.\n";
    echo "     -f|--vm <vmName>            [opt] New name of the VM when it gets imported to vApp or vAppTemplate.\n";
    echo "     -g|--cat <catName>          [req] Name of the catalog where to add the vAppTemplate.\n";
    echo "     -h|--desc <description>     [opt] Description of the vApp or vApp template.\n";
    echo "     -i|--import <vapp/template> [req] Specify import as vApp or vApp template.\n";
    echo "     -j|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php importvm.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b org -c vdc -d vapp -e vm-13 -g catalog -i vapp\n";
    echo "     # php importvm.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a vim -b org -c vdc -d vapp -e vm-13 -g catalog -i vapp -j certificatepath\n";
    echo "     # php importvm.php -a vim -b org -c vdc -d vapp -e vm-13 -g cat -i template // using config.php to set login credentials\n\n";
}
?>
