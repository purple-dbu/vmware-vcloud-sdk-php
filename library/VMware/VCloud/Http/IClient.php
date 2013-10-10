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
 * An interface for HTTP connection.
 *
 * @package VMware_VCloud_SDK_Http
 */
interface VMware_VCloud_SDK_Http_Client_Interface
{
    /**
     * Set the login user name and password.
     *
     * @param array $auth   In array('username'=><username>,
     *                               'password'=><password>) format
     * @since Version 1.0.0
     */
    public function setAuth($auth);

    /**
     * Set the HTTP configurations used by HTTP request.
     *
     * @param array $config   In array('<param1>'=><value1>,
     *                                 '<param2>'=><value2>, ...
     *                                 '<paramn>'=><valuen>) format
     * @since Version 1.0.0
     */
    public function setConfig($config);

    /**
     * HTTP GET request
     *
     * @param string $url       URL to send an HTTP request
     * @param array  $headers   HTTP request headers
     * @return VMware_VCloud_SDK_Http_Response_Interface
     * @since Version 1.0.0
     */
    public function get($url, $headers=null);

    /**
     * HTTP POST request
     *
     * @param string $url       URL to send an HTTP request
     * @param array $headers    HTTP request headers
     * @param mixed $data       HTTP request body
     * @return VMware_VCloud_SDK_Http_Response_Interface
     * @since Version 1.0.0
     */
    public function post($url, $headers, $data);

    /**
     * HTTP POST request
     *
     * @param string $url       URL to send an HTTP request
     * @param array $headers    HTTP request headers
     * @param mixed $data       HTTP request body
     * @return VMware_VCloud_SDK_Http_Response_Interface
     * @since Version 1.0.0
     */
    public function put($url, $headers, $data);

    /**
     * HTTP DELETE request
     *
     * @param string $url       URL to send an HTTP request
     * @return VMware_VCloud_SDK_Http_Response_Interface|null
     * @since Version 1.0.0
     */
    public function delete($url);

    /**
     * Download a file and dump to specified location.
     *
     * @param string $url      Download source
     * @param array $headers   HTTP request headers
     * @param string $dest  Destination of the file to write to
     * @since Version 1.0.0
     */
    public function download($url, $headers, $dest);

    /**
     * Upload a file.
     *
     * @param string $url      Target to upload the file
     * @param array $headers   HTTP request headers
     * @param string $file     Full path of the file to be uploaded
     * @since Version 1.0.0
     */
    public function upload($url, $headers, $file);
}
// end of interface VMware_VCloud_SDK_Http_Client_Interface
?>
