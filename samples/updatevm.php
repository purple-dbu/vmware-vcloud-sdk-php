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
 * Sample for updating memory, disks, guestCustomizationSection of a vm.
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


$longs  = array(
    "server:",    //-s|--server [required]
    "user:",      //-u|--user   [required]
    "pswd:",      //-p|--pswd   [required]
    "org:",       //-a|--org    [required]
    "vdc:",       //-b|--vdc    [required]
    "vapp:",      //-c|--vapp   [required]
    "vm:",        //-d|--vm     [required]
    "op:",        //-e|--op     [required]  mem, disk, guest
    "value::",    //-f|--value  [required for modifying mem or adding a disk]
    "bussub::",   //-g|--bussub [required for adding a disk]
    "gpswd::",    //-h|--gpswd
);

$opts = getopt($shorts, $longs);

// Initialize parameters
$httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);

$orgName = null;
$vdcName = null;
$vAppName = null;
$vmName = null;
$op = null;
$value = null;  //for setting mem/disk new size, or guest customization script name
$guestPassword = 'guest123';  // the default password of the guest administrator

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
        $vmName = $opts['d'];
        break;
    case "vm":
        $vmName = $opts['vm'];
        break;

    case "e":
        $op = $opts['e'];
        break;
    case "op":
        $op = $opts['op'];
        break;

    case "f":
        $value = $opts['f'];
        break;
    case "value":
        $value = $opts['value'];
        break;

    case "g":
        $busSubType = $opts['g'];
        break;
    case "bussub":
        $busSubType = $opts['bussub'];
        break;

    case "h":
        $guestPassword = $opts['h'];
        break;
    case "gpswd":
        $guestPassword = $opts['gpswd'];
        break;    
}

