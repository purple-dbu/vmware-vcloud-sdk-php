<?php
/**
 * VMware vCloud SDK for PHP
 *
 * PHP version 5
 * *******************************************************
 * Copyright VMware, Inc. 2010-2012.  All Rights Reserved.
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

/**
 * The Hello vCloud sample
 *
 *  The sample demonstrates logging in, browsing an organization,
 *  finding a template, getting vDC, instantiating the template, deploying
 *  and powering on the vApp, uploading the vAppTemplate, getting information about the vApp, and
 *  finally deleting the vApp and logging out to the vCloud.
 */

// add library to the include_path
set_include_path(implode(PATH_SEPARATOR, array('.','../library',
                         get_include_path(),)));

require_once 'VMware/VCloud/Helper.php';
require_once dirname(__FILE__) . '/config.php';
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";
$shorts .= "o::";
$shorts .= "d::";
$shorts .= "g::";
$shorts .= "i::";
$shorts .= "a::";
$shorts .= "n:";
$shorts .= "v:";
$shorts .= "b::";
$shorts .= "m::";
$shorts .= "h";

$longs  = array(
    "server:",       //-s|--server   [required]
    "user:",         //-u|--user     [required]
    "pswd:",         //-p|--pswd     [required]
    "org::",         //-o|--org
    "vdc::",         //-d|--vdc
    "catalog::",     //-g|--catalog
    "item::",        //-i|--item
    "vapp::",        //-a|vapp
    "name:",         //-n|--name     [required]
    "ovf:",          //-v|--ovf      [required]
    "des::",         //-b|--des
    "manifest::",    //-m|--manifest
    "help"           //-h|--help
);
$opts = getopt($shorts, $longs);
#var_dump($opts);

// Initialize parameters
$httpConfig = array(
                    'ssl_verify_peer'=>false,
                    'ssl_verify_host'=>false
                   );
#$server = null;
#$user = null;
#$pswd = null;
$orgName = null;
$vdcName = null;
$catalogName = null;
$catalogItemName = null;
$vappName = 'hellovcloudVApp';
$vappTempName= null;
$ovfDescriptorPath= null;
$description= null;
$manifestRequired= null;

// loop through command arguments
foreach (array_keys($opts) as $opt) switch ($opt)
{
    case "h":
    case "help":
        usage();
        exit(0);

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

    case "o":
        $orgName = $opts['o'];
        break;
    case "org":
        $orgName = $opts['org'];
        break;

    case "d":
        $vdcName = $opts['d'];
        break;
    case "vdc":
        $vdcName = $opts['vdc'];
        break;

    case "g":
        $catalogName = $opts['g'];
        break;
    case "catlog":
        $catalogName = $opts['catlog'];
        break;

    case "i":
        $catalogItemName = $opts['i'];
        break;
    case "item":
        $catalogItemName = $opts['item'];
        break;

    case "a":
        $vappName = $opts['a'];
        break;
    case "vapp":
        $vappName = $opts['vapp'];
        break;

    case "n":
        $vappTempName = $opts['n'];
        break;
    case "name":
        $vappTempName = $opts['name'];
        break;

    case "v":
        $ovfDescriptorPath = $opts['v'];
        break;
    case "ovf":
        $ovfDescriptorPath = $opts['ovf'];
        break;

    case "b":
        $description = $opts['b'];
        break;
    case "des":
        $description = $opts['des'];
        break;

    case "m":
        $manifestRequired = $opts['m'];
        break;
    case "manifest":
        $manifestRequired = $opts['manifest'];
        break;

}

if (!$server || !$user || !$pswd)
{
    usage();
    exit(1);
}

