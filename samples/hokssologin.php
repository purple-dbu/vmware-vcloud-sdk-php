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
    * Login to vCloud Director using the SAML Assertion XML from vSphere SSO/SAML IDP's.
    * This sample can be used in performing: SSO login with HOK tokens and the signature attributes in Org/System scope. vCD also does the signature verification of the provided HOK token.
    * Note: For Bearer tokens there is no signature verification done.
    */

    // Get parameters from command line
    $shorts  = "";
    $shorts .= "s:";
    $shorts .= "v:";
    $shorts .= "o:";
    $shorts .= "a:";
    $shorts .= "u:";
    $shorts .= "c:";

    $longs  = array(
        "server:",    //-s|--server    [required] vCloud Director server IP/hostname should be something like "https://<ip>:<port>"
        "sdkver:",    //-v|--sdkver    [required]
        "org:",       //-o|--org       [required] vCloud Director login organization name
        "signalgo:",  //-a|--signalgo  [required] Standard signature algorithm name
        "useHok:",    //-u|--useHok    [required] set true for HOK Token and false for Bearer Token
        "certpath:",  //-c|--certpath  [optional] local certificate path
    );

    $opts = getopt($shorts, $longs);

    // Initialize parameters
    $httpConfig = array('ssl_verify_peer'=>false, 'ssl_verify_host'=>false);
    $org = null;
    $signature_alg = null;
    $useHok = null;

    // loop through command arguments
    foreach (array_keys($opts) as $opt) switch ($opt)
    {
        case "s":
            $server = $opts['s'];
            break;
        case "server":
            $server = $opts['server'];
            break;

        case "v":
            $sdkversion = $opts['v'];
            break;
        case "sdkver":
            $sdkversion = $opts['sdkver'];
            break;

        case "o":
            $org = $opts['o'];
            break;
        case "org":
            $org = $opts['org'];
            break;

        case "a":
            $signature_alg = $opts['a'];
            break;
        case "signalgo":
            $signature_alg = $opts['signalgo'];
            break;

        case "u":
            $useHok = $opts['u'];
            break;
        case "useHok":
            $useHok = $opts['useHok'];
            break;

        case "c":
            $certPath = $opts['c'];
            break;
        case "certpath":
            $certPath = $opts['certpath'];
            break;
    }

    // parameters validation
    if (!isset($server) || !isset($sdkversion) || !isset($org) || !isset($signature_alg) || !isset($useHok))
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

      /**
       * Change the SAML Assertion XML.
       * For vSphere SSO - Use the vSphere webservice Java/.NET SDK SSO Samples to get the
       * Holder-Of-Key(SAML Assertion XML).
       */
        $samlAssertionXML = "<saml2:Assertion xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ID=\"_1ba569c3-ec3d-427f-801d-608be1a70511\" IssueInstant=\"2013-05-06T07:58:20.581Z\" Version=\"2.0\"><saml2:Issuer Format=\"urn:oasis:names:tc:SAML:2.0:nameid-format:entity\">https://wdc-eeapps-dhcp204.eng.vmware.com/websso/SAML2/Metadata/vsphere.local</saml2:Issuer><ds:Signature xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm=\"http://www.w3.org/2001/10/xml-exc-c14n#\"/><ds:SignatureMethod Algorithm=\"http://www.w3.org/2001/04/xmldsig-more#rsa-sha256\"/><ds:Reference URI=\"#_1ba569c3-ec3d-427f-801d-608be1a70511\"><ds:Transforms><ds:Transform Algorithm=\"http://www.w3.org/2000/09/xmldsig#enveloped-signature\"/><ds:Transform Algorithm=\"http://www.w3.org/2001/10/xml-exc-c14n#\"><ec:InclusiveNamespaces xmlns:ec=\"http://www.w3.org/2001/10/xml-exc-c14n#\" PrefixList=\"xs xsi\"/></ds:Transform></ds:Transforms><ds:DigestMethod Algorithm=\"http://www.w3.org/2001/04/xmlenc#sha256\"/><ds:DigestValue>aDnjMtP46F9PmwLuU7QpmK7GSUuCO0+D5J+FuHMjtRs=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>AMVtgZ+jPihs2I6vPk8U+woaxnaxCZ5//EbULnRI4986AGx8Tlj1bl+sRAc5aHpkfjC0secCLE6M
