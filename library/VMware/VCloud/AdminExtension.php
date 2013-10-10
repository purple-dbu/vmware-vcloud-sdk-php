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

/**
 * Contains VMware vCloud SDK for PHP utility functions
 */
require_once 'VMware/VCloud/Helper.php';

/**
 * A class provides convenient methods on a VMware vCloud extension entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension extends VMware_VCloud_SDK_Abstract
{
    /**
     * Constructor
     */
    public function __construct($svc)
    {
        parent::__construct($svc, $svc->getAdminExtensionUrl());
    }

    /**
     * Get the VMware vCloud admin extension entry point.
     *
     * @return VMware_VCloud_API_Extension_VMWExtensionType
     * @since Version 1.0.0
     */
    public function getExtension()
    {
        return $this->getDataObj();
    }

    /**
     * Get the references to all the hosts.
     *
     * @param string $name   Name of the host to get. If null, returns all
     * @return array         VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getHostRefs($name=null)
    {
        $url = $this->url . '/hostReferences';
        $hostRefs = $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getHostReference',
                                       $hostRefs);
    }

    /**
     * Get all hosts.
     *
     * @param string $name   Name of the host. If null, returns all
     * @return array         VMware_VCloud_API_Extension_HostType object array
     * @since Version 1.0.0
     */
    public function getHosts($name=null)
    {
        $refs = $this->getHostRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to VMware vim servers.
     *
     * @param string $name   Name of the vim server to get. If null, returns all
     * @return array         VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getVimServerRefs($name=null)
    {
        $url = $this->url . '/vimServerReferences';
        $refs = $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getVimServerReference',
                                       $refs);
    }

    /**
     * Get VMware vim server objects.
     *
     * @param string $name   Name of the vim server to get. If null, returns all.
     * @return array         VMware_VCloud_API_Extension_VimServerType object
     *                       array. 
     * @since Version 1.0.0
     */
    public function getVimServers($name=null)
    {
        $refs = $this->getVimServerRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to VMware provider vDCs.
     *
     * @param string $name   Name of the VMware provider vDC to get. If null,
     *                       returns all.
     * @return array         VMware_VCloud_API_ReferenceType object array.
     * @since Version 1.0.0
     */
    public function getVMWProviderVdcRefs($name=null)
    {
        $url = $this->url . '/providerVdcReferences';
        $pvdcRefs = $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getProviderVdcReference',
                                       $pvdcRefs);
    }

    /**
     * Get list of provider vDCs by using REST API general QueryHandler. This is read only list and is not bound to specific states. If filter is provided it will be applied to the corresponding result set. Format determines the elements representation - references, records or idrecords. Default format is records.
     *
     * @param string $name   Name of the VMware provider vDC query to get. If null,
     *                       returns all.
     * @return array         VMware_VCloud_API_ReferenceType object array.
     * @since Version 5.1.0
     */
    public function getVMWProviderVdcQueryRefs($name=null)
    {
        $url = $this->url . '/providerVdcReferences/query';
        return $this->svc->get($url);
    }

    /**
     * Create a Extension service.
     *
     * @param VMware_VCloud_API_Extension_AdminServiceType $params
     * @return VMware_VCloud_API_Extension_AdminServiceType
     * @since Version 5.1.0
     */
    public function createService($params)
    {
        $url = $this->url . '/service';
        $type = VMware_VCloud_SDK_Constants::EXTENSION_SERVICE_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Get a Extension service.
     *
     *
     * @return list of all the extension services.
     * @since Version 5.1.0
     */
    public function getExtensionService()
    {
        $type = 'adminService';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Get VMware provider vDC objects.
     *
     * @param string $name   Name of the VMware provider vDC to get. If null,
     *                       returns all
     * @return array         VMware_VCloud_API_Extension_VMWProviderVdcType
     *                       objects array
     * @since Version 1.0.0
     */
    public function getVMWProviderVdcs($name=null)
    {
        $refs = $this->getVMWProviderVdcRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to VMware external network.
     *
     * @param string $name   Name of the VMware external network to get. If null,
     *                       returns all
     * @return array         VMware_VCloud_API_ReferenceType object
     *                       array
     * @since Version 1.0.0
     */
    public function getVMWExternalNetworkRefs($name=null)
    {
        $url = $this->url . '/externalNetworkReferences';
        $exnetRefs = $this->svc->get($url);
        return $this->getContainedRefs(null, $name,
                                   'getExternalNetworkReference', $exnetRefs);
    }

    /**
     * Get external network objects.
     *
     * @param string $name   Name of the external network to get. If null,
     *                       returns all
     * @return array         VMware_VCloud_API_Extension_VMWExternalNetworkType
     *                       object array
     * @since Version 1.0.0
     */
    public function getVMWExternalNetworks($name=null)
    {
        $refs = $this->getVMWExternalNetworkRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to VMware network pools.
     *
     * @param string $name   Name of the VMware network pool. If null, returns
     *                       all
     * @return array         VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getVMWNetworkPoolRefs($name=null)
    {
        $url = $this->url . '/networkPoolReferences';
        $poolRefs = $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getNetworkPoolReference',
                                       $poolRefs);
    }

    /**
     * Get VMware network pool objects.
     *
     * @param string $name   Name of the VMware network pool. If null, returns all
     * @return array         VMware_VCloud_API_Extension_VMWNetworkPoolType
     *                       object array.
     * @since Version 1.0.0
     */
    public function getVMWNetworkPools($name=null)
    {
        $refs = $this->getVMWNetworkPoolRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get the references of data stores.
     *
     * @param string $name   Name of the data store to get. If null, returns all
     * @return array         VMware_VCloud_API_ReferenceType object array
     * @since Version 1.5.0
     */
    public function getDatastoreRefs($name=null)
    {
        $url = $this->url . '/datastores';
        $dsRefs = $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getReference', $dsRefs);
    }

    /**
     * Get the data store objects.
     *
     * @param string $name   Name of the data store to get. If null, returns all.
     * @return array         VMware_VCloud_API_Extension_DatastoreType object
     *                       array.
     * @since Version 1.5.0
     */
    public function getDatastores($name=null)
    {
        $refs = $this->getDatastoreRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get the references of the blocking tasks.
     *
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.5.0
     */
    public function getBlockingTaskRefs()
    {
        $url = $this->url . '/blockingTasks';
        $refs = $this->svc->get($url);
        return $refs->getReference();
    }

    /**
     * Get the blocking tasks objects.
     *
     * @return array VMware_VCloud_API_Extension_BlockingTaskType object
     *               array.
     * @since Version 1.5.0
     */
    public function getBlockingTasks()
    {
        $refs = $this->getBlockingTaskRefs();
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get the references of the licensing report objects.
     *
     * @return array VMware_VCloud_API_LinkType object array
     * @since Version 1.5.0
     * @deprecated since version 5.5.0
     */
    public function getLicensingReportRefs()
    {
        $url = $this->url . '/licensing/reports';
        $lrRefs = $this->svc->get($url);
        return $lrRefs->getReport();
    }

    /**
     * Get the licensing report objects.
     *
     * @return array VMware_VCloud_API_Extension_LicensingReportType object array
     * @since Version 1.5.0
     * @deprecated since version 5.5.0
     */
    public function getLicensingReports()
    {
        $refs = $this->getLicensingReportRefs();
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Create a provider vDC.
     *
     * @param VMware_VCloud_API_Extension_VMWProviderVdcType $providerVdc
     * @return VMware_VCloud_API_Extension_VMWProviderVdcType
     * @since Version 1.0.0
     * @deprecated deprecated since version 5.1.0
     */
    public function createVMWProviderVdc($providerVdc)
    {
        $url = $this->url . '/providervdcs';
        $type = VMware_VCloud_SDK_Constants::EXTENSION_PROVIDER_VDC_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $providerVdc);
    }

    /**
     * Create a provider vDC.
     *
     * @param VMware_VCloud_API_Extension_VMWProviderVdcParamsType $params
     * @return VMware_VCloud_API_Extension_VMWProviderVdcType
     * @since Version 5.1.0
     */
    public function createProviderVdc($params)
    {
        $url = $this->url . '/providervdcsparams';
        $type = VMware_VCloud_SDK_Constants::CREATE_PROVIDER_VDC_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Create an external network.
     *
     * @param VMware_VCloud_API_Extension_VMWExternalNetworkType $externalNet
     * @return VMware_VCloud_API_Extension_VMWExternalNetworkType
     * @since Version 1.0.0
     */
    public function createVMWExternalNetwork($externalNet)
    {
        $url = $this->url . '/externalnets';
        $type = VMware_VCloud_SDK_Constants::EXTERNAL_NET_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $externalNet);
    }

    /**
     * Create a network pool.
     *
     * @param VMware_VCloud_API_Extension_PortGroupPoolType |
     *        VMware_VCloud_API_Extension_VlanPoolType |
     *        VMware_VCloud_API_Extension_FencePoolType $vmwNetPool
     * @return VMware_VCloud_API_Extension_PortGroupPoolType |
     *        VMware_VCloud_API_Extension_VlanPoolType |
     *        VMware_VCloud_API_Extension_FencePoolType
     * @since Version 1.0.0
     */
    public function createVMWNetworkPool($vmwNetPool)
    {
        $url = $this->url . '/networkPools';
        $type = VMware_VCloud_SDK_Constants::NETWORK_POOL_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $vmwNetPool);
    }

    /**
     * Register a Vim Server and a vShield manager.
     *
     * @param VMware_VCloud_API_Extension_RegisterVimServerParamsType $params
     * @return VMware_VCloud_API_Extension_RegisterVimServerParamsType
     * @since Version 1.0.0
     */
    public function registerVimServer($params)
    {
        $url = $this->url . '/action/registervimserver';
        $type = VMware_VCloud_SDK_Constants::
                REGISTER_VIM_SERVER_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Unregister Vim Server and vShield manager.
     *
     * @param VMware_VCloud_API_ReferenceType $vimServerRef   Reference to the
     *                                                        vim server to be
     *                                                        unregistered
     * @param boolean $disable Indicates whether disable the vim server first.
     * @return VMware_VCloud_API_TaskType
     * @throws VMware_VCloud_SDK_Exception
     * @since Version 1.0.0
     */
    public function unregisterVimServer($vimServerRef, $disable=true)
    {
        $url = $vimServerRef->get_href() . '/action/unregister';
        if (true === $disable)
        {
            $sdkVimServer = $this->svc->createSDKObj($vimServerRef);
            try
            {
                $task = $sdkVimServer->disable();
                if ('VMware_VClous_API_TaskType' == get_class($task))
                {
                    $this->svc->wait($task, get_status, array('success'),
                                    $iteration=2, $interval=10);
                }
            }
            catch (Exception $e)
            {
                throw $e;
            }
        }
        return $this->svc->post($url, 202);
    }

    /**
     * Retrieves the system settings.
     *
     * @return VMware_VCloud_API_Extension_SystemSettingsType
     * @since Version 1.5.0
     */
    public function getSystemSettings()
    {
        $url = $this->url . '/settings';
        return $this->svc->get($url);
    }

    /**
     * Updates the system settings.
     *
     * @param VMware_VCloud_API_Extension_SystemSettingsType $settings
     * @return VMware_VCloud_API_Extension_SystemSettingsType
     * @since Version 1.5.0
     */
    public function updateSystemSettings($settings)
    {
        $url = $this->url . '/settings';
        $type = VMware_VCloud_SDK_Constants::SYSTEM_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Retrieves the AMQP settings.
     *
     * @return VMware_VCloud_API_Extension_AmqpSettingsType
     * @since Version 1.5.0
     */
    public function getAmqpSettings()
    {
        $url = $this->url . '/settings/amqp';
        return $this->svc->get($url);
    }

    /**
     * Updates the AMQP settings.
     *
     * @param VMware_VCloud_API_Extension_AmqpSettingsType $settings
     * @return VMware_VCloud_API_Extension_AmqpSettingsType
     * @since Version 1.5.0
     */
    public function updateAmqpSettings($settings)
    {
        $url = $this->url . '/settings/amqp';
        $type = VMware_VCloud_SDK_Constants::AMQP_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Resets AMQP certificate.
     * 
     * @since Version 5.1.0
     */
    public function resetAmqpCertificate()
    {
        $url = $this->url . '/settings/amqp/action/resetAmqpCertificate';
        return $this->svc->post($url, 204);
    }

    /**
     * Resets AMQP truststore.
     *
     * @since Version 5.1.0
     */
    public function resetAmqpTruststore()
    {
        $url = $this->url . '/settings/amqp/action/resetAmqpTruststore';
        return $this->svc->post($url, 204);
    }

    /**
     * Updates AMQP certificate. AMQP certificate and trust store are mutually exclusive. Overrides AMQP trust store, if update is successful.
     * @param VMware_VCloud_API_CertificateUpdateParamsType $params
     * @return VMware_VCloud_API_CertificateUploadSocketType
     * @since Version 5.1.0
     */
    public function updateAmqpCertificate($params)
    {
        $url = $this->url . '/settings/amqp/action/updateAmqpCertificate';
        $type = VMware_VCloud_SDK_Constants::UPDATE_AMQP_CERTIFICATE_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Updates AMQP trust store. AMQP certificate and trust store are mutually exclusive. Overrides AMQP certificate, if update is successful.
     * @param VMware_VCloud_API_TrustStoreUpdateParamsType $params
     * @return VMware_VCloud_API_TrustStoreUploadSocketType
     * @since Version 5.1.0
     */
    public function updateAmqpTruststore($params)
    {
        $url = $this->url . '/settings/amqp/action/updateAmqpTruststore';
        $type = VMware_VCloud_SDK_Constants::UPDATE_AMQP_TRUSTSTORE_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Tests the AMQP connection.
     * @param VMware_VCloud_API_Extension_AmqpSettingsType $settings
     * @return boolean
     * @since Version 1.5.0
     */
    public function testAmqpConnection($settings)
    {
        $url = $this->url . '/settings/amqp/action/test';
        $type = VMware_VCloud_SDK_Constants::AMQP_SETTINGS_CONTENT_TYPE;
        $test = $this->svc->post($url, 200, $type, $settings);
        return $test->getValid();
    }

    /**
     * Returns global blocking task settings.
     *
     * @return VMware_VCloud_API_Extension_BlockingTaskSettingsType
     * @since Version 1.5.0
     */
    public function getBlockingTaskSettings()
    {
        $url = $this->url . '/settings/blockingTask';
        return $this->svc->get($url);
    }

    /**
     * Updates global blocking task settings.
     *
     * @param VMware_VCloud_API_Extension_BlockingTaskSettingsType $settings
     * @return VMware_VCloud_API_Extension_BlockingTaskSettingsType
     * @since Version 1.5.0
     */
    public function updateBlockingTaskSettings($settings)
    {
        $url = $this->url . '/settings/blockingTask';
        $type = VMware_VCloud_SDK_Constants::
                  BLOCKING_TASK_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Gets blocking task operations which are enabled.
     *
     * @return VMware_VCloud_API_TaskOperationListType
     * @since Version 1.5.0
     */
    public function getEnabledBlockingTaskOperations()
    {
        $url = $this->url . '/settings/blockingTask/operations';
        return $this->svc->get($url);
    }

    /** 
     * Updates the blocking task operations which are enabled.
     *
     * @param VMware_VCloud_API_TaskOperationListType $list
     * @return VMware_VCloud_API_TaskOperationListType
     * @since Version 1.5.0
     */
    public function updateEnabledBlockingTaskOperations($list)
    {
        $url = $this->url . '/settings/blockingTask/operations';
        $list->set_tagName('vmext:BlockingTaskOperations');
        $type = VMware_VCloud_SDK_Constants::TASK_OPERATION_LIST_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $list);
    }

    /**
     * Retrieves the product branding settings.
     *
     * @return VMware_VCloud_API_Extension_BrandingSettingsType
     * @since Version 1.5.0
     */
    public function getBrandingSettings()
    {
        $url = $this->url . '/settings/branding';
        return $this->svc->get($url);
    }

    /**
     * Updates the product branding settings.
     *
     * @param VMware_VCloud_API_Extension_BrandingSettingsType $settings
     * @return VMware_VCloud_API_Extension_BrandingSettingsType
     * @since Version 1.5.0
     */
    public function updateBrandingSettings($settings)
    {
        $url = $this->url . '/settings/branding';
        $type = VMware_VCloud_SDK_Constants::BRANDING_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Retrieves the email related settings.
     *
     * @return VMware_VCloud_API_Extension_EmailSettingsType
     * @since Version 1.5.0
     */
    public function getEmailSettings()
    {
        $url = $this->url . '/settings/email';
        return $this->svc->get($url);
    }

    /**
     * Updates email related settings.
     *
     * @param VMware_VCloud_API_Extension_EmailSettingsType $settings
     * @return VMware_VCloud_API_Extension_EmailSettingsType
     * @since Version 1.5.0
     */
    public function updateEmailSettings($settings)
    {
        $url = $this->url . '/settings/email';
        $type = VMware_VCloud_SDK_Constants::EMAIL_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Retrieves the general settings.
     *
     * @return VMware_VCloud_API_Extension_GeneralSettingsType
     * @since Version 1.5.0
     */
    public function getGeneralSettings()
    {
        $url = $this->url . '/settings/general';
        return $this->svc->get($url);
    }

    /**
     * Updates the general settings.
     *
     * @param VMware_VCloud_API_Extension_GeneralSettingsType $settings
     * @return VMware_VCloud_API_Extension_GeneralSettingsType
     * @since Version 1.5.0
     */
    public function updateGeneralSettings($settings)
    {
        $url = $this->url . '/settings/general';
        $type = VMware_VCloud_SDK_Constants::GENERAL_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Resets vCenter trust store.
     *
     * @since Version 5.1.0
     */
    public function resetVcTrustsore()
    {
        $url = $this->url . '/settings/general/action/resetVcTrustsore';
        return $this->svc->post($url, 204);
    }

    /**
     * Updates vCenter trust store.
     *
     * @param VMware_VCloud_API_Extension_VcTrustStoreUpdateParamsType $params
     * @return VMware_VCloud_API_Extension_VcTrustStoreUploadSocketType
     * @since Version 5.1.0
     */
    public function updateVcTrustsore($params)
    {
        $url = $this->url . '/settings/general/action/updateVcTrustsore';
        $type = VMware_VCloud_SDK_Constants::UPDATE_VC_TRUSTSORE_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Retrieves the LDAP settings.
     *
     * @return VMware_VCloud_API_Extension_LdapSettingsType
     * @since Version 1.5.0
     * @deprecated deprecated since version 5.1.0
     */
    public function getLdapSettings()
    {
        $url = $this->url . '/settings/ldapSettings';
        return $this->svc->get($url);
    }

    /**
     * Updates the LDAP settings.
     *
     * @param VMware_VCloud_API_Extension_LdapSettingsType $settings
     * @return VMware_VCloud_API_Extension_LdapSettingsType
     * @since Version 1.5.0
     * @deprecated since version 5.1.0
     */
    public function updateLdapSettings($settings)
    {
        $url = $this->url . '/settings/ldapSettings';
        $type = VMware_VCloud_SDK_Constants::LDAP_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Resets system LDAP SSL certificate.
     *
     * @since Version 5.1.0
     */
    public function resetLdapCertificate()
    {
        $url = $this->url . '/settings/ldapSettings/action/resetLdapCertificate';
        return $this->svc->post($url, 204);
    }

    /**
     * Resets system LDAP keystore.
     *
     * @since Version 5.1.0
     */
    public function resetLdapKeyStore()
    {
        $url = $this->url . '/settings/ldapSettings/action/resetLdapKeyStore';
        return $this->svc->post($url, 204);
    }

    /**
     * Resets system LDAP SSPI key tab.
     *
     * @since Version 5.1.0
     */
    public function resetLdapSspiKeytab()
    {
        $url = $this->url . '/settings/ldapSettings/action/resetLdapSspiKeytab';
        return $this->svc->post($url, 204);
    }

    /**
     * Updates system LDAP SSL certificate.
     *
     * @param VMware_VCloud_API_CertificateUpdateParamsType $params
     * @return VMware_VCloud_API_CertificateUploadSocketType
     * @since Version 5.1.0
     */
    public function updateLdapCertificate($params)
    {
        $url = $this->url . '/settings/ldapSettings/action/updateLdapCertificate';
        $type = VMware_VCloud_SDK_Constants::UPDATE_LDAP_CERTIFICATE_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Updates system LDAP keystore.
     *
     * @param VMware_VCloud_API_KeystoreUpdateParamsType $params
     * @return VMware_VCloud_API_KeystoreUploadSocketType
     * @since Version 5.1.0
     */
    public function updateLdapKeyStore($params)
    {
        $url = $this->url . '/settings/ldapSettings/action/updateLdapKeyStore';
        $type = VMware_VCloud_SDK_Constants::UPDATE_LDAP_KEYSTORE_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Updates system LDAP SSPI key tab.
     *
     * @param VMware_VCloud_API_SspiKeytabUpdateParamsType $params
     * @return VMware_VCloud_API_SspiKeytabUploadSocketType
     * @since Version 5.1.0
     */
    public function updateLdapSspiKeytab($params)
    {
        $url = $this->url . '/settings/ldapSettings/action/updateLdapSspiKeytab';
        $type = VMware_VCloud_SDK_Constants::UPDATE_LDAP_SSPI_KEYTAB_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Retrieves the license settings.
     *
     * @return VMware_VCloud_API_Extension_LicenseType
     * @since Version 1.5.0
     */
    public function getLicenseSettings()
    {
        $url = $this->url . '/settings/license';
        return $this->svc->get($url);
    }

    /**
     * Updates the license settings.
     *
     * @param VMware_VCloud_API_Extension_LicenseType $settings
     * @return VMware_VCloud_API_Extension_LicenseType
     * @since Version 1.5.0
     */
    public function updateLicenseSettings($settings)
    {
        $url = $this->url . '/settings/license';
        $type = VMware_VCloud_SDK_Constants::LICENSE_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Retrieves the notifications settings.
     *
     * @return VMware_VCloud_API_Extension_NotificationsSettingsType
     * @since Version 1.5.0
     */
    public function getNotificationsSettings()
    {
        $url = $this->url . '/settings/notifications';
        return $this->svc->get($url);
    }

    /**
     * Updates the notifications settings.
     *
     * @param VMware_VCloud_API_Extension_NotificationsSettingsType $settings
     * @return VMware_VCloud_API_Extension_NotificationsSettingsType
     * @since Version 1.5.0
     */
    public function updateNotificationsSettings($settings)
    {
        $url = $this->url . '/settings/notifications';
        $type = VMware_VCloud_SDK_Constants::NOTIFICATIONS_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Checks non-blocking extensions is enabled or not.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isNotificationEnabled()
    {
        $n = $this->getNotificationsSettings();
        return $n->getEnableNotifications();
    }

    /**
     * Enables non-blocking extensions.
     *
     * @return VMware_VCloud_API_Extension_NotificationsSettingsType
     * @since Version 1.5.0
     */
    public function enableNotification($enable=true)
    {
        $sets = new VMware_VCloud_API_Extension_NotificationsSettingsType();
        $sets->setEnableNotifications($enable);
        return $this->updateNotificationsSettings($sets);
    }

    /**
     * Disables non-blocking extensions.
     *
     * @return VMware_VCloud_API_Extension_NotificationsSettingsType
     * @since Version 1.5.0
     */
    public function disableNotification()
    {
        return $this->enableNotification(false);
    }

    /**
     * Retrieves the password policy settings at the system level.
     *
     * @return VMware_VCloud_API_Extension_SystemPasswordPolicySettingsType
     * @since Version 1.5.0
     */
    public function getPasswordPolicySettings()
    {
        $url = $this->url . '/settings/passwordPolicy';
        return $this->svc->get($url);
    }

    /**
     * Updates the system password policy settings.
     *
     * @param VMware_VCloud_API_Extension_SystemPasswordPolicySettingsType $settings
     * @return VMware_VCloud_API_Extension_SystemPasswordPolicySettingsType
     * @since Version 1.5.0
     */
    public function updatePasswordPolicySettings($settings)
    {
        $url = $this->url . '/settings/passwordPolicy';
        $type = 
          VMware_VCloud_SDK_Constants::SYSTEM_PASSWORD_POLICY_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Retrieves the kerberos settings.
     *
     * @return VMware_VCloud_API_Extension_KerberosSettingsType
     * @since Version 5.1.0
     */
    public function getKerberosSettings()
    {
        $url = $this->url . '/settings/kerberosSettings';
        return $this->svc->get($url);
    }

    /** 
     * Updates the system kerberos settings.
     *
     * @param VMware_VCloud_API_Extension_KerberosSettingsType $settings
     * @return VMware_VCloud_API_Extension_KerberosSettingsType
     * @since Version 5.1.0
     */
    public function updateKerberosSettings($settings)
    {
        $url = $this->url . '/settings/kerberosSettings';
        $type = VMware_VCloud_SDK_Constants::KERBEROS_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Retrieves the lookupService settings.
     *
     * @return VMware_VCloud_API_Extension_LookupServiceSettingsType
     * @since Version 5.1.0
     */
    public function getLookUpServiceSettings()
    {
        $url = $this->url . '/settings/lookupService';
        return $this->svc->get($url);
    }

    /**
     * Register / unregister Lookup Service to / from vCD. In LookupServiceParamsType 1. If LookupServiceUrl is set and not empty, the action is for register lookup service. 2. If LookupServiceUrl is unset or empty, the action is for unregister lookup service.
     *
     * @param VMware_VCloud_API_Extension_LookupServiceParamsType $settings
     * @return VMware_VCloud_API_Extension_TaskType
     * @since Version 5.1.0
     * @deprecated since version 5.1.0
     */
    public function updateLookUpServiceSettings($settings)
    {
        $url = $this->url . '/settings/lookupService';
        $type = VMware_VCloud_SDK_Constants::LOOKUPSERVICE_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $settings);
    }

    /**
     * Retrieve vCloud Director catalog setting details.
     *
     * @return VMware_VCloud_API_Extension_CatalogSettingsType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function getCatalogSettings()
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::CATALOG_SETTINGS_URL;
        return $this->svc->get($url);
    }

    /**
     * Update catalog settings details.
     *
     * @param VMware_VCloud_API_Extension_CatalogSettingsType $settings
     * @return VMware_VCloud_API_Extension_CatalogSettingsType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function updateCatalogSettings($settings)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::CATALOG_SETTINGS_URL;
        $type = VMware_VCloud_SDK_Constants::CATALOG_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Gets the representation of a strandedItem.
     *
     * @return VMware_VCloud_API_Extension_StrandedItemType
     * @since Version 5.1.0
     */
    public function getStrandedItems()
    {
        $type = 'strandedItem';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Clears all the rights that are not associated with a role or acl rule and which extension service is already deleted.
     *
     * @return null
     * @version Since 5.1.0
     */
    public function clearUnusedRights()
    {
        $url = $this->url . '/service/action/clearunusedrights';
        return $this->svc->post($url, 204);
    }

    /**
     * Cleanup for unused external localization resources.
     *
     * @return null
     * @version Since 5.1.0
     */
    public function clearUnusedLocalizationBundles()
    {
        $url = $this->url . '/service/action/clearunusedlocalizationbundles';
        $this->svc->post($url, 204);
    }

    /**
     * Checks user authorization for all services with enabled authorization, URL and request verb.
     * @param VMware_VCloud_API_Extension_AuthorizationCheckParamsType $params
     * @return boolean
     * @since Version 5.1.0
     */
    public function isAuthorized($params)
    {
        $url = $this->url . '/service/authorizationcheck';
        $type = VMware_VCloud_SDK_Constants::
                  AUTHORIZATION_CHECK_CONTENT_TYPE;
        $authorizedresponse= $this->svc->post($url, 200, $type, $params);
        return $authorizedresponse->getIsAuthorized();
    }

    /**
     * Initiates localization bundle upload.
     * @param VMware_VCloud_API_Extension_BundleUploadParamsType $params
     * @return VMware_VCloud_API_Extension_BundleUploadSocketType
     * @since Version 5.1.0
     */
    public function initiatesLocalizationBundles($params)
    {
        $url = $this->url . '/service/localizationbundles';
        $type = VMware_VCloud_SDK_Constants::
                  LOCALIZATION_BUNDLES_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Retrieve extension services query.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @version Since 5.1.0
     */
    public function getQueryExtensionServices()
    {
        $url = $this->url . '/service/query?&format=references';
        return $this->svc->get($url);
    }
}
// end of class VMware_VCloud_SDK_Extension


/**
 * A class provides convenient methods on a vCloud stranded item.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_StrandedItem extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the stranded item.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getStrandedItemRef()
    {
        return $this->getRef(
                      VMware_VCloud_SDK_Constants::STRANDED_ITEM_CONTENT_TYPE);
    }

    /**
     * Get the stranded item data object.
     *
     * @return VMware_VCloud_API_Extension_StrandedItemType
     * @since Version 5.1.0
     */
    public function getStrandedItem()
    {
        return $this->getDataObj();
    }

    /**
     * Remove stranded item.
     *
     * @return VMware_VCloud_API_TaskType
     * @version Since 5.1.0
     */
    public function removeStrandedItem()
    {
        return $this->svc->delete($this->url);
    }

    /**
     * Remove stranded item and its children.
     *
     * @return VMware_VCloud_API_TaskType
     * @version Since 5.1.0
     */
    public function forceDeleteStrandedItem()
    {
        $url = $this->url . '/action/forceDelete';
        return $this->svc->post($url, 202);
    }
}
// end of class VMware_VCloud_SDK_StrandedItem


/**
 * A class provides convenient methods on a VMware vCloud extension provider
 * vDC entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_VMWProviderVdc extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the provider vDC entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.5.0
     */
    public function getVMWProviderVdcRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::PROVIDER_VDC_CONTENT_TYPE);
    }

    /**
     * Get the provider vDC data object.
     *
     * @return VMware_VCloud_API_Extension_VMWProviderVdcType
     * @since Version 1.0.0
     */
    public function getVMWProviderVdc()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the provider vDC from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:providervdc:' . $this->getUuid();
    }

    /**
     * Get references of network pool settings.
     *
     * @param string $name Name of the network pool to get. If null, returns all
     * @return array       VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getNetworkPoolRefs($name=null)
    {
        return $this->getContainedRefs(null, $name, 'getNetworkPoolReference',
                     $this->getVMWProviderVdc()->getNetworkPoolReferences());
    }

    /**
     * Get references to VMware external network.
     *
     * @return array       VMware_VCloud_API_ReferenceType object array
     * @since Version 5.1.0
     */
    public function getExternalNetworkRefs()
    {
        return $this->getVMWProviderVdc()->getAvailableNetworks()->getNetwork();
    }

    /**
     * Get network pool settings objects.
     *
     * @param string $name Name of the network pool to get. If null, returns all
     * @return array  VMware_VCloud_API_Extension_FencePoolType |
     *                VMware_VCloud_API_Extension_PortGroupPoolType |
     *                VMware_VCloud_API_Extension_VlanPoolType object array
     * @since Version 1.0.0
     */
    public function getNetworkPools($name=null)
    {
        $refs = $this->getNetworkPoolRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Retrieve provider vDC resource pool set.
     *
     * @return VMware_VCloud_API_Extension_VMWProviderVdcResourcePoolSetType
     * @access private
     */
    private function getResourcePoolSet()
    {
        $url = $this->url . '/resourcePools';
        return $this->svc->get($url);
    }

    /**
     * Retrieve a list of provider vDC resource pools.
     *
     * @return array|null
     *              VMware_VCloud_API_Extension_VMWProviderVdcResourcePoolType
     *              object array or null
     * @since Version 1.5.0
     */
    public function getResourcePools()
    {
        $set = $this->getResourcePoolSet();
        return isset($set)? $set->getVMWProviderVdcResourcePool() : null;
    }

    /**
     * Retrieve a list of provider vDC resource pool references.
     *
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.5.0
     */
    public function getResourcePoolRefs()
    {
        $pools = $this->getResourcePools();
        if (!isset($pools))
        {
            return null;
        }
        $refs = array();
        foreach ($pools as $pool)
        {
            $ref = $pool->getResourcePoolRef();
            array_push($refs, $ref);
        }
        return $refs;
    }

    /**
     * Update backing resource pools for provider vDC.
     *
     * @param VMware_VCloud_API_Extension_UpdateResourcePoolSetParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function updateResourcePools($params)
    {
        $url = $this->url . '/action/updateResourcePools';
        $type = VMware_VCloud_SDK_Constants::
                RESOURCE_POOL_SET_UPDATE_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Modify a provider vDC.
     *
     * @param VMware_VCloud_API_Extension_VMWProviderVdcType $providerVdc
     * @return VMware_VCloud_API_Extension_VMWProviderVdcType
     * @since Version 1.0.0
     */
    public function modify($providerVdc)
    {
        $type = VMware_VCloud_SDK_Constants::
                EXTENSION_PROVIDER_VDC_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $providerVdc);
    }

    /**
     * Enable this provider vDC.
     *
     * @param boolean $enable  To enable, set to true; to disable, set to false
     * @return null
     * @since Version 1.0.0
     */
    public function enable($enable=true)
    {
        $url = $this->url . '/action/';
        if (true === $enable)
        {
            $url .= 'enable';
        }
        else
        {
            $url .= 'disable';
        }
        $this->svc->post($url, 204);
    }

    /**
     * Disable this VMware provider vDC.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function disable()
    {
        $this->enable(false);
    }

    /**
     * Check the provide vDC is enabled or not.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isEnabled()
    {
        return $this->getVMWProviderVdc()->getIsEnabled();
    }

    /**
     * Delete a VMware provider vDC.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function delete()
    {
        $task = $this->svc->delete($this->url, 202);
        $this->destroy();
        return $task;
    }

    /**
     * Merge provider vDCs. 
     *
     * @param VMware_VCloud_API_Extension_ProviderVdcMergeParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function merge($params)
    {
        $url = $this->url . '/action/merge';
        $type = VMware_VCloud_SDK_Constants::
                    EXTENSION_MERGE_PARAMS_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Migrate a VM to a different resource pool.
     *
     * @param VMware_VCloud_API_Extension_MigrateParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function migrateVms($params)
    {
        $url = $this->url . '/action/migrateVms';
        $type = VMware_VCloud_SDK_Constants::MIGRATE_VM_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Retrieve a list of storage profiles that can be added to the
     * specified provider vDC.
     *
     * @return VMware_VCloud_API_Extension_VMWStorageProfilesType
     * @since Version 5.1.0
     */
    public function getAvailableStorageProfiles()
    {
        $url = $this->url . '/availableStorageProfiles';
        return $this->svc->get($url);
    }

    /**
     * Update storage profiles belonging to a provider vDC. The request
     * parameters specify storage profiles to add or remove.
     *
     * @param VMware_VCloud_API_Extension_UpdateProviderVdcStorageProfilesParamsType
     *         $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function updateStorageProfiles($params)
    {
        $url = $this->url . '/storageProfiles';
        $type = VMware_VCloud_SDK_Constants::
                       UPDATE_PROVIDER_VDC_STORAGE_PROFILES_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Get references of provider vDC storage profiles.
     *
     * @return array|null VMware_VCloud_API_Extension_ReferenceType object array
     *                    or null
     * @since Version 5.1.0
     */
    public function getStorageProfileRefs($name=null)
    {
        return $this->getContainedRefs('vmwPvdcStorageProfile', $name,
                                       'getProviderVdcStorageProfile',
                          $this->getVMWProviderVdc()->getStorageProfiles());
    }

    /**
     * Get provider vDC storage profiles.
     *
     * @return array|null VMware_VCloud_API_Extension_VMWProviderVdcStorageProfileType
     *         object array or null
     * @since Version 5.1.0
     */
    public function getStorageProfiles($name=null)
    {
        $refs = $this->getStorageProfileRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }
}
// end of class VMware_VCloud_SDK_Extension_VMWProviderVdc


/**
 * A class provides convenient methods for a provider vDC storage profile entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_VMWProviderVdcStorageProfile extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the provider vDC storage profile entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getProviderVdcStorageProfileRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             VMW_PROVIDER_VDC_STORAGE_PROFILE_CONTENT_TYPE);
    }

    /**
     * Get the provider vDC storage profile data object.
     *
     * @return VMware_VCloud_API_Extension_VMWProviderVdcStorageProfileType
     * @since Version 5.1.0
     */
    public function getProviderVdcStorageProfile()
    {
        return $this->getDataObj();
    }

    /**
     * Modify the provider vDC storage profile.
     *
     * @param VMware_VCloud_API_Extension_VMWProviderVdcStorageProfileType $profile
     * @return VMware_VCloud_API_Extension_VMWProviderVdcStorageProfileType
     * @since Version 5.1.0
     */
    public function modify($profile)
    {
        $type = VMware_VCloud_SDK_Constants::
                VMW_PROVIDER_VDC_STORAGE_PROFILE_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $profile);
    }
}


/**
 * A class provides convenient methods on a VMware external network entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_VMWExternalNetwork extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the external network entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.5.0
     */
    public function getVMWExternalNetworkRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::EXTERNAL_NET_CONTENT_TYPE);
    }

    /**
     * Get the external network data object.
     *
     * @return VMware_VCloud_API_Extension_VMWExternalNetworkType
     * @since Version 1.0.0
     */
    public function getVMWExternalNetwork()
    {
        return $this->getDataObj();
    }

    /**
     * Construct vCloud ID of the external network from its UUID. 
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:network:' . $this->getUuid();
    }

    /**
     * Modify the external network.
     *
     * @param VMware_VCloud_API_Extension_VMWExternalNetworkType $externalNet
     * @return VMware_VCloud_API_Extension_VMWExternalNetworkType
     * @since Version 1.0.0
     */
    public function modify($externalNet)
    {
        $type = VMware_VCloud_SDK_Constants::EXTERNAL_NET_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $externalNet);
    }

    /**
     * Delete the external network.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function delete()
    {
        $task = $this->svc->delete($this->url, 202);
        $this->destroy();
        return $task;
    }

    /**
     * Reset the external network.
     *
     * @return VMware_VCloud_API_TaskType|null
     * @since Version 1.5.0
     * @deprecated since API version 5.1.0 and SDK 5.1.0
     * @this method will not work in SDK 5.1.0
     */
    public function reset()
    {
        $url = str_replace('extension/externalnet', 'network', $this->url);
        $url = $url . '/action/reset';
        return $this->svc->post($url, 202);
    }
}
// end of class VMware_VCloud_SDK_Extension_VMWExternalNetwork


/**
 * A class provides convenient methods on a VMware vCloud network pool entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_VMWNetworkPool extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the network pool entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.5.0
     */
    public function getVMWNetworkPoolRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::NETWORK_POOL_CONTENT_TYPE);
    }

    /**
     * Get the network pool data object.
     *
     * @return VMware_VCloud_API_Extension_PortGroupPoolType |
     *         VMware_VCloud_API_Extension_VlanPoolType |
     *         VMware_VCloud_API_Extension_FencePoolType
     * @since Version 1.0.0
     */
    public function getVMWNetworkPool()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the network pool from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:networkpool:' . $this->getUuid();
    }

    /**
     * Modify this network pool.
     *
     * @param VMware_VCloud_API_Extension_PortGroupPoolType |
     *        VMware_VCloud_API_Extension_VlanPoolType |
     *        VMware_VCloud_API_Extension_FencePoolType $vmwNetPool
     * @return VMware_VCloud_API_Extension_PortGroupPoolType |
     *        VMware_VCloud_API_Extension_VlanPoolType |
     *        VMware_VCloud_API_Extension_FencePoolType
     * @since Version 1.0.0
     */
    public function modify($vmwNetPool)
    {
        $type = VMware_VCloud_SDK_Constants::NETWORK_POOL_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $vmwNetPool);
    }

    /**
     * Retrieve services associated with this network pool.
     *
     * @return VMware_VCloud_API_VendorServicesType
     * @since Version 5.1.0
     */
    public function getVMWVendorServices()
    {
        $url = $this->url . '/vendorServices';
        return $this->svc->get($url);
    }

    /**
     * Delete the network pool.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function delete()
    {
        $ret = $this->svc->delete($this->url, 202);
        $this->destroy();
        return $ret;
    }
}
// end of class VMware_VCloud_SDK_Extension_VMWNetworkPool


/**
 * A class provides convenient methods on a VMware vCloud resource pool.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_VMWProviderVdcResourcePool extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the resource pool entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getResourcePoolRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::PROVIDER_VDC_RESOURCE_POOL_CONTENT_TYPE);
    }

    /**
     * Gets the resource pool data object.
     *
     * @return VMware_VCloud_API_Extension_ContainerType
     * @since Version 5.1.0
     */
    public function getResourcePool()
    {
        return $this->getDataObj();
    }


    /**
     * List all VMs using this resource pool.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getVMWResourcePoolVMList()
    {
        $url = $this->url . '/vmList?&format=references';
        return $this->svc->get($url);
    }
}
// end of class VMware_VCloud_SDK_Extension_VMWProviderVdcResourcePool


/**
 * A class provides convenient methods on a VMware vCloud host entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_Host extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a host entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.5.0
     */
    public function getHostRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::HOST_CONTENT_TYPE);
    }

    /**
     * Get a host object.
     *
     * @return VMware_VCloud_API_Extension_HostType
     * @since Version 1.0.0
     */
    public function getHost()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the host from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:host:' . $this->getUuid();
    }

    /**
     * A common function for host operations.
     *
     * @param string $name  Name of the action.
     * @param int  $expect  Expected return code of the HTTP request on success
     * @param mix    $obj   Represents as the HTTP request body
     * @param string $type  HTTP request Content-Type.
     * @access private
     */
    private function action($name, $expect=202, $obj=null, $type=null)
    {
        $url = $this->url . "/action/$name";
        return $this->svc->post($url, $expect, $type, $obj);
    }

    /**
     * Prepare a host.
     *
     * @param string $username   Username of the host.
     * @param string $password   Password of the host.
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function prepare($username, $password)
    {
        $param = new VMware_VCloud_API_Extension_PrepareHostParamsType();
        $param->setUsername($username);
        $param->setPassword($password);
        $type = VMware_VCloud_SDK_Constants::PREPARE_HOST_PARAMS_CONTENT_TYPE;
        return $this->action('prepare', 202, $param, $type);
    }

    /**
     * Unprepare a host.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function unprepare()
    {
        return $this->action('unprepare');
    }

    /**
     * Enable a host.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function enable()
    {
        return $this->action('enable');
    }

    /**
     * Disable a host.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function disable()
    {
        return $this->action('disable');
    }

    /**
     * Check whether the host is enabled or not.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isEnabled()
    {
        return $this->getHost()->getEnabled();
    } 

    /**
     * Check whether the host is ready or not.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isReady()
    {
        return $this->getHost()->getReady();
    }

    /**
     * Repair a host.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function repair()
    {
        return $this->action('repair');
    }

    /**
     * Upgrade a host agent to the current version. If the version of the host
     * is current, no action will be performed.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function upgrade()
    {
        return $this->action('upgrade');
    }
}
// end of class VMware_VCloud_SDK_Extension_Host


/**
 * A class provides convenient methods on a VMware vim server entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_VimServer extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a Vim server entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.5.0
     */
    public function getVimServerRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             VIM_SERVER_CONTENT_TYPE);
    }

    /**
     * Get a Vim server settings.
     *
     * @return VMware_VCloud_API_Extension_VimServerType
     * @since Version 1.0.0
     */
    public function getVimServer()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the Vim server from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:vimserver:' . $this->getUuid();
    }

    /**
     * Modify a vim server.
     *
     * @param VMware_VCloud_API_Extension_VimServerType $vc
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modify($vc)
    {
        $type = VMware_VCloud_SDK_Constants::VIM_SERVER_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $vc);
    }

    /**
     * Enable the Vim server.
     *
     * @param boolean $enable  To enable, set to true; to disable, set to false
     * @return VMware_VCloud_API_TaskType
     * @throws VMware_VCloud_SDK_Exception
     * @since Version 1.0.0
     */
    public function enable($enable=true)
    {
        $vc = $this->getVimServer();
        if ($vc instanceof VMware_VCloud_API_Extension_VimServerType)
        {
            if ($enable xor $vc->getIsEnabled())
            {
                $vc->setIsEnabled($enable);
                return $this->modify($vc);
            }
        }
        else
        {
            throw new VMware_VCloud_SDK_Exception("Retrieve Vim Server error\n");
        }
    }

    /**
     * Disable the Vim server.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function disable()
    {
        return $this->enable(false);
    }

    /**
     * Check whether the Vim server is enabled or not.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isEnabled()
    {
        return $this->getVimServer()->getIsEnabled();
    }
    /**
     * Get references to all VMs.
     *
     * @return VMware_VCloud_API_Extension_VmObjectRefsListType
     * @since Version 1.0.0
     */
    public function getVmRefsList()
    {
        return $this->svc->get($this->url . '/vmsList');
    }

    /**
     * Get VMs list by page number.
     *
     * @param int $page   Page number, starts from 1. Default to 0.
     * @param int $size   Page size, maximum to 100. Default to null.
     * @return VMware_VCloud_API_Extension_VmObjectRefsListType
     * @since Version 1.0.0
     */
    public function getVmRefsListByPage($page=1, $size=null)
    {
       $url = $this->url . '/vmsList?page=' . $page;
       if ($size && $size >= 1)
       {
           $url = $url . '&pageSize=' . $size;
       }
       return $this->svc->get($url);
    }

    /**
     * Retrieves references to all resource pools.  Resource pools that are
     * not assigned to org or provider vDC.
     *
     * @return VMware_VCloud_API_Extension_ResourcePoolListType
     * @since Version 1.0.0
     */
    public function getResourcePoolList()
    {
        return $this->svc->get($this->url . '/resourcePoolList');
    }

    /**
     * Get resource pools or resource pool with specified name.
     *
     * @param string $name   Name of the resource pool to retrieve. If null,
     *                       returns all
     * @return array|null   VMware_VCloud_API_Extension_ResourcePoolType array
     *                       object or null.
     * @since Version 1.0.0
     */
    public function getResourcePools($name=null)
    {
        $list = $this->getResourcePoolList();
        if (!isset($list))
        {
            return null;
        }
        $pools = $list->getResourcePool();
        if (isset($name))
        {
            $ret = array();
            foreach ($pools as $pool)
            {
                if ($name == $pool->get_name())
                {
                    array_push($ret, $pool);
                    return $ret;
                }
            }
            return null;
        }
        return $pools;
    }

    /**
     * Get references for all the available networks for the Vim server.
     *
     * @return array|null VMware_VCloud_API_Extension_VimObjectRefType objects
     *                    array or null
     * @since Version 1.5.0
     */
    public function getAvailableNetworks()
    {
        $url = $this->url . '/networks';
        $refList = $this->svc->get($url);
        $refs = $refList->getVimObjectRefs();
        if ($refs)
        {
            return $refs->getVimObjectRef();
        }
        return null;
    }

    /**
     * Imports a VM from vSphere to a vDC as a vApp.
     *
     * @param VMware_VCloud_API_Extension_ImportVmAsVAppParamsType $params
     * @return VMware_VCloud_API_VAppType
     * @since Version 1.0.0
     */
    public function importVmAsVApp($params)
    {
        $url = $this->url . '/importVmAsVApp';
        $type = VMware_VCloud_SDK_Constants::IMPORT_VMASVAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Imports a VM from vSphere to a vDC as a vApp template.
     *
     * @param VMware_VCloud_API_Extension_ImportVmAsVAppTemplateParamsType $params
     * @return VMware_VCloud_API_VAppTemplateType
     * @since Version 1.0.0
     */
    public function importVmAsVAppTemplate($params)
    {
        $url = $this->url . '/importVmAsVAppTemplate';
        $type = VMware_VCloud_SDK_Constants::
                IMPORT_VMASVAPPTEMPLATE_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Import media from vSphere to a vDC.
     *
     * @param VMware_VCloud_API_Extension_ImportMediaParamsType $params
     * @return VMware_VCloud_API_MediaType
     * @since Version 1.5.0
     */
    public function importMedia($params)
    {
        $url = $this->url . '/importMedia';
        $type = VMware_VCloud_SDK_Constants::IMPORT_MEDIA_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Import a vSphere VM into an existing vApp.
     *
     * @param VMware_VCloud_API_Extension_ImportVmIntoExistingVAppParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */ 
    public function importVmIntoExistingVApp($params)
    {
        $url = $this->url . '/importVmIntoExistingVApp';
        $type = VMware_VCloud_SDK_Constants::
                IMPORT_VM_INTO_EXISTING_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Force Vim server reconnect.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function forceReconnect()
    {
        $url = $this->url . '/action/forcevimserverreconnect';
        return $this->svc->post($url, 202);
    }

    /**
     * Refresh storage profiles data from a registered Vim server.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function refreshStorageProfiles()
    {
        $url = $this->url . '/action/refreshStorageProfiles';
        return $this->svc->post($url, 202);
    }

    /**
     * Refresh vCenter server.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function refresh()
    {
        $url = $this->url . '/action/refresh';
        return $this->svc->post($url, 202);
    }

    /**
     * Gets all the references for each host attached to the Vim server.
     *
     * @return array|null VMware_VCloud_API_ReferenceType object array or null
     * @since Version 1.5.0
     */
    public function getHostRefs()
    {
        $url = $this->url . '/hostReferences';
        $list = $this->svc->get($url);
        return (0 == sizeof($list))? null : $list->getHostReference();
    }

    /**
     * Retrieves all storage profiles in the Vim server.
     *
     * @return VMware_VCloud_API_Extension_VMWStorageProfilesType
     * @since Version 5.1.0
     *
     */
    public function getStorageProfiles()
    {
        $url = $this->url . '/storageProfiles';
        return $this->svc->get($url);
    }

    /**
     * Get a link to the registered vShield Manager.
     *
     * @return VMware_VCloud_API_LinkType
     * @since Version 5.1.0
     */
    public function getVsmRef()
    {
        $links = $this->getContainedLinks('vshieldmanager', 'down');
        return (0 == sizeof($links))? null : $links[0]; 
    }

    /**
     * Get configuration details of registered vShield Manager.
     *
     * @return VMware_VCloud_API_Extension_ShieldManagerType
     * @since Version 5.1.0
     */
    public function getVsm()
    {
        $link = $this->getVsmRef();
        if ($link == null)
        {
            return null;
        }
        else
        {
            return $this->svc->get($link->get_href());
        }
    }

    /**
     * Update configuration details of registered vShield Manager. 
     *
     * @param VMware_VCloud_API_Extension_ShieldManagerType $vsm
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function updateVsm($vsm)
    {
        $link = $this->getVsmRef();
        if ($link == null)
        {
            return null;
        }
        else
        {
            $url = $link->get_href();
            $type = VMware_VCloud_SDK_Constants::VSHIELD_MANAGER_CONTENT_TYPE;
            return $this->svc->put($url, 202, $type, $vsm);
        }
    }

    /**
     * Retrieve the vSphere URL of an object.
     *
     * @param vimObjectType
     * @param vimObjectMoref
     * @return VMware_VCloud_API_Extension_VSphereWebClientUrlType
     * @since  Version 5.1.0
     */
    public function getVSphereWebClientUrl($vimObjectType,$vimObjectMoref)
    {
        $url = $this->url . '/'.$vimObjectType.'/'.$vimObjectMoref.'/vSphereWebClientUrl';
        return $this->svc->get($url, 200);
    }
}
// end of class VMware_VCloud_SDK_Extension_VimServer


/**
 * A class provides convenient methods on a VMware data store entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_Datastore extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a data store entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.5.0
     */
    public function getDatastoreRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::DATASTORE_CONTENT_TYPE);
    }

    /**
     * Get a data store settings.
     *
     * @return VMware_VCloud_API_Extension_DatastoreType
     * @since Version 1.5.0
     */
    public function getDatastore()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the data store from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:datastore:' . $this->getUuid();
    }

    /**
     * Get MoRef of the data store.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getMoRef()
    {
        return $this->getDatastore()->getVimObjectRef()->getMoRef();
    }

    /**
     * Get the reference of the Vim server.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.5.0
     */
    public function getVimServerRef()
    {
        return $this->getDatastore()->getVimObjectRef()->getVimServerRef();
    }

    /**
     * Modify the data store.
     *
     * @param VMware_VCloud_API_Extension_DatastoreType $ds
     * @return VMware_VCloud_API_Extension_DatastoreType
     * @since Version 1.5.0
     */
    public function modify($ds)
    {
        $type = VMware_VCloud_SDK_Constants::DATASTORE_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $ds);
    }

    /**
     * Enable the data store.
     *
     * @return VMware_VCloud_API_Extension_DatastoreType
     * @since Version 1.5.0
     */
    public function enable()
    {
        $url = $this->url . '/action/enable';
        return $this->svc->post($url, 200);
    }

    /**
     * Disable the data store.
     *
     * @return VMware_VCloud_API_Extension_DatastoreType
     * @since Version 1.5.0
     */
    public function disable()
    {
        $url = $this->url . '/action/disable';
        return $this->svc->post($url, 200);
    }

    /**
     * Remove the data store.
     *
     * @return null
     * @since Version 1.5.0
     */
    public function delete()
    {
        $this->svc->delete($this->url);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_Datastore


/**
 * A class provides convenient methods on a VMware vCloud blocking task
 * entity.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_BlockingTask extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Gets the blocking task data object.
     *
     * @return VMware_VCloud_API_Extension_BlockingTaskType
     * @since Version 1.5.0
     */
    public function getBlockingTask()
    {
        return $this->getDataObj();
    }

    /**
     * Aborts a request.
     *
     * @param VMware_VCloud_API_Extension_BlockingTaskOperationParamsType $params
     * @return null
     * @since Version 1.5.0
     */
    public function abort($params)
    {
        $url = $this->url . '/action/abort';
        $type = VMware_VCloud_SDK_Constants::
                  BLOCKING_TASK_OPERATION_PARAMS_CONTENT_TYPE;
        $this->svc->post($url, 204, $type, $params);
    }

    /**
     * Fails a request.
     *
     * @param VMware_VCloud_API_Extension_BlockingTaskOperationParamsType $params
     * @return null
     * @since Version 1.5.0
     */
    public function fail($params)
    {
        $url = $this->url . '/action/fail';
        $type = VMware_VCloud_SDK_Constants::
                  BLOCKING_TASK_OPERATION_PARAMS_CONTENT_TYPE;
        $this->svc->post($url, 204, $type, $params);
    }

    /**
     * Resumes a request.
     *
     * @param VMware_VCloud_API_Extension_BlockingTaskOperationParamsType $params
     * @return null
     * @since Version 1.5.0
     */
    public function resume($params)
    {
        $url = $this->url . '/action/resume';
        $type = VMware_VCloud_SDK_Constants::
                  BLOCKING_TASK_OPERATION_PARAMS_CONTENT_TYPE;
        $this->svc->post($url, 204, $type, $params);
    }

    /**
     * Updates progress.
     *
     * @param VMware_VCloud_API_Extension_BlockingTaskUpdateProgressParamsType
     *        $params
     * @return VMware_VCloud_API_Extension_BlockingTaskType
     * @since Version 1.5.0
     */
    public function updateProgress($params)
    {
        $url = $this->url . '/action/updateProgress';
        $type = VMware_VCloud_SDK_Constants::
                  BLOCKING_TASK_UPDPROG_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }
}
// end of class VMware_VCloud_SDK_Extension_BlockingTask


