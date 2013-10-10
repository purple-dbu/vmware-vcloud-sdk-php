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
 * An interface for HTTP response.
 *
 * @package VMware_VCloud_SDK_Http
 */
Interface VMware_VCloud_SDK_Http_Response_Interface
{
    /**
     * Set response
     *
     * @param mix $response   A response object
     * @since Version 1.0.0
     */
    public function setResponse($response);

    /**
     * Get HTTP response status code
     *
     * @since Version 1.0.0
     */
    public function getStatus();

    /**
     * Get HTTP response body
     *
     * @since Version 1.0.0
     */
    public function getBody();

    /**
     * Get HTTP response header
     *
     * @param string $key   The key name of the HTTP header, if null,
     *                      returns all headers
     * @since Version 1.0.0
     */
    public function getHeader($key=null);
}
// end of interface VMware_VCloud_SDK_Http_Response_Interface
?>
