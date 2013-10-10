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
 * Class for query parameters
 */
require_once 'VMware/VCloud/Query/QueryParams.php';

/**
 * Query abstract class
 */
require_once 'VMware/VCloud/Query/QueryAbstract.php';

/**
 * Class for supported query types
 */
require_once 'VMware/VCloud/Query/QueryTypes.php';

/**
 * A class provides query service for VMware vCloud Director.
 *
 * @package VMware_VCloud_SDK
 */
class VMware_VCloud_SDK_Query extends VMware_VCloud_SDK_Query_Abstract
{
    /**
     * An instance of VMware_VCloud_SDK_Service.
     */
    private $svc = null;

    /**
     * An instance of VMware_VCloud_SDK_Query
     */
    private static $sdkQuery = null;

    /**
     * The base query URL.
     */
    private $queryUrl = null;

    /**
     * 'references' query format.
     */
    const QUERY_REFERENCES = 'references';

    /**
     * 'records' query format.
     */
    const QUERY_RECORDS = 'records';

    /**
     * 'idrecords' query format.
     */
    const QUERY_IDRECORDS = 'idrecords';

    /**
     * Constructor
     *
     * @param VMware_VCloud_SDK_Service
     * @access private
     */
    private function __construct($svc)
    {
        $this->svc = $svc;
        $this->queryUrl = $svc->getBaseUrl() . '/query?type=';
    }

    /**
     * Returns an instance of VMware_VCloud_SDK_Query object.
     *
     * @param VMware_VCloud_SDK_Service
     * @return VMware_VCloud_SDK_Query
     * @since Version 1.5.0
     */ 
    public static function getInstance($svc)
    {
        if (self::$sdkQuery)
        {
            return self::$sdkQuery;
        }
        return new VMware_VCloud_SDK_Query($svc);
    }

    /**
     * Retrieves references for the specified query type.
     *
     * @param string $type
     * @param array|VMware_VCloud_SDK_QueryParams $params
     * @return VMware_VCloud_API_ReferencesType
     * @since Version 1.5.0
     */
    public function queryReferences($type, $params=null)
    {
        return $this->query($type, $params, self::QUERY_REFERENCES);
    }

    /**
     * Retrieves records for the specified query type.
     *
     * @param string $type
     * @param array|VMware_VCloud_SDK_QueryParams $params
     * @return VMware_VCloud_API_QueryResultRecordsType
     * @since Version 1.5.0
     */
    public function queryRecords($type, $params=null)
    {
        return $this->query($type, $params, self::QUERY_RECORDS);
    }

    /**
     * Retrieves idrecords for the specified query type.
     *
     * @param string $type
     * @param array|VMware_VCloud_SDK_QueryParams $params
     * @return VMware_VCloud_API_QueryResultRecordsType
     * @since Version 1.5.0
     */
    public function queryIdRecords($type, $params=null)
    {
        return $this->query($type, $params, self::QUERY_IDRECORDS);
    }

    /**
     * Retrieves idrecords for the specified query type.
     *
     * @param string $type
     * @param array|VMware_VCloud_SDK_QueryParams $params
     * @param string $format
     * @return VMware_VCloud_API_QueryResultRecordsType
     * @access private
     */
    private function query($type, $params, $format)
    {
        $url = $this->constructUrl($type, $params);
        $url .= '&format=' . $format;
        return $this->svc->get($url);
    }

    /**
     * Constructs a query URL, including query type and query parameters.
     *
     * @param string $type
     * @param array|VMware_VCloud_SDK_QueryParams $params
     * @return string
     * @access private
     */
    private function constructUrl($type, $params=null)
    {
        $url = $this->queryUrl . $type;
        if (!is_null($params))
        {
            $ps = method_exists($params, 'getParams')? 
                  $params->getParams() : $params;
            foreach ($ps as $key => $value)
            {
                if (isset($value))
                {
                    $url .= '&' . "$key=$value";
                }
            }
        }
        //echo "query URL = $url\n";
        return $url;
    }
}
?>
