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
 */

// Get parameters from command line
$shorts  = "";
$shorts .= "s:";
$shorts .= "o:";
$shorts .= "v:";
$shorts .= "c:";

$longs  = array(
    "server:",    //-s|--server    [required] vCloud Director server IP/hostname
    "org:",       //-o|--org       [required] vCloud Director login organization name
    "sdkver:",    //-v|--sdkver    [required]
    "certpath:",  //-c|--certpath  [optional] local certificate path
);

$opts = getopt($shorts, $longs);

// loop through command arguments
foreach (array_keys($opts) as $opt) switch ($opt)
{
    case "s":
        $server = $opts['s'];
        break;
    case "server":
        $server = $opts['server'];
        break;

    case "o":
        $org = $opts['o'];
        break;
    case "org":
        $org = $opts['org'];
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
if (!isset($server) || !isset($org) || !isset($sdkversion))
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
    $samlAssertionXML = "<saml2:Assertion xmlns:saml2=\"urn:oasis:names:tc:SAML:2.0:assertion\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ID=\"_9d0080ee-1536-470a-a313-2840133b14a0\" IssueInstant=\"2012-08-03T09:34:42.725Z\" Version=\"2.0\"><saml2:Issuer Format=\"urn:oasis:names:tc:SAML:2.0:nameid-format:entity\">https://wdc-eeapps-dhcp501.eng.vmware.com:7444/STS</saml2:Issuer><ds:Signature xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm=\"http://www.w3.org/2001/10/xml-exc-c14n#\"/><ds:SignatureMethod Algorithm=\"http://www.w3.org/2001/04/xmldsig-more#rsa-sha256\"/><ds:Reference URI=\"#_9d0080ee-1536-470a-a313-2840133b14a0\"><ds:Transforms><ds:Transform Algorithm=\"http://www.w3.org/2000/09/xmldsig#enveloped-signature\"/><ds:Transform Algorithm=\"http://www.w3.org/2001/10/xml-exc-c14n#\"><ec:InclusiveNamespaces xmlns:ec=\"http://www.w3.org/2001/10/xml-exc-c14n#\" PrefixList=\"xs xsi\"/></ds:Transform></ds:Transforms><ds:DigestMethod Algorithm=\"http://www.w3.org/2001/04/xmlenc#sha256\"/><ds:DigestValue>g5IqxFbB/nwuQvVS6obGsjqScpAq1W3n+iZ0dectfiE=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>mKCR5Aj9IVggGXVDRVz8pR/jhXDVC8lkqf7+/pzkIpWwKy/R+7oCa2cMiPvxIE+rD3aq5Os07aku"
. "yGQD+w/XDHxTz86h8Fw4uX29uhkLWfUVi6vXxf7u/kHTdpAxKtGDCssTGr+91UDRH0p3v4Xi5UJ7"
. "9lpiT32gv3CPyq59dxtjt6am03l9MugfzLu9cZho8F10IfdTyZ0YF+Gc8aV5bxUMzsK9jjUNzcrA"
. "qvb5dnpQY2NGcZBV7gnPkdfwpALp9h6kcxVdNzMbXZKQgvrKSW4kNLvB8RNF9Qqr1KM21EaUn/dt"
. "BXq/eCaOrvCQgf1mNhfQlq0B3S5YEklnPdo9Ew==</ds:SignatureValue><ds:KeyInfo><ds:X509Data><ds:X509Certificate>MIIExjCCA66gAwIBAgIBATANBgkqhkiG9w0BAQsFADCBsTELMAkGA1UEBhMCVVMxEzARBgNVBAgT"
. "CkNhbGlmb3JuaWExEjAQBgNVBAcTCVBhbG8gQWx0bzEVMBMGA1UEChMMVk13YXJlLCBJbmMuMTYw"
. "NAYDVQQDEy13ZGMtZWVhcHBzLWRoY3A1MDEuZW5nLnZtd2FyZS5jb20gQ0EgYmQ0MGY2MmExKjAo"
. "BgkqhkiG9w0BCQEWG3NzbC1jZXJ0aWZpY2F0ZXNAdm13YXJlLmNvbTAeFw0xMjA3MzAwNTQ0MTVa"
. "Fw0yMjA3MjkwNTQ0MTZaMIIBDDELMAkGA1UEBhMCVVMxEzARBgNVBAgTCkNhbGlmb3JuaWExEjAQ"
. "BgNVBAcTCVBhbG8gQWx0bzEVMBMGA1UEChMMVk13YXJlLCBJbmMuMSowKAYDVQQLEyFWTXdhcmUg"
. "dkNlbnRlciBTZXJ2ZXIgQ2VydGlmaWNhdGUxKjAoBgkqhkiG9w0BCQEWG3NzbC1jZXJ0aWZpY2F0"
. "ZXNAdm13YXJlLmNvbTEqMCgGA1UEAxMhd2RjLWVlYXBwcy1kaGNwNTAxLmVuZy52bXdhcmUuY29t"
. "MTkwNwYJKoZIhvcNAQkCEyoxMzQzNzEzNDU1LGY2MzJlZmRhLDU2NGQ3NzYxNzI2NTIwNDk2ZTYz"
. "MmUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC4fpVkMwXzTr/tfj4drRAnYiS9ET3O"
. "D7P5FTGxJJBcBB0AFKd6KsT9xxSQdyHhkCnyJA46ZwyOoVkMsJhvQEPh4E2+YxEkPQi/2T9gecif"
. "KzEmdWsGGgKnO2pkI7V8ULfgJyijUdzesv3kgL6LQAi5/1+63h6giNvbPENs5U2Z7+RhyhYOMENS"
. "DJTGJV/IAAPmdAstjXu9tQ4ugQ9oSK/XuPGh5Ne6u3N1x4GttIc1aAZfUpmHM4AXcmApGv89CF/6"
. "UAmH1LPO6O2yLQarVxjo4Uh/gtvgqXAE1oXZ4Iqm1QoOzbChbr/iQl5dfAT/QYeFSiGto/b88AWy"
. "5Q0ua++9AgMBAAGjgYowgYcwCQYDVR0TBAIwADALBgNVHQ8EBAMCBLAwEwYDVR0lBAwwCgYIKwYB"
. "BQUHAwEwWAYDVR0RBFEwT4Ihd2RjLWVlYXBwcy1kaGNwNTAxLmVuZy52bXdhcmUuY29tghJ3ZGMt"
. "ZWVhcHBzLWRoY3A1MDGHBAqEY8mHEP6AAAAAAAAAAgwp//5wLCgwDQYJKoZIhvcNAQELBQADggEB"
. "AATJFEbDNNY3MJ4MtWnJV//wduxNRABg9PnB9XWcug5058Mx2/HzIn253O38+MXszROym9XLy+jP"
. "rTBtdjK/YrkiGA2+1oWsoltHxepshjN2LT+Tbz2kgOjWVnDdfoneXc1xKNpYaYTEPGJdBfzhT7vB"
. "arBY4jwDC/d0lLfzPPMUKQv+t/CjvEC3itqf9YDqTplbwm3BabBuqlTPC0ce7KxBNtDX+ApoeH85"
. "Rci+jomRFEnjtWANog+hpCsOG2q7k1BNIwUenxKlHmy4v8buqlbBy9xcul4hdVk2tnp4lgdEYzPp"
. "nJl4XBRzN8ApAcU5J3BKtaSbWbGmXPPKAcMeYFE=</ds:X509Certificate></ds:X509Data></ds:KeyInfo></ds:Signature><saml2:Subject><saml2:NameID Format=\"http://schemas.xmlsoap.org/claims/UPN\">root@localos</saml2:NameID><saml2:SubjectConfirmation Method=\"urn:oasis:names:tc:SAML:2.0:cm:holder-of-key\"><saml2:SubjectConfirmationData xsi:type=\"saml2:KeyInfoConfirmationDataType\"><ds:KeyInfo xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\"><ds:X509Data><ds:X509Certificate>MIIChTCCAe6gAwIBAgIIAZENpy5vgRcwDQYJKoZIhvcNAQEFBQAwgYQxCzAJBgNVBAYTAlVTMRMwEQYDVQQIEwpDYWxpZm9ybmlhMRIwEAYDVQQHEwlQYWxvIEFsdG8xFTATBgNVBAoTDFZNd2FyZSwgSW5jLjEeMBwGA1UECxMVRWNvc3lzdGVtIEVuZ2luZWVyaW5nMRUwEwYDVQQDDAwqLnZtd2FyZS5jb20wHhcNMTIwODAzMDkzNDEyWhcNMTMwODAzMDkzNDEyWjCBhDELMAkGA1UEBhMCVVMxEzARBgNVBAgTCkNhbGlmb3JuaWExEjAQBgNVBAcTCVBhbG8gQWx0bzEVMBMGA1UEChMMVk13YXJlLCBJbmMuMR4wHAYDVQQLExVFY29zeXN0ZW0gRW5naW5lZXJpbmcxFTATBgNVBAMMDCoudm13YXJlLmNvbTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAgpBTJCRbhqmU759o+P2yiOEXP4JwYM4ISpJ1/Pw9EHhgcXvtCs4EMpFzksy9GzSXxcvAFpJ76Rg6d5KPqM/lruGFSpDmqrOXo60LAQcoQbG/+jJmQcChhua7/sN/y/zE5pgo9oRSLg3xhwSAk6E4kxjiVtIv1hiChP2GNatSqOECAwEAATANBgkqhkiG9w0BAQUFAAOBgQBT/Yic+6Y9/mQSGbpIM5UGNJoFVa8Sy8kPckzcJY2SHjPGyIUlXQffXJC+/WNb+mSj3scl0hcw7pL+kvRLUJimca5+MhSp+INz3ozf78JeRialbK2qbC2rQWRD53k3lkmr6JnuY/3jJdbAp+uPO2dVgGzvSSQ9mKYNF2lnPICPhw==</ds:X509Certificate></ds:X509Data></ds:KeyInfo></saml2:SubjectConfirmationData></saml2:SubjectConfirmation></saml2:Subject><saml2:Conditions NotBefore=\"2012-08-03T09:34:13.676Z\" NotOnOrAfter=\"2012-08-03T10:04:13.676Z\"><saml2:Condition xmlns:rsa=\"http://www.rsa.com/names/2009/12/std-ext/SAML2.0\" Count=\"10\" Postdatable=\"false\" RenewExpired=\"false\" xsi:type=\"rsa:RenewRestrictionType\"/></saml2:Conditions><saml2:AuthnStatement AuthnInstant=\"2012-08-03T09:34:42.725Z\"><saml2:AuthnContext><saml2:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml2:AuthnContextClassRef></saml2:AuthnContext></saml2:AuthnStatement><saml2:AttributeStatement><saml2:Attribute FriendlyName=\"Subject Type\" Name=\"http://rsa.com/schemas/attr-names/2009/01/SubjectType\" NameFormat=\"urn:oasis:names:tc:SAML:2.0:attrname-format:uri\"><saml2:AttributeValue xsi:type=\"xs:anyURI\">http://www.rsa.com/names/2009/12/std-ext/SAML2.0/subjects/user</saml2:AttributeValue></saml2:Attribute><saml2:Attribute FriendlyName=\"Group\" Name=\"http://rsa.com/schemas/attr-names/2009/01/GroupIdentity\" NameFormat=\"urn:oasis:names:tc:SAML:2.0:attrname-format:uri\"><saml2:AttributeValue xsi:type=\"xs:string\">System-Domain\\__LookupServiceAdministrators__</saml2:AttributeValue><saml2:AttributeValue xsi:type=\"xs:string\">localos\vami</saml2:AttributeValue><saml2:AttributeValue xsi:type=\"xs:string\">System-Domain\\__Administrators__</saml2:AttributeValue><saml2:AttributeValue xsi:type=\"xs:string\">localos\\coredump</saml2:AttributeValue><saml2:AttributeValue xsi:type=\"xs:string\">localos\root</saml2:AttributeValue><saml2:AttributeValue xsi:type=\"xs:string\">localos\\shellaccess</saml2:AttributeValue></saml2:Attribute><saml2:Attribute FriendlyName=\"First Name\" Name=\"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname\" NameFormat=\"urn:oasis:names:tc:SAML:2.0:attrname-format:uri\"><saml2:AttributeValue xsi:type=\"xs:string\">root</saml2:AttributeValue></saml2:Attribute><saml2:Attribute FriendlyName=\"isSolution\" Name=\"http://vmware.com/schemas/attr-names/2011/07/isSolution\" NameFormat=\"urn:oasis:names:tc:SAML:2.0:attrname-format:uri\"><saml2:AttributeValue xsi:type=\"xs:string\">false</saml2:AttributeValue></saml2:Attribute></saml2:AttributeStatement></saml2:Assertion>";


    // Login
    $service = VMware_VCloud_SDK_Service::getService();
    $res = $service->SSOLogin($server, $samlAssertionXML, $org, $httpConfig, $sdkversion);
    echo "Vcloud SSO Login Successfully.\n";
    echo "SSO Login Response:\n";
    print_r($res);
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
    echo "     This sample demonstrates logging into VMware vCloud Director.\n";
    echo "\n";
    echo "  [Usage]\n";
    echo "     # php ssologin.php -s <server> -o <organizationname> -v <sdkversion>\n";
    echo "\n";
    echo "     -s|--server <IP|hostname>        [req] IP or hostname of the vCloud Director.\n";
    echo "     -o|--org <organizationname>      [req] organization name.\n";
    echo "                                      for the vCloud Director.\n";
    echo "     -v|--sdkver <sdkversion>         [req] SDK Version e.g. 1.5, 5.1 and 5.5.\n";
    echo "     -c|--certpath <certificatepath>  [opt] Local certificate's full path.\n";
    echo "  [Examples]\n";
    echo "     # php ssologin.php -s 127.0.0.1 -o Org -v 5.5\n";
    echo "     # php ssologin.php -s 127.0.0.1 -o Org -v 5.5 -c certificatepath\n";
    echo "     # php ssologin.php  // using config.php to set login credentials\n\n";
}
?>
