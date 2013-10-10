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
 * This sample illustrates the correlation of the objects in vCloud to objects
 * in vCenter.
 *
 * Running this sample requires System Administrator privileges.
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
     * Correlation sample.
     *
     * @throws VMware_VCloud_SDK_Exception
     */
    getVdcCorrelation();
    getOrgVdcNetworkCorrelation();
    getVappNetworkCorrelation();
    getVMCorrelation();
    getIndependentDiskCorrelation();
    getProviderVdcCorrelation();
    getExternalNetworkCorrelation();
    getNetworkPoolCorrelation();
    getHostCorrelation();
    getDatastoreCorrelation();
}
else
{
    echo "\n\nLogin Failed due to certification mismatch.";
    return;
}

/**
 * Lists the Vdc's correlation data with vCenter.
 *
 * a. vCenter server information.
 *
 * b. Resource pools information.
 *
 * @throws VMware_VCloud_SDK_Exception
 *
 */
function getVdcCorrelation()
{
    global $service;
    echo "---------------\n";
    echo "Vdc Correlation\n";
    echo "---------------\n";
    // admin org vdc query
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::ADMIN_ORG_VDC, $param);
    if (!empty($records))
    {
        $records = $records->getRecord();
        // iterate through the admin org vdc's
        foreach ($records as $record)
        {
            echo " " . $record->get_name()."\n";
            // org vdc resource pool relation query
            $param=new VMware_VCloud_SDK_Query_Params();
            $param->setPageSize(128);
            $param->setFilter('vdc==' . $record->get_href());
            $query = $service->getQueryService();
            $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::ORG_VDC_RESOURCE_POOL_RELATION, $param);
            if (!empty($records))
            {
                $records = $records->getRecord();
                // iterate through the org vdc resource pool relation result.
                foreach ($records as $record)
                {
                    // get the vcenter information.
                    try
                    {
                        $hasVC = $record->get_vc();
                        if(isset($hasVC))
                        {
                            $vc = $service->createSDKObj($record->get_vc());
                            echo "     (vCenter) " . $vc->getVimServer()->get_name() . "(";
                            echo $vc->getVimServer()->getUrl() . ")\n";
                        }
                        echo "     (Resource Pool) ";
                        echo $record->get_resourcePoolMoref() . "\n";
                    }
                    catch (Exception $e)
                    {
                        echo "     vCenter does not exist.\n";
                    }
                }
            }
        }
    }
}

/**
 * Lists the Vdc Network's correlation data with vCenter and its network
 * resources.
 *
 * a. vCenter server information.
 *
 * b. vCenter Network information.
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function getOrgVdcNetworkCorrelation()
{
    global $service;
    echo "\n-----------------------";
    echo "\nVdc Network Correlation";
    echo "\n-----------------------\n";
    // org vdc network query
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::ORG_VDC_NETWORK, $param);
    if (!empty($records))
    {
        $records = $records->getRecord();
        // iterate through the org vdc network's
        foreach ($records as $record)
        {
            echo " " . $record->get_name()."\n";
            $parentNetworkName = $record->get_name();
            try
            {
                $adminOrgVdcNetwork = $service->createSDKObj($record->get_href());
                $configuration = $adminOrgVdcNetwork->getAdminNetwork()->getConfiguration();
                if (!empty($configuration))
                {
                    // find the parent network if any.
                    if ($configuration->getParentNetwork() != null)
                    {
                        $parentNetworkName = $configuration->getParentNetwork()->get_name();
                    }
                }
            }
            catch (Exception $e)
            {
                echo "orgVdcNetwork does not exist.\n";
            }
            // port group query to get the vcenter network information.
            $param=new VMware_VCloud_SDK_Query_Params();
            $param->setPageSize(128);
            $parentNetworkName = str_replace(" ", "%20", $parentNetworkName);
            $param->setFilter("networkName==" . $parentNetworkName);
            $query = $service->getQueryService();
            $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::PORTGROUP, $param);
            if (!empty($records))
            {
                $records = $records->getRecord();
                // iterate through the port group query result.
                foreach ($records as $record)
                {
                    // get the vcenter information.
                    try
                    {
                        $vc = $service->createSDKObj($record->get_vc());
                        echo "     (vCenter) " . $vc->getVimServer()->get_name() . "(";
                        echo $vc->getVimServer()->getUrl() . ")\n";
                    }
                    catch (Exception $e)
                    {
                        echo "     vCenter does not exist.\n";
                    }
                    echo "     (vCenter Network) " . $record->get_moref() . "(" . $record->get_name() . ")\n";
                }
            }
        }
    }
}

/**
 * Lists the VApp Network's correlation data with vCenter and its network
 * resources.
 *
 * a. vCenter server information.
 *
 * b. vCenter Network information.
 *
 * @throws VMware_VCloud_SDK_Exception
 *
 */
