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
 * Contains VMware vCloud SDK for PHP global utility functions
 */
require_once 'VMware/VCloud/Helper.php';

/**
 * A class provides convenient methods on a VMware vCloud administrative entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Admin extends VMware_VCloud_SDK_Abstract
{
    /**
     * Constructor
     */
    public function __construct($svc)
    {
        parent::__construct($svc, $svc->getAdminUrl());
    }

    /**
     * Get a VMware vCloud admin entity.
     *
     * @return VMware_VCloud_API_VCloudType
     * @since Version 1.0.0
     */
    public function getVCloud()
    {
        return $this->getDataObj();
    }

    /**
     * Get a link for adding a role operation.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.0.0
     */
    public function getAddRoleRef()
    {
        $links = $this->getContainedLinks('role', 'add');
        return (1 == count($links))? $links[0] : null;
    }

    /**
     * Get a link for adding an organization operation.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.0.0
     */
    public function getAddOrgRef()
    {
        $links = $this->getContainedLinks('organization', 'add');
        return (1 == count($links))? $links[0] : null;
    }

    /**
     * Get references to admin organization entities.
     *
     * @param string $name   Name of the admin organization to get. If null,
     *                       returns all
     * @return array VMware_VCloud_API_OrganizationReferenceType object array
     * @since Version 1.0.0
     */
    public function getAdminOrgRefs($name=null)
    {
        return $this->getContainedRefs('organization', $name,
                          'getOrganizationReference',
                          $this->getVCloud()->getOrganizationReferences());
    }

    /**
     * Get all admin organization or admin organization with the given name.
     *
     * @param string $name   Name of the admin organization to get. If null,
     *                       returns all.
     * @return array VMware_VCloud_API_AdminOrgType object array
     * @since Version 1.0.0
     */
    public function getAdminOrgs($name=null)
    {
        $refs = $this->getAdminOrgRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to all provider vDCs or provider vDC with the given name.
     *
     * @param string $name   Name of the provider vDC. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getProviderVdcRefs($name=null)
    {
        return $this->getContainedRefs('providervdc', $name,
                          'getProviderVdcReference',
                          $this->getVCloud()->getProviderVdcReferences());
    }

    /**
     * Get all provider vDCs or provider vDC with the given name.
     *
     * @param string $name   Name of the provider vDC. If null, returns all
     * @return array VMware_VCloud_API_ProviderVdcType object array
     * @since Version 1.0.0
     */
    public function getProviderVdcs($name=null)
    {
        $refs = $this->getProviderVdcRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to all right entities or right entity with the given name.
     *
     * @param string $name   Name of the right. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getRightRefs($name=null)
    {
        return $this->getContainedRefs('right', $name, 'getRightReference',
                                  $this->getVCloud()->getRightReferences());
    }

    /**
     * Get all right entities or right entity with the given name.
     *
     * @param string $name   Name of the right. If null, returns all
     * @return array VMware_VCloud_API_RightType object array
     * @since Version 1.0.0
     */
    public function getRights($name=null)
    {
        $refs = $this->getRightRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to all role entities or role entity with the given name.
     *
     * @param string $name   Name of the role. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getRoleRefs($name=null)
    {
        return $this->getContainedRefs('role', $name, 'getRoleReference',
                                 $this->getVCloud()->getRoleReferences());
    }

    /**
     * Get all role entities or role entity with the given name.
     *
     * @param string $name   Name of the role. If null, returns all
     * @return array VMware_VCloud_API_RoleType object array
     * @since Version 1.0.0
     */
    public function getRoles($name=null)
    {
        $refs = $this->getRoleRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to all external network entities or external
     * network entity with the given name.
     *
     * @param string $name   Name of the external network. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getExternalNetworkRefs($name=null)
    {
        return $this->getContainedRefs(null, $name, 'getNetwork',
                                       $this->getVCloud()->getNetworks());
    }

    /**
     * Get all external network entities or external network entity
     * with the given name.
     *
     * @param string $name   Name of the external network. If null, returns all
     * @return array VMware_VCloud_API_ExternalNetworkType object array
     * @since Version 1.0.0
     */
    public function getExternalNetworks($name=null)
    {
        $refs = $this->getExternalNetworkRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Create an organization entity in vCloud.
     *
     * @param VMware_VCloud_API_AdminOrgType $adminOrg
     * @return VMware_VCloud_API_AdminOrgType|null
     * @since Version 1.0.0
     */
    public function createOrganization($adminOrg)
    {
        $url = $this->url . '/orgs';
        $type = VMware_VCloud_SDK_Constants::ADMIN_ORGANIZATION_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $adminOrg);
    }

    /**
     * Create a role in vCloud.
     *
     * @param VMware_VCloud_API_RoleType $role
     * @return VMware_VCloud_API_RoleType
     * @since Version 1.0.0
     */
    public function createRole($role)
    {
        $url = $this->url . '/roles';
        $type = VMware_VCloud_SDK_Constants::ROLE_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $role);
    }

    /**
     * Get a link to the system organization.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     */
    public function getSystemOrgRef()
    {
        $links = $this->getContainedLinks('systemOrganization', 'down');
        return (1 == count($links))? $links[0] : null;
    }

    /**
     * Get a system organization data object.
     *
     * @return VMware_VCloud_API_AdminOrgType|null
     * @since Version 1.5.0
     */
    public function getSystemOrg()
    {
        $ref = $this->getSystemOrgRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Retrieves a list of groups for organization the org admin belongs to by using REST API general QueryHandler; If filter is provided it will be applied to the corresponding result set. Format determines the elements representation - references or records. Default format is references.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getGroupQuery()
    {
        $url = $this->url . '/groups/query?&format=references';
        return $this->svc->get($url, 200);
    }
}
// end of class VMware_VCloud_SDK_Admin


/**
 * A class provides convenient administrative methods on a VMware vCloud
 * organization entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_AdminOrg extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud admin organization entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getAdminOrgRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             ADMIN_ORGANIZATION_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud admin organization entity.
     *
     * @return VMware_VCloud_API_AdminOrgType
     * @since Version 1.0.0
     */
    public function getAdminOrg()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this organization from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:org:' . $this->getUuid();
    }

    /**
     * Get a link to a VMware vCloud organization entity.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     */
    public function getOrgRef()
    {
        $links = $this->getContainedLinks('org', 'alternate');
        return isset($links)? $links[0] : null;
    }

    /**
     * Get a VMware vCloud organization entity.
     *
     * @return VMware_VCloud_API_OrgType|null
     * @since Version 1.5.0
     */
    public function getOrg()
    {
        $ref = $this->getOrgRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Modify a VMware vCloud admin organization.
     *
     * @param  VMware_VCloud_API_AdminOrgType $adminOrg
     * @return VMware_VCloud_API_AdminOrgType
     * @since Version 1.0.0
     */
    public function modify($adminOrg)
    {
        $type = VMware_VCloud_SDK_Constants::ADMIN_ORGANIZATION_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $adminOrg);
    }

    /**
     * Check whether the organization is enabled.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isEnabled()
    {
        return $this->getAdminOrg()->getIsEnabled();
    }

    /**
     * Enable the organization.
     *
     * @param boolean $enable   To enable, set to true; to disable, set to false
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
     * Disable the organization.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function disable()
    {
        $this->enable(false);
    }

    /**
     * Delete the organization.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function delete()
    {
        $this->enable(false);
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }

    /**
     * Allocate a vDC to the organization.
     *
     * @param VMware_VCloud_API_AdminVdcType $adminVdc
     * @return VMware_VCloud_API_AdminVdcType
     * @since Version 1.0.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function createAdminVdc($adminVdc)
    {
        $url = $this->url . '/vdcs';
        $type = VMware_VCloud_SDK_Constants::ADMIN_VDC_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $adminVdc);
    }

    /**
     * Create an organization vDC.
     *
     * @param VMware_VCloud_API_CreateVdcParamsType $params
     * @return VMware_VCloud_API_AdminVdcType
     * @since Version 5.1.0
     */
    public function createAdminOrgVdc($params)
    {
        $url = $this->url . '/vdcsparams';
        $type = VMware_VCloud_SDK_Constants::CREATE_VDC_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Create a catalog.
     *
     * @param VMware_VCloud_API_AdminCatalogType $catalog
     * @return VMware_VCloud_API_AdminCatalogType
     * @since Version 1.0.0
     */
    public function createCatalog($catalog)
    {
        $url = $this->url . '/catalogs';
        $type = VMware_VCloud_SDK_Constants::ADMIN_CATALOG_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $catalog);
    }

    /**
     * Create or import a user.
     *
     * @param VMware_VCloud_API_UserType $user
     * @return VMware_VCloud_API_UserType
     * @since Version 1.0.0
     */
    public function createUser($user)
    {
        $url = $this->url . '/users';
        $type = VMware_VCloud_SDK_Constants::USER_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $user);
    }

    /**
     * Import a group.
     *
     * @param VMware_VCloud_API_GroupType $group
     * @return VMware_VCloud_API_GroupType
     * @since Version 1.0.0
     */
    public function importGroup($group)
    {
        $url = $this->url . '/groups';
        $type = VMware_VCloud_SDK_Constants::GROUP_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $group);
    }

    /**
     * Add a network in an organization.
     *
     * @param VMware_VCloud_API_OrgNetworkType $orgNetwork
     * @return VMware_VCloud_API_OrgNetworkType
     * @since Version 1.0.0
     * @deprecated since version 5.1.0
     */
    public function addOrgNetwork($orgNetwork)
    {
        $url = $this->url . '/networks';
        $type = VMware_VCloud_SDK_Constants::ADMIN_ORG_NETWORK_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $orgNetwork);
    }

    /**
     * Get reference(s) for all users or named user in this organization.
     *
     * @param string $name   Name of the user to get. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getUserRefs($name=null)
    {
        return $this->getContainedRefs('user', $name, 'getUserReference',
                                       $this->getAdminOrg()->getUsers());
    }

    /**
     * Get users.
     *
     * @param string $name   Name of the user to get. If null, returns all
     * @return array VMware_VCloud_API_UserType object array
     * @since Version 1.0.0
     */
    public function getUsers($name=null)
    {
        $refs = $this->getUserRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get reference(s) for all groups or named group in this organization.
     *
     * @param string $name   Name of the group. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getGroupRefs($name=null)
    {
        return $this->getContainedRefs('group', $name, 'getGroupReference',
                                       $this->getAdminOrg()->getGroups());
    }

    /**
     * Get groups.
     *
     * @param string $name   Name of the group. If null, returns all
     * @return array VMware_VCloud_API_GroupType object array
     * @since Version 1.0.0
     */
    public function getGroups($name=null)
    {
        $refs = $this->getGroupRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references for all catalogs or named catalog in this organization.
     *
     * @param string $name   Name of the catalog. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getAdminCatalogRefs($name=null)
    {
        return $this->getContainedRefs('catalog', $name,
                  'getCatalogReference', $this->getAdminOrg()->getCatalogs());
    }

    /**
     * Get catalogs.
     *
     * @param string $name   Name of the catalog to get. If null, returns all
     * @return array VMware_VCloud_API_AdminCatalogType object array
     * @since Version 1.0.0
     */
    public function getAdminCatalogs($name=null)
    {
        $refs = $this->getAdminCatalogRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references for all vDCs or named vDC in this organization.
     *
     * For System/Cloud Administrators, this method returns Admin Vdc references
     * which can be used in
     * {@link AdminVdc#getAdminVdcRef()}
     * For Organization/Tenant Administrators/Users this method returns Vdc
     * references which can be used in
     * {@link Vdc#getVdcRef()}
     * @param string $name   Name of the vDC. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getAdminVdcRefs($name=null)
    {
        return $this->getContainedRefs('vdc', $name, 'getVdc',
                                       $this->getAdminOrg()->getVdcs());
    }

    /**
     * Get vDCs.
     *
     * For System/Cloud Administrators, this method returns Admin Vdc references
     * which can be used in
     * {@link AdminVdc#getAdminVdcRef()}
     * For Organization/Tenant Administrators/Users this method returns Vdc
     * references which can be used in
     * {@link Vdc#getVdcRef()}
     * @param string $name   Name of the vDC to get. If null, returns all
     * @return array VMware_VCloud_API_AdminVdcType object array
     * @since Version 1.0.0
     */
    public function getAdminVdcs($name=null)
    {
        $refs = $this->getAdminVdcRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get reference(s) for all organization networks or organization networks
     * with the given name in administrative representation.
     *
     * @param string $name   Name of the organization network to get. If null,
     *                       returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function getAdminNetworkRefs($name=null)
    {
        return $this->getContainedRefs(null, $name, 'getNetwork',
                                       $this->getAdminOrg()->getNetworks());
    }

    /**
     * Get organization networks entities or organization networks with the
     * given name in administrative representation.
     *
     * @param string $name   Name of the organization network to get. If null,
     *                       returns all
     * @return array VMware_VCloud_API_OrgNetworkType object array for API 1.5
     * @return array VMware_VCloud_API_OrgVdcNetworkType object array for API 5.1
     * @since Version 1.0.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function getAdminNetworks($name=null)
    {
        $refs = $this->getAdminNetworkRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Gets organizational settings for this organization.
     *
     * @return VMware_VCloud_API_OrgSettingsType
     * @since Version 1.5.0
     */
    public function getOrgSettings()
    {
        $url = $this->url . '/settings';
        return $this->svc->get($url);
    }

    /**
     * Updates organization settings for this organization.
     *
     * @param VMware_VCloud_API_OrgSettingsType $settings
     * @return VMware_VCloud_API_OrgSettingsType
     * @since Version 1.5.0
     */
    public function updateOrgSettings($settings)
    {
        $url = $this->url . '/settings';
        $type = VMware_VCloud_SDK_Constants::ORG_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Gets email settings of this organization.
     *
     * @return VMware_VCloud_API_OrgEmailSettingsType
     * @since Version 1.5.0
     */
    public function getOrgEmailSettings()
    {
        $url = $this->url . '/settings/email';
        return $this->svc->get($url);
    }

    /**
     * Updates organization email settings for this organization.
     *
     * @param VMware_VCloud_API_OrgEmailSettingsType $settings
     * @return VMware_VCloud_API_OrgEmailSettingsType
     * @since Version 1.5.0
     */
    public function updateOrgEmailSettings($settings)
    {
        $url = $this->url . '/settings/email';
        $type = VMware_VCloud_SDK_Constants::ORG_EMAIL_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

	/**
     * Retrieve other organization settings.
     *
     * @return VMware_VCloud_API_OrgFederationSettingsType
     * @since API 1.5.0
     * @since SDK 5.1.0
     */
    public function getOrgFederationSettings()
    {
        $url = $this->url . '/settings/federation';
        return $this->svc->get($url);
    }

    /**
     * Update other organization settings.
     *
     * @param VMware_VCloud_API_OrgFederationSettingsType $settings
     * @return VMware_VCloud_API_OrgFederationSettingsType
     * @since  API 1.5.0
     * @since SDK 5.1.0
     */
    public function updateOrgFederationSettings($settings)
    {
        $url = $this->url . '/settings/federation';
        $type = VMware_VCloud_SDK_Constants::ORG_FEDERATION_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }
	
    /**
     * Retrieves the password policy settings for the organization.
     *
     * @return VMware_VCloud_API_OrgPasswordPolicySettingsType
     * @since Version 1.5.0
     */
    public function getOrgPasswordPolicySettings()
    {
        $url = $this->url . '/settings/passwordPolicy';
        return $this->svc->get($url);
    }

    /**
     * Updates the organization password policy settings.
     *
     * @param VMware_VCloud_API_OrgPasswordPolicySettingsType $settings
     * @return VMware_VCloud_API_OrgPasswordPolicySettingsType
     * @since Version 1.5.0
     */
    public function updateOrgPasswordPolicySettings($settings)
    {
        $url = $this->url . '/settings/passwordPolicy';
        $type = 
          VMware_VCloud_SDK_Constants::ORG_PASSWORD_POLICY_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Gets organization vApp lease settings on the level of vApp.
     *
     * @return VMware_VCloud_API_OrgLeaseSettingsType
     * @since Version 1.5.0
     */
    public function getOrgVAppLeaseSettings()
    {
        $url = $this->url . '/settings/vAppLeaseSettings';
        return $this->svc->get($url);
    }

    /**
     * Updates organization vApp lease settings on the level of vApp.
     *
     * @param VMware_VCloud_API_OrgLeaseSettingsType $settings
     * @return VMware_VCloud_API_OrgLeaseSettingsType
     * @since Version 1.5.0
     */
    public function updateOrgVAppLeaseSettings($settings)
    {
        $url = $this->url . '/settings/vAppLeaseSettings';
        $type = VMware_VCloud_SDK_Constants::VAPP_LEASE_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Gets organization vApp template lease settings on the level of vApp.
     *
     * @return VMware_VCloud_API_OrgVAppTemplateLeaseSettingsType
     * @since Version 1.5.0
     */
    public function getOrgVAppTemplateLeaseSettings()
    {
        $url = $this->url . '/settings/vAppTemplateLeaseSettings';
        return $this->svc->get($url);
    }

    /**
     * Updates organization vApp template lease settings on the level of vApp.
     *
     * @param VMware_VCloud_API_OrgVAppTemplateLeaseSettingsType $settings
     * @return VMware_VCloud_API_OrgVAppTemplateLeaseSettingsType
     * @since Version 1.5.0
     */
    public function updateOrgVAppTemplateLeaseSettings($settings)
    {
        $url = $this->url . '/settings/vAppTemplateLeaseSettings';
        $type = VMware_VCloud_SDK_Constants::
                 VAPP_TEMPLATE_LEASE_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Gets organization general settings.
     *
     * @return VMware_VCloud_API_OrgGeneralSettingsType
     * @since Version 1.5.0
     */
    public function getOrgGeneralSettings()
    {
        $url = $this->url . '/settings/general';
        return $this->svc->get($url);
    }

    /**
     * Updates organization general settings.
     *
     * @param VMware_VCloud_API_OrgGeneralSettingsType $settings
     * @return VMware_VCloud_API_OrgGeneralSettingsType
     * @since Version 1.5.0
     */
    public function updateOrgGeneralSettings($settings)
    {
        $url = $this->url . '/settings/general';
        $type = VMware_VCloud_SDK_Constants::ORG_GENERAL_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Gets organization LDAP settings.
     *
     * @return VMware_VCloud_API_OrgLdapSettingsType
     * @since Version 1.5.0
     * @deprecated since version 5.1.0
     */
    public function getOrgLdapSettings()
    {
        $url = $this->url . '/settings/ldap';
        return $this->svc->get($url);
    }

    /**
     * Updates organization LDAP settings.
     *
     * @param VMware_VCloud_API_OrgLdapSettingsType $settings
     * @return VMware_VCloud_API_OrgLdapSettingsType
     * @since Version 1.5.0
     * @deprecated since version 5.1.0
     */
    public function updateOrgLdapSettings($settings)
    {
        $url = $this->url . '/settings/ldap';
        $type = VMware_VCloud_SDK_Constants::ORG_LDAP_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 200, $type, $settings);
    }

    /**
     * Resets organization LDAP SSL certificate.
     *
     * @since Version 5.1.0
     */
    public function resetLdapCertificate()
    {
        $url = $this->url . '/settings/ldap/action/resetLdapCertificate';
        return $this->svc->post($url, 204);
    }

    /**
     * Resets organization LDAP keystore.
     *
     * @since Version 5.1.0
     */
    public function resetLdapKeyStore()
    {
        $url = $this->url . '/settings/ldap/action/resetLdapKeyStore';
        return $this->svc->post($url, 204);
    }

    /**
     * Resets organization LDAP keytab.
     *
     * @since Version 5.1.0
     */
    public function resetLdapSspiKeytab()
    {
        $url = $this->url . '/settings/ldap/action/resetLdapSspiKeytab';
        return $this->svc->post($url, 204);
    }

    /**
     * Updates organization LDAP SSL certificate.
     * @param VMware_VCloud_API_CertificateUpdateParamsType $params
     * @return VMware_VCloud_API_CertificateUploadSocketType
     * @since Version 5.1.0
     */
    public function updateLdapCertificate($params)
    {
        $url = $this->url . '/settings/ldap/action/updateLdapCertificate';
        $type = VMware_VCloud_SDK_Constants::UPDATE_LDAP_CERTIFICATE_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Updates organization LDAP keystore.
     * @param VMware_VCloud_API_KeystoreUpdateParamsType $params
     * @return VMware_VCloud_API_KeystoreUploadSocketType
     * @since Version 5.1.0
     */
    public function updateLdapKeyStore($params)
    {
        $url = $this->url . '/settings/ldap/action/updateLdapKeyStore';
        $type = VMware_VCloud_SDK_Constants::UPDATE_LDAP_KEYSTORE_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Updates organization LDAP SSPI keytab.
     * @param VMware_VCloud_API_SspiKeytabUpdateParamsType $params
     * @return VMware_VCloud_API_SspiKeytabUploadSocketType
     * @since Version 5.1.0
     */
    public function updateLdapSspiKeytab($params)
    {
        $url = $this->url . '/settings/ldap/action/updateLdapSspiKeytab';
        $type = VMware_VCloud_SDK_Constants::UPDATE_LDAP_SSPI_KEYTAB_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $params);
    }

    /**
     * Get metadata associated with the organization or metadata
     * associated with the organization for the specified key in the specified
     * domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|
     *         VMware_VCloud_API_MetadataValueType|null
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }

    /**
     * Merges the metadata for the organization with the information provided.
     *
     * @param VMware_VCloud_API_MetadataType $meta
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function mergeMetadata($meta)
    {
        $url = $this->getMetadataUrl();
        $type = VMware_VCloud_SDK_Constants::METADATA_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $meta);
    }

    /**
     * Sets the metadata for the particular key in the specified domain for the
     * organization to the value provided. Note: This will replace any existing
     * metadata information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function setMetadataByKey($key, $value, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        $type = VMware_VCloud_SDK_Constants::METADATA_VALUE_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $value);
    }

    /**
     * Deletes the metadata for the particular key in the specified domain for
     * the organization.
     *
     * @param string $key
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function deleteMetadataByKey($key, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        return $this->svc->delete($url, 202);
    }

    /**
     * Return an alternate SDK orgznization object.
     *
     * @return VMware_VCloud_SDK_Org|null
     * @since Version 1.5.0
     */
    public function getSdkOrg()
    {
        $ref = $this->getOrgRef();
        return isset($ref)? $this->svc->createSDKObj($ref) : null;
    }

    /**
     * Posts external event to the system.
     *
     * @param VMware_VCloud_API_EventType $event
     * @return null
     * @since Version 5.1.0
     */
    public function postExtEvent($event)
    {
        $url = $this->url . '/events';
        $type = VMware_VCloud_SDK_Constants::EVENT_CONTENT_TYPE;
        $this->svc->post($url, 204, $type, $event);
    }

    /**
     * Retrieve organization defaults for guest personalization settings.
     *
     * @return VMware_VCloud_API_OrgGuestPersonalizationSettingsType
     * @since Version 5.1.0
     */
    public function getGuestPersonalizationSettings()
    {
        $url = $this->url . '/settings/guestPersonalizationSettings';
        return $this->svc->get($url);
    }

    /**
     * Modify organization defaults for guest personalization settings.
     *
     * @param VMware_VCloud_API_OrgGuestPersonalizationSettingsType
     *        $settings
     * @return VMware_VCloud_API_OrgGuestPersonalizationSettingsType
     * @since Version 5.1.0
     */
    public function modifyGuestPersonalizationSettings($settings)
    {
        $url = $this->url . '/settings/guestPersonalizationSettings';
        $type = VMware_VCloud_SDK_Constants::
                  ORG_GUEST_PERSONALIZATION_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $settings);
    }

    /**
     * Retrieve organization operation limits settings.
     *
     * @return VMware_VCloud_API_OrgOperationLimitsSettingsType
     * @since Version 5.1.0
     */
    public function getOperationLimitsSettings()
    {
        $url = $this->url . '/settings/operationLimitsSettings';
        return $this->svc->get($url);
    }

    /**
     * Modify organization operation limits settings.
     *
     * @param VMware_VCloud_API_OrgOperationLimitsSettingsType $settings
     * @return VMware_VCloud_API_OrgOperationLimitsSettingsType
     * @since Version 5.1.0
     */
    public function modifyOperationLimitsSettings($settings)
    {
        $url = $this->url . '/settings/operationLimitsSettings';
        $type = VMware_VCloud_SDK_Constants::
                  ORG_OPERATION_LIMITS_SETTINGS_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $settings);
    }
}
// end of class VMware_VCloud_SDK_AdminOrg


/**
 * A class provides convenient administrative methods on a VMware vCloud vDC
 * entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_AdminVdc extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud admin vDC entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getAdminVdcRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::ADMIN_VDC_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud vDC entity in administrative view.
     *
     * @return VMware_VCloud_API_AdminVdcType
     * @since Version 1.0.0
     */
    public function getAdminVdc()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this vDc from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:vdc:' . $this->getUuid();
    }

    /**
     * Get a link to a VMware vCloud vDC entity.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     */
    public function getVdcRef()
    {
        $links = $this->getContainedLinks('vdc', 'alternate');
        return isset($links)? $links[0] : null;
    }

    /**
     * Get a VMware vCloud vDC entity.
     *
     * @return VMware_VCloud_API_VdcType|null
     * @since Version 1.5.0
     */
    public function getVdc()
    {
        $ref = $this->getVdcRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Get the link to the container entity of the VMware vCloud vDC in an
     * administrator view.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     */
    public function getAdminOrgRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the container entity of the VMware vCloud vDC in an administrator
     * view.
     *
     * @return VMware_VCloud_API_AdminOrgType|null
     * @since Version 1.5.0
     */
    public function getAdminOrg()
    {
        $ref = $this->getAdminOrgRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Modify vDC.
     *
     * @param VMware_VCloud_API_AdminVdcType $adminVdc
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modify($adminVdc)
    {
        $type = VMware_VCloud_SDK_Constants::ADMIN_VDC_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $adminVdc);
    }

    /**
     * Check whether the vDC is enabled.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isEnabled()
    {
        return $this->getAdminVdc()->getIsEnabled();
    }

     /**
     * Enable the vDC.
     *
     * @param boolean $enable   To enable, set to true; to disable, set to false
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
     * Disable the vDC.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function disable()
    {
        $this->enable(false);
    }

    /**
     * Delete the vDC.
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
     * Check whether fast provisioning is enabled.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isFastProvisioningEnabled()
    {
        $adminVdc = $this->getAdminVdc();
        $fp = $adminVdc->getUsesFastProvisioning();
        return is_null($fp)? false : $fp;
    }

    /**
     * Enable fast provisioning.
     *
     * @param boolean $enable
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function enableFastProvisioning($enable=true)
    {
        $adminVdc = $this->getAdminVdc();
        $adminVdc->setUsesFastProvisioning($enable);
        return $this->modify($adminVdc);
    }

    /**
     * Disable fast provisioning.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function disableFastProvisioning()
    {
        return $this->enableFastProvisioning(false);
    }

    /**
     * Get metadata associated with the vDC or metadata associated with the
     * vDC for the specified key in the specified domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|
     *         VMware_VCloud_API_MetadataValueType|null
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }

    /**
     * Merges the metadata for the vDC item with the information provided.
     *
     * @param VMware_VCloud_API_MetadataType $meta
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function mergeMetadata($meta)
    {
        $url = $this->getMetadataUrl();
        $type = VMware_VCloud_SDK_Constants::METADATA_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $meta);
    }

    /**
     * Sets the metadata for the particular key in the specified domain for the
     * vDC to the value provided. Note: This will replace any existing metadata
     * information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function setMetadataByKey($key, $value, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        $type = VMware_VCloud_SDK_Constants::METADATA_VALUE_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $value);
    }

    /**
     * Deletes the metadata for the particular key in the specified domain for
     * the vDC.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function deleteMetadataByKey($key, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        return $this->svc->delete($url, 202);
    }

    /**
     * Retrieves resource pools associated with an organization vDC.
     *
     * @return VMware_VCloud_API_Extension_OrganizationResourcePoolSetType|null
     * @since Version 1.5.0
     */
    public function getResourcePools()
    {
        $links = $this->getContainedLinks('OrganizationVdcResourcePoolSet',
                                          'down');
        return isset($links)? $this->svc->get($links[0]->get_href()) : null;
    }

    /**
     * Return an alternate SDK vDC object.
     *
     * @return VMware_VCloud_SDK_Vdc|null
     * @since Version 1.5.0
     */
    public function getSdkVdc()
    {
        $ref = $this->getVdcRef();
        return isset($ref)? $this->svc->createSDKObj($ref) : null;
    }

    /** 
    * List all networks for this Org vDC.
    *
    * @return array|null VMware_VCloud_API_ReferenceType object array or null
    * @since Version 5.1.0
    */
    public function getOrgVdcNetworks($name=null)
    {
        $url = $this->url . '/networks?&format=references';
        $refs= $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getReference',
                                       $refs);
    }

    /**
     * Create an Org vDC network.
     *
     * @param VMware_VCloud_API_OrgVdcNetworkType $vdcNetwork
     * @return VMware_VCloud_API_OrgVdcNetworkType
     * @since Version 5.1.0
     */
    public function addvdcNetwork($vdcNetwork)
    {
        $url = $this->url . '/networks';
        $type = VMware_VCloud_SDK_Constants::ORG_VDC_NETWORK_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $vdcNetwork);
    }

    /**
     * Get vDC storage profile references.
     *
     * @param string $name  Name of the vDC storage profile. If null, returns all
     * @return array|null VMware_VCloud_API_ReferenceType object array or null
     * @since Version 5.1.0
     */
    public function getAdminVdcStorageProfileRefs($name=null)
    {
        return $this->getContainedRefs('vdcStorageProfile', $name,
         'getVdcStorageProfile', $this->getAdminVdc()->getVdcStorageProfiles());
    }

    /**
     * Get vDC storage profiles.
     *
     * @param string $name  Name of the vDC storage profile. If null, returns all
     * @return array|null VMware_VCloud_API_AdminVdcStorageProfileType object
     *                    array or null
     * @since Version 5.1.0
     */
    public function getAdminVdcStorageProfiles($name=null)
    {
         $refs = $this->getAdminVdcStorageProfileRefs($name);
         return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Add or remove vDC storage profiles.
     *
     * @param VMware_VCloud_API_UpdateVdcStorageProfilesType $profile
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function updateVdcStorageProfiles($profile)
    {
        $url = $this->url . '/vdcStorageProfiles';
        $type = VMware_VCloud_SDK_Constants::
                UPDATE_VDC_STORAGE_PROFILES_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $profile);
    }

    /**
     * Get references to all edgegateways for this Org vDC with the given name.
     *
     * @param string $name Name of the edgegateway to get. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 5.1.0
     */
    public function getEdgeGatewayRefs($name=null)
    {
        $url = $this->url . '/edgeGateways?&format=references';
        $gatewayRefs= $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getReference',
                                       $gatewayRefs);
    }

    /**
     * Create a edgegateway.
     *
     * @param VMware_VCloud_API_GatewayType $params
     * @return VMware_VCloud_API_GatewayType
     * @since Version 5.1.0
     */
    public function createEdgeGateways($params)
    {
        $url = $this->url . '/edgeGateways';
        $type = VMware_VCloud_SDK_Constants::
                EDGE_GATEWAYS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Create a vApp based on a set of .vmx files and resource mappings
     *
     * @param VMware_VCloud_API_RegisterVAppParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function registerVApp($params)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_REGISTER_VAPP_URL;
        $type = VMware_VCloud_SDK_Constants::
                REGISTER_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }
}
// end of class VMware_VCloud_SDK_AdminVdc


/**
 * A class provides convenient methods on a VMware vCloud edgegateway entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_EdgeGateway extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud edgegateway entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getEdgeGatewayRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::EDGE_GATEWAYS_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud edgegateway entity.
     *
     * @return VMware_VCloud_API_GatewayType
     * @since Version 5.1.0
     */
    public function getEdgeGateway()
    {
        return $this->getDataObj();
    }

    /**
     * Get up link to the admin org vdc reference.
     *
     * @return VMware_VCloud_API_LinkType object
     * @since SDK Version 5.1.0
     */
    public function getAdminVdcRef()
    {
        $vdcReference = null;
        $links = $this->getEdgeGateway()->getLink();
        foreach ($links as $link)
        {
            if (($link->get_rel()== VMware_VCloud_SDK_Constants::RELATION_TYPE_UP) && ($link->get_Type() == VMware_VCloud_SDK_Constants::ADMIN_VDC_CONTENT_TYPE))
            {
                $vdcReference = $link;
            }
        }
        return $vdcReference;
    }

    /**
     * Modify a edgeGateway.
     *
     * @param VMware_VCloud_API_GatewayType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function modify($params)
    {
        $type = VMware_VCloud_SDK_Constants::EDGE_GATEWAYS_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $params);
    }

    /**
     * Update edgeGateway configuration.
     *
     * @param VMware_VCloud_API_GatewayFeaturesType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function configureServices($params)
    {
        $url = $this->url . '/action/configureServices';
        $type = VMware_VCloud_SDK_Constants::EDGEGATEWAY_SERVICECONFIGURATION_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Reapply services on an edgeGateway.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function createReapplyServices()
    {
        $url = $this->url . '/action/reapplyServices';
        return $this->svc->post($url, 202);
    }

    /**
     * Redeploy edgeGateway.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function createRedeploy()
    {
        $url = $this->url . '/action/redeploy';
        return $this->svc->post($url, 202);
    }

    /**
     * Synchronize syslog server settings on an edgeGateway.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function createSyncSyslogServerSettings()
    {
        $url = $this->url . '/action/syncSyslogServerSettings';
        return $this->svc->post($url, 202);
    }

    /**
     * Upgrade edgeGateway configuration from compact to full.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function createUpgradeConfig()
    {
        $url = $this->url . '/action/upgradeConfig';
        return $this->svc->post($url, 202);
    }

    /**
     * Delete a edgegateway.
     *
     * @return null
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 202);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_EdgeGateway


/**
 * A class provides convenient methods on a VMware vCloud provider vDC entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_ProviderVdc extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud provider vDC entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0
     */
    public function getProviderVdcRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             PROVIDER_VDC_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud provider vDC object.
     *
     * @return VMware_VCloud_API_ProviderVdcType
     * @since Version 1.5.0
     */
    public function getProviderVdc()
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
     * Retrieves all organization vDCs for the given provider vDC.
     *
     * @return array|null An array of VMware_VCloud_API_ReferenceType or null.
     * @since Version 1.5.0
     */
    public function getVdcRefs($name=null)
    {
        $url = $this->url . '/vdcReferences';
        $refs = $this->svc->get($url);
        return VMware_VCloud_SDK_Helper::getObjsByName($refs->getVdcReference(),
                                                       $name);
    }

    /**
     * Get provider vDC storage profile.
     *
     * @return an array of VMware_VCloud_API_ReferenceType objects
     * @since Version 5.1.0
     */
    public function getProviderVdcStorageProfileRefs()
    {
         return $this->getProviderVdc()->getStorageProfiles()->getProviderVdcStorageProfile();
    }

    /**
     * Get references to VMware network pools.
     *
     * @return array         VMware_VCloud_API_ReferenceType object array
     * @since Version 5.1.0
     */
    public function getVMWNetworkPoolRefs()
    {
         return $this->getProviderVdc()->getNetworkPoolReferences()->getNetworkPoolReference();
    }

    /**
     * Get references to VMware external network.
     *
     * @return array         VMware_VCloud_API_ReferenceType object
     *                       array
     * @since Version 5.1.0
     */
    public function getExternalNetworkRefs()
    {
        return $this->getProviderVdc()->getAvailableNetworks()->getNetwork();
    }

    /**
     * Get metadata associated with the provider vDC or metadata associated with
     * the provider vDC for the specified key in the specified domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|VMware_VCloud_API_MetadataValueType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }

    /**
     * Merges the metadata for the provider vDC with the information provided.
     *
     * @param VMware_VCloud_API_MetadataType $meta
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function mergeMetadata($meta)
    {
        $url = $this->getMetadataUrl();
        $type = VMware_VCloud_SDK_Constants::METADATA_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $meta);
    }

    /**
     * Sets the metadata for the particular key in the specified domain for the
     * provider vDC to the value provided. Note: This will replace any existing
     * metadata information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function setMetadataByKey($key, $value, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        $type = VMware_VCloud_SDK_Constants::METADATA_VALUE_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $value);
    }

    /**
     * Deletes the metadata for the particular key in the specified domain for
     * the provider vDC.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function deleteMetadataByKey($key, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        return $this->svc->delete($url, 202);
    }
}
// end of class VMware_VCloud_SDK_ProviderVdc


/**
 * A class provides convenient methods on a VMware vCloud role entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Role extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud role entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getRoleRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::ROLE_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud role entity.
     *
     * @return VMware_VCloud_API_RoleType
     * @since Version 1.0.0
     */
    public function getRole()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this role from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:role:' . $this->getUuid();
    }

    /**
     * Modify a role.
     *
     * @param VMware_VCloud_API_RoleType $role
     * @return VMware_VCloud_API_RoleType
     * @since Version 1.0.0
     */
    public function modify($role)
    {
        $type = VMware_VCloud_SDK_Constants::ROLE_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $role);
    }

    /**
     * Delete a role.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Role


/**
 * A class provides convenient methods on a VMware vCloud right entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Right extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud right entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getRightRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::RIGHT_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud right entity.
     *
     * @return VMware_VCloud_API_RightType
     * @since Version 5.1.0
     */
    public function getRight()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this right from its UUID.
     *
     * @return string
     * @since Version 5.1.0
     */
    public function getId()
    {
        return 'urn:vcloud:right:' . $this->getUuid();
    }

    /**
     * Updates a right.
     *
     * @param VMware_VCloud_API_RightType $params
     * @return VMware_VCloud_API_RightType
     * @since Version 5.1.0
     */
    public function updateRights($params)
    {
        $type = VMware_VCloud_SDK_Constants::RIGHT_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $params);
    }

    /**
     * Delete a right.
     *
     * @return null
     * @since Version 5.1.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Right


/**
 * A class provides convenient methods on a VMware vCloud user entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_User extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud user entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getUserRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::USER_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud user entity.
     *
     * @return VMware_VCloud_API_UserType
     * @since Version 1.0.0
     */
    public function getUser()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this user from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:user:' . $this->getUuid();
    }

    /**
     * Enable a user.
     *
     * @param boolean $enable   To enable, set to true; to disable, set to false
     * @return VMware_VCloud_API_UserType|null
     * @since Version 1.0.0
     */
    public function enable($enable=true)
    {
        $user = $this->getUser();
        $isEnabled = $user->getIsEnabled();
        if ($isEnabled === $enable)
        {
            return null;
        }
        $user->setIsEnabled($enable);
        return $this->modify($user);
    }

    /**
     * Disable a user.
     *
     * @return VMware_VCloud_API_UserType|null
     * @since Version 1.0.0
     */
    public function disable()
    {
        return $this->enable(false);
    }

    /**
     * Modify a user.
     *
     * @param VMware_VCloud_API_UserType $user
     * @return VMware_VCloud_API_UserType
     * @since Version 1.0.0
     */
    public function modify($user)
    {
        $type = VMware_VCloud_SDK_Constants::USER_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $user);
    }

    /**
     * Delete a user.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }

    /**
     * Unlock a user.
     *
     * @return null
     * @since Version 1.5.0
     * @deprecated since version 5.1.0
     */
    public function unlock()
    {
        $url = $this->url . '/action/unlock';
        return $this->svc->post($url, 204);
    }

    /**
     * Queries user privileges on a set of entities.
     *
     * @param VMware_VCloud_API_Extension_EntityReferencesType $refs
     * @return VMware_VCloud_API_Extension_UserEntityRightsType
     * @since Version 5.1.0
     */
    public function getEntityRights($refs)
    {
        $url = $this->url . '/entityRights';
        $type = VMware_VCloud_SDK_Constants::ENTITY_REFERENCES_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $refs);
    }

    /**
     * Queries user granted privileges.
     *
     * @return VMware_VCloud_API_ReferencesType
     * @since Version 5.1.0
     */
    public function getGrantedRights()
    {
        $url = $this->url . '/grantedRights';
        return $this->svc->get($url);
    }
}
// end of class VMware_VCloud_SDK_User