$auth = array('username'=>$user, 'password'=>$pswd);
try
{
    echo "###############################################\n";
    echo " 1. Logging in\n";
    echo "###############################################\n";
    // Create a service object";
    $service = VMware_VCloud_SDK_Service::getService();
    // Login to the service portal, parameters are set from command line
    $service->login($server, $auth, $httpConfig);

    echo "###############################################\n";
    echo " 2. Browsing an organization\n";
    echo "###############################################\n";
    //   a. Get the organization objects (in vCloud data object format).
    $orgs = $service->getOrgs($orgName);
    //   b. Get an organization object and display the content.
    checkValue($orgs, "organization $orgName");
    echo $orgs[0]->export();

    echo "###############################################\n";
    echo " 3. Finding a vApp template\n";
    echo "###############################################\n";
    //   a. Get a reference to the organization.
    $orgRefs = $service->getOrgRefs($orgName);
    //   b. Create an SDK organization object.
    $sdkOrg = $service->createSDKObj($orgRefs[0]);
    //   c. Get a reference to the catalog in the organization
    $catRefs = $sdkOrg->getCatalogRefs($catalogName);
    checkValue($catRefs, "catalog $catalogName");
    //   d. Create an SDK catalog object.
    $sdkCat = $service->createSDKObj($catRefs[0]);
    //   e. Get a catalog data object.
    $cat = $sdkCat->getCatalog();
    //   f. Display the catalog content.
    echo $cat->export() . "\n";
    //   g. Get a catalog item data object in the catalog.
    $catalogItems = $sdkCat->getCatalogItems($catalogItemName);
    checkValue($catalogItems, "catalog item $catalogItemName");
    //   h. Display the content of the catalog item.
    echo $catalogItems[0]->export() . "\n";
    //   i. Get a reference to the catalog item (vAppTemplate) in the catalog.
    $vAppTemplateRef = $catalogItems[0]->getEntity();
    checkValue($vAppTemplateRef, "entity $catalogItemName");
    //   j. Display the content of the vAppTemplate reference object.
    echo $vAppTemplateRef->export() . "\n";

    echo "###############################################\n";
    echo " 4. Getting information about a vDC\n";
    echo "###############################################\n";
    //   a. Get references to the vDC in an organization
    $vdcRefs = $sdkOrg->getVdcRefs($vdcName);
    checkValue($vdcRefs, "vDC $vdcName");
    //   b. Create an SDK vDC object.
    $sdkVdc = $service->createSDKObj($vdcRefs[0]);
    //   c. Get a vDC data object
    $vdc = $sdkVdc->getVdc();
    //   d. Display the content of the vDC data object.
    echo $vdc->export();

    echo "###############################################\n";
    echo " 5. Instantiating the vApp template in the vDC\n";
    echo "###############################################\n";
    //   a. Instantiate the vApp template with default settings
    $vApp = $sdkVdc->instantiateVAppTemplateDefault($vappName,
                                                    $vAppTemplateRef);
    //   b. Display the returned vApp data object
    echo $vApp->export() . "\n";
    //   c. Get task
    $tasks = $vApp->getTasks()->getTask();
    if ($tasks)
    {
        $task = $tasks[0];
        $service->waitForTask($task);
        // refetch the vApp.
        $vApp = $service->refetch($vApp);
        echo $vApp->export();
    }

    echo "###############################################\n";
    echo " 6. Deploying and powering on the vApp\n";
    echo "###############################################\n";
    //   a. Get a reference to the newly created vApp from vDC.
    $vAppRefs = $sdkVdc->getVAppRefs($vappName);
    checkValue($vAppRefs, "vApp $vappName");
    //   b. Create an SDK vApp object.
    $sdkVApp = $service->createSDKObj($vAppRefs[0]);
    //   c. Create a VMware_VCloud_API_DeployVAppParamsType data object.
    $params = createDeployVAppParamsTypeObj();
    //   d. Deploy and power on the vApp.
    $task = $sdkVApp->deploy($params);
    //   e. Wait for the task to finish.
    $service->waitForTask($task);

    echo "###############################################\n";
    echo " 7. Uploading the vAppTemplate\n";
    echo "###############################################\n";
    //   a. Get a reference to the VdcStorageProfile from vDC.
    $vdcrefs=$sdkVdc->getVdcStorageProfileRefs();
    $vdcStorageProfileRef=$vdcrefs[0];
    //   b. Get a reference to the Catalog from org.
    $catRefs = $sdkOrg->getCatalogRefs($catalogName);
    $catalogRef=$catRefs[0];
    //   c. Upload vApp template from an OVF pacakage.
    $sdkVdc->uploadOVFAsVAppTemplate($vappTempName, $ovfDescriptorPath, $description, $manifestRequired, $vdcStorageProfileRef, $catalogRef);

    echo "###############################################\n";
    echo " 8. Getting information about the vApp\n";
    echo "###############################################\n";
    //   a. Retrieve the vApp data object.
    $vApp = $sdkVApp->getVApp();
    //   b. Display the content of the vApp.
    echo $vApp->export();

    echo "###############################################\n";
    echo " 9. Deleting the vApp\n";
    echo "###############################################\n";
    //   a. Create a VMware_VCloud_API_DeployVAppParamsType data object.
    $uParams = createUnDeployVAppParamsTypeObj();
    //   b. Power off and undeploy the vApp. Wait for the task to finish
    $task = $sdkVApp->undeploy($uParams);
    $service->waitForTask($task);
    //   c. Delete the vApp.
    $sdkVApp->delete();

    echo "###############################################\n";
    echo " 10. Logging out\n";
    echo "###############################################\n";
    $service->logout();
}
catch (Exception $e)
{
    echo $e->getMessage();
    echo "\n";
}


