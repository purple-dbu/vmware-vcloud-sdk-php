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
 * @see VMware_VCloud_SDK_Abstract
 */
require_once 'VMware/VCloud/Abstract.php';

/**
 * A class provides convenient methods on a VMware vCloud organization entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Org extends VMware_VCloud_SDK_Abstract
{
    /**
     * Return a reference to a VMware vCloud organization entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getOrgRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             ORGANIZATION_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud organization entity.
     *
     * @return VMware_VCloud_API_OrgType
     * @since Version 1.0.0
     */
    public function getOrg()
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
     * Get references to VMware vCloud vDC entities.
     *
     * @param string $name  Name of the VMware vCloud vDC. If null, returns all
     * @return array VMware_VCloud_API_LinkType object array
     * @since Version 1.0.0
     */
    public function getVdcRefs($name=null)
    {
        return $this->getContainedRefs('vdc', $name);
    }

    /**
     * Get all VMware vCloud vDCs or vDCs with a given name in the organization.
     *
     * @param string $name  Name of the VMware vCloud vDC. If null, returns all
     * @return array VMware_VCloud_API_VdcType object array
     * @since Version 1.0.0
     */
    public function getVdcs($name=null)
    {
        $refs = $this->getVdcRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to VMware vCloud catalog entities.
     *
     * @param string $name  Name of the catalog. If null, returns all
     * @return array        VMware_VCloud_API_LinkType object array
     * @since Version 1.0.0
     */
    public function getCatalogRefs($name=null)
    {
        return $this->getContainedRefs('vcloud.catalog', $name);
    }

    /**
     * Get references to VMware vCloud catalog entities.
     *
     * @param string $name  Name of the catalog. If null, returns all
     * @return array        VMware_VCloud_API_ReferenceType object array
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function getCatalogReferences($name=null)
    {
        $refs= array();
        $links = $this->getCatalogRefs($name);
        if($links)
        {
            foreach($links as $link)
            {
                $ref = VMware_VCloud_SDK_Helper::createReferenceTypeObj($link->get_href(), null, $link->get_type(), $link->get_name());
                array_push($refs, $ref);
            }
        }
        return $refs;
    }

    /**
     * Get all catalogs or catalogs with a given name in the organization.
     *
     * @param string $name  Name of the catalog. If null, returns all
     * @return array        VMware_VCloud_API_CatalogType object array
     * @since Version 1.0.0
     */
    public function getCatalogs($name=null)
    {
        $refs = $this->getCatalogRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to VMware vCloud organization network entities.
     *
     * @param string $name  Name of the network. If null, returns all
     * @return array        VMware_VCloud_API_LinkType object array
     * @since Version 1.0.0
     * @deprecated since SDK 5.1.0
     */
    public function getOrgNetworkRefs($name=null)
    {
        return $this->getContainedRefs('orgNetwork', $name);
    }

    /**
     * Get VMware vCloud organization network entity.
     *
     * @param string $name  Name of the network. If null, returns all
     * @return array        VMware_VCloud_API_OrgNetworkType object array
     * @since Version 1.0.0
     * @deprecated since SDK 5.1.0
     */
    public function getOrgNetworks($name=null)
    {
        $refs = $this->getOrgNetworkRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get a reference to VMware vCloud tasks list entity.
     *
     * @return VMware_VCloud_API_LinkType
     * @since Version 1.0.0
     */
    public function getTasksListRef()
    {
        $refs = $this->getContainedRefs('tasksList');
        return $refs[0];
    }

    /**
     * Get a VMware vCloud tasks list entity.
     *
     * @return VMware_VCloud_API_TasksListType
     * @since Version 1.0.0
     */
    public function getTasksList()
    {
        $ref = $this->getTasksListRef();
        return $this->svc->get($ref->get_href());
    }

    /**
     * Get all the tasks in the organization.
     *
     * @return array VMware_VCloud_API_TaskType object array
     * @since Version 1.5.0
     */
    public function getTasks()
    {
        $list = $this->getTasksList();
        return $list->getTask();
    }

    /**
     * Get metadata associated with the organization or metadata associated
     * with the organization for the specified key in the specified domain.
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
}
// end of class VMware_VCloud_SDK_Org


/**
 * A class provides convenient methods on a VMware vCloud vDC entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Vdc extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud vDC entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getVdcRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::VDC_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud vDC entity.
     *
     * @return VMware_VCloud_API_VdcType
     * @since Version 1.0.0
     */
    public function getVdc()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this vDC from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:vdc:' . $this->getUuid();
    }

    /**
     * Get the link to the container of the vDC.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.0.0
     */
    public function getOrgRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the container entity of the vDC.
     *
     * @return VMware_VCloud_API_OrgType|null
     * @since Version 1.0.0
     */
    public function getOrg()
    {
        $ref = $this->getOrgRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Get references to VMware vCloud available organization networks.
     *
     * @param string $name  Name of the available networks. If null, returns all
     * @return array        VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getAvailableNetworkRefs($name=null)
    {
        return $this->getContainedRefs(null, $name, 'getNetwork',
                       $this->getVdc()->getAvailableNetworks());
    }

    /**
     * Get references to all edgegateways for this Org vDC with the given name.
     *
     * @param string $name Name of the edgegateway to get. If null, returns all
     * @return array VMware_VCloud_API_ReferenceType object array
     * @since API Version 5.1.0
     * @since SDK Version 5.5.0
     */
    public function getEdgeGatewayRefs($name = null)
    {
        $links = $this->getVdc()->getLink();
        foreach ($links as $link)
        {
            if ($link->get_rel() == 'edgeGateways')
            {
                $url = $link->get_href();
                $edgeGatewayReferences = $this->svc->get($url . '?&format=references');
                return $this->getContainedRefs(null, $name, 'getReference', $edgeGatewayReferences);
            }
        }
        throw new VMware_VCloud_SDK_Exception ("Link not found.\n");
    }

    /**
     * Get VMware vCloud available organization networks/organization vdc networks.
     *
     * @param string $name  Name of the available networks. If null, returns all
      * @return array        VMware_VCloud_API_OrgNetworkType object array for 1.5
     * @return array        VMware_VCloud_API_OrgVdcNetworkType object array for 5.1
     * @since Version 1.0.0
     */
    public function getAvailableNetworks($name=null)
    {
        $refs = $this->getAvailableNetworkRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Create an isolated/routed Org vDC network
     * which can be created by an org administrator.
     *
     * @param VMware_VCloud_API_OrgVdcNetworkType $vdcNetwork
     * @return VMware_VCloud_API_OrgVdcNetworkType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function addOrgVdcNetwork($vdcNetwork)
    {
        $url = null;
        $links = $this->getVdc()->getLink();
        foreach ($links as $link)
        {
            if ($link->get_rel() == 'orgVdcNetworks')
            {
                $url = $link->get_href();
                break;
            }
        }
        if (is_null($url))
        {
            throw new VMware_VCloud_SDK_Exception ("Link not found.\n");
        }
        $type = VMware_VCloud_SDK_Constants::ORG_VDC_NETWORK_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $vdcNetwork);
    }

    /**
     * Get references to all vApps or vApps with a given name in the vDC.
     *
     * @param string $name  Name of the vApp. If null, returns all
     * @return array        VMware_VCloud_API_ResourceReferenceType object array
     * @since Version 1.0.0
     */
    public function getVAppRefs($name=null)
    {
        return $this->getContainedRefs('vApp', $name, 'getResourceEntity',
                              $this->getVdc()->getResourceEntities());
    }

    /**
     * Get all vApps or vApps with a given name in the vDC.
     *
     * @param string $name  Name of the vApp. If null, returns all
     * @return array        VMware_VCloud_API_VAppType object array
     * @since Version 1.0.0
     */
    public function getVApps($name=null)
    {
        $refs = $this->getVAppRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get references to all VMware vCloud vApp templates or vApp templates with
     * a given name in the vDC.
     *
     * @param string $name  Name of the vApp template. If null, returns all
     * @return array        VMware_VCloud_API_ReferenceType object array
     * @since Version 1.0.0
     */
    public function getVAppTemplateRefs($name=null)
    {
        return $this->getContainedRefs('vAppTemplate',
                                       $name,
                                       'getResourceEntity',
                                       $this->getVdc()->getResourceEntities());
    }

    /**
     * Get all VMware vCloud vApp templates in this vDC or vApp templates with
     * a given name.
     *
     * @param string $name  Name of the vApp template. If null, returns all
     * @return array        VMware_VCloud_API_VAppTemplateType object array
     * @since Version 1.0.0
     */
    public function getVAppTemplates($name=null)
    {
        $refs = $this->getVAppTemplateRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Upload vApp template from an OVF pacakage.
     *
     * @param string $name                     Name of the vApp template to be created
     * @param string $ovfDescriptorPath        Path to the OVF descriptor
     * @param string $description              Description of the vApp template to be
     *                                         created
     * @param boolean $manifestRequired        A flag indicates the manifest file is
     *                                         required or not
     * @param VMware_VCloud_API_ReferenceType  $vdcStorageProfileRef
     * @param VMware_VCloud_API_ReferenceType  $catalogRef
     * @return string A URL of the newly created vApp template.
     * @throws VMware_VCloud_SDK_Exception
     * @since Version 1.0.0
     */
    public function uploadOVFAsVAppTemplate($name, $ovfDescriptorPath, $description=null, $manifestRequired=null, 

$vdcStorageProfileRef, $catalogRef)
    {
        //Check if the resource name is already existing in the catalog.
        $catalog= $this->checkCatalogForDuplicates($catalogRef, $name);

        //step 1: initial the upload by sending a upload vApp template request
        $vAppTemp = $this->sendUploadVAppTemplateRequest($name, 'ovf',
                                             $description, $manifestRequired, $vdcStorageProfileRef);
        if (!isset($vAppTemp) ||
            !($vAppTemp instanceof VMware_VCloud_API_VAppTemplateType))
        {
            throw new VMware_VCloud_SDK_Exception (
                        "Send upload vApp template request failed.\n");
        }

        $status = $vAppTemp->get_status();
        if ($status != 0)
        {
             throw new VMware_VCloud_SDK_Exception (
                       "vApp template status is not 0, status = $status.\n");
        }

        //step 2: get OVF descriptor upload URL from response vApp template
        $files = $this->getUploadFiles($vAppTemp);
        $refs = VMware_VCloud_SDK_Helper::getContainedLinks(null,
                                              'upload:default', $files[0]);
        $ovfUrl = $refs[0]->get_href();

        //step 3: upload an OVF descriptor
        $this->uploadOVFDescriptor($ovfUrl, $ovfDescriptorPath);
        //wait until OVF descriptor get uploaded
        $vAppTemp2 = $this->svc->wait($vAppTemp, 'get_ovfDescriptorUploaded',
                                      array(true));

        //step 4: get upload URL for each virtual disk and upload the disk file
        $files = $this->getUploadFiles($vAppTemp2);
        foreach ($files as $file)
        {
            $refs = $file->getLink();
            $diskUrl = $refs[0]->get_href();
            $name = $file->get_name();
            $diskPath = null;
            $ovfFileName=substr($ovfDescriptorPath, strrpos($ovfDescriptorPath, '/')+1);
            $diskPath=str_replace($ovfFileName, $name, $ovfDescriptorPath);
            $this->svc->upload($diskUrl, $diskPath);
        }
        $this->addResourceToCatalog($vAppTemp2, $catalog);
        return $vAppTemp2->get_href();
    }

    /**
     * Check if the resource name is already existing in the catalog.
     * @param VMware_VCloud_API_ReferenceType  $catalogRef
     * @param string $resourceName Name of the vApp template to be created.
     * @since API 1.5 
     * @since SDK 5.1
     */
    public function checkCatalogForDuplicates($catalogRef, $resourceName)
        {
            $catalog=$this->svc->createSDKObj($catalogRef);
            $CatalogItems=$catalog->getCatalogItems();
            foreach ($CatalogItems as $CatalogItem)
            {
                if($CatalogItem->get_name() == $resourceName)
                {
                    echo "Duplicate Resource Name Found: ".$resourceName;
                }
            }
            return $catalog;
        }

    /**
     * Add the resource to catalog.
     * @param VMware_VCloud_API_ReferenceType $catalogRef
     * @param VMware_VCloud_API_CatalogType object  $catalog
     * @since API 1.5 
     * @since SDK 5.1
     */
    public function addResourceToCatalog($resourceRef, $catalog)
    {
        $resourceReference = VMware_VCloud_SDK_Helper::createReferenceTypeObj($resourceRef->get_href());
        $catalogItem=new VMware_VCloud_API_CatalogItemType();
        $catalogItem->set_name($resourceRef->get_name());
        $catalogItem->setEntity($resourceReference);
        $catalog->addCatalogItem($catalogItem);
    }

    /**
     * Upload OVF descriptor.
     *
     * @param string $url        HTTP request URL
     * @param string $filename   Path to the OVF descriptor
     * @return null
     * @since Version 1.0.0
     */
    public function uploadOVFDescriptor($url, $filename)
    {
        $this->svc->upload($url, $filename, 'text/xml');
    }

    /**
     * Get file information for uploading vApp template.
     *
     * @param VMware_VCloud_API_VAppTemplateType $vAppTemplate
     * @return array VMware_VCloud_API_FileType object array
     * @throws VMware_VCloud_SDK_Exception
     * @since Version 1.0.0
     */
    public function getUploadFiles($vAppTemplate)
    {
        $fileList = $vAppTemplate->getFiles();
        if (!isset($fileList))
        {
            throw new VMware_VCloud_SDK_Exception (
                        "vApp template does not contain the upload URLs.\n");
        }
        $files = $fileList->getFile();
        $outFiles = array();
        foreach ($files as $file)
        {
            $size = $file->get_size();
            $transferred = $file->get_bytesTransferred();
            if (0 == $transferred || $transferred < $size)
            {
                array_push($outFiles, $file);
            }
        }
        return $outFiles;
    }

    /**
     * Send a upload vApp template request.
     *
     * @param string $name                Name of the vApp template
     * @param string $format              Format of the package to be
     *                                    uploaded
     * @param string $description         Description of the vApp template
     *                                    to be created
     * @param boolean $manifestRequired   A flag indicates the manifest
     *                                    file is required or not
     * @return VMware_VCloud_API_VAppTemplateType
     * @since Version 1.0.0
     */
    public function sendUploadVAppTemplateRequest($name, $format,
                           $description=null, $manifestRequired=false)
    {
        $url = $this->url . '/action/uploadVAppTemplate';
        $type =
          VMware_VCloud_SDK_Constants::UPLOAD_VAPP_TEMPLATE_PARAMS_CONTENT_TYPE;
        $params = new VMware_VCloud_API_UploadVAppTemplateParamsType();
        $params->set_name($name);
        $params->set_transferFormat('application/' . $format . '+xml');
        $params->setDescription($description);
        $params->set_manifestRequired($manifestRequired);
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Get references to all VMware vCloud media or media with the given
     * name in the vDC.
     *
     * @param string $name  Name of the media. If null, returns all
     * @return array        VMware_VCloud_API_ResourceReferenceType object array
     * @since Version 1.0.0
     */
    public function getMediaRefs($name=null)
    {
        return $this->getContainedRefs('media', $name, 'getResourceEntity',
                              $this->getVdc()->getResourceEntities());
    }

    /**
     * Get all VMware vCloud media or media with the given name in the vDC.
     *
     * @param string $name   Name of the media. If null, returns all
     * @return array         VMware_VCloud_API_MediaType object array
     * @since Version 1.0.0
     */
    public function getMedias($name=null)
    {
        return $this->getObjsByContainedRefs($this->getMediaRefs($name));
    }

    /**
     * Send upload media request.
     *
     * @param VMware_VCloud_API_MediaType $media
     * @return VMware_VCloud_API_MediaType
     * @since Version 1.0.0
     */
    private function sendUploadMediaRequest($media)
    {
        $url = $this->url . '/media';
        $type = VMware_VCloud_SDK_Constants::MEDIA_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $media);
    }

    /**
     * Get a media upload information.
     *
     * @param VMware_VCloud_API_MediaType $media
     * @return string  Media upload URL
     * @throws VMware_VCloud_SDK_Exception
     * @access private
     */
    private function getMediaUploadInfo($media)
    {
        if (!$media instanceof VMware_VCloud_API_MediaType ||
             $media->get_status() != 0)
        {
            throw new VMware_VCloud_SDK_Exception (
                                  "Wrong media parameter passed in.\n");
        }
        $files = $media->getFiles()->getFile();
        if (!$files)
        {
            throw new VMware_VCloud_SDK_Exception ("Get upload file failed.\n");
        }
        $file = $files[0];
        $refs = VMware_VCloud_SDK_Helper::getContainedLinks(null,
                                                    "upload:default", $file);
        if (!$refs)
        {
            throw new VMware_VCloud_SDK_Exception (
                                         "Get upload file reference failed.\n");
        }
        return $refs[0]->get_href();
    }

    /**
     * Upload media.
     *
     * @param string $filename    Path to the media to be uploaded
     * @param string $imageType   ImageType ('iso' or 'floppy')
     * @param VMware_VCloud_API_MediaType $mediaType
     * @return VMware_VCloud_API_MediaType
     * @access private
     */
    private function uploadMedia($filename, $imageType, $mediaType)
    {
        $mediaUpInfo = $this->sendUploadMediaRequest($mediaType);
        $url = $this->getMediaUploadInfo($mediaUpInfo);
        $durl =  $mediaUpInfo->get_href();
        $this->svc->upload($url, $filename);
        return $this->svc->get($durl);
    }

    /**
     * Upload an ISO format media.
     *
     * @param string $isoName   Media full path file name
     * @param VMware_VCloud_API_MediaType $mediaType
     * @return VMware_VCloud_API_MediaType
     * @since Version 1.0.0
     */
    public function uploadIsoMedia($isoName, $mediaType)
    {
        return $this->uploadMedia($isoName, 'iso', $mediaType);
    }

    /**
     * Upload a floppy format media.
     *
     * @param string $floppyName   Full path of a floppy file
     * @param VMware_VCloud_API_MediaType $mediaType
     * @return VMware_VCloud_API_MediaType
     * @since Version 1.0.0
     */
    public function uploadFloppyMedia($floppyName, $mediaType)
    {
        return $this->uploadMedia($floppyName, 'floppy', $mediaType);
    }

    /**
     * Capture a vApp to create a vApp template.
     *
     * @param VMware_VCloud_API_CaptureVAppParamsType $params
     * @return VMware_VCloud_API_VAppTemplateType
     * @since Version 1.0.0
     */
    public function captureVApp($params)
    {
        $url = $this->url . '/action/captureVApp';
        $type = VMware_VCloud_SDK_Constants::CAPTURE_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Clone or move a media image.
     *
     * @param VMware_VCloud_API_CloneMediaParamsType $params
     * @return VMware_VCloud_API_MediaType
     * @since Version 1.0.0
     */
    public function cloneMoveMedia($params)
    {
        $url = $this->url . '/action/cloneMedia';
        $type = VMware_VCloud_SDK_Constants::CLONE_MEDIA_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Clone or move a vApp.
     *
     * @param VMware_VCloud_API_CloneVAppParamsType $params
     * @return VMware_VCloud_API_VAppType
     * @since Version 1.0.0
     */
    public function cloneMoveVApp($params)
    {
        $url = $this->url . '/action/cloneVApp';
        $type = VMware_VCloud_SDK_Constants::CLONE_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Clone or move a vApp template.
     *
     * @param VMware_VCloud_API_CloneVAppTemplateParamsType $params
     * @return VMware_VCloud_API_VAppTemplateType
     * @since Version 1.0.0
     */
    public function cloneMoveVAppTemplate($params)
    {
        $url = $this->url . '/action/cloneVAppTemplate';
        $type =
           VMware_VCloud_SDK_Constants::CLONE_VAPP_TEMPLATE_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Compose a vApp.
     *
     * @param VMware_VCloud_API_ComposeVAppParamsType $params
     * @return VMware_VCloud_API_VAppType
     * @since Version 1.0.0
     */
    public function composeVApp($params)
    {
        $url = $this->url . '/action/composeVApp';
        $type = VMware_VCloud_SDK_Constants::COMPOSE_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Instantiate a vApp template by providing a
     * VMware_VCloud_API_InstantiateVAppTemplateParamsType data object.
     *
     * @param VMware_VCloud_API_InstantiateVAppTemplateParamsType $params
     * @return VMware_VCloud_API_VAppType
     * @since Version 1.0.0
     */
    public function instantiateVAppTemplate($params)
    {
        $url = $this->url . '/action/instantiateVAppTemplate';
        $type = VMware_VCloud_SDK_Constants::
                INSTANTIATE_VAPP_TEMPLATE_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Instantiate a vApp template with default settings.
     *
     * @param string $name          Name of the vApp to be created
     * @param VMware_VCloud_API_ReferenceType $sourceRef   Reference to the
     *                                                     source vApp template
     * @param boolean $deploy       A flag indicates deploy or not after
     *                              instantiation
     * @param boolean $powerOn      A flag indicates power on or not after
     *                              instantiation
     * @param string $description   Description of the vApp to be created
     * @return VMware_VCloud_API_VAppType
     * @since Version 1.0.0
     */
    public function instantiateVAppTemplateDefault($name, $sourceRef,
                     $deploy=false, $powerOn=false, $description=null)
    {
        $instantiateVAppTemplateParams =
                     new VMware_VCloud_API_InstantiateVAppTemplateParamsType();
        $instantiateVAppTemplateParams->set_name($name);
        $sourceRef->set_tagName('Source');
        $instantiateVAppTemplateParams->setSource($sourceRef);
        $instantiateVAppTemplateParams->set_deploy($deploy);
        $instantiateVAppTemplateParams->set_powerOn($powerOn);
        $instantiateVAppTemplateParams->setDescription($description);
        return $this->instantiateVAppTemplate($instantiateVAppTemplateParams);
    }

    /**
     * Get metadata associated with the vDC or metadata associated with
     * the vDC for the specified key in the specified domain.
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
     * Create a disk.
     *
     * @param VMware_VCloud_API_DiskCreateParamsType $params
     * @return VMware_VCloud_API_DiskType
     * @since Version 5.1.0
     */
    public function createDisk($params)
    {
        $url = $this->url . '/disk';
        $type = VMware_VCloud_SDK_Constants::DISK_CREATE_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Get references to all disks or a disk with a given name in the vDC.
     *
     * @param string $name  Name of the disk. If null, returns all
     * @return array        VMware_VCloud_API_ResourceReferenceType object array
     * @since Version 5.1.0
     */
    public function getDiskRefs($name=null)
    {
        return $this->getContainedRefs('disk', $name, 'getResourceEntity',
                              $this->getVdc()->getResourceEntities());
    }

    /**
     * Get all disks or disk with a given name in the vDC.
     *
     * @param string $name  Name of the disk. If null, returns all
     * @return array        VMware_VCloud_API_DiskType object array
     * @since Version 1.0.0
     */
    public function getDisks($name=null)
    {
        $refs = $this->getVAppRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Get vDC storage profile references.
     *
     * @param string $name  Name of the vDC storage profile. If null, returns all
     * @return array|null VMware_VCloud_API_ReferenceType object array or null
     * @since Version 5.1.0
     */
    public function getVdcStorageProfileRefs($name=null)
    {
        return $this->getContainedRefs('vdcStorageProfile', $name,
              'getVdcStorageProfile', $this->getVdc()->getVdcStorageProfiles());
    }

    /**
     * Get vDC storage profiles.
     *
     * @param string $name  Name of the vDC storage profile. If null, returns all
     * @return array|null VMware_VCloud_API_VdcStorageProfileType object array
     *                    or null
     * @since Version 5.1.0
     */
    public function getVdcStorageProfiles($name=null)
    {
         $refs = $this->getVdcStorageProfileRefs($name);
         return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Creating vApp by uploading an ovf package.
     *
     * @param VMware_VCloud_API_InstantiateOvfParamsType  $params
     * @param string $ovfDescriptorPath   Path to the OVF descriptor
     * @return VMware_VCloud_API_VAppType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function uploadOVFAsVApp($params, $ovfDescriptorPath)
    {
        //step 1: initial the upload by sending a upload vApp request
        $vApp = $this->instantiateOvf($params);
        if (!isset($vApp) ||
            !($vApp instanceof VMware_VCloud_API_VAppType))
        {
            throw new VMware_VCloud_SDK_Exception (
                        "Send upload vApp request failed.\n");
        }

        $status = $vApp->get_status();
        if ($status != 0)
        {
             throw new VMware_VCloud_SDK_Exception (
                       "vApp status is not 0, status = $status.\n");
        }
        $vAppob=$this->svc->createSDKObj($vApp->get_href());

        //step 2: get OVF descriptor upload file names from response vApp
        $files = $vAppob->getUploadFileNames($vApp);
        //get OVF descriptor upload URL
        $ovfUrl = $vAppob->getUploadOVFDescriptorUrl($files);

        //step 3: upload an OVF descriptor file
        $vAppob->uploadOVFDescriptor($ovfUrl, $ovfDescriptorPath);
        //wait until OVF descriptor get uploaded
        $vApp1 = $this->svc->wait($vApp, 'get_ovfDescriptorUploaded',
                                      array(true));

        //step 4: get OVF descriptor upload file names from response vApp
        $files = $vAppob->getUploadFileNames($vApp1);
        //get upload URL for each virtual disk and upload the disk file
        $vAppob->uploadFile($files, $ovfDescriptorPath);
        $vApp1 = $this->svc->wait($vApp1, 'get_status',
                                      array(true));
        return $vApp1;
    }

    /**
     * Instantiate a vApp or VM from an OVF.
     *
     * @param VMware_VCloud_API_InstantiateOvfParamsType  $params
     * @return VMware_VCloud_API_VAppType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function instantiateOvf($params)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_INSTANTIATE_OVF_URL;
        $type = VMware_VCloud_SDK_Constants::INSTANTIATE_OVF_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $params);
    }
}
// end of class VMware_VCloud_SDK_Vdc


/**
 * A class provides common actions for a vApp or a VM.
 *
 * @package VMware_VCloud_SDK
 */
abstract class VMware_VCloud_SDK_VApp_Abstract extends
               VMware_VCloud_SDK_Abstract
{
    /**
     * Deploy the vApp or VM.
     *
     * @param VMware_VCloud_API_DeployVAppParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function deploy($params)
    {
        $url = $this->url . '/action/deploy';
        $type = VMware_VCloud_SDK_Constants::DEPLOY_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Undeploy the vApp or VM.
     *
     * @param VMware_VCloud_API_UndeployVAppParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function undeploy($params)
    {
        $url = $this->url . '/action/undeploy';
        $type = VMware_VCloud_SDK_Constants::UNDEPLOY_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Delete the vApp or VM. The vApp or VM has to be powered off and
     * undeployed before it can be deleted.
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
     * Power on the VM or all Vms contained in the vApp.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function powerOn()
    {
        return $this->action('powerOn');
    }

    /**
     * Power off the VM or all VMs contained in the vApp.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function powerOff()
    {
        return $this->action('powerOff');
    }

    /**
     * Reset the VM or all VMs contained in the vApp.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function reset()
    {
        return $this->action('reset');
    }

    /**
     * Reboot the VM or all VMs contained in the vApp.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function reboot()
    {
        return $this->action('reboot');
    }

    /**
     * Suspend the VM or all VMs contained in the vApp.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function suspend()
    {
        return $this->action('suspend');
    }

    /**
     * Discard suspended state of the VM or all VMs contained in the vApp.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function discardSuspendedState()
    {
        $url = $this->url . '/action/discardSuspendedState';
        return $this->svc->post($url, 202);
    }

    /**
     * Shutdown the VM or all VMs contained in the vApp.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function shutdown()
    {
        return $this->action('shutdown');
    }

    /**
     * Get metadata associated with the vApp/VM or metadata associated with
     * the vApp/VM for the specified key in the specified domain.
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
     * Merges the metadata for the vApp or VM with the information provided.
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
     * vApp or VM to the value provided. Note: This will replace any existing
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
     * the vApp or VM.
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
     * Common function of operation on the VM or all VMs contained in
     * the vApp.
     *
     * @param string $op    Name of the operation
     * @param int $expect   Expected return code
     * @return VMware_VCloud_API_TaskType
     * @access private
     */
    private function action($op, $expect=202)
    {
        $url = $this->url . '/power/action/' . $op;
        return $this->svc->post($url, $expect);
    }

    /**
     * The abstract getStatus() function.
     */
    abstract protected function getStatus();
}
// end of class VMware_VCloud_SDK_VApp_Abstract


/**
 * A class provides convenient methods on a VMware vCloud vApp entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_VApp extends VMware_VCloud_SDK_VApp_Abstract
{
    /**
     * Get a reference to a VMware vCloud vApp entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getVAppRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::VAPP_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud vApp entity.
     *
     * @return VMware_VCloud_API_VAppType
     * @since Version 1.0.0
     */
    public function getVApp()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this vApp from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:vapp:' . $this->getUuid();
    }

    /**
     * Get the link to the vDC to which this vApp belongs.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.0.0
     */
    public function getVdcRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the vDC to which this vApp belongs.
     *
     * @return VMware_VCloud_API_VdcType|null
     * @since Version 1.0.0
     */
    public function getVdc()
    {
        $ref = $this->getVdcRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Modify name and/or description of this vApp.
     *
     * @param string $name   New name of the vApp
     * @param string $description   New description of the vApp
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modify($name=null, $description=null)
    {
        $vApp = new VMware_VCloud_API_VAppType();
        $name = isset($name)? $name : $this->getVApp()->get_name();
        $vApp->set_name($name);
        if (!is_null($description))
        {
            $vApp->setDescription($description);
        }
        $type = VMware_VCloud_SDK_Constants::VAPP_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $vApp);
    }

    /**
     * Recompose a vApp.
     *
     * @param VMware_VCloud_API_RecomposeVAppParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function recompose($params)
    {
        $url = $this->url . '/action/recomposeVApp';
        $type = VMware_VCloud_SDK_Constants::RECOMPOSE_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Get references to contained vApps or contained vApps with given name.
     *
     * @param string $name   Name of the vApp. If null, returns all contained
     *                       vApps.
     * @return array         VMware_VCloud_API_ReferenceType object array.
     * @since Version 1.0.0
     */
    public function getContainedVAppRefs($name=null)
    {
        $refs = array();
        $vapps = $this->getContainedVApps($name);
        if ($vapps)
        {
            foreach ($vapps as $vapp)
            {
                $type = VMware_VCloud_SDK_Constants::VAPP_CONTENT_TYPE;
                $ref = VMware_VCloud_SDK_Helper::createReferenceTypeObj(
                         $vapp->get_href(), 'Reference', $type);
                array_push($refs, $ref);
            }
        }
        return $refs;
    }

    /**
     * Get references to contained VMs or contained VMs with given name.
     *
     * @param string $name   Name of the VM. If null, returns all contained VMs
     * @return array         VMware_VCloud_API_ReferenceType object array.
     * @since Version 1.0.0
     */
    public function getContainedVmRefs($name=null)
    {
        $refs = array();
        $vms = $this->getContainedVms($name);
        if ($vms)
        {
            foreach ($vms as $vm)
            {
                $t = VMware_VCloud_SDK_Constants::VM_CONTENT_TYPE;
                $n = $vm->get_name();
                $ref = VMware_VCloud_SDK_Helper::createReferenceTypeObj(
                             $vm->get_href(), 'Reference', $t, $n);
                array_push($refs, $ref);
            }
        }
        return $refs;
    }

    /**
     * Get contained vApps.
     *
     * @param string $name   Name of the vApp. If null, returns all contained
     *                       vApps. 
     * @return array         VMware_VCloud_API_VAppType object array.
     * @since Version 1.0.0
     */
    public function getContainedVApps($name=null)
    {
        $arr = array();
        $vAppChildren = $this->getVApp()->getChildren();
        if ($vAppChildren)
        {
            $vapps = $vAppChildren->getVApp();
            if (!$name)
            {
                return $vapps;
            }
            foreach ($vapps as $vapp)
            {
                if ($name == $vapp->get_name())
                {
                    array_push($arr, $vapp);
                }
            }
        }
        return $arr;
    }

    /**
     * Get contained VMs.
     *
     * @param string $name   Name of the VM. If null, returns all contained VMs
     * @return array         VMware_VCloud_API_VmType object array.
     * @since Version 1.0.0
     */
    public function getContainedVms($name=null)
    {
        $arr = array();
        $vAppChildren = $this->getVApp()->getChildren();
        if (isset($vAppChildren))
        {
            $vms = $vAppChildren->getVm();
            if (!isset($name))
            {
                return $vms;
            }
            foreach ($vms as $vm)
            {
                if ($name == $vm->get_name())
                {
                    array_push($arr, $vm);
                }
            }
        }
        return $arr;
    }

    /**
     * Get network configuration settings of the vApp.
     *
     * @return VMware_VCloud_API_NetworkConfigSectionType
     * @since Version 1.0.0
     */
    public function getNetworkConfigSettings()
    {
        $url = $this->url . '/networkConfigSection';
        return $this->svc->get($url);
    }

    /**
     * Modify network configuration settings of the vApp.
     *
     * @param VMware_VCloud_API_NetworkConfigSectionType $netConfig
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyNetworkConfigSettings($netConfig)
    {
        $url = $this->url . '/networkConfigSection';
        $type = VMware_VCloud_SDK_Constants::NETWORK_CONFIG_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $netConfig);
    }

    /**
     * Get the lease settings of this vApp.
     *
     * @return VMware_VCloud_API_LeaseSettingsSectionType
     * @since Version 1.0.0
     */
    public function getLeaseSettings()
    {
        $url = $this->url . '/leaseSettingsSection';
        return $this->svc->get($url);
    }

    /**
     * Modify the lease settings of this vApp.
     *
     * @param VMware_VCloud_API_LeaseSettingsSectionType $leaseSettings
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyLeaseSettings($leaseSettings)
    {
        $url = $this->url . '/leaseSettingsSection';
        $type = VMware_VCloud_SDK_Constants::LEASE_SETTINGS_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $leaseSettings);
    }

    /**
     * Get startup section of the vApp.
     *
     * @return VMware_VCloud_API_OVF_StartupSection_Type
     * @since Version 1.0.0
     */
    public function getStartupSettings()
    {
        $url = $this->url . '/startupSection';
        return $this->svc->get($url);
    }

    /**
     * Modify the startup settings of the vApp.
     *
     * @param VMware_VCloud_API_StartupSection_Type $startupSection
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyStartupSettings($startupSection)
    {
        $url = $this->url . '/startupSection';
        $type = VMware_VCloud_SDK_Constants::STARTUP_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $startupSection);
    }

    /**
     * Get the network settings of this vApp.
     *
     * @return VMware_VCloud_API_OVF_NetworkSection_Type
     * @since Version 1.0.0
     */
    public function getNetworkSettings()
    {
        $url = $this->url . '/networkSection';
        return $this->svc->get($url);
    }

    /**
     * Get the control access of the vApp.
     *
     * @return VMware_VCloud_API_ControlAccessParamsType
     * @since Version 1.0.0
     */
    public function getControlAccess()
    {
        $url = $this->url . '/controlAccess';
        return $this->svc->get($url);
    }

    /**
     * Modify the control access of the vApp.
     *
     * @param VMware_VCloud_API_ControlAccessParamsType $controlAccess
     * @return VMware_VCloud_API_ControlAccessParamsType
     * @since Version 1.0.0
     */
    public function modifyControlAccess($controlAccess)
    {
        $url = $this->url . '/action/controlAccess';
        $type = VMware_VCloud_SDK_Constants::CONTROL_ACCESS_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $controlAccess);
    }

    /**
     * Get the status of this vApp.
     *
     * The following are the possible vApp status:
     * <ul>
     *    <li> -1 -- could not be created
     *    <li>  0 -- unresolved
     *    <li>  1 -- resolved
     *    <li>  3 -- suspended
     *    <li>  4 -- powered on
     *    <li>  5 -- waiting for user input
     *    <li>  6 -- in an unknown state
     *    <li>  7 -- in an unrecognized state
     *    <li>  8 -- powered off
     *    <li>  9 -- in an inconsistent state
     *    <li> 10 -- VMs in the vApp are in mixed states
     * </ul>
     *
     * @return int vApp status
     * @since Version 1.0.0
     */
    public function getStatus()
    {
        $vApp = $this->getVApp();
        return $vApp->get_status();
    }

    /**
     * Get vApp owner.
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
     * Change vApp owner.
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
     * VApp enters into maintenance mode.
     *
     * @return null
     * @since Version 1.5.0
     */
    public function enterMaintenanceMode()
    {
        $url = $this->url . '/action/enterMaintenanceMode';
        return $this->svc->post($url, 204);
    }

    /**
     * VApp exits maintenance mode.
     *
     * @return null
     * @since Version 1.5.0
     */
    public function exitMaintenanceMode()
    {
        $url = $this->url . '/action/exitMaintenanceMode';
        return $this->svc->post($url, 204);
    }

    /**
     * Resets vApp network.
     *
     * @return VMware_VCloud_API_TaskType|null
     * @since Version 1.5.0
     */
    public function resetVAppNetwork()
    {
        $set = $this->getNetworkConfigSettings();
        if (!isset($set))
        {
            return null;
        }
        $conf = $set->getNetworkConfig();
        if (!isset($conf))
        {
            return null;
        }
        $links = VMware_VCloud_SDK_Helper::getContainedLinks(null,
                                                             'repair', $conf);
        if (1 == count($links))
        {
            $url = $links[0]->get_href();
            return $this->svc->post($url, 202);
        }
        return null;
    }

    /**
     * Retrieve the OVF descriptor of a vApp directly.
     *
     * @return VMware_VCloud_API_OVF_EnvelopeType
     * @since Version 5.1.0
     */
    public function getOVFDescriptor()
    {
        $url = $this->url . '/ovf';
        return $this->svc->get($url);
    }

    /**
     * Retrieve the OVF descriptor of a vApp directly as string.
     *
     * @return String
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function getOVFDescriptorAsString()
    {
        $url = $this->url . '/ovf';
        return $this->svc->get($url, '', false);
    }

    /**
     * Retrieve VM BIOS UUID as described in the OVF Virtual System.
     *
     * @return array VM BIOS UUIDs
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function getVmUUIDs()
    {
        $uuid = array();
        $ovf = $this->getOVFDescriptor();
        $content = $ovf->getContent()->getContent();
        foreach($content as $c)
        {
            $section = $c->getSection();
            foreach($section as $s)
            {
                if(get_class($s) == 'VMware_VCloud_API_OVF_VirtualHardwareSection_Type')
                {
                    $any = $s->getAny();
                    foreach($any as $a)
                    {
                        if($a->get_key() == 'uuid')
                        {
                            array_push($uuid, $a->get_value());
                        }
                    }
                }
            }
        }
        return $uuid;
    }

    /**
     * Retrieve SnapshotSection element for a vApp or VM.
     *
     * @return VMware_VCloud_API_SnapshotSectionType
     * @since Version 5.1.0
     */
    public function getSnapshotSection()
    {
        $url = $this->url . '/snapshotSection';
        return $this->svc->get($url);
    }

    /**
     * Creates new snapshot of a virtual machine or of all the virtual machines in a vApp. Prior to creation of the new 

snapshots, any existing user created snapshots associated with the virtual machines are removed.
     *
     * @param VMware_VCloud_API_CreateSnapshotParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function createSnapshot($params)
    {
        $url = $this->url . '/action/createSnapshot';
        $type = VMware_VCloud_SDK_Constants::
                CREATE_SNAPSHOT_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Hide hardware-assisted CPU virtualization from guest OS.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function disableNestedHypervisor()
    {
        $url = $this->url . '/action/disableNestedHypervisor';
        return $this->svc->post($url, 202);
    }

    /**
     * Expose hardware-assisted CPU virtualization to guest OS.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function enableNestedHypervisor()
    {
        $url = $this->url . '/action/enableNestedHypervisor';
        return $this->svc->post($url, 202);
    }

    /**
     * Removes all user created snapshots for a vApp or virtual machine.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function removeAllSnapshots()
    {
        $url = $this->url . '/action/removeAllSnapshots';
        return $this->svc->post($url, 202);
    }

    /**
     * Reverts a vApp or virtual machine to the current snapshot, if any.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function revertToCurrentSnapshot()
    {
        $url = $this->url . '/action/revertToCurrentSnapshot';
        return $this->svc->post($url, 202);
    }

    /**
     * Disable a vApp for downloading.
     *
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function disableDownload()
    {
        $url = $this->url . '/action/disableDownload';
        $this->svc->post($url, 204);
    }

    /**
     * Enable a vApp for downloading.
     *
     * @param boolean $wait     To wait till finish, set to true
     * @return VMware_VCloud_API_TaskType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function enableDownload($wait=true)
    {
        $url = $this->url . '/action/enableDownload';
        $task = $this->svc->post($url, 202);
        return ($wait)? $this->svc->waitForTask($task) : $task;
    }

    /**
     * Get the suffix or prefix of a string with a seeking pattern.
     *
     * @param string $string  String for which to search (the haystack)
     * @param bool $getSuffix A flag indicates get suffix (true) or prefix(false)
     * @param string $seek    Pattern to search for (the needle)
     * @return string         The suffix or prefix
     * @access private
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    private function getStringSuffix($string, $getSuffix=true, $seek='/')
    {
        $pos = strrpos($string, $seek);
        return $getSuffix? substr($string, $pos+1) : substr($string, 0, $pos);
    }

    /**
     * Download an OVF descriptor.
     *
     * @param string $ovfDescUrl   URL of the OVF descriptor file
     * @param string $ovfFile      Destination of the OVF file descriptor.
     *                             If null, the content will not be dumped
     *                             to a file
     * @return string Content of the OVF descriptor file
     * @access private
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    private function downloadOVFDescriptor($ovfDescUrl=null, $ovfFile=null)
    {
        if (is_null($ovfDescUrl))
        {
            $ovfDescUrl = $this->getDownloadOVFDescriptorUrl();
        }
        $resp = $this->svc->get($ovfDescUrl, '', false);
        if (isset($ovfFile))
        {
            $fh = fopen($ovfFile, 'w');
            fwrite($fh, $resp);
            fclose($fh);
        }
        return $resp;
    }

    /**
     * Download OVF package.
     *
     * @param string $destDir   Directory where to save the downloaded file
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function downloadOVFFile($destDir='.')
    {
        $ovfDescUrl = $this->getDownloadOVFDescriptorUrl();
        if (!$ovfDescUrl)
        {
            throw new VMware_VCloud_SDK_Exception(
                                      "getDownloadOVFDescriptorUrl() failed\n");
        }
        $dest = implode('/', array($destDir, 'descriptor.ovf'));
        $this->svc->download($ovfDescUrl, $dest);
        $srcDir = $this->getStringSuffix($ovfDescUrl, False);
        $fnames = $this->getOVFPackageFileNames($ovfDescUrl);
        if (!isset($fnames))
        {
            throw new VMware_VCloud_SDK_Exception("getOVFPackageFileNames() failed\n");
        }
        foreach ($fnames as $fname)
        {
            $src = implode('/', array($srcDir, $fname));
            $dest = implode('/', array($destDir, $fname));
            $this->svc->download($src, $dest);
        }
    }

    /**
     * Download the vApp as an ovf package. The ovf file and its vmdk
     * contents are downloaded to the specified location. Before downloading
     * make sure the vapp is enabled for download.
     * @see Vapp#enableDownload()
     *
     * @param string $destDir   Directory where to save the downloaded file
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function downloadVapp($destDir='.')
    {
        $ovfDescUrl = $this->getDownloadOVFDescriptorUrl();
        if (!$ovfDescUrl)
        {
            throw new VMware_VCloud_SDK_Exception(
                                      "getDownloadOVFDescriptorUrl() failed\n");
        }
        $dest = implode('/', array($destDir, 'descriptor.ovf'));
        $this->svc->download($ovfDescUrl, $dest);
        $srcDir = $this->getStringSuffix($ovfDescUrl, False);
        $fnames = $this->getOVFPackageFileNames($ovfDescUrl);
        if (!isset($fnames))
        {
            throw new VMware_VCloud_SDK_Exception("getOVFPackageFileNames() failed\n");
        }
        foreach ($fnames as $fname)
        {
            $src = implode('/', array($srcDir, $fname));
            $dest = implode('/', array($destDir, $fname));
            $this->svc->download($src, $dest);
        }
    }

    /**
     * Get download URL of an OVF descriptor.
     *
     * @return string|null   OVF descriptor URL or null
     * @access private
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    private function getDownloadOVFDescriptorUrl()
    {
        $refs = $this->getContainedLinks(null, 'download:default');
        if (1 == count($refs))
        {
            return $refs[0]->get_href();
        }
        return null;
    }

    /**
     * Download lossless vApp OVF.
     * Lossless download mode generates ovf without loosing any of its configurations.
     *
     * @param string $destDir   Directory where to save the downloaded file
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function downloadLosslessOVFFile($destDir='.')
    {
        $ovfDescUrl = $this->getDownloadLosslessOVFDescriptorUrl();
        if (!$ovfDescUrl)
        {
            throw new VMware_VCloud_SDK_Exception(
                                      "getDownloadLosslessOVFDescriptorUrl() failed\n");
        }
        $srcDir = $this->getStringSuffix($ovfDescUrl, False);
        $fnames = $this->getOVFPackageFileNames($ovfDescUrl);
        if (!isset($fnames))
        {
            throw new VMware_VCloud_SDK_Exception("getOVFPackageFileNames() failed\n");
        }
        foreach ($fnames as $fname)
        {
            $src = implode('/', array($srcDir, $fname));
            $dest = implode('/', array($destDir, $fname));
            $this->svc->download($src, $dest);
        }
    }

    /**
     * Download lossless vApp OVF.
     * Lossless download mode generates ovf without loosing any of its configurations.
     *
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    private function getDownloadLosslessOVFDescriptorUrl()
    {
        $refs = $this->getContainedLinks(null, 'download:identity');
        if (1 == count($refs))
        {
            return $refs[0]->get_href();
        }
        return null;
    }

    /**
     * Get names of the component files in the OVF package.
     *
     * @param string $ovfDescUrl   OVF descriptor URL
     * @return array File names
     * @access private
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    private function getOVFPackageFileNames($ovfDescUrl)
    {
        $ovfFiles = array();
        $envelope = $this->downloadOVFDescriptor($ovfDescUrl);
        $envObj = VMware_VCloud_API_Helper::parseString($envelope);
        $files = $envObj->getReferences()->getFile();
        foreach ($files as $file)
        {
            $fileName = $file->get_href();
            if ($fileName)
            {
                array_push($ovfFiles, $fileName);
            }
        }
        return $ovfFiles;
    }

    /**
     * Get file information after uploading vApp.
     *
     * @param VMware_VCloud_API_VAppType $vApp
     * @return array VMware_VCloud_API_FileType object array
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function getUploadedFileNames($vApp)
    {
        $fileList = $vApp->getFiles();
        $outFiles = array();
        if (!is_null($fileList))
        {
            $files = $fileList->getFile();
            foreach ($files as $file)
            {
                $size = $file->get_size();
                $transferred = $file->get_bytesTransferred();
                if (0 != $transferred && $transferred == $size)
                {
                    array_push($outFiles, $file);
                }
            }
        }
        return $outFiles;
    }

    /**
     * Get file information before uploading vApp.
     *
     * @param VMware_VCloud_API_VAppType $vApp
     * @return array VMware_VCloud_API_FileType object array
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function getUploadFileNames($vApp)
    {
        $fileList = $vApp->getFiles();
        if (!isset($fileList))
        {
            throw new VMware_VCloud_SDK_Exception (
                        "vApp does not contain the upload URLs.\n");
        }
        $files = $fileList->getFile();
        $outFiles = array();
        foreach ($files as $file)
        {
            $size = $file->get_size();
            $transferred = $file->get_bytesTransferred();
            if (0 == $transferred || $transferred < $size)
            {
                array_push($outFiles, $file);
            }
        }
        return $outFiles;
    }

    /**
     * Get upload URL of an OVF descriptor file.
     *
     * @return string|null   OVF descriptor file URL or null
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function getUploadOVFDescriptorUrl($files)
    {
        $refs = VMware_VCloud_SDK_Helper::getContainedLinks(null, 'upload:default', $files[0]);
        if (1 == count($refs))
        {
            return $refs[0]->get_href();
        }
        return null;
    }

    /**
     * Upload OVF descriptor file.
     *
     * @param string $url        HTTP request URL
     * @param string $filename   Path to the OVF descriptor
     * @return null
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function uploadOVFDescriptor($url, $filename)
    {
        $this->svc->upload($url, $filename, 'text/xml');
    }

    /**
     * Upload the vApp related files. This can be vmdk or a manifest.
     *
     * @param array VMware_VCloud_API_FileType object array  $files
     * @param string $ovfDescriptorPath   Path to the OVF descriptor
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function UploadFile($files, $ovfDescriptorPath)
    {
        foreach ($files as $file)
        {
            $refs = $file->getLink();
            $diskUrl = $refs[0]->get_href();
            $name = $file->get_name();
            $diskPath = null;
            $ovfFileName=substr($ovfDescriptorPath, strrpos($ovfDescriptorPath, '/')+1);
            $diskPath=str_replace($ovfFileName, $name, $ovfDescriptorPath);
            $this->svc->upload($diskUrl, $diskPath);
        }
    }
}
// end of class VMware_VCloud_SDK_VApp


/**
 * A class provides convenient methods on a VMware vCloud VM entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Vm extends VMware_VCloud_SDK_VApp_Abstract
{
    /**
     * Get a reference to a VMware vCloud VM entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getVmRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::VM_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud VM entity.
     *
     * @return VMware_VCloud_API_VmType
     * @since Version 1.0.0
     */
    public function getVm()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs the vCloud entity ID of this VM from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:vm:' . $this->getUuid();
    }

    /**
     * Get the link to the container of the VM.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.0.0
     */
    public function getContainerVAppRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the container of the VM.
     *
     * @return VMware_VCloud_API_VAppType|null
     * @since Version 1.0.0
     */
    public function getContainerVApp()
    {
        $ref = $this->getContainerVAppRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Get virtual hardware settings of this VM.
     *
     * @return VMware_VCloud_API_OVF_VirtualHardwareSection_Type
     * @since Version 1.0.0
     */
    public function getVirtualHardwareSettings()
    {
        $url = $this->url . '/virtualHardwareSection';
        return $this->svc->get($url);
    }

    /**
     * Modify the guest virtual hardware settings.
     *
     * @param VMware_VCloud_API_OVF_VirtualHardwareSection_Type $vhSection
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyVirtualHardwareSettings($vhSection)
    {
        $url = $this->url . '/virtualHardwareSection';
        $type = VMware_VCloud_SDK_Constants::VIRTUAL_HARDWARE_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $vhSection);
    }

    /**
     * Get the CPU settings of this VM.
     *
     * @return VMware_VCloud_API_OVF_RASD_Type
     * @since Version 1.0.0
     */
    public function getVirtualCpu()
    {
        $url = $this->url . '/virtualHardwareSection/cpu';
        return $this->svc->get($url, '', true, 'VMware_VCloud_API_OVF_RASD_Type');
    }

    /**
     * Modify the CPU settings of this VM.
     *
     * @param VMware_VCloud_API_OVF_RASD_Type $cpu
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyVirtualCpu($cpu)
    {
        $url = $this->url . '/virtualHardwareSection/cpu';
        $type = VMware_VCloud_SDK_Constants::RASD_ITEM_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $cpu);
    }

    /**
     * Get the memory settings of this VM.
     *
     * @return VMware_VCloud_API_OVF_RASD_Type
     * @since Version 1.0.0
     */
    public function getVirtualMemory()
    {
        $url = $this->url . '/virtualHardwareSection/memory';
        return $this->svc->get($url, '', true, 'VMware_VCloud_API_OVF_RASD_Type');
    }

    /**
     * Modify the memory settings of this VM.
     *
     * @param VMware_VCloud_API_OVF_RASD_Type $mem
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyVirtualMemory($mem)
    {
        $url = $this->url . '/virtualHardwareSection/memory';
        $type = VMware_VCloud_SDK_Constants::RASD_ITEM_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $mem);
    }

    /**
     * Get a list of virtual hardware items.
     *
     * @param string $item
     * @return VMware_VCloud_API_RasdItemsListType
     * @access private
     */
    private function getVirtualHardwareItems($item)
    {
        $url = $this->url . '/virtualHardwareSection/' . $item;
        return $this->svc->get($url);
    }

    /**
     * Get a RASD list object representing virtual disks of this VM.
     *
     * @return VMware_VCloud_API_RasdItemsListType
     * @since Version 1.0.0
     */
    public function getVirtualDisks()
    {
        return $this->getVirtualHardwareItems('disks');
    }

    /**
     * Modify virtual disk(s) of this VM.
     *
     * @param VMware_VCloud_API_RasdItemsListType $rasdItems
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyVirtualDisks($rasdItems)
    {
        $url = $this->url . '/virtualHardwareSection/disks';
        $type = VMware_VCloud_SDK_Constants::RASD_ITEMS_LIST_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $rasdItems);
    }

    /**
     * Get a RASD list object representing virtual network cards of this VM.
     *
     * @return VMware_VCloud_API_RasdItemsListType
     * @since Version 1.0.0
     */
    public function getVirtualNetworkCards()
    {
        return $this->getVirtualHardwareItems('networkCards');
    }

    /**
     * Modify virtual network card(s) of this VM.
     *
     * @param VMware_VCloud_API_RasdItemsListType $rasdItems
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyVirtualNetworkCards($rasdItems)
    {
        $url = $this->url . '/virtualHardwareSection/networkCards';
        $type = VMware_VCloud_SDK_Constants::RASD_ITEMS_LIST_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $rasdItems);
    }

    /**
     * Get a RASD list object representing virtual floppy and CD/DVD drives
     * of this VM.
     *
     * @return VMware_VCloud_API_RasdItemsListType
     * @since Version 1.0.0
     */
    public function getVirtualMedia()
    {
        return $this->getVirtualHardwareItems('media');
    }

    /**
     * Get the operating system settings of this VM.
     *
     * @return VMware_VCloud_API_OVF_OperatingSystemSection_Type
     * @since Version 1.0.0
     */
    public function getOperatingSystemSettings()
    {
        $url = $this->url . '/operatingSystemSection';
        return $this->svc->get($url);
    }

    /**
     * Modify the operating system settings of this VM.
     *
     * @param VMware_VCloud_API_OVF_OperatingSystemSection_Type $os
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyOperatingSystemSettings($os)
    {
        $url = $this->url . '/operatingSystemSection';
        $type = VMware_VCloud_SDK_Constants::
                OPERATING_SYSTEM_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $os);
    }

    /**
     * Updates Vm name, Description, and any or all of the following sections.
     * VirtualHardwareSection
     * OperatingSystemSection
     * NetworkConnectionSection
     * GuestCustomizationSection
     *
     * @param VMware_VCloud_API_VmType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function reconfigureVm($params)
    {
        $url = $this->url . '/action/reconfigureVm';
        $type = VMware_VCloud_SDK_Constants::VM_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Get the network connection settings of this VM.
     *
     * @return VMware_VCloud_API_NetworkConnectionSectionType
     * @since Version 1.0.0
     */
    public function getNetworkConnectionSettings()
    {
        $url = $this->url . '/networkConnectionSection';
        return $this->svc->get($url);
    }

    /**
     * Modify the network connection settings of this VM.
     *
     * @param VMware_VCloud_API_NetworkConnectionSectionType $net
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyNetworkConnectionSettings($net)
    {
        $url = $this->url . '/networkConnectionSection';
        $type = VMware_VCloud_SDK_Constants::
                NETWORK_CONNECTION_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $net);
    }

    /**
     * Get guest customization settings.
     *
     * @return VMware_VCloud_API_GuestCustomizationSectionType
     * @since Version 1.0.0
     */
    public function getGuestCustomizationSettings()
    {
        $url = $this->url . '/guestCustomizationSection';
        return $this->svc->get($url);
    }

    /**
     * Modify the customization settings of this VM.
     *
     * @param VMware_VCloud_API_GuestCustomizationSectionType $cs
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyGuestCustomizationSettings($cs)
    {
        $url = $this->url . '/guestCustomizationSection';
        $type = VMware_VCloud_SDK_Constants::
                GUEST_CUSTOMIZATION_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $cs);
    }

    /**
     * Insert a virtual media.
     *
     * @param VMware_VCloud_API_MediaInsertOrEjectParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function insertMedia($params)
    {
        $url = $this->url . '/media/action/insertMedia';
        $type = VMware_VCloud_SDK_Constants::
                MEDIA_INSERT_OR_EJECT_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Eject a virtual media.
     *
     * @param VMware_VCloud_API_MediaInsertOrEjectParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function ejectMedia($params)
    {
        $url = $this->url . '/media/action/ejectMedia';
        $type = VMware_VCloud_SDK_Constants::
                MEDIA_INSERT_OR_EJECT_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Get a VM screen thumbnail image.
     *
     * @param string $thumbnail   Path of the thumbnail image destination
     * @return null
     * @since Version 1.0.0
     */
    public function getScreenThumbnailImage($thumbnail)
    {
        $url = $this->url . "/screen";
        $this->svc->download($url, $thumbnail, 'image/png');
    }

    /**
     * Get a VM screen ticket.
     *
     * @return VMware_VCloud_API_ScreenTicketType
     * @since Version 1.0.0
     */
    public function getScreenTicket()
    {
        $url = $this->url . '/screen/action/acquireTicket';
        return $this->svc->post($url, 200);
    }

    /**
     * Get a VM screen ticket tokens, in IP address, VM MOF, and encided-ticket
     *
     * @return array String array, keys are:
     * <ul>
     *     <li> ip -- IP address to be connected by the remote console
     *     <li> mof -- managed object reference of the virtual machine
     *     <li> ticket -- decoded screen ticket
     * </ul>
     * @since Version 1.0.0
     */
    public function getScreenTicketTokens()
    {
        $tokens = array();
        $ticketObj = $this->getScreenTicket();
        if ($ticketObj)
        {
            $ticket = $ticketObj->get_valueOf();
            $matches = preg_split('/\/|\?|=/', $ticket);
            $tokens = array ('ip' => $matches[2],
                             'mof' => $matches[3],
                             'ticket' => urldecode($matches[5])
                            );
        }
        return $tokens;
    }

    /**
     * Retrieve a mks ticket that you can use to gain access to the console of a running VM.
     *
     * @return VMware_VCloud_API_MksTicketType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function acquireMksTicket()
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_ACQUIRE_MKSTICKET_URL;
        return $this->svc->post($url, 200);
    }

    /**
     * Get a VM pending question.
     *
     * @return VMware_VCloud_API_VmPendingQuestionType
     * @since Version 1.0.0
     */
    public function getPendingQuestion()
    {
        return $this->svc->get($this->url . '/question');
    }

    /**
     * Answer a VM pending question.
     *
     * @param VMware_VCloud_API_VmQuestionAnswerType $answer
     * @return null
     * @since Version 1.0.0
     */
    public function answerPendingQuestion($answer)
    {
        $url = $this->url . '/question/action/answer';
        $type = VMware_VCloud_SDK_Constants::VM_PENDING_ANSWER_CONTENT_TYPE;
        return $this->svc->post($url, 204, $type, $answer);
    }

    /**
     * Get the status of this VM.
     *
     * The following are the possible VM status:
     * <ul>
     *    <li> -1 -- could not be created
     *    <li>  0 -- unresolved
     *    <li>  1 -- resolved
     *    <li>  3 -- suspended
     *    <li>  4 -- powered on
     *    <li>  5 -- waiting for user input
     *    <li>  6 -- in an unknown state
     *    <li>  7 -- in an unrecognized state
     *    <li>  8 -- powered off
     *    <li>  9 -- in an inconsistent state
     * </ul>
     *
     * @return int VM status
     * @since Version 1.0.0
     */
    public function getStatus()
    {
        return $this->getVm()->get_status();
    }

    /**
     * Modify name and/or description of this VM.
     *
     * @param string $name   New name of the VM
     * @param string $description   New description of the VM
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modify($name=null, $description=null)
    {
        $vm = new VMware_VCloud_API_VmType();
        $name = (isset($name))? $name : $this->getVm()->get_name();
        $vm->set_name($name);
        if (!is_null($description))
        {
            $vm->setDescription($description);
        }
        $type = VMware_VCloud_SDK_Constants::VM_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $vm);
    }

    /**
     * Installs VMware tools to the VM.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function installVMwareTools()
    {
        $url = $this->url . '/action/installVMwareTools';
        return $this->svc->post($url, 202);
    }

    /**
     * Get the runtime info section of the VM.
     *
     * @return VMware_VCloud_API_RuntimeInfoSectionType
     * @since  Version 1.5.0
     */
    public function getRuntimeInfo()
    {
        $url = $this->url . '/runtimeInfoSection';
        return $this->svc->get($url);
    }

    /**
     * Get the installed software information of the VM.
     *
     * @return array|null VMware_VCloud_API_OVF_ProductSection_Type object array
     *                    or null
     * @since Version 1.5.0
     */
    public function getProductInfo()
    {
        $url = $this->url . '/productSections';
        $list = $this->svc->get($url);
        if (isset($list) && $list->getProductSection())
        {
            return $list->getProductSection();
        }
        return null;
    }

    /**
     * Modify the installed software information of the VM.
     *
     * @param  VMware_VCloud_API_OVF_ProductSection_Type
     * @return VMware_VCloud_API_TaskType
     * @since  Version 1.5.0
     */
    public function modifyProductInfo($productInfo)
    {
        $url = $this->url . '/productSections';
        $type = VMware_VCloud_SDK_Constants::PRODUCT_SECTIONS_CONTENT_TYPE;
        return $this->svc->put($url, 202);
    }

    /**
     * Consolidates the VM.
     *
     * @return VMware_VCloud_API_TaskType
     * @since  Version 1.5.0
     */
    public function consolidate()
    {
        $url = $this->url . '/action/consolidate';
        return $this->svc->post($url, 202);
    }

    /**
     * Relocates the VM.
     *
     * @param  VMware_VCloud_API_RelocateParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since  Version 1.5.0
     * @deprecated since version 5.1.0
     */
    public function relocate($params)
    {
        $url = $this->url . '/action/relocate';
        $type = VMware_VCloud_SDK_Constants::RELOCATE_VM_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Upgrade virtual hardware version of the VM.
     *
     * @return VMware_VCloud_API_TaskType
     * @since  Version 1.5.0
     */
    public function upgradeHardwareVersion()
    {
        $url = $this->url . '/action/upgradeHardwareVersion';
        return $this->svc->post($url, 202);
    }

    /**
     * Attach a disk to a VM.
     *
     * @param VMware_VCloud_API_DiskAttachOrDetachParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since  Version 5.1.0
     */
    public function attachDisk($params)
    {
        $url = $this->url . '/disk/action/attach';
        $type = VMware_VCloud_SDK_Constants::
                  DISK_ATTACH_DETACH_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Detach a disk to a VM.
     *
     * @param VMware_VCloud_API_DiskAttachOrDetachParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since  Version 5.1.0
     */
    public function detachDisk($params)
    {
         $url = $this->url . '/disk/action/detach';
         $type = VMware_VCloud_SDK_Constants::
                   DISK_ATTACH_DETACH_PARAMS_CONTENT_TYPE;
         return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Enable nested hypervisor of the VM.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function enableNestedHypervisor()
    {
        $url = $this->url . '/action/enableNestedHypervisor';
        return $this->svc->post($url, 202);
    }

    /**
     * Disable nested hypervisor of the VM.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function disableNestedHypervisor()
    {
        $url = $this->url . '/action/disableNestedHypervisor';
        return $this->svc->post($url, 202);
    }

    /**
     * Perform storage profile compliance check on a VM.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function CheckCompliance()
    {
        $url = $this->url . '/action/checkCompliance';
        return $this->svc->post($url, 202);
    }

    /**
     * Retrieve SnapshotSection element for a vApp or VM.
     *
     * @return VMware_VCloud_API_SnapshotSectionType
     * @since Version 5.1.0
     */
    public function getSnapshotSection()
    {
        $url = $this->url . '/snapshotSection';
        return $this->svc->get($url);
    }

    /**
     * Get storage profile compliance result of the VM.
     *
     * @return VMware_VCloud_API_ComplianceResultType
     * @since Version 5.1.0
     */
    public function getComplianceResult()
    {
        $url = $this->url . '/complianceResult';
        return $this->svc->get($url);
    }

    /**
     * Get capability of the VM.
     *
     * @return VMware_VCloud_API_VmCapabilitiesType
     * @since Version 5.1.0
     */
    public function getVmCapabilities()
    {
        $url = $this->url . '/vmCapabilities';
        return $this->svc->get($url);
    }

    /**
     * Update capability of the VM.
     *
     * @param VMware_VCloud_API_VmCapabilitiesType
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function updateVmCapabilities($capType)
    {
        $url = $this->url . '/vmCapabilities';
        $type = VMware_VCloud_SDK_Constants::VM_CAPABILITIES_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $capType);
    }

    /**
     * Creates new snapshot of a virtual machine or of all the virtual machines in a vApp. Prior to creation of the new 

snapshots, any existing user created snapshots associated with the virtual machines are removed.
     *
     * @param VMware_VCloud_API_CreateSnapshotParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function createSnapshot($params)
    {
        $url = $this->url . '/action/createSnapshot';
        $type = VMware_VCloud_SDK_Constants::
                CREATE_SNAPSHOT_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Removes all user created snapshots for a vApp or virtual machine.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function removeAllSnapshots()
    {
        $url = $this->url . '/action/removeAllSnapshots';
        return $this->svc->post($url, 202);
    }

    /**
     * Reverts a vApp or virtual machine to the current snapshot, if any.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function revertToCurrentSnapshot()
    {
        $url = $this->url . '/action/revertToCurrentSnapshot';
        return $this->svc->post($url, 202);
    }

    /**
     * Get collection of shadow vm references. vAppTemplate VM contains the shadow vm references.
     * Applicable for vAppTemplate VM not for vApp VM.
     * @param string $name   Name of the shadow vm to get. If null, returns all
     * @return array         VMware_VCloud_API_ReferenceType object array.
     * @since Version 1.5.0
     */
    public function getShadowVmsRefs($name=null)
    {
        $url = $this->url . '/shadowVms';
        $shadowvmsRefs = $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getReference', $shadowvmsRefs);
    }
}
// end of class VMware_VCloud_SDK_Vm


/**
 * A class provides convenient methods on a VMware vCloud vApp template entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_VAppTemplate extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud vApp template entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getVAppTemplateRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             VAPP_TEMPLATE_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud vApp template entity.
     *
     * @return VMware_VCloud_API_VAppTemplateType
     * @since Version 1.0.0
     */
    public function getVAppTemplate()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this vApp template from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:vapptemplate:' . $this->getUuid();
    }

    /**
     * Get the link to the container of the vApp template.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.0.0
     */
    public function getVdcRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the container of the vApp template.
     *
     * @return VMware_VCloud_API_VdcType|null
     * @since Version 1.0.0
     */
    public function getVdc()
    {
        $ref = $this->getVdcRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Get status of a vApp template.
     *
     * The following are the possible vApp template status:
     * <ul>
     *    <li> -1 -- could not be created
     *    <li>  0 -- unresolved
     *    <li>  1 -- resolved
     *    <li>  6 -- in an unknown state
     *    <li>  7 -- in an unrecognized state
     *    <li>  8 -- powered off
     *    <li> 10 -- VMs in the vApp template are in Mixed state
     *    <li> 11 -- descriptor pending
     *    <li> 12 -- copying contents
     *    <li> 13 -- disk contents pending
     *    <li> 14 -- quarantined
     *    <li> 15 -- quarantine expired
     *    <li> 16 -- rejected
     *    <li> 17 -- transfer timeout
     * </ul>
     *
     * @return int vApp template status
     * @since Version 1.0.0
     */
    public function getStatus()
    {
        return $this->getVAppTemplate()->get_status();
    }

    /**
     * Relocate a VM to a different datastore.
     *
     * @param  VMware_VCloud_API_RelocateParamsType $params
     * @param string $name   Name of the shadow vm
     * @return VMware_VCloud_API_TaskType
     * @since  Version 1.5.0
     * @deprecated since version 5.1.0
     */
    public function relocate($params, $name)
    {
        $vmrefs = $this->getVAppTemplate()->getChildren()->getVm();
        foreach ($vmrefs as $ref)
        {
            if($ref->get_name() == $name)
            $url = $ref->get_href();
        }
        $url = $url . '/action/relocate';
        $type = VMware_VCloud_SDK_Constants::RELOCATE_VM_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Enable a vApp template for downloading.
     *
     * @param boolean $wait     To wait till finish, set to true
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function enableDownload($wait=true)
    {
        $url = $this->url . '/action/enableDownload';
        $task = $this->svc->post($url, 202);
        return ($wait)? $this->svc->waitForTask($task) : $task;
    }

    /**
     * Disable a vApp template for downloading.
     *
     * @return null
     * @since Version 1.0.0
     */
    public function disableDownload()
    {
        $url = $this->url . '/action/disableDownload';
        return $this->svc->post($url, 204);
    }

    /**
     * Retrieve the OVF descriptor of a vApp Template directly.
     *
     * @return VMware_VCloud_API_OVF_EnvelopeType
     * @since API Version 1.5.0
     * @since SDK Version 5.5.0
     */
    public function getOVFDescriptor()
    {
        $url = $this->url . '/ovf';
        return $this->svc->get($url);
    }

    /**
     * Retrieve the OVF descriptor of a vApp Template directly as string.
     *
     * @return String
     * @since API Version 1.5.0
     * @since SDK Version 5.5.0
     */
    public function getOVFDescriptorAsString()
    {
        $url = $this->url . '/ovf';
        return $this->svc->get($url, '', false);
    }

    /**
     * Download an OVF descriptor.
     *
     * @param string $ovfDescUrl   URL of the OVF descriptor file
     * @param string $ovfFile      Destination of the OVF file descriptor.
     *                             If null, the content will not be dumped
     *                             to a file
     * @return string Content of the OVF descriptor file
     * @access private
     */
    private function downloadOVFDescriptor($ovfDescUrl=null, $ovfFile=null)
    {
        if (is_null($ovfDescUrl))
        {
            $ovfDescUrl = $this->getDownloadOVFDescriptorUrl();
        }
        $resp = $this->svc->get($ovfDescUrl, '', false);
        if (isset($ovfFile))
        {
            $fh = fopen($ovfFile, 'w');
            fwrite($fh, $resp);
            fclose($fh);
        }
        return $resp;
    }

    /**
     * Download files to form OVF package.
     *
     * @param string $destDir   Directory where to save the downloaded file
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since Version 1.0.0
     */
    public function downloadOVFFiles($destDir='.')
    {
        $ovfDescUrl = $this->getDownloadOVFDescriptorUrl();
        if (!$ovfDescUrl)
        {
            throw new VMware_VCloud_SDK_Exception(
                                      "getDownloadOVFDescriptorUrl() failed\n");
        }
        $srcDir = $this->getStringSuffix($ovfDescUrl, False);
        $fnames = $this->getOVFFileNames($ovfDescUrl);
        if (!isset($fnames))
        {
            throw new VMware_VCloud_SDK_Exception("getOVFFileNames() failed\n");
        }
        foreach ($fnames as $fname)
        {
            $src = implode('/', array($srcDir, $fname));
            $dest = implode('/', array($destDir, $fname));
            $this->svc->download($src, $dest);
        }
    }

    /**
     * Get download URL of an OVF descriptor.
     *
     * @return string|null   OVF descriptor URL or null
     * @access private
     */
    private function getDownloadOVFDescriptorUrl()
    {
        $refs = $this->getContainedLinks(null, 'download:default');
        if (1 == count($refs))
        {
            return $refs[0]->get_href();
        }
        return null;
    }

    /**
     * Download lossless vAppTemplate OVF.
     * Lossless download mode generates ovf without loosing any of its configurations.
     *
     * @param string $destDir   Directory where to save the downloaded file
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since Version 5.1.0
     */
    public function downloadLosslessOVFFile($destDir='.')
    {
        $ovfDescUrl = $this->getDownloadLosslessOVFDescriptorUrl();
        if (!$ovfDescUrl)
        {
            throw new VMware_VCloud_SDK_Exception(
                                      "getDownloadLosslessOVFDescriptorUrl() failed\n");
        }
        $srcDir = $this->getStringSuffix($ovfDescUrl, False);
        $fnames = $this->getOVFFileNames($ovfDescUrl);
        if (!isset($fnames))
        {
            throw new VMware_VCloud_SDK_Exception("getOVFFileNames() failed\n");
        }
        foreach ($fnames as $fname)
        {
            $src = implode('/', array($srcDir, $fname));
            $dest = implode('/', array($destDir, $fname));
            $this->svc->download($src, $dest);
        }
    }

    /**
     * Download lossless vAppTemplate OVF.
     * Lossless download mode generates ovf without loosing any of its configurations.
     *
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since Version 5.1.0
     */
    private function getDownloadLosslessOVFDescriptorUrl()
    {
        $refs = $this->getContainedLinks(null, 'download:identity');
        if (1 == count($refs))
        {
            return $refs[0]->get_href();
        }
        return null;
    }

    /**
     * Get names of the component files in the OVF package.
     *
     * @param string $ovfDescUrl   OVF descriptor URL
     * @return array File names
     * @access private
     */
    private function getOVFFileNames($ovfDescUrl)
    {
        $ovfFiles = array();
        $envelope = $this->downloadOVFDescriptor($ovfDescUrl);
        $envObj = VMware_VCloud_API_Helper::parseString($envelope);
        $files = $envObj->getReferences()->getFile();
        foreach ($files as $file)
        {
            $fileName = $file->get_href();
            if ($fileName)
            {
                array_push($ovfFiles, $fileName);
            }
        }
        return $ovfFiles;
    }

    /**
     * Get the network settings of this vApp template.
     *
     * @return VMware_VCloud_API_OVF_NetworkSection_Type
     * @since Version 1.0.0
     */
    public function getNetworkSettings()
    {
        return $this->svc->get($this->url . '/networkSection');
    }

    /**
     * Get the network configure settings of this vApp template.
     *
     * @return VMware_VCloud_API_NetworkConfigSectionType
     * @since Version 1.0.0
     */
    public function getNetworkConfigSettings()
    {
        return $this->svc->get($this->url . '/networkConfigSection');
    }

    /**
     * Get customization information of this vApp template.
     *
     * @return VMware_VCloud_API_CustomizationSectionType
     * @since Version 1.0.0
     */
    public function getCustomizationSettings()
    {
        return $this->svc->get($this->url . '/customizationSection');
    }

    /**
     * Modify customization settings of this vApp template.
     *
     * @param VMware_VCloud_API_CustomizationSectionType $customizationSection
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modifyCustomizationSettings($customizationSection)
    {
        $url = $this->url . '/customizationSection';
        $type = VMware_VCloud_SDK_Constants::CUSTOMIZATION_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $customizationSection);
    }

    /**
     * Get the lease settings of this vApp template.
     *
     * @return VMware_VCloud_API_LeaseSettingsSectionType
     * @since Version 1.5.0
     */
    public function getLeaseSettings()
    {
        $url = $this->url . '/leaseSettingsSection';
        return $this->svc->get($url);
    }

    /**
     * Modify the lease settings of this vApp template.
     *
     * @param VMware_VCloud_API_LeaseSettingsSectionType $leaseSettings
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function modifyLeaseSettings($leaseSettings)
    {
        $url = $this->url . '/leaseSettingsSection';
        $type = VMware_VCloud_SDK_Constants::LEASE_SETTINGS_SECTION_CONTENT_TYPE;
        return $this->svc->put($url, 202, $type, $leaseSettings);
    }

    /**
     * Delete this vApp template.
     *
     * @return VMware_VCloud_API_TaskType
     * @since API Version 1.0.0
     * @since SDK Version 5.5.0
     */
    public function delete()
    {
        $task = $this->svc->delete($this->url, 202);
        $this->destroy();
        return $task;
    }

    /**
     * Get a reference to a VMware vCloud catalog entity.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     * @since SDK 5.1.0
     */
    public function getCatalogItemLink()
    {
        $catalogItemReference = null;
        $links = $this->getContainedLinks(null, 'catalogItem');
        foreach ($links as $link)
        {
            if ($link->get_Type() == VMware_VCloud_SDK_Constants::CATALOG_ITEM_CONTENT_TYPE)
            {
                $catalogItemReference = $link;
            }
        }
        return $catalogItemReference;
    }

    /**
     * Returns true if vApp template is in Catalog, otherwise false.
     *
     * @return boolean
     * @since Version 1.5.0
     * @since SDK 5.1.0
     */
    public function isPartOfCatalogItem()
    {
        $links = $this->getCatalogItemLink();
        return ($links != null);
    }

    /**
     * Deletes vApp template and its catalog item. If the vApp template is attached to a catalog item.
     * Deletes vApp template alone. If the vApp template is not attached to any catalog item.
     *
     * @return VMware_VCloud_API_TaskType
     * @since API Version 1.5.0
     * @since SDK Version 5.5.0
     */
    public function deleteVAppTemplate()
    {
        if($this->isPartOfCatalogItem())
        {
            $this->svc->createSDKObj($this->getCatalogItemLink())->delete();
        }
        $task = $this->svc->delete($this->url, 202);
        $this->destroy();
        return $task;
    }

    /**
     * Modify name or/and description of this vApp template.
     *
     * @param string $name   New name of the vApp template
     * @param string $description   New description of the vApp template
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modify($name=null, $description=null)
    {
        $vAppTemp = new VMware_VCloud_API_VAppTemplateType();
        $name = isset($name)? $name : $this->getVAppTemplate()->get_name();
        $vAppTemp->set_name($name);
        if (!is_null($description))
        {
            $vAppTemp->setDescription($description);
        }
        $type = VMware_VCloud_SDK_Constants::VAPP_TEMPLATE_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $vAppTemp);
    }

    /**
     * Get the suffix or prefix of a string with a seeking pattern.
     *
     * @param string $string  String for which to search (the haystack)
     * @param bool $getSuffix A flag indicates get suffix (true) or prefix(false)
     * @param string $seek    Pattern to search for (the needle)
     * @return string         The suffix or prefix
     * @access private
     */
    private function getStringSuffix($string, $getSuffix=true, $seek='/')
    {
        $pos = strrpos($string, $seek);
        return $getSuffix? substr($string, $pos+1) : substr($string, 0, $pos);
    }

    /**
     * Get contained VMs in the vApp template.
     *
     * @param $name
     * @return array|null VMware_VCloud_API_VmType objects array or null
     * @since Version 1.5.0
     */
    public function getContainedVms($name=null)
    {
        $temp = $this->getVAppTemplate();
        $c = $temp->getChildren();
        return isset($c)?
            VMware_VCloud_SDK_Helper::getObjsByName($c->getVm(), $name) : null;
    }

    /**
     * Get vApp template owner.
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
     * Get a link to the shadow VMs of the VM in the vApp template.
     *
     * @param $name Name of the VM in the vApp template to search
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     */
    public function getShadowVmsLink($name=null)
    {
        $vms = $this->getContainedVms($name);
        if (0 == count($vms))
        {
            return null;
        }
        foreach ($vms as $vm)
        {
            $links = VMware_VCloud_SDK_Helper::getContainedLinks('shadowVms',
                                                             'shadowVms', $vm);
            if (1 == count($links))
            {
                return $links[0];
            }
        }
        return null;
    }

    /**
     * Get collection of shadow vm references. vAppTemplate VM contains the shadow vm references.
     * @param string $name   Name of the shadow vm to get.
     * @return array         VMware_VCloud_API_ReferenceType object array.
     * @since Version 1.5.0
     * @deprecated since version 5.1.0
     * Instead use VMware_VCloud_SDK_Vm->getShadowVmsRefs()
     */
    public function getShadowVmsRefs($name)
    {
        $vmrefs = $this->getVAppTemplate()->getChildren()->getVm();
        foreach ($vmrefs as $ref)
        {
            if($ref->get_name() == $name)
            $url = $ref->get_href();
        }
        $url = $url . '/shadowVms';
        $shadowvmsRefs = $this->svc->get($url);
        return $this->getContainedRefs(null, $name, 'getReference', $shadowvmsRefs);
    }

    /**
     * Get metadata associated with the vApp template or metadata associated with
     * the vApp template for the specified key in the specified domain.
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
     * Merges the metadata for a vApp template with the information provided.
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
     * vApp template to the value provided. Note: This will replace any existing
     * metadata information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string domain
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
     * the vApp template.
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
// end of class VMware_VCloud_SDK_VAppTemplate


/**
 * A class provides convenient methods on a VMware vCloud catalog entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Catalog extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud catalog entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getCatalogRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::CATALOG_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud catalog entity.
     *
     * @return VMware_VCloud_API_CatalogType
     * @since Version 1.0.0
     */
    public function getCatalog()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the catalog from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:catalog:' . $this->getUuid();
    }

    /**
     * Get the link to the container of the catalog.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.0.0
     */
    public function getOrgRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the container of the catalog.
     *
     * @return VMware_VCloud_API_OrgType|null
     * @since Version 1.0.0
     */
    public function getOrg()
    {
        $ref = $this->getOrgRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Get references to catalog items entity in this catalog entity.
     *
     * @param string $name   Name of the catalog item. If null, returns all
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
     * @param string $name   Name of the catalog item. If null, returns all
     * @return array VMware_VCloud_API_CatalogItemType object array
     * @since Version 1.0.0
     */
    public function getCatalogItems($name=null)
    {
        $refs = $this->getCatalogItemRefs($name);
        return $this->getObjsByContainedRefs($refs);
    }

    /**
     * Add a catalog item to this VMware vCloud catalog.
     *
     * @param VMware_VCloud_API_CatalogItemType $catalogItem
     * @return VMware_VCloud_API_CatalogItemType Represents the newly created
     *                                           catalog item
     * @since Version 1.0.0
     */
    public function addCatalogItem($catalogItem)
    {
        $url = $this->url . '/catalogItems';
        $type = VMware_VCloud_SDK_Constants::CATALOG_ITEM_CONTENT_TYPE;
        return $this->svc->post($url, 201, $type, $catalogItem);
    }

    /**
     * Construct control access url for a catalog.
     *
     * @param  string $action   Operation on the control access
     * @return string
     * @access private
     */
    private function getControlAccessUrl($action=null)
    {
        $orgUrl = $this->getOrgRef()->get_href();
        $url = $orgUrl . "/catalog/" . $this->getUuid();
        $url = ("action" == $action)? "$url/$action" : $url;
        return "$url/controlAccess";
    }

    /**
     * Get the control access of this catalog.
     *
     * @return VMware_VCloud_API_ControlAccessParamsType
     * @since Version 1.0.0
     */
    public function getControlAccess()
    {
        $url = $this->getControlAccessUrl();
        return $this->svc->get($url);
    }

    /**
     * Modify the control access of this catalog. Modify catalog sharing can
     * be done with this function.
     *
     * @param VMware_VCloud_API_ControlAccessParamsType $controlAccess
     * @return VMware_VCloud_API_ControlAccessParamsType
     * @since Version 1.0.0
     */
    public function modifyControlAccess($controlAccess)
    {
        $url = $this->getControlAccessUrl('action');
        $type = VMware_VCloud_SDK_Constants::CONTROL_ACCESS_CONTENT_TYPE;
        return $this->svc->post($url, 200, $type, $controlAccess);
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
     * Creating vAppTemplate by uploading an ovf package.
     *
     * @param string $name                     Name of the vApp template to be created
     * @param string $ovfDescriptorPath        Path to the OVF descriptor
     * @param string $description              Description of the vApp template to be
     *                                         created
     * @param boolean $manifestRequired        A flag indicates the manifest file is
     *                                         required or not
     * @param VMware_VCloud_API_ReferenceType  $vdcStorageProfileRef
     * @return VMware_VCloud_API_VAppTemplateType object.
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function uploadOVFAsVAppTemplate($name, $ovfDescriptorPath, $description=null, $manifestRequired=null, $vdcStorageProfileRef)
    {
        //Check if the resource name is already existing in the catalog.
        $this->checkCatalogForDuplicates($this, $name);

        //step 1: initial the upload by sending a upload vApp template request
        $vAppTemplate = $this->createVappTemplateRequest($name, $description, $manifestRequired, $vdcStorageProfileRef);
        if (!isset($vAppTemplate) ||
            !($vAppTemplate instanceof VMware_VCloud_API_CatalogItemType))
        {
            throw new VMware_VCloud_SDK_Exception (
                        "Send upload vApp template request failed.\n");
        }

        $vAppTemp1 = $this->svc->get($vAppTemplate->getEntity()->get_href());

        //step 2: get OVF descriptor upload URL from response vApp template
        $files = $this->getUploadFiles($vAppTemp1);
        $refs = VMware_VCloud_SDK_Helper::getContainedLinks(null,
                                              'upload:default', $files[0]);
        $ovfUrl = $refs[0]->get_href();

        //step 3: upload an OVF descriptor
        $this->uploadOVFDescriptor($ovfUrl, $ovfDescriptorPath);
        //wait until OVF descriptor get uploaded
        $vAppTemp2 = $this->svc->wait($vAppTemp1, 'get_ovfDescriptorUploaded',
                                      array(true));

        //step 4: get upload URL for each virtual disk and upload the disk file
        $files = $this->getUploadFiles($vAppTemp2);
        foreach ($files as $file)
        {
            $refs = $file->getLink();
            $diskUrl = $refs[0]->get_href();
            $name = $file->get_name();
            $diskPath = null;
            $ovfFileName=substr($ovfDescriptorPath, strrpos($ovfDescriptorPath, DIRECTORY_SEPARATOR)+1);
            $diskPath=str_replace($ovfFileName, $name, $ovfDescriptorPath);
            $this->svc->upload($diskUrl, $diskPath);
        }
        return $vAppTemp2;
    }

    /**
     * Check if the resource name is already existing in the catalog.
     * @param VMware_VCloud_SDK_Catalog object  $catalog
     * @param string $resourceName Name of the vApp template to be created.
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function checkCatalogForDuplicates($catalog, $resourceName)
    {
        $CatalogItems=$catalog->getCatalogItems();
        foreach ($CatalogItems as $CatalogItem)
        {
            if($CatalogItem->get_name() == $resourceName)
            {
                throw new VMware_VCloud_SDK_Exception (
                          "Duplicate Resource Name Found: $resourceName\n");
            }
        }
        return $catalog;
    }

    /**
     * Creating vAppTemplate request by uploading an ovf package.
     *
     * @param string $name                Name of the vApp template
     * @param string $description         Description of the vApp template
     *                                    to be created
     * @param boolean $manifestRequired   A flag indicates the manifest
     *                                    file is required or not
     * @return VMware_VCloud_API_CatalogItemType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    private function createVappTemplateRequest($name, $description=null, $manifestRequired=false, $vdcStorageProfileRef)
    {
        $url = $this->url . '/action/upload';
        $type =
          VMware_VCloud_SDK_Constants::UPLOAD_VAPP_TEMPLATE_PARAMS_CONTENT_TYPE;
        $params = new VMware_VCloud_API_UploadVAppTemplateParamsType();
        $params->set_name($name);
        $params->set_transferFormat(VMware_VCloud_SDK_Constants::OVF_TRANSFER_FORMAT);
        $params->setDescription($description);
        $params->set_manifestRequired($manifestRequired);
        $params->setVdcStorageProfile($vdcStorageProfileRef);
        return $this->svc->post($url, 201, $type, $params);
    }

    /**
     * Get file information for uploading vApp template.
     *
     * @param VMware_VCloud_API_VAppTemplateType $vAppTemplate
     * @return array VMware_VCloud_API_FileType object array
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function getUploadFiles($vAppTemplate)
    {
        $fileList = $vAppTemplate->getFiles();
        if (!isset($fileList))
        {
            throw new VMware_VCloud_SDK_Exception (
                        "vApp template does not contain the upload URLs.\n");
        }
        $files = $fileList->getFile();
        $outFiles = array();
        foreach ($files as $file)
        {
            $size = $file->get_size();
            $transferred = $file->get_bytesTransferred();
            if (0 == $transferred || $transferred < $size)
            {
                array_push($outFiles, $file);
            }
        }
        return $outFiles;
    }

    /**
     * Upload OVF descriptor.
     *
     * @param string $url        HTTP request URL
     * @param string $filename   Path to the OVF descriptor
     * @return null
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function uploadOVFDescriptor($url, $filename)
    {
        $this->svc->upload($url, $filename, 'text/xml');
    }

    /**
     * Create a vApp template in this library from a vApp.
     *
     * @param VMware_VCloud_API_CaptureVAppParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function captureVApp($params)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_CAPTURE_VAPP_URL;
        $type = VMware_VCloud_SDK_Constants::CAPTURE_VAPP_PARAMS_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Force sync the library to the remote subscribed library.
     *
     * @return VMware_VCloud_API_TaskType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function sync()
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_SYNC_URL;
        return $this->svc->post($url, 202);
    }

    /**
     * Copy a library item from one library to another.
     *
     * @param VMware_VCloud_API_CopyOrMoveCatalogItemParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function copyCatalogItem($params)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_COPY_CATALOGITEM_URL;
        $type = VMware_VCloud_SDK_Constants::COPY_OR_MOVE_CATALOGITEM_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }

    /**
     * Move a library item from one library to another.
     *
     * @param VMware_VCloud_API_CopyOrMoveCatalogItemParamsType $params
     * @return VMware_VCloud_API_TaskType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function moveCatalogItem($params)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_MOVE_CATALOGITEM_URL;
        $type = VMware_VCloud_SDK_Constants::COPY_OR_MOVE_CATALOGITEM_CONTENT_TYPE;
        return $this->svc->post($url, 202, $type, $params);
    }
}
// end of class VMware_VCloud_SDK_Catalog


/**
 * A class provides convenient methods on a VMware vCloud catalog item entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_CatalogItem extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud catalog item entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getCatalogItemRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             CATALOG_ITEM_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud catalog item entity.
     *
     * @return VMware_VCloud_API_CatalogItemType
     * @since Version 1.0.0
     */
    public function getCatalogItem()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the catalog item from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:catalogitem:' . $this->getUuid();
    }

    /**
     * Get the link to the catalog to which this catalog item belongs.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     */
    public function getCatalogRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the catalog to which this catalog item belongs.
     *
     * @return VMware_VCloud_API_CatalogType
     * @since Version 1.5.0
     */
    public function getCatalog()
    {
        $ref = $this->getCatalogRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Modify a VMware vCloud catalog item.
     *
     * @param VMware_VCloud_API_CatalogItemType $catalogItem
     * @return VMware_VCloud_API_CatalogItemType
     * @since Version 1.0.0
     */
    public function modify($catalogItem)
    {
        $type = VMware_VCloud_SDK_Constants::CATALOG_ITEM_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $catalogItem);
    }

    /**
     * Force sync the library item to the remote subscribed library.
     *
     * @return VMware_VCloud_API_TaskType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function sync()
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_SYNC_URL;
        return $this->svc->post($url, 202);
    }

    /**
     * Delete a VMware vCloud catalog item entity.
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
     * Get metadata associated with the catalog item or metadata associated
     * with the catalog item for the specified key in the specified domain.
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
     * Merges the metadata for the catalog item with the information provided.
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
     * catalog item to thevalue provided. Note: This will replace any existing
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
     * the catalog item.
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
// end of class VMware_VCloud_SDK_CatalogItem


/**
 * A class provides convenient methods on a VMware vCloud media entity.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Media extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud media entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getMediaRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::MEDIA_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud media entity.
     *
     * @return VMware_VCloud_API_MediaType
     * @since Version 1.0.0
     */
    public function getMedia()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the media from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:media:' . $this->getUuid();
    }

    /**
     * Get the link to the container of the media.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.0.0
     */
    public function getVdcRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the container entity, a vDC, of the media
     *
     * @return VMware_VCloud_API_VdcType
     * @since Version 1.0.0
     */
    public function getVdc()
    {
        $ref = $this->getVdcRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Modify name and/or description of the media.
     *
     * @param string $name   New name of the media
     * @param string $description   New description of the media
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.0.0
     */
    public function modify($name=null, $description=null)
    {
        $media = $this->getMedia();
        if (isset($name))
        {
            $media->set_name($name);
        }
        if (!is_null($description))
        {
            $media->setDescription($description);
        }
        $type = VMware_VCloud_SDK_Constants::MEDIA_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $media);
    }

    /**
     * Delete a VMware vCloud virtual media.
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
     * Get a reference to a VMware vCloud catalog entity.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     * @since SDK 5.1.0
     */
    public function getCatalogItemLink()
    {
        $catalogItemReference = null;
        $links = $this->getContainedLinks(null, 'catalogItem');
        foreach ($links as $link)
        {
            if ($link->get_Type() == VMware_VCloud_SDK_Constants::CATALOG_ITEM_CONTENT_TYPE)
            {
                $catalogItemReference = $link;
            }
        }
        return $catalogItemReference;
    }

    /**
     * Returns true if media is in Catalog, otherwise false.
     *
     * @return boolean
     * @since Version 1.5.0
     * @since SDK 5.1.0
     */
    public function isPartOfCatalogItem()
    {
        $links = $this->getCatalogItemLink();
        return ($links != null);
    }

    /**
     * Deletes media and its catalog item. If the media is attached to a catalog item.
     * Deletes media alone. If the media is not attached to any catalog item.
     *
     * @return VMware_VCloud_API_TaskType
     * @since API Version 1.5.0
     * @since SDK Version 5.5.0
     */
    public function deleteMedia()
    {
        if($this->isPartOfCatalogItem())
        {
            $this->svc->createSDKObj($this->getCatalogItemLink())->delete();
        }
        $task = $this->svc->delete($this->url, 202);
        $this->destroy();
        return $task;
    }

    /**
     * Get media owner.
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
     * Get metadata associated with the media or metadata associated with
     * the media for the specified key in the specified domain.
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
     * Merges the metadata for the media with the information provided.
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
     * media to the value provided. Note: This will replace any existing
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
     * the media.
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
     * Enable downloading for the media.
     *
     * @param boolean $wait     To wait till finish, set to true
     * @return VMware_VCloud_API_TaskType
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function enableDownload($wait=true)
    {
        $url = $this->url . VMware_VCloud_SDK_Constants::ACTION_ENABLE_DOWNLOAD_URL;
        $task = $this->svc->post($url, 202);
        return ($wait)? $this->svc->waitForTask($task) : $task;
    }

    /**
     * Get download URL of an iso or floppy media.
     *
     * @param array VMware_VCloud_API_FileType object array $files
     * @return string  an iso media URL
     * @access private
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    private function getDownloadMediaUrl($files)
    {
        $refs = VMware_VCloud_SDK_Helper::getContainedLinks(null, 'download:default', $files[0]);
        if (1 == count($refs))
        {
            return $refs[0]->get_href();
        }
    }

    /**
     * Download the media as an iso or floppy image.
     * contents are downloaded to the specified location. Before downloading
     * make sure the media is enabled for download.
     * The downloaded media file name will be the same as the current media file name in execution.
     * @see Media#enableDownload()
     *
     * @param string $destDir   Directory where to save the downloaded file
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function downloadMedia($destDir='.')
    {
        $mediaName = $this->getMedia()->get_name();
        if(is_null($this->getMedia()->getFiles()))
        {
            throw new VMware_VCloud_SDK_Exception ( "Make sure the media is enabled for downloading.\n");
        }
        $files = $this->getMedia()->getFiles()->getFile();
        // get download URL of an iso media.
        $mediaUrl = $this->getDownloadMediaUrl($files);

        if(!is_null($mediaUrl))
        {
            $dest = implode('/', array($destDir, $mediaName));
            $this->svc->download($mediaUrl, $dest);
        }
        else
        {
            throw new VMware_VCloud_SDK_Exception ( "Media doesn't have download url.\n");
        }
    }

    /**
     * Download the media as an iso or floppy image.
     * contents are downloaded to the specified location. Before downloading
     * make sure the media is enabled for download.
     * @see Media#enableDownload()
     *
     * @param string $destDir   Directory where to save the downloaded file
     * @param string $mediaName  Name of the Media which you want to give
     * @return null
     * @throws VMware_VCloud_SDK_Exception
     * @since API Version 5.5.0
     * @since SDK Version 5.5.0
     */
    public function downloadMediaByName($destDir='.', $mediaName)
    {
        $mediaName = $mediaName .'.'. $this->getMedia()->get_imageType();
        if(is_null($this->getMedia()->getFiles()))
        {
            throw new VMware_VCloud_SDK_Exception ( "Make sure the media is enabled for downloading.\n");
        }
        $files = $this->getMedia()->getFiles()->getFile();
        // get download URL of an iso media.
        $mediaUrl = $this->getDownloadMediaUrl($files);

        if(!is_null($mediaUrl))
        {
            $dest = implode('/', array($destDir, $mediaName));
            $this->svc->download($mediaUrl, $dest);
        }
        else
        {
            throw new VMware_VCloud_SDK_Exception ( "Media doesn't have download url.\n");
        }
    }
}
// end of class VMware_VCloud_SDK_Media


/**
 * A class provides convenient methods on a VMware vCloud organization network
 * entity.
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Network extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud organization network entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 1.0.0
     */
    public function getNetworkRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::NETWORK_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud organization network entity.
     *
     * @return VMware_VCloud_API_OrgNetworkType
     * @since Version 1.0.0
     */
    public function getNetwork()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of the network from its UUID.
     *
     * @return string
     * @since Version 1.5.0
     */
    public function getId()
    {
        return 'urn:vcloud:network:' . $this->getUuid();
    }

    /**
     * Get up link to the org vdc reference.
     *
     * @return VMware_VCloud_API_LinkType object
     * @since SDK Version 5.1.0
     */
    public function getVdcRef()
    {
        $vdcReference = null;
        $links = $this->getNetwork()->getLink();
        foreach ($links as $link)
        {
            if (($link->get_rel()== VMware_VCloud_SDK_Constants::RELATION_TYPE_UP) && ($link->get_Type() == VMware_VCloud_SDK_Constants::VDC_CONTENT_TYPE))
            {
                $vdcReference = $link;
            }
        }
        return $vdcReference;
    }

    /**
     * Get the org vdc this network belongs to.
     *
     * @return VMware_VCloud_API_VdcType object
     * @since SDK Version 5.1.0
     */
    public function getVdc()
    {
        return $this->svc->get($this->getVdcRef()->get_href());
    }

    /**
     * Get the link to the organization to which this network belongs.
     *
     * @return VMware_VCloud_API_LinkType|null
     * @since Version 1.5.0
     * @deprecated since API version 1.5.0, since SDK 5.1.0
     * @This method works only for API 1.5
     */
    public function getOrgRef()
    {
        return $this->getContainerLink();
    }

    /**
     * Get the organization this network belongs to.
     *
     * @return VMware_VCloud_API_OrgType|null
     * @since Version 1.5.0
     * @deprecated since API version 1.5.0, since SDK 5.1.0
     * @This method works only for API 1.5
     */
    public function getOrg()
    {
        $ref = $this->getOrgRef();
        return isset($ref)? $this->svc->get($ref->get_href()) : null;
    }

    /**
     * Get metadata associated with the organization network or metadata
     * associated with the organization network for the specified key in the
     * specified domain.
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
     * Retrieve a list of IP addresses allocated to the network.
     *
     * @return array|null VMware_VCloud_API_AllocatedIpAddressType objects array
     *         or null
     * @since Version 5.1.0
     */
    public function getAllocatedIpAddresses()
    {
        $url = $this->url . '/allocatedAddresses';
        $addrArr = $this->svc->get($url);
        return (0 == sizeof($addrArr)) ? null : $addrArr->getIpAddress();
    }
}
// end of class VMware_VCloud_SDK_Network


/**
 * A class provides convenient methods on a VMware vCloud task entity
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Task extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a VMware vCloud task entity.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function getTask()
    {
        return $this->getDataObj();
    }

    /**
     * Cancel a task.
     *
     * @return null
     * @since Version 1.5.0
     */
    public function cancel()
    {
        if ($this->isRunning())
        {
            $url = $this->url . '/action/cancel';
            $this->svc->post($url, 204);
        }
    }

    /**
     * Check whether this task is running.
     *
     * @return boolean
     * @since Version 1.5.0
     */
    public function isRunning()
    {
        $task = $this->getTask();
        return in_array($task->get_status(), array('queued',
                                'preRunning', 'running'));
    }

    /**
     * Wait for a task to finish.
     *
     * @param int $iteration   Wait loops
     * @param int $interval    Wait interval in seconds
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     */
    public function wait($iteration=15, $interval=20)
    {
        $task = $this->getTask();
        return $this->svc->waitForTask($task, $iteration, $interval);
    }

    /**
     * Update a task.
     *
     * @param VMware_VCloud_API_TaskType $task
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function update($task)
    {
        $type = VMware_VCloud_SDK_Constants::TASK_CONTENT_TYPE;
        return $this->svc->put($this->url, 200, $type, $task);
    }
}
// end of class VMware_VCloud_SDK_Task


/**
 * A class provides convenient methods on a VMware vCloud disk entity
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Disk extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to a VMware vCloud disk entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getDiskRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::DISK_CONTENT_TYPE);
    }

    /**
     * Get a VMware vCloud disk entity.
     *
     * @return VMware_VCloud_API_DiskType
     * @since Version 5.1.0
     */
    public function getDisk()
    {
        return $this->getDataObj();
    }

    /**
     * Constructs vCloud ID of this disk from its UUID.
     *
     * @return string
     * @since Version 5.1.0
     */
    public function getId()
    {
        return 'urn:vcloud:disk:' . $this->getUuid();
    }

    /**
     * Modify the name, description, and storage profile of a disk.
     *
     * @param VMware_VCloud_API_DiskType
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function modify($disk)
    {
        $type = VMware_VCloud_SDK_Constants::DISK_CONTENT_TYPE;
        return $this->svc->put($this->url, 202, $type, $disk);
    }

    /**
     * Delete a disk.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function delete()
    {
        return $this->svc->delete($this->url, 202);
    }

    /**
     * Retrieve a list of all VMs attached to a disk.
     *
     * @return array|null VMware_VCloud_API_ReferenceType object array or null.
     * @since Version 5.1.0
     */
    public function getAttachedVms()
    {
        $url = $this->url . '/attachedVms';
        $vmsArr = $this->svc->get($url);
        return (0 == sizeof($vmsArr)) ? null : $vmsArr->getVmReference();
    }

    /**
     * Retrieve the owner of a disk.
     *
     * @return VMware_VCloud_API_OwnerType
     * @since Version 5.1.0
     */
    public function getOwner()
    {
        $url = $this->url . '/owner';
        return $this->svc->get($url);
    }

    /**
     * Change the owner of a disk.
     *
     * @param VMware_VCloud_API_OwnerType $owner
     * @return null
     * @since Version 5.1.0
     */
    public function changeOwner($owner)
    {
        $url = $this->url . '/owner';
        $type = VMware_VCloud_SDK_Constants::OWNER_CONTENT_TYPE;
        $this->svc->put($url, 204, $type, $owner);
    }

    /**
     * Get metadata associated with the disk or metadata
     * associated with the disk for the specified key in the
     * specified domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|VMware_VCloud_API_MetadataValueType|null
     * @since Version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }

    /**
     * Merges the metadata for the disk with the information provided.
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
     * disk to the value provided. Note: This will replace any existing
     * metadata information.
     *
     * @param string $key
     * @param VMware_VCloud_API_MetadataValueType $value
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
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
     * the disk.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_TaskType
     * @version 5.1.0
     */
    public function deleteMetadataByKey($key, $domain=null)
    {
        $url = $this->getMetadataUrl($key, $domain);
        return $this->svc->delete($url, 202);
    }
}
// end of class VMware_VCloud_SDK_Disk


/**
 * A class provides convenient methods on a VMware vCloud vDC Storage Profile
 * entity
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_VdcStorageProfile extends VMware_VCloud_SDK_Abstract
{
    /**
     * Get a VMware vCloud vDC storage profile entity.
     *
     * @return VMware_VCloud_API_VdcStorageProfileType
     * @since Version 5.1.0
     */
    public function getVdcStorageProfile()
    {
        return $this->getDataObj();
    }

    /**
     * Get a reference to a vDC storage profile entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getVdcStorageProfileRef()
    {
        return $this->getRef(VMware_VCloud_SDK_Constants::
                             VDC_STORAGE_PROFILE_CONTENT_TYPE);
    }

    /**
     * Get metadata associated with the vDC storage profile or metadata
     * associated with the vDC storage profile for the specified key in the
     * specified domain.
     *
     * @param string $key
     * @param string $domain
     * @return VMware_VCloud_API_MetadataType|VMware_VCloud_API_MetadataValueType|null
     * @since Version 5.1.0
     */
    public function getMetadata($key=null, $domain=null)
    {
        return $this->svc->get($this->getMetadataUrl($key, $domain));
    }
}
// end of class VMware_VCloud_SDK_VdcStorageProfile


/**
 * A class provides convenient methods on a VMware vCloud user Service.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_UserService extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Get a reference to the user service entity.
     *
     * @return VMware_VCloud_API_ReferenceType
     * @since Version 5.1.0
     */
    public function getUserServiceRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::USER_SERVICE_CONTENT_TYPE);
    }
    /**
     * Gets the user service data object.
     *
     * @return VMware_VCloud_API_ServiceType
     * @since Version 5.1.0
     */
    public function getUserService()
    {
        return $this->getDataObj();
    }

	/**
     * Retrieves API Definition.
     *
     * @return VMware_VCloud_API_ApiDefinitionType
     * @since Version 5.1.0
     */
    public function getAPIDefinitionRefs()
    {
        $type = 'apiDefinition';
        return $this->svc->queryReferencesByType($type);
    }
}
// end of class VMware_VCloud_SDK_UserService


/**
 * A class provides convenient methods on a VMware vCloud Service api definition.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_APIDefinition extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Returns the API definitions registered by this service.
     *
     * @return VMware_VCloud_API_ApiDefinitionType
     * @since Version 5.1.0
     */
    public function getAPIDefinitionRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::APIDEFINITION_CONTENT_TYPE);
    }

    /**
     * Gets the service api definition data object.
     *
     * @return VMware_VCloud_API_ApiDefinitionType
     * @since Version 5.1.0
     */
    public function getAPIDefinition()
    {
        return $this->getDataObj();
    }

    /**
     * Returns all file descriptors for the API definition.
     *
     * @return VMware_VCloud_API_ContainerType
     * @since Version 5.1.0
     */
    public function getFileDescriptor()
    {
        $url = $this->url . '/files';
        return $this->svc->get($url);
    }
}
// end of class VMware_VCloud_SDK_APIDefinition


/**
 * A class provides convenient methods on a VMware vCloud shadowVm.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_ShadowVm extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Returns the shadowVm.
     *
     * @return VMware_VCloud_API_ReferencesType
     * @since Version 5.1.0
     */
    public function getShadowVmRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::SHADOW_VMS_CONTENT_TYPE);
    }

    /**
     * Gets the shadowVm data object.
     *
     * @return VMware_VCloud_API_ReferencesType
     * @since Version 5.1.0
     */
    public function getShadowVm()
    {
        return $this->getDataObj();
    }

    /**
     * Deletes shadow VM.
     *
     * @return VMware_VCloud_API_TaskType
     * @since Version 1.5.0
     * @deprecated since version 5.1.0
     */
    public function delete()
    {
        $task = $this->svc->delete($this->url, 202);
        $this->destroy();
        return $task;
    }
}
// end of class VMware_VCloud_SDK_ShadowVm


/**
 * A class provides convenient methods on a VMware vCloud taskslist.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_TasksList extends
      VMware_VCloud_SDK_Abstract
{
    /**
     * Returns the taskslist.
     *
     * @return VMware_VCloud_API_ReferencesType
     * @since Version 5.1.0
     */
    public function getTasksListRef()
    {
        return $this->getRef(
                       VMware_VCloud_SDK_Constants::TASK_LISTS_CONTENT_TYPE);
    }

    /**
     * Gets the taskslist data object.
     *
     * @return VMware_VCloud_API_ReferencesType
     * @since Version 5.1.0
     */
    public function getTasksList()
    {
        return $this->getDataObj();
    }

    /**
     * Create task.
     *
     * @param VMware_VCloud_API_TaskType $params
     * @return VMware_VCloud_API_TaskType
     * @since Version 5.1.0
     */
    public function createTasksList($params)
    {
        $type = VMware_VCloud_SDK_Constants::TASK_CONTENT_TYPE;
        return $this->svc->post($this->url, 200, $type, $params);
    }
}
// end of class VMware_VCloud_SDK_TasksList
?>