/**
 * A class provides convenient methods on a VMware vCloud group entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Group extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud group entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getGroupRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::GROUP_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud group entity.
     *
     * @return VMware_VCloud_API_GroupType
     * @since Version 1.0.0
     */
    public function getGroup()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this group from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:group:' . $this->getUuid();
    }

    /**
     * Modify a group.
     *
     * @param VMware_VCloud_API_GroupType $group
     * @return VMware_VCloud_API_GroupType
     * @since Version 1.0.0
     */
    public function modify($group)
    {
        $type = VMware_VCloud_SDK_Constants::GROUP_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $group);
    }

    /**
     * Delete a group.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }
}
// end of class VMware_VCloud_SDK_Group


/**
 * A class provides convenient administrative methods on a VMware vCloud catalog
 * entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_AdminCatalog extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud catalog entity in an administrator view.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getAdminCatalogRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             ADMIN_CATALOG_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud catalog entity in an administrator view.
     *
     * @return VMware_VCloud_API_AdminCatalogType
     * @since Version 1.0.0
     */
    public function getAdminCatalog()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this catalog from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:catalog:' . $this->getUuid();
    }

    /**
     * Get a reference to a VMware vCloud catalog entity.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     */
    public function getCatalogRef()
    {
        $links = $this->getContainedLinks('catalog', 'alternate');
        return isset($links)? $links[0] : null;
    }

    /**
     * Get a VMware vCloud catalog entity.
     *
     * @return VMware_VCloud_API_CatalogType
     * @since Version 1.0.0
     */
    public function getCatalog()
    {
        $ref = $this->getCatalogRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Get the link to the container entity of the catalog in an administrator
     * view.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     */
    public function getAdminOrgRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the container entity of the catalog in an administrator view.
     *
     * @return VMware_VCloud_API_AdminOrgType|null
     * @since Version 1.5.0
     */
    public function getAdminOrg()
    {
        $ref = $this->getAdminOrgRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Publish a catalog.
     *
     * @param boolean $ispub
     * @return null
     * @since Version 1.0.0
     */
    public function publish($ispub=true)
    {
        $url = $this->url . '/action/publish';
        $params = new VMware_VCloud_API_PublishCatalogParamsType();
        $params->setIsPublished($ispub);
        $type = VMware_VCloud_SDK_Constants::PUBLISH_CATALOG_PARAMS_CONTENT_TYPE;
        $this->svc->post($url, 204, $type, $params);
    }

    /**
     * Unpublish a catalog.
     *
     * @return null
     * @since Version 1.5.0
     */
    public function unpublish()
    {
        $this->publish(false);
    }

    /**
     * Modify a catalog.
     *
     * @param VMware_VCloud_API_AdminCatalogType $catalog
     * @return VMware_VCloud_API_AdminCatalogType
     * @since Version 1.0.0
     */
    public function modify($catalog)
    {
        $type = VMware_VCloud_SDK_Constants::ADMIN_CATALOG_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $catalog);
    }

    /**
     * Delete a catalog.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function delete()
    {
        $this->svc->delete($this->url, 204);
        $this->destroy();
    }

    /**
     * Get references to catalog items in this catalog entity.
     *
     * @param string $name   Name of the catalog item to get. If null,
     *                       returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getCatalogItemRefs($name=null)
    {
        return $this->getContainedRefs('catalogItem', $name, 'getCatalogItem',
                                      $this->getCatalog()->getCatalogItems());
    }

    /**
     * Get all catalog items or catalog items with the given name in this
     * catalog.
     *
     * @param string $name   Name of the catalog item to get. If null,
     *                       returns all
     * @return array VMware_VCloud_API_CatalogItemType object array
     * @since Version 1.0.0
     */
    public function getCatalogItems($name=null)
    {
        $refs = $this->getCatalogItemRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Returns owner of the catalog.
     *
     * @return VMware_VCloud_API_OwnerType
     * @since Version 1.5.0
     */
    public function getOwner()
    {
        $url = $this->url . '/owner';
        return $this->svc->get($url);
    }

    /**
     * Changes owner of the catalog.
     *
     * @param VMware_VCloud_API_OwnerType $owner
     * @return null
     * @since Version 1.5.0
     */
    public function changeOwner($owner)
    {
        $url = $this->url . '/owner';
        $type = VMware_VCloud_SDK_Constants::OWNER_CONTENT_TYPE;
        $this->svc->put($url, 204, $type, $owner);
    }

    /**
     * Get metadata associated with the catalog or metadata associated with
     * the catalog for the specified key in the specified domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|VMware_VCloud_API_MetadataValueType|null
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }

    /**
     * Merges the metadata for the catalog with the information provided.
     *
     * @param VMware_VCloud_API_MetadataType $meta
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function mergeMetadata($meta)
    {
        $url = $this->getMetadataUrl();
        $type = VMware_VCloud_SDK_Constants::METADATA_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $meta);
    }

    /**
     * Sets the metadata for the particular key in the specified domain for the
     * catalog to the value provided. Note: This will replace any existing
     * metadata information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function setMetadataByKey($key, $value, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        echo "adminCat meta url: $url\n";
        echo $value->export() . "\n";
        $type = VMware_VCloud_SDK_Constants::METADATA_VALUE_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $value);
    }

    /**
     * Deletes the metadata for the particular key in the specified domain for
     * the catalog.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function deleteMetadataByKey($key, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        return $this->svc->delete($url, 202);
    }

    /**
     * Return an alternate SDK catalog object.
     *
     * @return VMware_VCloud_SDK_Catalog|null
     * @since Version 1.5.0
     */
    public function getSdkCatalog()
    {
        $ref = $this->getCatalogRef();
        return isset($ref)? $this->svc->createSDKObj($ref) : null;
    }

    /**
     * Publish a catalog to external orgs.
     *
     * @param VMware_VCloud_API_PublishExternalCatalogParamsType $params
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function publishToExternalOrganizations($params)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_PUBLISH_TO_EXTERNAL_ORGANIZATIONS_URL;
        $type = VMware_VCloud_SDK_Constants::PUBLISH_TO_EXTERNAL_ORGANIZATIONS_CONTENT_TYPE;
        $this->svc->post($url, 204, $type, $params);
    }

    /**
     * Subscribe to an external catalog.
     * @param VMware_VCloud_API_ExternalCatalogSubscriptionParamsType $params
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function subscribeToExternalCatalog($params)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_SUBSCRIBE_TO_EXTERNAL_CATALOG_URL;
        $type = VMware_VCloud_SDK_Constants::SUBSCRIBE_TO_EXTERNAL_CATALOG_CONTENT_TYPE;
        $this->svc->post($url, 204, $type, $params);
    }
}
// end of class VMware_VCloud_SDK_AdminCatalog


/**
 * A class provides convenient admininistrative methods on a VMware vCloud
 * network entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_AdminNetwork extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud network entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getAdminNetworkRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             ADMIN_NETWORK_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud network entity in an administrative view.
     *
     * @return VMware_VCloud_API_OrgNetworkType|VMware_VCloud_API_ExternalNetworkType
     * @since Version 1.0.0
     */
    public function getAdminNetwork()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this network from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:network:' . $this->getUuid();
    }

    /**
     * Get up link to the admin org vdc reference.
     *
     * @return VMware_VCloud_API_LinkType object
     * @since SDK Version 5.1.0
     */
    public function getAdminVdcRef()
    {
        $vdcReference = null;
        $links = $this->getAdminNetwork()->getLink();
        foreach ($links as $link)
        {
            if (($link->get_rel()== VMware_VCloud_SDK_Constants::RELATION_TYPE_UP) && ($link->get_Type() == VMware_VCloud_SDK_Constants::ADMIN_VDC_CONTENT_TYPE))
            {
                $vdcReference = $link;
            }
        }
        return $vdcReference;
    }

    /**
     * Get admin org vdc this network belongs to.
     *
     * @return VMware_VCloud_API_AdminVdcType object
     * @since SDK Version 5.1.0
     */
    public function getAdminVdc()
    {
        return $this->svc->get($this->getAdminVdcRef()->get_href());
    }

    /**
     * Get a link to a VMware vCloud network entity. 
     * This method works only for API 1.5 which contains Org Networks.
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function getNetworkRef($type='orgNetwork')
    {
        $links = $this->getContainedLinks($type, 'alternate');
        return isset($links)? $links[0] : null;
    }

    /**
     * Get a VMware vCloud network entity.
     *
     * @return VMware_VCloud_API_OrgNetworkType for API 1.5
     * @return VMware_VCloud_API_OrgVdcNetworkType for API 5.1
     * @since Version 1.0.0
     */
    public function getNetwork()
    {
        $ref = $this->getNetworkRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Get the link to the container entity of the organization network in an
     * administrator view.
     * This method works only for API 1.5 which returns Org Networks.
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function getAdminOrgRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the container entity of the organization network in an administrator
     * view.
     *
     * @return VMware_VCloud_API_AdminOrgType|null
     * @since Version 1.5.0
     * @deprecated since API version 1.5.0, since SDK 5.1.0
     */
    public function getAdminOrg()
    {
        $ref = $this->getAdminOrgRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Modify an organization network. This method works only for API 1.5.
     *
     * @param VMware_VCloud_API_OrgNetworkType $orgNetwork
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function modify($orgNetwork)
    {
        $type = VMware_VCloud_SDK_Constants::ADMIN_ORG_NETWORK_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $orgNetwork);
    }

    /**
     * Update an organization vdc network.
     *
     * @param VMware_VCloud_API_OrgVdcNetworkType $orgVdcNetwork
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function updateOrgVdcNetwork($orgVdcNetwork)
    {
        $type = VMware_VCloud_SDK_Constants::ORG_VDC_NETWORK_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $orgVdcNetwork);
    }

    /**
     * Reset only Isolated Organization Vdc Network.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @deprecated since API version 5.1.0 and SDK 5.1.0
     * @this method will not work in SDK 5.1.0
     */
    public function reset()
    {
        $url = $this->url . '/action/reset';
        return $this->svc->post($url, 202);
    }

    /**
     * Delete an organization network or an organization Vdc Network.
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
     * Get metadata associated with the network or metadata associated with the
     * organization network for the specified key in the specified domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|VMware_VCloud_API_MetadataValueType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }

    /**
     * Merges the metadata for the organization network with the information
     * provided.
     *
     * @param VMware_VCloud_API_MetadataType $meta
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function mergeMetadata($meta)
    {
        $url = $this->getMetadataUrl();
        $type = VMware_VCloud_SDK_Constants::METADATA_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $meta);
    }

    /**
     * Sets the metadata for the particular key in the specified domain for the
     * organization network to the value provided. Note: This will replace any
     * existing metadata information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function setMetadataByKey($key, $value, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        $type = VMware_VCloud_SDK_Constants::METADATA_VALUE_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $value);
    }

    /**
     * Deletes the metadata for the particular key in the specified domain for
     * the organization network.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @version 5.1.0
     */
    public function deleteMetadataByKey($key, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        return $this->svc->delete($url, 202);
    }

    /**
     * Synchronize syslog server settings of a logical network with system
     * defaults. Synchronize operation can be performed on:
     *   - routed organization network
     *   - routed/fenced vApp networks
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function syncSyslogServerSettings()
    {
        $url = $this->url . '/action/syncSyslogServerSettings';
        return $this->svc->post($url, 202);
    }

    /**
     * Return an alternate SDK organization network object.
     *
     * @return VMware_VCloud_SDK_Network|null
     * @since Version 1.5.0
     * @deprecated since API version 5.1.0, since SDK 5.1.0
     */
    public function getSdkNetwork()
    {
        $ref = $this->getNetworkRef();
        return isset($ref)? $this->svc->createSDKObj($ref) : null;
    }

    /**
     * Returns the allocated IPs associated with the network.
     *
     * @return array|null VMware_VCloud_API_AllocatedIpAddressType objects
     *         array or null
     * @since Version 5.1.0
     */
    public function getAllocatedIpAddresses()
    {
        $url = $this->url . '/allocatedAddresses';
        $addrArr = $this->svc->get($url);
        return (0 == sizeof($addrArr)) ? null : $addrArr->getIpAddress();
    }
}
// end of class VMware_VCloud_SDK_AdminNetwork


/**
 * A class provides convenient admininistrative methods on a VMware vCloud vDC
 * storage profile entity
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_AdminVdcStorageProfile extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a VMware vCloud vDC storage profile entity in an administrative view.
     *
     * @return VMware_VCloud_API_AdminVdcStorageProfileType
     * @since Version 5.1.0
     */
    public function getAdminVdcStorageProfile()
    {
        return $this->getDataObj();
    }

    /**
     * Get a reference to a vDC storage profile entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getAdminVdcStorageProfileRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                            ADMIN_VDC_STORAGE_PROFILE_CONTENT_TYPE);
    }

    /**
     * Updates a single vDC storage profile.
     *
     * @param VMware_VCloud_API_AdminVdcStorageProfileType $profile
     * @return VMware_VCloud_API_AdminVdcStorageProfileType
     * @since Version 5.1.0
     */
    public function update($profile)
    {
        $type = VMware_VCloud_SDK_Constants::
                ADMIN_VDC_STORAGE_PROFILE_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $profile);
    }

    /**
     * Get metadata associated with the vDC storage profile or metadata
     * associated with the vDC storage profile for the specified key
     * in the specified domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|
     *         VMware_VCloud_API_MetadataValueType|null
     * @since Version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }

    /**
     * Merges the metadata for the vDC storage profile with the information
     * provided.
     *
     * @param VMware_VCloud_API_MetadataType $meta
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function mergeMetadata($meta)
    {
        $url = $this->getMetadataUrl();
        $type = VMware_VCloud_SDK_Constants::METADATA_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $meta);
    }

    /**
     * Sets the metadata for the particular key in the specified domain for the
     * vDC storage profile to the value provided. Note: This will
     * replace any existing metadata information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function setMetadataByKey($key, $value, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        $type = VMware_VCloud_SDK_Constants::METADATA_VALUE_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $value);
    }

    /**
     * Deletes the metadata for the particular key in the specified domain for
     * the vDC storage profile.
     *
     * @param string $key
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function deleteMetadataByKey($key, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        return $this->svc->delete($url, 202);
    }

    /**
     * Get provider vDC storage profile reference.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getProviderVdcStorageProfileRef()
    {
        return $this->getAdminVdcStorageProfile()->getProviderVdcStorageProfile();
    }

    /**
     * Get provider vDC storage profile.
     *
     * @return VMware_VCloud_API_ProviderVdcStorageProfileType
     * @since Version 5.1.0
     */
    public function getProviderVdcStorageProfile()
    {
         $ref = $this->getProviderVdcStorageProfileRef();
         return $this->svc->get($ref->get_href());
    }
}
// end of class VMware_VCloud_SDK_AdminVdcStorageProfile


