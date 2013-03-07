<?php
/**
 * SAML 2.0 remote IdP metadata for simpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://rnd.feide.no/content/idp-remote-metadata-reference
 */

/*
 * Metadata for FH-Duesseldorf Shibboleth-IdentityProvider
 */
$metadata['https://idp.fh-duesseldorf.de/idp/shibboleth'] = array (
    'entityid' => 'https://idp.fh-duesseldorf.de/idp/shibboleth',
    'name' =>
    array (
        'de' => 'Fachhochschule Düsseldorf',
    ),
    'description' =>
    array (
        'de' => 'Fachhochschule Düsseldorf',
    ),
    'OrganizationName' =>
    array (
        'de' => 'Fachhochschule Düsseldorf',
    ),
    'OrganizationDisplayName' =>
    array (
        'de' => 'Fachhochschule Düsseldorf',
    ),
    'url' =>
    array (
        'de' => 'http://www.fh-duesseldorf.de',
    ),
    'OrganizationURL' =>
    array (
        'de' => 'http://www.fh-duesseldorf.de',
    ),
    'contacts' =>
    array (
        0 =>
        array (
            'contactType' => 'technical',
            'givenName' => 'Roland',
            'surName' => 'Conradshaus',
            'emailAddress' =>
            array (
                0 => 'roland.conradshaus@fh-duesseldorf.de',
            ),
        ),
        1 =>
        array (
            'contactType' => 'administrative',
            'givenName' => 'Roland',
            'surName' => 'Conradshaus',
            'emailAddress' =>
            array (
                0 => 'roland.conradshaus@fh-duesseldorf.de',
            ),
        ),
    ),
    'metadata-set' => 'saml20-idp-remote',
    'expire' => 1362063708,
    'SingleSignOnService' =>
    array (
        0 =>
        array (
            'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
            'Location' => 'https://idp.fh-duesseldorf.de/idp/profile/Shibboleth/SSO',
        ),
        1 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'Location' => 'https://idp.fh-duesseldorf.de/idp/profile/SAML2/POST/SSO',
        ),
        2 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
            'Location' => 'https://idp.fh-duesseldorf.de/idp/profile/SAML2/POST-SimpleSign/SSO',
        ),
        3 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'Location' => 'https://idp.fh-duesseldorf.de/idp/profile/SAML2/Redirect/SSO',
        ),
    ),
    'SingleLogoutService' =>
    array (
    ),
    'ArtifactResolutionService' =>
    array (
        0 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:1.0:bindings:SOAP-binding',
            'Location' => 'https://idp.fh-duesseldorf.de:8443/idp/profile/SAML1/SOAP/ArtifactResolution',
            'index' => 1,
        ),
        1 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
            'Location' => 'https://idp.fh-duesseldorf.de:8443/idp/profile/SAML2/SOAP/ArtifactResolution',
            'index' => 2,
        ),
    ),
    'keys' =>
    array (
        0 =>
        array (
            'encryption' => true,
            'signing' => false,
            'type' => 'X509Certificate',
            'X509Certificate' => '
MIIFezCCBGOgAwIBAgIEEU5FUzANBgkqhkiG9w0BAQUFADCBjzELMAkGA1UEBhMC REUxFzAVBgNVBAoTDkZIIER1ZXNzZWxkb3JmMQwwCgYDVQQLEwNEVloxMzAxBgNV BAMTKlplcnRpZml6aWVydW5nc3N0ZWxsZSBGSCBEdWVzc2VsZG9yZiAtIEcwMjEk MCIGCSqGSIb3DQEJARYVcGtpQGZoLWR1ZXNzZWxkb3JmLmRlMB4XDTExMDExMzA5 MzcwN1oXDTE0MDExMjA5MzcwN1owYzELMAkGA1UEBhMCREUxIzAhBgNVBAoTGkZh Y2hob2Noc2NodWxlIER1ZXNzZWxkb3JmMQ8wDQYDVQQLEwZTZXJ2ZXIxHjAcBgNV BAMTFWlkcC5maC1kdWVzc2VsZG9yZi5kZTCCASIwDQYJKoZIhvcNAQEBBQADggEP ADCCAQoCggEBAKHr4GsKk5j+5SafTR3aNu962v7Ys5rrGUBABB8jU1MQZ+kN0fpo FzSlpfNvwgIkDWAKj3Un9Qq0il5c0W10jCSOIPdkV2HqEsiJjKlj2UEH2CEtgS5/ VLRg+K54sdnBg5yfeskBxcm02TXx8OP2z7MdRxqgQkiVoBynaMecJc0vHj3G6/bb C6G10La0A0BJhOahIIrRaKSg2LvLJ6rZY+9dd81BtLOnku0jd1xHlqyYr6QASg9s Bt64WgTpA3+BWp6tsLvOhM4h1ndXiRKSz4qTtjBLfTTtifxqCv0e2L3K50+B4x59 kPfgItloZaT8i8CuplxDPP3s5Pen34kLspUCAwEAAaOCAggwggIEMAkGA1UdEwQC MAAwCwYDVR0PBAQDAgXgMDQGA1UdJQQtMCsGCCsGAQUFBwMCBggrBgEFBQcDAQYK KwYBBAGCNwoDAwYJYIZIAYb4QgQBMB0GA1UdDgQWBBQTTDWstp1ovgrktfT1xy9P T//XuzAfBgNVHSMEGDAWgBSe8wPRIOch6nFIXRFDBlV4+cgO5jA7BgNVHREENDAy ghVpZHAuZmgtZHVlc3NlbGRvcmYuZGWCGXd3dy5pZHAuZmgtZHVlc3NlbGRvcmYu ZGUwgZMGA1UdHwSBizCBiDBCoECgPoY8aHR0cDovL2NkcDEucGNhLmRmbi5kZS9m aC1kdWVzc2VsZG9yZi1jYS9wdWIvY3JsL2dfY2FjcmwuY3JsMEKgQKA+hjxodHRw Oi8vY2RwMi5wY2EuZGZuLmRlL2ZoLWR1ZXNzZWxkb3JmLWNhL3B1Yi9jcmwvZ19j YWNybC5jcmwwgaAGCCsGAQUFBwEBBIGTMIGQMEYGCCsGAQUFBzAChjpodHRwOi8v Y2RwMS5wY2EuZGZuLmRlL2ZoLWR1ZXNzZWxkb3JmLWNhL3B1Yi9jYS9jYWNlcnQu Y3J0MEYGCCsGAQUFBzAChjpodHRwOi8vY2RwMi5wY2EuZGZuLmRlL2ZoLWR1ZXNz ZWxkb3JmLWNhL3B1Yi9jYS9jYWNlcnQuY3J0MA0GCSqGSIb3DQEBBQUAA4IBAQAY q0VkPw7f9LSdKWZ1pQY8OvNc9cPbd1ObnpdaWDzNmNWaOoidVPCWU/9JKUos/spL VuYibTPDhc8TdcvKNRTL7ti3u7wv5U+buIsIdYPh/uf5+kjTkqD6dSa/PIDUqfKW 8woY63VWdkwCQJhSVFBylC9x22ggqn3Hf+hizrRbRHoqvaP+270IZjJbjHc/0KtF 7MmJYA0DLvjPjhK/lYYhv9INU38caM2y4Cqe9p+sPeBJyYPsQAhK8/42Dg9bvKr4 PrjgvyPNxkU9mdhcGp8RlSt66Di+Yxh39FU5INx1MBzIisFaGmz1aVSnjYyhiPlO HAfOyhCqg1OPKRSVyFh7
',
        ),
        1 =>
        array (
            'encryption' => false,
            'signing' => true,
            'type' => 'X509Certificate',
            'X509Certificate' => '
MIIFezCCBGOgAwIBAgIEEU5FUzANBgkqhkiG9w0BAQUFADCBjzELMAkGA1UEBhMC REUxFzAVBgNVBAoTDkZIIER1ZXNzZWxkb3JmMQwwCgYDVQQLEwNEVloxMzAxBgNV BAMTKlplcnRpZml6aWVydW5nc3N0ZWxsZSBGSCBEdWVzc2VsZG9yZiAtIEcwMjEk MCIGCSqGSIb3DQEJARYVcGtpQGZoLWR1ZXNzZWxkb3JmLmRlMB4XDTExMDExMzA5 MzcwN1oXDTE0MDExMjA5MzcwN1owYzELMAkGA1UEBhMCREUxIzAhBgNVBAoTGkZh Y2hob2Noc2NodWxlIER1ZXNzZWxkb3JmMQ8wDQYDVQQLEwZTZXJ2ZXIxHjAcBgNV BAMTFWlkcC5maC1kdWVzc2VsZG9yZi5kZTCCASIwDQYJKoZIhvcNAQEBBQADggEP ADCCAQoCggEBAKHr4GsKk5j+5SafTR3aNu962v7Ys5rrGUBABB8jU1MQZ+kN0fpo FzSlpfNvwgIkDWAKj3Un9Qq0il5c0W10jCSOIPdkV2HqEsiJjKlj2UEH2CEtgS5/ VLRg+K54sdnBg5yfeskBxcm02TXx8OP2z7MdRxqgQkiVoBynaMecJc0vHj3G6/bb C6G10La0A0BJhOahIIrRaKSg2LvLJ6rZY+9dd81BtLOnku0jd1xHlqyYr6QASg9s Bt64WgTpA3+BWp6tsLvOhM4h1ndXiRKSz4qTtjBLfTTtifxqCv0e2L3K50+B4x59 kPfgItloZaT8i8CuplxDPP3s5Pen34kLspUCAwEAAaOCAggwggIEMAkGA1UdEwQC MAAwCwYDVR0PBAQDAgXgMDQGA1UdJQQtMCsGCCsGAQUFBwMCBggrBgEFBQcDAQYK KwYBBAGCNwoDAwYJYIZIAYb4QgQBMB0GA1UdDgQWBBQTTDWstp1ovgrktfT1xy9P T//XuzAfBgNVHSMEGDAWgBSe8wPRIOch6nFIXRFDBlV4+cgO5jA7BgNVHREENDAy ghVpZHAuZmgtZHVlc3NlbGRvcmYuZGWCGXd3dy5pZHAuZmgtZHVlc3NlbGRvcmYu ZGUwgZMGA1UdHwSBizCBiDBCoECgPoY8aHR0cDovL2NkcDEucGNhLmRmbi5kZS9m aC1kdWVzc2VsZG9yZi1jYS9wdWIvY3JsL2dfY2FjcmwuY3JsMEKgQKA+hjxodHRw Oi8vY2RwMi5wY2EuZGZuLmRlL2ZoLWR1ZXNzZWxkb3JmLWNhL3B1Yi9jcmwvZ19j YWNybC5jcmwwgaAGCCsGAQUFBwEBBIGTMIGQMEYGCCsGAQUFBzAChjpodHRwOi8v Y2RwMS5wY2EuZGZuLmRlL2ZoLWR1ZXNzZWxkb3JmLWNhL3B1Yi9jYS9jYWNlcnQu Y3J0MEYGCCsGAQUFBzAChjpodHRwOi8vY2RwMi5wY2EuZGZuLmRlL2ZoLWR1ZXNz ZWxkb3JmLWNhL3B1Yi9jYS9jYWNlcnQuY3J0MA0GCSqGSIb3DQEBBQUAA4IBAQAY q0VkPw7f9LSdKWZ1pQY8OvNc9cPbd1ObnpdaWDzNmNWaOoidVPCWU/9JKUos/spL VuYibTPDhc8TdcvKNRTL7ti3u7wv5U+buIsIdYPh/uf5+kjTkqD6dSa/PIDUqfKW 8woY63VWdkwCQJhSVFBylC9x22ggqn3Hf+hizrRbRHoqvaP+270IZjJbjHc/0KtF 7MmJYA0DLvjPjhK/lYYhv9INU38caM2y4Cqe9p+sPeBJyYPsQAhK8/42Dg9bvKr4 PrjgvyPNxkU9mdhcGp8RlSt66Di+Yxh39FU5INx1MBzIisFaGmz1aVSnjYyhiPlO HAfOyhCqg1OPKRSVyFh7
',
        ),
    ),
    'scope' =>
    array (
        0 => 'fh-duesseldorf.de',
    ),
    'certFingerprint' => '54:C8:66:C1:2C:F5:64:0A:2A:0C:38:06:EC:03:17:16:A1:08:EE:31',

    /* Enable the authproc filter to remove oid and urn prefixes from all attributes to have cleartext attributnames
      * For further information of the attribute names see /attributemap/oid2name.php.
      */
    'authproc' => array(
        10 => array(
            'class' => 'core:AttributeMap', 'oid2name'
        ),
    ),
);