function getVappNetworkCorrelation()
{
    global $service;
    echo "\n------------------------";
    echo "\nVApp Network Correlation";
    echo "\n------------------------\n";
    // vapp network query
    $param = new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::ADMIN_VAPP_NETWORK, $param);
    if (!empty($records))
    {
        $records = $records->getRecord();
        // iterate through the vapp network's
        foreach ($records as $record)
        {
            echo " " . $record->get_name() . "\n";
            $parentNetworkName = $record->get_name();
            try
            {
                $vappNetwork = $service->createSDKObj($record->get_href());
                $configuration = $vappNetwork->getNetwork()->getConfiguration();
                if (!empty($configuration))
                {
                    // find the parent network if any.
                    if ($configuration->getParentNetwork() != null)
                    {
                        $vapp = $configuration->getParentNetwork()->get_href();
                        $adminOrgVdcNetwork = $service->createSDKObj($vapp);
                        $networkConfig = $adminOrgVdcNetwork->getAdminNetwork()->getConfiguration();
                        if (!empty($networkConfig))
                        {
                            if ($networkConfig->getParentNetwork() != null)
                            {
                                $parentNetworkName = $networkConfig->getParentNetwork()->get_name();
                            }
                        }
                    }
                }
            }
            catch (Exception $e)
            {
                echo "vappNetwork does not exist.\n";
            }

            // port group query to get the vcenter network information.
            $param=new VMware_VCloud_SDK_Query_Params();
            $param->setPageSize(128);
            $parentNetworkName = str_replace(" ", "%20", $parentNetworkName);
            $param->setFilter('networkName==' . $parentNetworkName);
            $query = $service->getQueryService();
            $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::PORTGROUP,$param);
            if (!empty($records))
            {
                $records = $records->getRecord();
                // iterate through the port group query result.
                foreach ($records as $record)
                {
                    // get the vcenter information.
                    try
                    {
                        $vc = $service->createSDKObj($record->get_vc());
                        echo "     (vCenter) " . $vc->getVimServer()->get_name() . "(";
                        echo $vc->getVimServer()->getUrl() . ")\n";
                    }
                    catch (Exception $e)
                    {
                        echo "     vCenter does not exist.\n";
                    }
                    echo "     (vCenter Network) " . $record->get_moref() . "(" . $record->get_name() . ")\n";
                }
            }
        }
    }
}