// parameters validation
if ((!isset($server) || !isset($user) || !isset($pswd)) ||
    !isset($orgName) || !isset($vdcName) || !isset($vAppName) ||
    !isset($vmName)  || !isset($op) ||
    ('guest' != $op && !isset($value)) ||
    ('disk' == $op && !isset($busSubType)))
{
    echo "Error: missing required parameters\n";
    usage();
    exit(1);
}
if (!in_array($op, array('mem' , 'disk', 'guest')))
{
    exit("$op is not supported, allowed operations are: (modify) mem,
    (add) disk, and (modify) guest (customization)\n");
}

// login
$service = VMware_VCloud_SDK_Service::getService();
$service->login($server, array('username'=>$user, 'password'=>$pswd), $httpConfig);

// create an SDK Org object
$orgRefs = $service->getOrgRefs($orgName);
if (0 == count($orgRefs))
{
    exit("No organization with name $orgName is found\n");
}
$orgRef = $orgRefs[0];
$sdkOrg = $service->createSDKObj($orgRef);

// create an SDK vDC object
$vdcRefs = $sdkOrg->getVdcRefs($vdcName);
if (0 == count($vdcRefs))
{
    exit("No vDC with name $vdcName is found\n");
}
$vdcRef = $vdcRefs[0];
$sdkVdc = $service->createSDKObj($vdcRef);

// get a reference to a vApp in the vDC
$vAppRefs = $sdkVdc->getVAppRefs($vAppName);
if (!$vAppRefs)
{
    exit("No vApp with name $vAppName is found\n");
}
$vAppRef = $vAppRefs[0];
// create an SDK vApp object
$sdkVApp = $service->createSDKObj($vAppRef);

// get references to contained Vms
$vmRefs = $sdkVApp->getContainedVmRefs($vmName);
if (0 == count($vmRefs))
{
    exit("No Vm with name $vmName is found\n");
}
$vmRef = $vmRefs[0];
// create an SDK Vm object
$sdkVm = $service->createSDKObj($vmRef);

switch ($op)
{
    case 'mem':
        $mem = $sdkVm->getVirtualMemory();  // get memory data object
        $vq = $mem->getVirtualQuantity();   // get quantity
        $vq->set_valueOf($value);           // set to the new value
        $mem->setVirtualQuantity($vq); // set the new quantity to the mem object
        $sdkVm->modifyVirtualMemory($mem); // send the updated value to vCloud
        break;
    case 'disk':
        $disks = $sdkVm->getVirtualDisks();
        $items = $disks->getItem();
        $diskId = getDiskCount($items) + 1;
        $ctlId = checkDiskController($disks, $busSubType);   // get Bus Number
        $size = $value;
        
        // Create a new disk item
        $ndisk = new VMware_VCloud_API_OVF_RASD_Type();

        $order = getDiskOrder($items, $ctlId);   // get Unit Number
        $addr = new VMware_VCloud_API_OVF_cimString();
        $addr->set_valueOf($order);
        $ndisk->setAddressOnParent($addr);

        $desc = new VMware_VCloud_API_OVF_cimString();
        $desc->set_valueOf('Hard disk');
        $ndisk->setDescription($desc);

        $elementName = new VMware_VCloud_API_OVF_cimString();
        $elementName->set_valueOf('Hard disk ' . $diskId);
        $ndisk->setElementName($elementName);

        $hr = new VMware_VCloud_API_OVF_cimString();
        $anyAttrs = array('vcloud:capacity'=>$size,
                          'vcloud:busSubType'=>$busSubType,
                          'vcloud:busType'=>"6");
        $hr->set_anyAttributes($anyAttrs);
        $ndisk->setHostResource(array($hr));

        $p = new VMware_VCloud_API_OVF_cimString();
        $p->set_valueOf($ctlId);
        $ndisk->setParent($p);

        $instanceID = new VMware_VCloud_API_OVF_cimString();
        $i = getNextInstanceID($items);
        $instanceID->set_valueOf($i);
        $ndisk->setInstanceID($instanceID);

        $res = new VMware_VCloud_API_OVF_ResourceType();
        $res->set_valueOf(17);  //17 for disk drive
        $ndisk->setResourceType($res);

        //add the new disk to the existing disks list
        $disks->addItem($ndisk);
        $sdkVm->modifyVirtualDisks($disks);
        break;
    case 'guest':
        // note: the vm needs to be powered off;
        // vmware tools needs to be at a certain version 
        $sets = $sdkVm->getGuestCustomizationSettings();
        $file = $value;
        if (isset($file))
        {
            $data = null;
            if ($fh = fopen($file, 'r'))
            {
                $data = fread($fh, filesize($file));// get content of the script
            }
            fclose($fh);
            $sets->setCustomizationScript($data); // set the script content
        }
        $sets->setEnabled(true);
        // the following are needed when join domain is disabled.
        if (false === $sets->getJoinDomainEnabled())
        {
            $sets->setDomainName(null);
            $sets->setDomainUserName(null);
            $sets->setDomainUserPassword(null);
        }
        $sets->setAdminPasswordEnabled(true);
        $sets->setAdminPasswordAuto(false);
        $sets->setAdminPassword($guestPassword);
        $sets->setResetPasswordRequired(true);
        $sdkVm->modifyGuestCustomizationSettings($sets);
        break;
}


// helper functions for adding a new virtual disk.
function getDiskCount($items)
{
    if (0 == count($items))
    {
        return 0;
    }
    $count = 0;
    foreach ($items as $item)
    {
        if (17 == $item->getResourceType()->get_valueOf())
        {
            $count++;
        }
    }
    return $count;
}

// return instance id of the SCSI controller
function checkDiskController($disks, $subType)
{
    $items = $disks->getItem();
    if (0 == count($items))
    {
        $item = createSCSIDiskController($subType, 0);
        $disks->addItem($item);
        return 1;
    }
    $ctlCount = 0;
    foreach ($items as $item)
    {
        if (6 == $item->getResourceType()->get_valueOf())
        {
            $ctlCount++;
            if ($subType == $item->getResourceSubType()->get_valueOf())
            {
                return $item->getInstanceID()->get_valueOf(); //found SCSI controller.
            }
        }
    }
    $item = createSCSIDiskController($subType, $ctlCount);
    $disks->addItem($item);
    return $ctlCount+1;
}

function createSCSIDiskController($subType, $id)
{
    // Create a new disk controller item
    $ctl = new VMware_VCloud_API_OVF_RASD_Type();

    $addr = new VMware_VCloud_API_OVF_cimString();
    $addr->set_valueOf($id);
    $ctl->setAddress($addr);
    
    $desc = new VMware_VCloud_API_OVF_cimString();
    $desc->set_valueOf('SCSI Controller');
    $ctl->setDescription($desc);

    $name = new VMware_VCloud_API_OVF_cimString();
    $name->set_valueOf('SCSI Controller ' . $id);
    $ctl->setElementName($name);

    $instance = new VMware_VCloud_API_OVF_cimString();
    $instance->set_valueOf($id + 1);
    $ctl->setInstanceID($instance);

    $sub = new VMware_VCloud_API_OVF_cimString();
    $sub->set_valueOf($subType);
    $ctl->setResourceSubType($sub);

    $res = new VMware_VCloud_API_OVF_cimString();
    $res->set_valueOf('6'); // SCSI controller
    $ctl->setResourceType($res);

    return $ctl;
}

function getDiskOrder($items, $parent)
{
    if (0 == count($items))
    {
        return 0;
    }
    $addr = 0;
    foreach ($items as $item)
    {
        if (17 == $item->getResourceType()->get_valueOf() &&
            $parent == $item->getParent()->get_valueOf())
        {
            $addr++;
        }
    }
    return $addr;
}

function getNextInstanceID($items)
{
    if (0 == count($items))
    {
        return 2000;  //assuming starts from 2000.
    }
    $max = 2000; 
    foreach ($items as $item)
    {
        if (17 == $item->getResourceType()->get_valueOf())
        {
            $id = $item->getInstanceID()->get_valueOf();
            $max = ($max < $id)? $id : $max;
        }
    }
    return $max+1;
}

/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample demonstrates modifying memory, guest customization, adding a virtual disk operation.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php updatevm.php -s <server> -u <username> -p <password> [Options]\n";
    echo "\n";
    echo "     -s|--server <IP|hostname> [req] IP or hostname of the vCloud Director.\n";
    echo "     -u|--user <username>      [req] User name in the form user@organization\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -p|--pswd <password>      [req] Password for user.\n";
    echo "\n";
    echo "  [Options]\n";
    echo "     -a|--org <orgName>        [req] Name of an existing organization in the vCloud Director.\n";
    echo "     -b|--vdc <vdcName>        [req] Name of an existing vDC in the organization.\n";
    echo "     -c|--vapp <vappName>      [req] Name of an existing vApp in the vDC.\n";
    echo "     -d|--vm <vmName>          [req] Name of an existing VM in the vApp.\n";
    echo "     -e|--op <mem/disk/guest>  [req] Sample supported operation.\n";
    echo "     -f|--value <value>        [opt] Required when op is mem or disk for the new capacity to \n";
    echo "                                      be updated. When op is guest, use this for the path of the script.\n";
    echo "     -g|--bussub <value>       [opt] Specify disk controller resource sub-type, e.g. lsilogic, buslogic,\n";
    echo "                                      lsilogicsas, VirtualSCSI. Required for adding a new disk.\n";
    echo "     -h|--gpswd <pswd>         [opt] Password of the guest administrator.\n";
    echo "\n";
    echo "  [Examples]\n";
    echo "     # php updatevm.php -s 127.0.0.1 -u admin@Org -p password -a org -b vdc -c vapp -d vm -e mem -f=64 // updating memory\n";
    echo "     # php updatevm.php -a org -b vdc -c vapp -d vm -e mem -f=64 // using config.php to set login credentials\n";
    echo "     # php updatevm.php -a org -b vdc -c vapp -d vm -e disk -f=8 -g=buslogic // adding a disk\n";
    echo "     # php updatevm.php -a org -b vdc -c vapp -d vm -e guest -f=/home/test.sh -h=guestpassword // guest customization\n\n";
}
?>