/**
 * Helper function to create a VMware_VCloud_API_DeployVAppParamsType object.
 */
function createDeployVAppParamsTypeObj($powerOn=true,
                                       $deploymentLeaseSeconds=null)
{
    $obj = new VMware_VCloud_API_DeployVAppParamsType();
    $obj->set_powerOn($powerOn);
    $obj->set_deploymentLeaseSeconds($deploymentLeaseSeconds);
    return $obj;
}

/**
 * Helper function to create a VMware_VCloud_API_UnDeployVAppParamsType object.
 */
function createUndeployVAppParamsTypeObj($action='default')
{
    $obj = new VMware_VCloud_API_UnDeployVAppParamsType();
    $obj->setUndeployPowerAction($action);
    return $obj;
}

/**
 * Helper function to check the given value.  If value is null (scalar) or
 * empty (array), print the message and exit the sample program. If the value
 * is not null or empty, return.
 */
function checkValue($value, $message)
{
    if (null==$value || empty($value))
    {
        exit ("No $message was found, exit.\n");
    }
    return;
}

/**
 * Helper function to convert a string to an array.
 */
function stringToArray($string, $delimiter = ',', $kv = '=>')
{
    if ($a = explode($delimiter, $string)) //create element array
    {
        foreach ($a as $e)
        {
            if ($pos = strpos($e, $kv))
            {
                $ka[trim(substr($e, 0, $pos))] = trim(substr($e, $pos + strlen($kv)));
            }
            else
            {
                $ka[] = trim($e);
            }
        }
        return $ka;
     }
}

/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates a work flow in vCloud by using vCloud SDK of PHP APIs\n";
    echo "      It does logging in, browsing an organization, finding a template, getting vDC,\n";
    echo "      instantiating the template, deploying and powering on the vApp, uploading the vAppTemplate getting\n";
    echo "      information about the vApp, and finally deleting the vApp and logging out to\n";
    echo "      the vCloud.  It assumes an organization, vDC, catalog, catalogItem, vApp\n";
    echo "      template, network have already been created in the cloud.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php hellovcloud.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -s|--server <IP|hostname>       *[req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>             [req] User name in the form user@organization\n";
    echo "                                         for the vCloud Director.\n";
    echo "     -p|--pswd <password>             [req] Password for user.\n";
    echo "     -o|--org <orgName>               [opt] Name of an existing organization to operate.**\n";
    echo "     -d|--vdc <vdcName>               [opt] Name of an existing vDC in the organization to operate.**\n";
    echo "     -g|--catalog <catalogName>       [opt] Name of an existing catalog to be find in the organization.**\n";
    echo "     -i|--item <catalogItemName>      [opt] Name of an existing catalog item to be find in the catalog.**\n";
    echo "     -a|--vapp <vappName>             [opt] Name of a new vApp to be created and operated.  The default value\n";
    echo "                                         is 'hellovcloudVApp'.\n";
    echo "     -n|--name <vappTempName>        *[req] Name of a new vApp Template to be created.\n";
    echo "     -v|--ovf <ovfDescriptorPath>    *[req] Path to the OVF descriptor.\n";
    echo "     -b|--des <description>           [opt] Description of the vApp template to be created.\n";
    echo "     -m|--manifest <manifestRequired> [opt] A flag indicates the manifest file is *required or not.\n";
    echo "     -h|--help                        [opt] Display this message.\n";
    echo "\n";
    echo "     *  [req] = [required], [opt] = [optional].\n";
    echo "     ** If --org, --vdc, --catalog, or --item is not given, the first\n";
    echo "         entity in the list will be retrieved.  If the list is empty, this sample exits.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php hellovcloud.php -s '127.0.0.1' -u 'admin@Org' -p 'password'\n";
    echo "     # php hellovcloud.php -s 'localhost' -u 'admin@Org' -p 'password' -o='org' -d='vdc' -g='catalog' -i='catalogItem'\n";
    echo "     # php hellovcloud.php -s 'localhost' -u 'admin@Org' -p 'password' -o='org' -d='vdc' -g='catalog' -i='catalogItem' -a='vapp' -n='vappTemplate' -v='E:/ovf/dsl-with-tools.ovf' -b='description about vappTemplate' -m='true'\n\n";
}
?>
