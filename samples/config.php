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

// add library to the include_path
set_include_path(implode(PATH_SEPARATOR, array('.','../library',
                         get_include_path(),)));

require_once 'VMware/VCloud/Helper.php';

/**
 * HTTP connection parameters
 */

// IP or hostname of the vCloud Director.
// Format is 'IP/hostname[:port]'
// For example, the following settings are allowed:
// $server = '127.0.0.1';         (using default port 443)
// $server = '127.0.0.1:8443';    (using port 8443)
$server = '<please set>';

// User name for login request, in the form user@organization
// System administrator must log in as a member of the System organization
$user = '<please set>';

// Password for user
$pswd = '<please set>';

// Organization name
$org = '<please set>';

// SDK Version
$sdkVersion = '<please set>';

// proxy host, optional
$phost = null;

// proxy port, optional
$pport = null;

// proxy username, optional
$puser = null;

// proxy password, optional
$ppswd = null;

// CA certificate file name with full directory path. To turn on certification
// verification, set ssl_verify_peer to true in the $httpConfig parameter.
$cert = null;

/**
 * Create $httpConfig as HTTP connection parameters used by HTTP_Request2
 * library. Please refer to HTTP_Request2 documentation $config variable
 * for details.
 */
$httpConfig = array('proxy_host'=>$phost,
                    'proxy_port'=>$pport,
                    'proxy_user'=>$puser,
                    'proxy_password'=>$ppswd,
                    'ssl_verify_peer'=>false,
                    'ssl_verify_host'=>false,
                    'ssl_cafile' => $cert
                   );
?>