/**
 * Lists the VM's correlation data with vCenter, host, datastore etc
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function getVMCorrelation()
{
    global $service;
    echo "\n--------------";
    echo "\nVM Correlation";
    echo "\n--------------\n";
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $param->setFilter('status!=' . 'UNRESOLVED');
    $query = $service->getQueryService();
    $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::ADMIN_VM, $param);
    if (!empty($records))
    {
        $adminVMResult = $records;
        $records = $records->getRecord();
        foreach ($records as $record)
        {
            echo " " . $record->get_name() . ($record->get_isVAppTemplate() ? "(VAppTemplate VM)\n" : "(VApp VM)\n");
            // get the vcenter information.
            try
            {
                if (!is_null($record->get_vc()))
                {
                    $vc = $service->createSDKObj($record->get_vc());
                    echo "     (vCenter) " . $vc->getVimServer()->get_name() . "(";
                    echo $vc->getVimServer()->getUrl() . ")\n";
                }
            }
            catch (Exception $e)
            {
                echo "     vCenter does not exist.\n";
            }
            echo "     (StorageProfile) " . $record->get_storageProfileName(). "\n";
            if ($record->get_isVAppTemplate())
            {
                try
                {
                    $vappTemplateVM = $service->createSDKObj($record->get_href());
                    $vCloudExtension = $vappTemplateVM->getVm()->getVCloudExtension();
                    if (!empty($vCloudExtension))
                    {
                        $any = $vCloudExtension[0]->getAny();
                        $moRef = $any[0]->getVmVimObjectRef()->getMoRef();
                        echo "     (VM Details) " . $moRef ."\n";
                        echo "     (Host Details) " . $any[0]->getHostVimObjectRef()->getMoRef() . "(" . $record->get_hostName() . ")\n";
                        echo "     (Datastore Details) " . $any[0]->getDatastoreVimObjectRef()->getMoRef() . "(" . $record->get_datastoreName() . ")\n";
                    }
                }
                catch (Exception $e)
                {
                    echo "     Template Storage profile is not set.\n";
                }
            }
            else
            {
                try
                {
                    $vm = $service->createSDKObj($record->get_href());
                    $vCloudExtension = $vm->getVm()->getVCloudExtension();
                    if (!empty($vCloudExtension))
                    {
                        $any = $vCloudExtension[0]->getAny();
                        $moRef = $any[0]->getVmVimObjectRef()->getMoRef();
                        echo "     (VM Details) " . $moRef . "\n";
                        echo "     (Host Details) " . $any[0]->getHostVimObjectRef()->getMoRef() . "(" . $record->get_hostName() . ")\n";
                        echo "     (Datastore Details) " . $any[0]->getDatastoreVimObjectRef()->getMoRef() . "(" . $record->get_datastoreName() . ")\n";
                    }
                }
                catch (Exception $e)
                {
                    echo "     VM does not exist.\n";
                }
            }
        }
    }
}

/**
 * Lists the disks and its correlation with the StorageProfile and
 * datastore.
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function getIndependentDiskCorrelation()
{
    global $service;
    echo "\n----------------------------";
    echo "\nIndependent Disk Correlation";
    echo "\n----------------------------\n";
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::ADMIN_DISK, $param);
    if (!empty($records))
    {
        $records = $records->getRecord();
        foreach ($records as $record)
        {
            echo " " . $record->get_name()."\n";
            // get the vcenter information.
            try
            {
                if (!is_null($record->get_vc()))
                {
                    $vc = $service->createSDKObj($record->get_vc());
                    echo "     (vCenter) " . $vc->getVimServer()->get_name() . "(";
                    echo $vc->getVimServer()->getUrl() . ")\n";
                }
            }
            catch (Exception $e)
            {
                echo "     vCenter does not exist.\n";
            }
            echo "     (StorageProfile) " . $record->get_storageProfileName(). "\n";
            $param=new VMware_VCloud_SDK_Query_Params();
            $param->setPageSize(128);
            $datastoreName = $record->get_datastoreName();
            $datastoreName = str_replace(" ", "%20", $datastoreName);
            $param->setFilter('name==' . $datastoreName);
            $query = $service->getQueryService();
            $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::DATASTORE, $param);
            if (!empty($records))
            {
                $records = $records->getRecord();
                foreach ($records as $record)
                {
                    echo "     (Datastore) " . $record->get_moref() . "(" . $record->get_name() . ")\n";
                }
            }
        }
    }
}

/**
 * Lists the correlation between the provider vdc and the resources in
 * vCenter(resource pool, datastore, storage profile etc)
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function getProviderVdcCorrelation()
{
    global $service;
    echo "\n------------------------";
    echo "\nProvider Vdc Correlation";
    echo "\n------------------------\n";
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $records = $query->queryRecords(VMware_VCloud_SDK_Query_Types::PROVIDER_VDC, $param);
    if (!empty ($records))
    {
        $records = $records->getRecord();
        foreach ($records as $record)
        {
            echo " " . $record->get_name()."\n";
            try
            {
                $adminExtension = $service->createSDKExtensionObj();
                $VMWProviderVdcRef = $adminExtension->getVMWProviderVdcRefs($record->get_name());
                if (!empty ($VMWProviderVdcRef))
                {
                    $VMWProviderVdc = $service->createSDKObj($VMWProviderVdcRef[0]->get_href());
                    $vimServers = $VMWProviderVdc->getVMWProviderVdc()->getVimServer();
                    if (!empty ($vimServers))
                    {
                        // get vcenter information
                        foreach ($vimServers as $vimServer)
                        {
                            echo "     (vCenter) " . $vimServer->get_name() . "(";
                            echo $vimServer->get_href() . ")\n";
                        }
                    }
                    // get the host information
                    $hostReference = $VMWProviderVdc->getVMWProviderVdc()->getHostReferences()->getHostReference();
                    if (!empty ($hostReference))
                    {
                        foreach ($hostReference as $host)
                        {
                            echo "     (Host) " . $host->get_name() . "\n";
                        }
                    }
                    // get the storage profile information
                    $storageProfileRefs = $VMWProviderVdc->getStorageProfileRefs();
                    if (!empty ($storageProfileRefs))
                    {
                        foreach ($storageProfileRefs as $storageProfileRef)
                        {
                            echo "     (StorageProfile) " . $storageProfileRef->get_name() . "\n";
                        }
                    }
                }
            }
            catch (Exception $e)
            {
                echo "     resource does not exist.\n";
            }
            // get the datastore information
            $param=new VMware_VCloud_SDK_Query_Params();
            $param->setPageSize(128);
            $param->setFilter('providerVdc==' . $record->get_href());
            $query = $service->getQueryService();
            $datastoreRecords = $query->queryRecords(VMware_VCloud_SDK_Query_Types::DATASTORE_PROV_VDC_RELATION, $param);
            if (!empty ($datastoreRecords))
            {
                $datastoreRecords = $datastoreRecords->getRecord();
                foreach ($datastoreRecords as $datastore)
                {
                    echo "     (Datastore) " . $datastore->get_moref() . "(" . $datastore->get_name() . ")\n";
                }
            }
            // get the resource pool information
            $param=new VMware_VCloud_SDK_Query_Params();
            $param->setPageSize(128);
            $param->setFilter('providerVdc==' . $record->get_href());
            $query = $service->getQueryService();
            $resourcePoolRecords = $query->queryRecords(VMware_VCloud_SDK_Query_Types::PROVIDER_VDC_RESOURCE_POOL_RELATION, $param);
            if (!empty ($resourcePoolRecords))
            {
                $resourcePoolRecords = $resourcePoolRecords->getRecord();
                foreach ($resourcePoolRecords as $resourcePool)
                {
                    echo "     (ResourcePool) " . $resourcePool->get_resourcepoolmoref() . "(" . $resourcePool->get_name() . ")\n";
                }
            }
        }
    }
}

/**
 * Lists the correlation between the external network and the vcenter
 * network information.
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function getExternalNetworkCorrelation()
{
    global $service;
    echo "\n---------------------------";
    echo "\nExternal Network Correlation";
    echo "\n---------------------------\n";
    // external network query
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $externalNetworkRecords = $query->queryRecords(VMware_VCloud_SDK_Query_Types::EXTERNAL_NETWORK, $param);
    if (!empty ($externalNetworkRecords))
    {
        $externalNetworkRecords = $externalNetworkRecords->getRecord();
        // iterate through the external network's
        foreach ($externalNetworkRecords as $externalNetworkRecord)
        {
            echo " " . $externalNetworkRecord->get_name() . "\n";
            // port group query to get the vcenter network information.
            $param=new VMware_VCloud_SDK_Query_Params();
            $param->setPageSize(128);
            $externalNetworkName = $externalNetworkRecord->get_name();
            $externalNetworkName = str_replace(" ", "%20", $externalNetworkName);
            $param->setFilter('networkName==' . $externalNetworkName);
            $query = $service->getQueryService();
            $portGroupResult = $query->queryRecords(VMware_VCloud_SDK_Query_Types::PORTGROUP, $param);
            if (!empty ($portGroupResult))
            {
                $portGroupRecords = $portGroupResult->getRecord();
                // iterate through the port group query result.
                foreach ($portGroupRecords as $portGroupRecord)
                {
                    // get the vcenter information.
                    try
                    {
                        if (!empty ($portGroupRecord))
                        {
                            $vc = $service->createSDKObj($portGroupRecord->get_vc());
                            echo "     (vCenter) " . $vc->getVimServer()->get_name() . "(";
                            echo $vc->getVimServer()->getUrl() . ")\n";
                        }
                    }
                    catch (Exception $e)
                    {
                        echo "     vCenter does not exist.\n";
                    }
                    echo "     (vCenter Network) ";
                    echo $portGroupRecord->get_moref() . "(";
                    echo $portGroupRecord->get_name() . ")\n";
                }
            }
        }
    }
}

/**
 * Lists the correlation between the network pools and the vcenter
 * portgroups and switches.
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function getNetworkPoolCorrelation()
{
    global $service;
    echo "\n------------------------";
    echo "\nNetwork Pool Correlation";
    echo "\n------------------------\n";
    // network pool query
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $networkPoolRecords = $query->queryRecords(VMware_VCloud_SDK_Query_Types::NETWORK_POOL, $param);
    if (!empty ($networkPoolRecords))
    {
        $networkPoolRecords = $networkPoolRecords->getRecord();
        // iterate through the network pools
        foreach ($networkPoolRecords as $networkPoolRecord)
        {
            $vCenterRef = null;
            echo " " . $networkPoolRecord->get_name() . "\n";
            try
            {
                $networkPool = $service->createSDKObj($networkPoolRecord->get_href());
                // VXLAN type
                if ($networkPoolRecord->get_networkPoolType() == 3)
                {
                    // not much data to be displayed
                    echo "\n";
                }
                // Cloud Network Isolation type
                else if ($networkPoolRecord->get_networkPoolType() == 1)
                {
                    $fencePoolType = $networkPool->getVMWNetworkPool();
                    echo "     (vCenter Network) " . $fencePoolType->getVimSwitchRef()->getMoRef() . "\n";
                    $vCenterRef = $fencePoolType->getVimSwitchRef()->getVimServerRef();
                }
                // Port Group type
                else if ($networkPoolRecord->get_networkPoolType() == 2)
                {
                    $portGroupPoolType = $networkPool->getVMWNetworkPool();
                    $portGroupVimRefs = $portGroupPoolType->getPortGroupRefs()->getVimObjectRef();
                    foreach ($portGroupVimRefs as $portGroupVimRef)
                    {
                        echo "     (vCenter Network) " . $portGroupVimRef->getMoRef() . "\n";
                    }
                    $vCenterRef = $portGroupPoolType->getVimServer();
                }
                // VLAN type
                else if ($networkPoolRecord->get_networkPoolType() == 0)
                {
                    $vlanPoolType = $networkPool->getVMWNetworkPool();
                    echo "     (vCenter Network) " . $vlanPoolType->getVimSwitchRef()->getMoRef() . "\n";
                    $vCenterRef = $vlanPoolType->getVimSwitchRef()->getVimServerRef();
                }
            }
            catch (Exception $e)
            {
                echo "     networkPool does not exist.\n";
            }
            // get the vcenter information.
            if ($vCenterRef != null)
            {
                try
                {
                    $vCenterServer = $service->createSDKObj($vCenterRef->get_href());
                    echo "     (vCenter) " . $vCenterServer->getVimServer()->get_name() . "(";
                    echo $vCenterServer->getVimServer()->get_href() . ")\n";
                }
                catch (Exception $e)
                {
                    echo "     vCenter does not exist.\n";
                }
            }
        }
    }
}

/**
 * Lists the correlation between the hosts and the vcenter.
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function getHostCorrelation()
{
    global $service;
    echo "\n----------------";
    echo "\nHost Correlation";
    echo "\n----------------\n";
    // host query
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $hostResult = $query->queryRecords(VMware_VCloud_SDK_Query_Types::HOST, $param);
    if (!empty ($hostResult))
    {
        $records = $hostResult->getRecord();
        // iterate through the hosts
        foreach ($records as $record)
        {
            echo " " . $record->get_name() . "(" . $record->get_osVersion() . ")\n";
            try
            {
                if (!is_null($record->get_vc()))
                {
                    $vc = $service->createSDKObj($record->get_vc());
                    echo "     (vCenter) " . $vc->getVimServer()->get_name() . "(";
                    echo $vc->getVimServer()->getUrl() . ")\n";
                }
            }
            catch (Exception $e)
            {
                echo "     vCenter does not exist.\n";
            }
        }
    }
}

/**
 * Lists the correlation between the datastore and the vcenter.
 *
 * @throws VMware_VCloud_SDK_Exception
 */
