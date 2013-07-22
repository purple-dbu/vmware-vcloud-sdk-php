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
 * Sample for recomposing a vApp, adding a vm from a vApp template to an
 * existing vApp.
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b:";
$shorts .= "c:";
$shorts .= "d:";
$shorts .= "e:";
$shorts .= "f:";
$shorts .= "g:";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "org:",       //-a|--org    [required]
    "vdc:",       //-b|--vdc    [required]
    "vapp:",      //-c|--vapp   [required]
    "temp:",      //-d|--temp   [required]
    "vm:",        //-e|--vm     [required]
    "net:",       //-f|--net    [required]
    "name:",      //-g|--name   [required]
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$vdcName = null;
$vAppName = null;
$vAppTempName = null;
$vmName = null;
$orgNetName = null;
$recomposedName = null;
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
        $orgNetName = $opts['f'];
        break;
    case "net":
        $orgNetName = $opts['net'];
        break;

    case "g":
        $recomposedName = $opts['g'];
        break;
    case "name":
        $recomposedName = $opts['name'];
        break;
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd)) ||
    !isset($orgName) || !isset($vdcName) || !isset($vAppName) ||
    !isset($vAppTempName) || !isset($vmName) || !isset($orgNetName) ||
    !isset($recomposedName))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

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

$vmRef1 = VMware_VCloud_SDK_Helper::createReferenceTypeObj($vm->get_href());

// configure the recomposed vApp network settings
$netRefs = $sdkOrg->getOrgNetworkRefs($orgNetName);
if (0 == count($netRefs))
{
    exit("Specified organization '$orgName' does not have organization " .
           "network '$orgNetName' configured\n");
}
$netRef = $netRefs[0];
$pnetwkRef = VMware_VCloud_SDK_Helper::createReferenceTypeObj(
                                        $netRef->get_href(), 'ParentNetwork');

$info = new VMware_VCloud_API_OVF_Msg_Type();
$info->set_valueOf("Configuration parameters for logical networks");

$conf = new VMware_VCloud_API_NetworkConfigurationType();
$conf->setParentNetwork($pnetwkRef);
$conf->setFenceMode('bridged');  // hard-coded

$netconf = new VMware_VCloud_API_VAppNetworkConfigurationType();
$netconf->set_networkName('VM Network');
$netconf->setConfiguration($conf);
$netconf->setIsDeployed(true);

$section = new VMware_VCloud_API_NetworkConfigSectionType();
$section->setInfo($info);
$section->setNetworkConfig(array($netconf));

$iparams = new VMware_VCloud_API_InstantiationParamsType();
$iparams->setSection(array($section));

$sourcedItem1 = new VMware_VCloud_API_SourcedCompositionItemParamType();
$ref1 = $vmRef1;
// set a new name for the vm which will be used in the recomposed vApp.
$ref1->set_name('src_' . time());   // hard-coded
$sourcedItem1->setSource($ref1);
$sourcedItem1->set_sourceDelete(false);

$sourceItems = array($sourcedItem1);

$params = new VMware_VCloud_API_RecomposeVAppParamsType();
$params->set_name($recomposedName);
$params->setDescription($recomposedDesc);
$params->setInstantiationParams($iparams);
$params->setSourcedItem($sourceItems);
//echo $params->export() . "\n";
// recompose the vApp
$sdkVApp->recompose($params);


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
    echo "     # php recomposevapp.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>        [req] Name of an existing organization in the vCloud Director.\n";
    echo "     -b|--vdc <vdcName>        [req] Name of an existing vDC in the organization.\n";
    echo "     -c|--vapp <vAppName>      [req] Name of an existing vApp to be recomposed.\n";
    echo "     -d|--temp <tempName>      [req] Name of an existing vApp template which has VM(s) in it.\n";
    echo "     -e|--vm <vmName>          [req] Name of an existing VM which is in the specified vApp template.\n";
    echo "     -f|--net <netName>        [req] Name of an existing organization network in the organization.\n";
    echo "     -g|--name <name>          [req] Name of the recomposed vApp.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php recomposevapp.php -s 127.0.0.1 -u admin@Org -p password -a org -b vdc -c vapp -d temp -e vm -f net -g name\n";
    echo "     # php recomposevapp.php -a org -b vdc -c vapp -d temp -e vm -f net -g name // using config.php to set login credentials\n\n";
}
?>
    