OdUzbmzP9NuRappA9cD40JqQb73kppZdwIC89g47SDe4p5chBo065if5WMwLK0eiCXnsC+KOeW4B
RwvrAtBUvJzzZI2VBjy98eswMhXKMe99aimpOV4Ez8hQqcj3XdqDMqojxnQAzddyjCizjD39n/mR
lCULit0XzRZmNJjzyHTkHAaWA9bgl84KbCpT8hkbKLbvBCiGJzYZ38c7KZXizvUcb6GGQT6WcXmI
Q/srJu4xK8MxB/cv5I2xQTPwU3800XqrKPAzdg==</ds:SignatureValue><ds:KeyInfo><ds:X509Data><ds:X509Certificate>MIIDzzCCAregAwIBAgIRALmYxWXCpSY+6gqeJWKo7MowDQYJKoZIhvcNAQELBQAwTzELMAkGA1UE
BhMCVVMxDzANBgNVBAoTBlZNd2FyZTEbMBkGA1UECxMSVk13YXJlIEVuZ2luZWVyaW5nMRIwEAYD
VQQDEwlDZXJUb29sQ0EwHhcNMTMwNTAxMDE0ODMxWhcNMjMwNDI5MDE0ODMxWjBlMQswCQYDVQQG
EwJVUzFWMFQGA1UEAxNNc3Nvc2VydmVyU2lnbixPVT1sZHUtZDEzOWMwMDEtZmNmNS00ODNkLWFi
YzctZDI5NWUyNzk2YThlLGRjPXZzcGhlcmUsZGM9bG9jYWwwggEiMA0GCSqGSIb3DQEBAQUAA4IB
DwAwggEKAoIBAQDJkh9nZ8z/giwx56bLbFNhD1/r5IV92Xg74LErB8rKiY1DnFdcq5SEvsqOpked
gbDaOwonuFkGFU3jPgFjJ3afwFGNjLoOl08e3gqoQZiCwEF1u7e4xDuOJil9NUHZXD5igz2TF7E0
EG9aoA7nmQRBhGuNETyrEAu3T9Nk4w3/RImFAqF5O9O4yUkOCAbG+uMIPSUTZlKDLRRLQaDEbMHS
jXoBd9b6ANJqWSW5iQYrdyHhmon1DbKxoSExSOoNuybw3GCu72hJKhI9s9w5vv0IMHZ+648ffxfD
R9d/qq/3XeBrKKCZtTL3eMJ/guDVh2l/m4CtaUzfKjvHfi71iHZ1AgMBAAGjgY8wgYwwDAYDVR0T
AQH/BAIwADAOBgNVHQ8BAf8EBAMCBPAwHwYDVR0jBBgwFoAUoY2/SkCY1JqTRrK2iB+vu1GOToMw
HQYDVR0OBBYEFAm7ddnSDxqkakxBSaiF3C8+FgeMMCwGA1UdEQQlMCOCIXdkYy1lZWFwcHMtZGhj
cDIwNC5lbmcudm13YXJlLmNvbTANBgkqhkiG9w0BAQsFAAOCAQEAfLNq/EgFoyXuUxwrTCyojEJt
9dyoVi+2a/gMQ4C9nzo5LYmLibKxhpgPmWsQHxzOabn20mf2vNS4R2vEfhnJMMpqQAVpX0AOPzC3
cmsa/FliQ0YY0R2sUjHF75xZbdbvmjlFlLkyN5A8OC0Z8IUJyStuRUH+YzQZPr0kWQM0MLt1CRBW
9XtmXzRjnGItFd8cqg1HlVZwN+OnZSON+sJkiNbCIsxYlin7UbSCeDtWJx/i+EUMqQSmQ0q72J45
lCfgigqPrDkQ6rTottX19jY7OJQ6ktdfPzh+fU3/yM9mFiRbTisBznGgAs08FB+J7amFJDfIoyld
4w92dJs3xSWAMg==</ds:X509Certificate><ds:X509Certificate>MIIDkDCCAnigAwIBAgIRAO4wgD0J8nR+/ApD8bI3slIwDQYJKoZIhvcNAQELBQAwTzELMAkGA1UE
BhMCVVMxDzANBgNVBAoTBlZNd2FyZTEbMBkGA1UECxMSVk13YXJlIEVuZ2luZWVyaW5nMRIwEAYD
VQQDEwlDZXJUb29sQ0EwHhcNMTMwNTAxMDE0NzQ4WhcNMjMwNDI5MDE0NzQ4WjBPMQswCQYDVQQG
EwJVUzEPMA0GA1UEChMGVk13YXJlMRswGQYDVQQLExJWTXdhcmUgRW5naW5lZXJpbmcxEjAQBgNV
BAMTCUNlclRvb2xDQTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBANEDeL/acTnBe238
P3JpsqFhUWSQFWMjQjpwBb6OcBOZJkA2eOhi5CJNaOsWczYga3a7Bu7wMW/T9o8hn+fDwdiCbziN
lxlv2OAWhhMip6rVFtVd6yBFE14boA/M0ULMFxSIAwoYMlp8GKoecUqEUrPBR9QLKauLCF0/YJS1
lo34O7t47ND2a3fd3vTK67GEYv0GB+0b1Fu6XM+0n0uRG12Wz1qeoVEllHKBfl4CovFHwJNlg+GQ
g3stANgbtJz+CW1GPRPjqlOQZHNuozvSYQ4g+704AfZTFm5dZA8ju2NiS8ShpsXZuBK/uLAt8Um6
F9ZDL/SEZpxgp5g1ml+hsa8CAwEAAaNnMGUwEgYDVR0TAQH/BAgwBgEB/wIBATAOBgNVHQ8BAf8E
BAMCAQYwHQYDVR0OBBYEFKGNv0pAmNSak0aytogfr7tRjk6DMCAGA1UdEQQZMBeBD3ZtY2FAdm13
YXJlLmNvbYcEfwAAATANBgkqhkiG9w0BAQsFAAOCAQEAbhwfMVPlC9wFUvzetSPu9cp6Dx0F8oal
m0XZ2dOIyrQY1OpFW9dFWB4gjZ5nB7I19I/gVarRvvzkyEgUR82D21fsoFpmZYe2PCfMVumLDSos
wDaWuGf5JXzhm3BKHYKM1O9vB4/LvTMxAaH9xDCVkOLDYsvSBnUr6OKVbUDfeaD+tiOiiel+8rxq
1QK5Ewbceaco8q04UBiBYo72KDLfgLwiyemkwIfxndiA7Dy3eP9zd3m2L0O7/p8fDz2ioB4gHu68
QtL0FE+2KITCkNM1lRqjpnQW5ZHY6VHN8NNnGb9rU86+ysLgEjaM+X0PN9zg9Zq7CSZBQvSYYXBG
k1ZVFw==</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature><saml2:Subject><saml2:NameID Format=\"http://schemas.xmlsoap.org/claims/UPN\">Administrator@VSPHERE.LOCAL</saml2:NameID><saml2:SubjectConfirmation Method=\"urn:oasis:names:tc:SAML:2.0:cm:bearer\"><saml2:SubjectConfirmationData NotOnOrAfter=\"2013-05-06T08:03:20.581Z\"/></saml2:SubjectConfirmation></saml2:Subject><saml2:Conditions NotBefore=\"2013-05-06T07:58:20.581Z\" NotOnOrAfter=\"2013-05-06T08:03:20.581Z\"><saml2:ProxyRestriction Count=\"0\"/><saml2:AudienceRestriction><saml2:Audience>https://wdc-eeapps-dhcp164.eng.vmware.com:443/cloud/org/ssotest/saml/metadata/alias/vcd</saml2:Audience></saml2:AudienceRestriction></saml2:Conditions><saml2:AuthnStatement AuthnInstant=\"2013-05-06T07:58:20.576Z\"><saml2:AuthnContext><saml2:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml2:AuthnContextClassRef></saml2:AuthnContext></saml2:AuthnStatement><saml2:AttributeStatement><saml2:Attribute FriendlyName=\"surname\" Name=\"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname\" NameFormat=\"urn:oasis:names:tc:SAML:2.0:attrname-format:uri\"><saml2:AttributeValue xsi:type=\"xs:string\">vsphere.local</saml2:AttributeValue></saml2:Attribute><saml2:Attribute FriendlyName=\"Groups\" Name=\"http://rsa.com/schemas/attr-names/2009/01/GroupIdentity\" NameFormat=\"urn:oasis:names:tc:SAML:2.0:attrname-format:uri\"><saml2:AttributeValue xsi:type=\"xs:string\">vsphere.local\\Users</saml2:AttributeValue><saml2:AttributeValue xsi:type=\"xs:string\">vsphere.local\\Administrators</saml2:AttributeValue><saml2:AttributeValue xsi:type=\"xs:string\">vsphere.local\\ComponentManager.Administrators</saml2:AttributeValue></saml2:Attribute><saml2:Attribute FriendlyName=\"givenName\" Name=\"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname\" NameFormat=\"urn:oasis:names:tc:SAML:2.0:attrname-format:uri\"><saml2:AttributeValue xsi:type=\"xs:string\">Administrator</saml2:AttributeValue></saml2:Attribute><saml2:Attribute FriendlyName=\"Subject Type\" Name=\"http://vmware.com/schemas/attr-names/2011/07/isSolution\" NameFormat=\"urn:oasis:names:tc:SAML:2.0:attrname-format:uri\"><saml2:AttributeValue xsi:type=\"xs:string\">false</saml2:AttributeValue></saml2:Attribute></saml2:AttributeStatement></saml2:Assertion>";

        $signature = 'CmwwXhbtEAWkjWP5Kc3tYwO8ii/Qj4l4Vba+vAttZrwFOcYTp4XRIWTekxCQ1LAvRWxMuOgpEmInu225HwYMsNK+z8gbHcLCa1fi8nhWG08Q474z/hVNUPqdHKXSEBCoLRsdJCFoCIN2pxkZ+u7yPSV9GuyZ833GdSst/tJQr1U=';


        /** This is all the settings you should ideally need to set:
         *   1. HoK in System scope: useHok = true and org="System"
         *   2. HoK in org scope:    useHok = true and org="<orgName>"
         *   3. Bearer in System:    useHok = false and org="System"
         *   4. Bearer in org scope: useHok = false and org="<orgName>"
         */
        if ($useHok == 'false')
        {
            $signature = null;
        }

        // Login
        $service = VMware_VCloud_SDK_Service::getService();
        echo "SDK Version: " . $sdkversion . "\n\n";
        $res = $service->HOKSSOLogin($server, $samlAssertionXML, $org, $httpConfig, $sdkversion, $signature, $signature_alg);
        echo "SSOLogin Response: \n\n";
        print_r($res);

        if ($useHok == 'true')
        {
            echo "HOK Login Response:\n\n";
        }
        else
        {
            echo "Bearer Login Response:\n\n";
        }
        $orgrefs = $service->getOrgRefs();
        echo "Get Vcloud Org References.\n\n";
        print_r($orgrefs);
    }
    else
    {
        echo "\n\nLogin Failed due to certification mismatch.";
        return;
    }

    // log out
    $service->logout();
    echo "logged out.\n";

    /**
     * Print the help message of the sample.
     */
    function usage()
    {
        echo "Usage:\n\n";
        echo "  [Description]\n";
        echo "     This sample demonstrates SSO logging into VMware vCloud Director.\n";
        echo "     This sample can be used in performing: SSO login with HOK tokens and the signature attributes in Org/System scope. vCD also does the\n";
        echo "     signature verification of the provided HOK token.\n";
        echo "     Note: For Bearer tokens there is no signature verification done.\n";
        echo "\n";
        echo "  [Usage]\n";
        echo "     # php hokssologin.php -s <server> -v <sdkversion> -o <organizationname>  -a <signature_alg>\n";
        echo "\n";
        echo "     -s|--server <IP|hostname>          [req] IP or hostname of the vCloud Director should be something like [https://<ip>:<port>].\n";
        echo "     -v|--sdkver <sdkversion>           [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
        echo "     -o|--org <organizationname>        [req] organization name.\n";
        echo "                                              for the vCloud Director.\n";
        echo "     -a|--signalgo <signature_alg>      [req] Standard signature algorithm name such as 'SHA512withRSA'.\n";
        echo "     -u|--useHok <useHok>               [req] Set true for HOK Token and false for Bearer Token.\n";
        echo "     -c|--certpath <certificatepath>    [opt] Local certificate's full path.\n";
        echo "  [Examples]\n";
        echo "     # php hokssologin.php -s 127.0.0.1 -v 5.5 -o Org -a signaturealgorithmname -u true\n";
        echo "     # php hokssologin.php -s 127.0.0.1 -v 5.5 -o Org -a signaturealgorithmname -u false -c certificatepath\n";
        echo "     # php hokssologin.php  // using config.php to set all required parameters\n\n";
    }
?>
