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
 * Sample for recomposing a vApp, adding a vm from a vApp template to an
 * existing vApp.
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
$shorts .= "f:";
$shorts .= "g:";
$shorts .= "h:";

$longs  = array(
    "server:",    //-s|--server    [required]
    "user:",      //-u|--user      [required]
    "pswd:",      //-p|--pswd      [required]
    "sdkver:",    //-v|--sdkver    [required]
    "org:",       //-a|--org       [required]
    "vdc:",       //-b|--vdc       [required]
    "vapp:",      //-c|--vapp      [required]
    "temp:",      //-d|--temp      [required]
    "vm:",        //-e|--vm        [required]
    "net:",       //-f|--net       [required]
    "name:",      //-g|--name      [required]
    "certpath:",  //-h|--certpath  [optional] local certificate path
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$vdcName = null;
$vAppName = null;
$vAppTempName = null;
$vmName = null;
$orgVdcNetName = null;
$recomposedName = null;
$certPath = null;
$recomposedDesc = "Reomposed vApp description.";  // hard-coded

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
        $vAppName = $opts['c'];
        break;
    case "vapp":
        $vAppName = $opts['vapp'];
        break;

    case "d":
        $vAppTempName = $opts['d'];
        break;
    case "temp":
        $vAppTempName = $opts['temp'];
        break;

    case "e":
        $vmName = $opts['e'];
        break;
    case "vm":
        $vmName = $opts['vm'];
        break;

    case "f":
        $orgVdcNetName = $opts['f'];
        break;
    case "net":
        $orgVdcNetName = $opts['net'];
        break;

    case "g":
        $recomposedName = $opts['g'];
        break;
    case "name":
        $recomposedName = $opts['name'];
        break;

    case "h":
        $certPath = $opts['h'];
        break;
    case "certpath":
        $certPath = $opts['certpath'];
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd) || !isset($sdkversion)) ||
    !isset($orgName) || !isset($vdcName) || !isset($vAppName) ||
    !isset($vAppTempName) || !isset($vmName) || !isset($orgVdcNetName) ||
    !isset($recomposedName))
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

    // create an SDK Org object
    $orgRefs = $service->getOrgRefs($orgName);
    if (0 == count($orgRefs))
    {
        exit("No organization with name '$orgName' is found\n");
    }
    $orgRef = $orgRefs[0];
    $sdkOrg = $service->createSDKObj($orgRef);

    // create an SDK vDC object
    $vdcRefs = $sdkOrg->getVdcRefs($vdcName);
    if (0 == count($vdcRefs))
    {
        exit("No vDC with name '$vdcName' is found\n");
    }
    $vdcRef = $vdcRefs[0];
    $sdkVdc = $service->createSDKObj($vdcRef);

    // get a reference to a vApp to be recomposed in the vDC
    $vAppRefs = $sdkVdc->getVAppRefs($vAppName);
    if (0 == count($vAppRefs))
    {
        exit("No vApp with name '$vAppName' is found\n");
    }
    $vAppRef = $vAppRefs[0];
    // create an SDK vApp object
    $sdkVApp = $service->createSDKObj($vAppRef);

    $vAppVMs=$sdkVApp->getContainedVmRefs();
    if (0 == count($vAppVMs))
    {
        exit("No vm is contained in vApp '$vAppName'\n");
    }
    $sdkVm = $service->createSDKObj($vAppVMs[0]->get_href());

    $netConSet = $sdkVm->getNetworkConnectionSettings();
    $netCon = $netConSet->getNetworkConnection();
    if (0 == count($netCon))
    {
        exit("No network connection is contained in vAppVM '$vAppVMs[0]'\n");
    }
    $netCon = $netCon[0];

    $vappNetConfigs = $sdkVApp->getNetworkConfigSettings()->getNetworkConfig();
    if (0 == count($vappNetConfigs))
    {
        exit("No vApp network is contained in vApp '$vAppName'\n");
    }
    $vappNetConfig = $vappNetConfigs[0];
    // get a reference to a vm in another vApp/vApp template to be added
    $vAppTempRefs = $sdkVdc->getVAppTemplateRefs($vAppTempName);
    if (0 == count($vAppTempRefs))
    {
        exit("No vApp template with name '$vAppTempName' is found\n");
    }
    $vAppTempRef = $vAppTempRefs[0];

    // get a reference to a vm in the vApp template
    $vAppTemp = $service->get($vAppTempRef->get_href());

    $vms = $vAppTemp->getChildren()->getVm();
    if (0 == count($vms))
    {
        exit("No VM is contained in vApp template '$vAppTempName'\n");
    }
    $vm = null;
    foreach ($vms as $v)
    {
        if ($vmName == $v->get_name())
        {
            $vm = $v;
            break;
        }
    }

    $vappTemplateVM = $vm;
    $vmRef1 = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vm->get_href());
    $sdkNet = $sdkVdc->getAvailableNetworks($orgVdcNetName);

    if (0 == count($sdkNet))
    {
        exit("Specified organization '$orgName' does not have organization " .
               "network '$orgVdcNetName' configured\n");
    }
    $sdkNet = $sdkNet[0];
    $netRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj($sdkNet->get_href(),'reference',$sdkNet->get_type(), $sdkNet->get_name());
    $conf = $sdkNet->getConfiguration();

    $info = new VMware_VCloud_API_OVF_Msg_Type();
    $info->set_valueOf("Configuration parameters for logical networks");

    $netconf = new VMware_VCloud_API_VAppNetworkConfigurationType();
    $netconf->set_networkName($vappNetConfig->get_networkName());
    $netconf->setConfiguration($conf);
    $netconf->getConfiguration()->setParentNetwork($netRef);

    $section = new VMware_VCloud_API_NetworkConfigSectionType();
    $section->setInfo($info);
    $section->setNetworkConfig(array($netconf));

    $iparams = new VMware_VCloud_API_InstantiationParamsType();
    $iparams->setSection(array($section));

    /**
     * VM and its Nic settings.
     */
    // Creating a vm item which needs to be added.
    $sourcedItem = new VMware_VCloud_API_SourcedCompositionItemParamType();
    $ref1 = $vmRef1;
    // set a new name for the vm which will be used in the recomposed vApp.
    $ref1->set_name('AddedVM' . time());   // hard-coded
    $sourcedItem->setSource($ref1);

    // get the nics from the vapp template vm.
    $sect = $vappTemplateVM->getSection();

    $nics = null;
    foreach ($sect as $s)
    {
        if ($s instanceof VMware_VCloud_API_NetworkConnectionSectionType)
        {
            $nics = $s;
            break;
        }
    }
    $nic = $nics->getNetworkConnection();
    $nic= $nic[0];
    // set the ip address allocation mode.
    $nic->setIpAddressAllocationMode($netCon->getIpAddressAllocationMode());
    // set the network name to which the nic needs to be attached to.
    $nic->set_network($netCon->get_network());
    // set the nic index which needs to be the primary nic.
    $priNetConIndex = $netConSet->getPrimaryNetworkConnectionIndex();
    $nics->setPrimaryNetworkConnectionIndex($priNetConIndex);

    // Creating instantiation params for adding the network connection
    // section - nics
    $vmInstantiationParams = new VMware_VCloud_API_InstantiationParamsType();
    $vmInstantiationParams->setSection(array($nics));
    $sourcedItem->setInstantiationParams($vmInstantiationParams);
    // Recompose VApp params
    $params = new VMware_VCloud_API_RecomposeVAppParamsType();
    //$params->set_name($recomposedName);
    $params->setDescription($recomposedDesc);
    $params->setInstantiationParams($iparams);
    $params->setSourcedItem(array($sourcedItem));
    $sdkVApp->recompose($params);
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
    echo "     This sample demonstrates recomposing a vApp, adding a VM from a vApp template to an existing vApp\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php recomposevapp.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                             for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>               [req] Name of an existing organization in the vCloud Director.\n";
    echo "     -b|--vdc <vdcName>               [req] Name of an existing vDC in the organization.\n";
    echo "     -c|--vapp <vAppName>             [req] Name of an existing vApp to be recomposed.\n";
    echo "     -d|--temp <tempName>             [req] Name of an existing vApp template which has VM(s) in it.\n";
    echo "     -e|--vm <vmName>                 [req] Name of an existing VM which is in the specified vApp template.\n";
    echo "     -f|--net <netName>               [req] Name of an existing organization network in the organization.\n";
    echo "     -g|--name <name>                 [req] Name of the recomposed vApp.\n";
    echo "     -h|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php recomposevapp.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b vdc -c vapp -d temp -e vm -f net -g name\n";
    echo "     # php recomposevapp.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -a org -b vdc -c vapp -d temp -e vm -f net -g name  -h certificatepath\n";
    echo "     # php recomposevapp.php -a org -b vdc -c vapp -d temp -e vm -f net -g name // using config.php to set login credentials\n\n";
}
?>