/**
 * A class provides convenient methods on a VMware vCloud Service.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_Service extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the service entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getServiceRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::EXTENSION_SERVICE_CONTENT_TYPE);
    }
    /**
     * Gets the service data object.
     *
     * @return VMware_VCloud_API_Extension_AdminServiceType
     * @since Version 5.1.0
     */
    public function getService()
    {
        return $this->getDataObj();
    }

    /**
     * Updates the Extension service.
     *
     * @param VMware_VCloud_API_Extension_AdminServiceType $settings
     * @return VMware_VCloud_API_Extension_AdminServiceType
     * @since Version 5.1.0
     */
    public function updatesService($settings)
    {
        $type = VMware_VCloud_SDK_Constants::EXTENSION_SERVICE_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $settings);
    }

    /**
     * Returns the API definitions registered by this service.
     *
     * @return VMware_VCloud_API_Extension_AdminApiDefinitionType
     * @since Version 5.1.0
     */
    public function getAPIDefinitions()
    {
        $type = 'adminApiDefinition';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     *Creates API definition.
     * @param VMware_VCloud_API_Extension_AdminApiDefinitionType $params
     * @return VMware_VCloud_API_Extension_AdminApiDefinitionType
     * @since Version 5.1.0
     */
    public function addAPIDefinitions($params)
    {
        $url = $this->url . '/apidefinitions';
        $type = VMware_VCloud_SDK_Constants::
                  API_DEFINITIONS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Returns the Links registered by this service.
     *
     * @return VMware_VCloud_API_Extension_AdminServiceLinkType
     * @since Version 5.1.0
     */
    public function getServiceLinks()
    {
        $type = 'serviceLink';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Create service link.
     * @param VMware_VCloud_API_Extension_AdminServiceLinkType $params
     * @return VMware_VCloud_API_Extension_AdminServiceLinkType
     * @since Version 5.1.0
     */
    public function createServiceLinks($params)
    {
        $url = $this->url . '/links';
        $type = VMware_VCloud_SDK_Constants::
                  LINKS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Retrieves API filter.
     *
     * @return VMware_VCloud_API_Extension_ApiFilterType
     * @since Version 5.1.0
     */
    public function getApiFilterRefs()
    {
        $type = 'apiFilter';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Creates API filter.
     * @param VMware_VCloud_API_Extension_ApiFilterType $params
     * @return VMware_VCloud_API_Extension_ApiFilterType
     * @since Version 5.1.0
     */
    public function createApiFilter($params)
    {
        $url = $this->url . '/apifilters';
        $type = VMware_VCloud_SDK_Constants::
                  API_FILTERS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * List registered resource class for extension service.
     *
     * @return VMware_VCloud_API_ContainerType
     * @since Version 5.1.0
     */
    public function getResourceClass()
    {
        $type='resourceClass';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Registers resource class for extension service.
     * @param VMware_VCloud_API_Extension_ResourceClassType $params
     * @return VMware_VCloud_API_Extension_ResourceClassType
     * @since Version 5.1.0
     */
    public function registerResourceClass($params)
    {
        $url = $this->url . '/resourceclasses';
        $type = VMware_VCloud_SDK_Constants::
                  RESOURCE_CLASSES_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * List registered resource class actions for extension service.
     *
     * @return VMware_VCloud_API_ResourceClassActionType
     * @since Version 5.1.0
     */
    public function getResourceClassAction()
    {
        $type='resourceClassAction';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * List registered service resources for extension service.
     *
     * @return VMware_VCloud_API_ContainerType
     * @since Version 5.1.0
     */
    public function getServiceResources()
    {
        $type='serviceResource';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Gives the rights registered by this extension service.
     *
     * @return VMware_VCloud_API_Extension_RightRefsType
     * @since Version 5.1.0
     */
    public function getRights()
    {
        $url = $this->url . '/rights';
        return $this->svc->get($url);

    }

    /**
     * Registers right to the rights managed by this extension service.
     * @param VMware_VCloud_API_RightType $params
     * @return VMware_VCloud_API_RightType
     * @since Version 5.1.0
     */
    public function registerRights($params)
    {
        $url = $this->url . '/rights';
        $type = VMware_VCloud_SDK_Constants::
                  RIGHT_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Registers right to the rights managed by this extension service.
     * @param VMware_VCloud_API_UpdateRightsParamsType $params
     * @return VMware_VCloud_API_RightRefsType
     * @since Version 5.1.0
     */
    public function updateRights($params)
    {
        $url = $this->url . '/rights/action/updateRights';
        $type = VMware_VCloud_SDK_Constants::
                  UPDATE_RIGHTS_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * List registered resource class ACL rule for extension service.
     *
     * @return VMware_VCloud_API_ContainerType
     * @since Version 5.1.0
     */
    public function getAclRule()
    {
        $type = 'aclRule';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Checks user authorization for service, URL and request verb.
     * @param VMware_VCloud_API_Extension_AuthorizationCheckParamsType $params
     * @return boolean
     * @since Version 5.1.0
     */
    public function isAuthorized($params)
    {
        $url = $this->url . '/authorizationcheck';
        $type = VMware_VCloud_SDK_Constants::
                  AUTHORIZATION_CHECK_CONTENT_TYPE;
        $authorizedresponse= $this->svc->post($url, 200, $type, $params);
        return $authorizedresponse->getIsAuthorized();
    }

    /**
     * Retrieve a file descriptor.
     *
     * @return VMware_VCloud_API_Extension_AdminFileDescriptorType
     * @since Version 5.1.0
     */
    public function getFileDescriptor()
    {
        $type='adminFileDescriptor';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Delete Service.
     *
     *
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_Service


/**
 * A class provides convenient methods on a VMware vCloud api filter.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_ApiFilter extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the api filter entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getApiFilterRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::API_FILTERS_CONTENT_TYPE);
    }

    /**
     * Gets the api filter data object.
     *
     * @return VMware_VCloud_API_Extension_ApiFilterType
     * @since Version 5.1.0
     */
    public function getApiFilter()
    {
        return $this->getDataObj();
    }
	
    /**
     * Deletes API filter.
     *
     * @return null
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_ApiFilter


/**
 * A class provides convenient methods on a VMware vCloud ServiceLink.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_ServiceLink extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the service link entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getServiceLinkRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::LINKS_CONTENT_TYPE);
    }

     /**
     * Gets the service link data object.
     *
     * @return VMware_VCloud_API_Extension_AdminServiceLinkType
     * @since Version 5.1.0
     */
    public function getServiceLink()
    {
        return $this->getDataObj();
    }

    /**
     * Delete Service link.
     *
     *
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_ServiceLink


/**
 * A class provides convenient methods on a VMware vCloud Service api definition.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_APIDefinition extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Returns the API definitions registered by this service.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getAPIDefinitionRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::API_DEFINITIONS_CONTENT_TYPE);
    }

    /**
     * Gets the service api definition data object.
     *
     * @return VMware_VCloud_API_Extension_AdminApiDefinitionType
     * @since Version 5.1.0
     */
    public function getAPIDefinition()
    {
        return $this->getDataObj();
    }

    /**
     * Creates file descriptor.
     * @param VMware_VCloud_API_Extension_AdminFileDescriptorType $params
     * @return VMware_VCloud_API_Extension_AdminFileDescriptorType
     * @since Version 5.1.0
     */
    public function createFileDescriptor($params)
    {
        $url = $this->url . '/files';
        $type = VMware_VCloud_SDK_Constants::
                  FILE_DESCRIPTOR_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Retrieve a file descriptor.
     *
     * @return VMware_VCloud_API_Extension_AdminFileDescriptorType
     * @since Version 5.1.0
     */
    public function getFileDescriptor()
    {
        $type='adminFileDescriptor';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Delete APIDefinition.
     *
     *
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_APIDefinition


/**
 * A class provides convenient methods on a VMware vCloud File.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_File extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Returns the Files registered by this service.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getFileRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::FILE_DESCRIPTOR_CONTENT_TYPE);
    }

    /**
     * Gets the File data object.
     *
     * @return VMware_VCloud_API_Extension_AdminFileDescriptorType
     * @since Version 5.1.0
     */
    public function getFile()
    {
        return $this->getDataObj();
    }

    /**
     * Retrieve a file descriptor.
     *
     * @return VMware_VCloud_API_Extension_AdminFileDescriptorType
     * @since Version 5.1.0
     */
    public function getFileDescriptor()
    {
        $type='adminFileDescriptor';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Deletes file descriptor.
     *
     *
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_File


/**
 * A class provides convenient methods on a VMware vCloud ResourceClass.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_ResourceClass extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the ResourceClass entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getResourceClassRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::RESOURCE_CLASSES_CONTENT_TYPE);
    }

    /**
     * Gets the ResourceClass data object.
     *
     * @return VMware_VCloud_API_ResourceClassType
     * @since Version 5.1.0
     */
    public function getResourceClass()
    {
        return $this->getDataObj();
    }

    /**
     * Registers resource class Action for extension service.
     * @param VMware_VCloud_API_Extension_ResourceClassActionType $params
     * @return VMware_VCloud_API_Extension_ResourceClassActionType
     * @since Version 5.1.0
     */
    public function registerResourceClassAction($params)
    {
        $url = $this->url . '/resourceclassactions';
        $type = VMware_VCloud_SDK_Constants::
                  RESOURCECLASSACTION_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * List registered resource class actions for extension service.
     *
     * @return VMware_VCloud_API_ResourceClassActionType
     * @since Version 5.1.0
     */
    public function getResourceClassAction()
    {
        $type='resourceClassAction';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Registers resource class Action for extension service.
     * @param VMware_VCloud_API_Extension_ServiceResourceType $params
     * @return VMware_VCloud_API_Extension_ServiceResourceType
     * @since Version 5.1.0
     */
    public function registerServiceResources($params)
    {
        $url = $this->url . '/serviceresources';
        $type = VMware_VCloud_SDK_Constants::
                  SERVICERESOURCES_CLASSES_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * List registered service resources for extension service.
     *
     * @return VMware_VCloud_API_ContainerType
     * @since Version 5.1.0
     */
    public function getServiceResources()
    {
        $type='serviceResource';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Deletes resource class.
     *
     *
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_ResourceClass


/**
 * A class provides convenient methods on a VMware vCloud ResourceClassAction.
 *
 * @package VMware_VCloud_SDK_Extension
 */

class VMware_VCloud_SDK_Extension_ResourceClassAction extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the ResourceClassAction entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getResourceClassActionRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::RESOURCECLASSACTION_CONTENT_TYPE);
    }
    /**
     * Gets the ResourceClassAction data object.
     *
     * @return VMware_VCloud_API_Extension_ResourceClassActionType
     * @since Version 5.1.0
     */
    public function getResourceClassAction()
    {
        return $this->getDataObj();
    }

    /**
     * Registers resource class ACL rule for extension service.
     * @param VMware_VCloud_API_Extension_AclRuleType $params
     * @return VMware_VCloud_API_Extension_AclRuleType
     * @since Version 5.1.0
     */
    public function createAclRule($params)
    {
        $url = $this->url . '/aclrules';
        $type = VMware_VCloud_SDK_Constants::
                  ACLRULE_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * List registered resource class ACL rule for extension service.
     *
     * @return VMware_VCloud_API_ContainerType
     * @since Version 5.1.0
     */
    public function getAclRuleRef()
    {
        $type = 'aclRule';
        return $this->svc->queryReferencesByType($type);
    }

    /**
     * Deletes resource class action.
     *
     *
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_ResourceClassAction


/**
 * A class provides convenient methods on a VMware vCloud AclRule.
 *
 * @package VMware_VCloud_SDK_Extension
 */

class VMware_VCloud_SDK_Extension_AclRule extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the AclRule entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getAclRuleRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::ACLRULE_CONTENT_TYPE);
    }
    /**
     * Gets the ACL rule data object.
     *
     * @return VMware_VCloud_API_Extension_AclRuleType
     * @since Version 5.1.0
     */
    public function getAclRule()
    {
        return $this->getDataObj();
    }

    /**
     * Delete ACL rule.
     *
     *
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_AclRule


/**
 * A class provides convenient methods on a VMware vCloud serviceResources.
 *
 * @package VMware_VCloud_SDK_Extension
 */
class VMware_VCloud_SDK_Extension_serviceResources extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the serviceResources entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getserviceResourcesRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::SERVICERESOURCES_CLASSES_CONTENT_TYPE);
    }

    /**
     * Gets the service Resources data object.
     *
     * @return VMware_VCloud_API_Extension_ServiceResourceType
     * @since Version 5.1.0
     */
    public function getserviceResource()
    {
        return $this->getDataObj();
    }

    /**
     * Delete service Resource.
     *
     *
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Extension_serviceResources


/**
 * Lists supported blocking task operations
 *
 * @package VMware_VCloud_SDK_Extension
 */
final class VMware_VCloud_SDK_Extension_TaskOps
{
    /**
     *  --[system] indicates system specific blocking task operations
     *  Others are org specific blocking task operations
     */
    // vApp Lifecycle
    const IMPORT_SINGLETON_VAPP = 'importSingletonVapp'; //Import Into New vApp --[system]
    const IMPORT_INTO_EXISTING_VAPP = 'importIntoExistingVapp'; // Import Into Existing vApp --[system]
    const VDC_INSTANTIATE_VAPP = 'vdcInstantiateVapp'; // Instantiate vApp from Template
    const VDC_COPY_VAPP = 'vdcCopyVapp';   // Copy and Move vApp
    const VDC_COMPOSE_VAPP = 'vdcComposeVapp'; // Build New vApp
    const VDC_DELETE_VAPP = 'vdcDeleteVapp'; // Delete vApp
    const VDC_UPDATE_VAPP = 'vdcUpdateVapp'; // Modify vApp
    const VDC_CAPTURE_TEMPLATE= 'vdcCaptureTemplate'; // Capture vApp Template

    // vApp Power Operations
    const VAPP_DEPLOY = 'vappDeploy';   // Start vApp (Deploy from API)
    const VAPP_UNDEPLOY_SUSPEND = 'vappUndeploySuspend'; // Suspend vApp (Undeploy from API)
    const VAPP_UNDEPLOY_POWER_OFF = 'vappUndeployPowerOff';  // Stop vApp (Undeploy from API)
    const VAPP_RESET = 'vappReset';       // Power Cycle vApp
    // API only
    const VAPP_REBOOT_GUEST = 'vappRebootGuest';  // Reboot vApp Guest OS
    const VAPP_POWER_OFF = 'vappPowerOff';    // Power Off vApp
    const VAPP_SHUTDOWN_GUEST = 'vappShutdownGuest'; // Shut down vApp Guest OS
    const VAPP_SUSPEND = 'vappSuspend';   // Suspend vApp

    // vApp Template
    const IMPORT_SINGLETON_TEMPLATE = 'importSingletonTemplate'; // Import Into New vApp Template -[system]
    const VDC_UPLOAD_OVF_CONTENTS = 'vdcUploadOvfContents'; // Upload vApp Template
    const VDC_ENABLE_DOWNLOAD = "vdcEnableDownload"; // Enable vApp Template Download --[system]
    const VDC_COPY_TEMPLATE = 'vdcCopyTemplate';  // Copy and Move vApp Template
    const VDC_DELETE_TEMPLATE = 'vdcDeleteTemplate'; // Delete vApp Template
    const VDC_UPDATE_TEMPLATE = 'vdcUpdateTemplate'; // Modify vApp Template Name and Description

    // Virtual Machine
    const VDC_RECOMPOSE_VAPP= 'vdcRecomposeVapp'; // Add, Move or Delete Virtual Machines from vApp
    const VAPP_UPDATE_VM = 'vappUpdateVm';  // Modify Virtual Machine Configuration
    const VAPP_UPGRADE_HW_VERSION = 'vappUpgradeHwVersion'; // Upgrade Virtual Machine Hardware Version

    // Media
    const IMPORT_MEDIA = 'importMedia';        // Import Media -- [system]
    const VDC_COPY_MEDIA = 'vdcCopyMedia';     // Copy Media
    const VDC_DELETE_MEDIA = 'vdcDeleteMedia'; // Delete Media
    const VDC_UPLOAD_MEDIA = 'vdcUploadMedia'; // Upload Media
    const VDC_UPDATE_MEDIA = 'vdcUpdateMedia'; // Modify Media Name and Description

    // Virtual Data Center
    const CREATE_PROVIDER_VDC = 'rclCreateProviderVdc';  // Create Provider vDC
    const DELETE_PROVIDER_VDC = 'rclDeleteProviderVdc';  // Delete Provider vDC
    const VDC_CREATE_VDC = 'vdcCreateVdc'; // Create Organization vDC
    const VDC_DELETE_VDC = 'vdcDeleteVdc'; // Delete Organization vDC
    const VDC_UPDATE_VDC = 'vdcUpdateVdc'; // Modify Organization vDC

    // Network
    const NETWORK_CREATE_EXTERNAL_NETWORK = 'networkCreateExternalNetwork';  // Create External Network --[system]
    const NETWORK_CREATE_NETWORK_POOL = 'networkCreateNetworkPool';  // Create VLAN or vSphere portgroup backed Network Pool --[system]
    const NETWORK_CREATE_ISOLATED_NETWORK_POOL = 'networkCreateFencePoolTypeNetworkPool'; // Create vCD isolation backed Network Pool --[system]
    const NETWORK_DELETE_NETWORK_POOL = 'networkDeleteNetworkPool'; // Delete Network Pool --[system]
    const NETWORK_UPDATE_NETWORK_POOL = 'networkUpdateNetworkPool'; // Modify Network Pool --[system]
    const NETWORK_UPDATE_VLAN_POOL = 'networkUpdateVlanPool';       // Modify VLAN Network Pool --[system]
    const NETWORK_UPDATE_NETWORK = 'networkUpdateNetwork';  // Modify Network
    const NETWORK_DELETE = 'networkDelete';  // Delete Network
}
// end of class VMware_VCloud_SDK_TaskOps
?>