function getDatastoreCorrelation()
{
    global $service;
    echo "\n---------------------";
    echo "\nDatastore Correlation";
    echo "\n---------------------\n";
    // datastore query
    $param=new VMware_VCloud_SDK_Query_Params();
    $param->setPageSize(128);
    $query = $service->getQueryService();
    $datastoreResult = $query->queryRecords(VMware_VCloud_SDK_Query_Types::DATASTORE, $param);
    if (!empty ($datastoreResult))
    {
        $records = $datastoreResult->getRecord();
        // iterate through the datastores
        foreach ($records as $record)
        {
            echo " " . $record->get_name() . "(" . $record->get_datastoreType() . ")\n";
            try
            {
                if (!is_null($record->get_vc()))
                {
                    $vc = $service->createSDKObj($record->get_vc());
                    echo "     (vCenter) " . $vc->getVimServer()->get_name() . "(";
                    echo $vc->getVimServer()->getUrl() . ")\n";
                }
            }
            catch (Exception $e)
            {
                echo "     vCenter does not exist.\n";
            }
            echo "     (Datastore)" . $record->get_moref() . "(" . $record->get_name() . ")\n";
        }
    }
}

/**
 * Print the help message of the sample.
 */
function usage()
{
    echo "Usage:\n\n";
    echo "  [Description]\n";
    echo "     This sample illustrates the correlation of the objects in vCloud to objects in vCenter.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php correlation.php -s <server> -u <username> -p <password> -v <sdkversion> [Options]\n";
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
    echo "     # php correlation.php -s 127.0.0.1 -u admin@Org -p password -v 5.5 -c=certificatepath\n";
    echo "     # php correlation.php -c=certificatepath // using config.php to set login credentials\n\n";
}
?>
