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
 * Sample for importing vm from vSphere to vDC as vApp or vApp template. 
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
$shorts .= "f::";
$shorts .= "g::";
$shorts .= "h::";
$shorts .= "i:";

$longs  = array(
    "server:",    //-s|--server [required] vCloud Director server IP/hostname
    "user:",      //-u|--user   [required] vCloud Director login username
    "pswd:",      //-p|--pswd   [required] vCloud Director login password
    "vim:",       //-a|--vim    [required]
    "org:",       //-b|--org    [required]
    "vdc:",       //-c|--vdc    [required]
    "vapp:",      //-d|--vapp   [required]
    "moref:",     //-e|--moref  [required]
    "vm::",       //-f|--vm
    "cat::",      //-g|--cat    [required when import is set as 'template']
    "desc::",     //-h|--desc
    "import:",    //-i|--import [required] allows: vapp, template
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
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd)) ||
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

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

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
        $sdkVimServer->importVmAsVApp($params);
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
        $sdkVimServer->importVmAsVAppTemplate($params);
        break;
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
    echo "     # php importvm.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>   [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>        [req] User name in the form user@organization\n";
    echo "                                        for the vCloud Director.\n";
    echo "     -p|--pswd <password>        [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>          [req] Name of a registered vim server in vCloud Director.\n";
    echo "     -b|--org <orgName>          [req] Name of an existing organization in vCloud Director.\n";
    echo "     -c|--vdc <vdcName>          [req] Name of an existing vDC in the organization.\n";
    echo "     -d|--vapp <vappName>        [req] Name of the vApp or vApp template to be created.\n";
    echo "     -e|--moref <moref>          [req] MoRef of the vm or template in vSphere.\n";
    echo "     -f|--vm <vmName>            [opt] New name of the VM when it gets imported to vApp or vAppTemplate.\n";
    echo "     -g|--cat <catName>          [opt] Name of the catalog where to add the vAppTemplate.\n";
    echo "     -h|--desc <description>     [opt] Description of the vApp or vApp template.\n";
    echo "     -i|--import <vapp/template> [req] Specify import as vApp or vApp template.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php importvm.php -s 127.0.0.1 -u admin@Org -p password -a vim -b org -c vdc -d vapp -e vm-13 -i vapp\n";
    echo "     # php importvm.php -a vim -b org -c vdc -d vapp -e vm-13 -g=cat -i template // using config.php to set login credentials\n\n";
}
?>
