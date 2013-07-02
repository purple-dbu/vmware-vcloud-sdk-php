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
 * Should be system administrator.
 * Should contain atleast a vCenter with resource pools
 */
// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "u:";
$shorts .= "p:";

$shorts .= "a:";
$shorts .= "b:";
$shorts .= "c:";
$shorts .= "d::";
$shorts .= "e::";
$shorts .= "f:";
$shorts .= "l";

$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "vim:",       //-a|--vim    [required]
    "pvdc:",      //-b|--pvdc   [required]
    "rp:",        //-c|--rp     [required]
    "cpu::",      //-d|--cpu  comma seperated value in Units, Allocation, Total, Used order
    "mem::",      //-e|--mem  comma seperated value in Units, Allocation, Total, Used order
    "stor:",      //-f|--stor   [required]
    "list",       //-l|--list
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$vimName = null;
$pvdcName = null;
$rpMoRef = null;
$cpuQut = array('MHz', 200, 1024, 0);  //in Units, Allocation, Total, Used order
$memQut = array('MB', 100, 512, 0);    //in Units, Allocation, Total, Used order
$storageProfile = null;
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

    case "d":
        $cpuQut = explode(',', $opts['d']);
        break;
    case "cpu":
        $cpuQut = explode(',', $opts['cpu']);
        break;

    case "e":
        $memQut = explode(',', $opts['e']);
        break;
    case "mem":
        $memQut = explode(',', $opts['mem']);
        break;

    case "f":
        $storageProfile = $opts['f'];
        break;
    case "stor":
        $storageProfile = $opts['stor'];
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
    (true !== $list && (!isset($vimName) || !isset($pvdcName) ||
    !isset($storageProfile) || !isset($rpMoRef))))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}

// vCloud login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

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
        global $cpuQut,$memQut;

        // set cpu capacity
        $cpu = new VMware_VCloud_API_ProviderVdcCapacityType();
        $cpu->setUnits($cpuQut[0]);
        $cpu->setAllocation($cpuQut[1]);
        $cpu->setTotal($cpuQut[2]);
        $cpu->setUsed($cpuQut[3]);

        // set memory capacity
        $mem = new VMware_VCloud_API_ProviderVdcCapacityType();
        $mem->setUnits($memQut[0]);
        $mem->setAllocation($memQut[1]);
        $mem->setTotal($memQut[2]);
        $mem->setUsed($memQut[3]);

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
     * @param string  $storageProfile
     * @return VMware_VCloud_API_Extension_VMWProviderVdcType
     */
    function updateProviderVdcParam($vimserverRef, $rpMoRef, $pvdcName, $storageProfile)
    {
        global $cpuQut,$memQut;

        // set cpu capacity
        $cpu = new VMware_VCloud_API_ProviderVdcCapacityType();
        $cpu->setUnits($cpuQut[0]);
        $cpu->setAllocation($cpuQut[1]);
        $cpu->setTotal($cpuQut[2]);
        $cpu->setUsed($cpuQut[3]);

        // set memory capacity
        $mem = new VMware_VCloud_API_ProviderVdcCapacityType();
        $mem->setUnits($memQut[0]);
        $mem->setAllocation($memQut[1]);
        $mem->setTotal($memQut[2]);
        $mem->setUsed($memQut[3]);

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

        // Add provider vDC in vCloud Director
        echo "Adding provider vDC. \n";
        $providervDC = createProviderVdcParam($vimserverRef, $rpMoRef, $pvdcName, $storageProfile);
        $providervDC = $sdkExt->createProviderVdc($providervDC);
        $task=$providervDC->getTasks()->getTask();
        if (sizeof($task) > 0){
        $service->waitForTask($task[0]);
        }
        echo "Added provider vDC : ".$providervDC->get_name();

        // create provider vdc object
        $refs = $sdkExt->getVMWProviderVdcRefs($pvdcName);
        $ref = $refs[0];
        $vmwprovidervdcob = $service->createSDKObj($ref);

        // Updated provider vDC
        echo "Update provider vDC. \n";
        $providervDC = updateProviderVdcParam($vimserverRef, $rpMoRef, $pvdcName."_Updated", $storageProfile);
        $providervDC = $vmwprovidervdcob->modify($providervDC);
        $task=$providervDC->getTasks()->getTask();
        if (sizeof($task) > 0){
        $service->waitForTask($task[0]);
        }
        echo "Updated provider vDC : ".$providervDC->get_name()."\n";

        // Get provider vDC
        echo "Get provider vDC. \n";
        getVMWProviderVdc($pvdcName);

        // Delete provider vDC
        echo "Deleting provider vDC. \n";
        deleteVMWProviderVdc($pvdcName);
        echo "Deleted provider vDC: ".$pvdcName."\n";


function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates creating a provider vDC.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php createprovidervdc.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>    [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>         [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>         [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--vim <vimName>           [req] Name of a registered Vim server in the vCloud Director.\n";
    echo "     -b|--pvdc <pvdcName>         [req] Name of the provider vDC to be created.\n";
    echo "     -c|--rp <rp>                 [req] MoRef of a resource pool.\n";
    echo "     -d|--cpu <cpu>               [opt] CPU settings: comma seperated value in Units, Allocation, Total, Used order.\n";
    echo "     -e|--mem <mem>               [opt] Memory settings: comma seperated value in Units, Allocation, Total, Used order.\n";
    echo "     -f|--stor <storageprofile>   [opt] Name of storage profile.\n";
    echo "     -l|--list                    [opt] List all provider vDC.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -a vim -b pvdc -c resgroup-84 -f storageprofile\n";
    echo "     # php createprovidervdc.php -s 127.0.0.1 -u admin@Org -p password -a vim -b pvdc -c resgroup-84 -e=\"'MHz', 200, 1024, 0\" -f storageprofile\n";
    echo "     # php createprovidervdc.php -a vim -b pvdc -c resgroup-84 -f storageprofile// using config.php to set login credentials\n";
    echo "     # php createprovidervdc.php -l// list all provider vDC\n\n";
}
?>