/**
 * A class provides convenient admininistrative methods on a VMware vCloud
 * provider vDC storage profile entity
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_ProviderVdcStorageProfile extends VMware_VCloud_SDK_Abstract
{
    /**
     * Retrieve provider vDC storage profile details.
     *
     * @return VMware_VCloud_API_ProviderVdcStorageProfileType
     * @since Version 5.1.0
     */
    public function getProviderVdcStorageProfile()
    {
        return $this->getDataObj();
    }

    /**
     * Get a reference to a provider vDC storage profile entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getProviderVdcStorageProfileRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                            PROVIDER_VDC_STORAGE_PROFILE_CONTENT_TYPE);
    }

    /**
     * Update a provider vDC storage profile. You can update a provider vDC
     * storage profile to enable or disable it.
     *
     * @param VMware_VCloud_API_ProviderVdcStorageProfileType $profile
     * @return VMware_VCloud_API_ProviderVdcStorageProfileType
     * @since Version 5.1.0
     */
    public function update($profile)
    {
        $type = VMware_VCloud_SDK_Constants::
                PROVIDER_VDC_STORAGE_PROFILE_CONTENT_TYPE;
        return $this->put($url, 200, $type, $profile);
    }

    /**
     * Get metadata associated with the provider vDC storage profile or metadata
     * associated with the provider vDC storage profile for the specified key
     * in the specified domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|
     *         VMware_VCloud_API_MetadataValueType|null
     * @since Version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }

    /**
     * Merges the metadata for the provider vDC storage profile with the
     * information provided.
     *
     * @param VMware_VCloud_API_MetadataType $meta
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function mergeMetadata($meta)
    {
        $url = $this->getMetadataUrl();
        $type = VMware_VCloud_SDK_Constants::METADATA_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $meta);
    }

    /**
     * Sets the metadata for the particular key in the specified domain for the
     * provider vDC storage profile to the value provided. Note: This will
     * replace any existing metadata information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function setMetadataByKey($key, $value, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        $type = VMware_VCloud_SDK_Constants::METADATA_VALUE_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $value);
    }

    /**
     * Deletes the metadata for the particular key in the specified domain for
     * the provider vDC storage profile.
     *
     * @param string $key
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function deleteMetadataByKey($key, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        return $this->svc->delete($url, 202);
    }
}
// end of class VMware_VCloud_SDK_ProviderVdcStorageProfile
?>
