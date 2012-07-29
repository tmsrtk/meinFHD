<?php
/**
 * SAML 2.0 remote IdP metadata for simpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://rnd.feide.no/content/idp-remote-metadata-reference
 */

/*
 * Metadata for local Ubuntu test shibboleth-server
 */
$metadata['https://idp.example.org/idp/shibboleth'] = array (
    'name' => 'FH-Duesseldorf local IdP',
    'entityid' => 'https://idp.example.org/idp/shibboleth',
    'contacts' =>
    array (
    ),
    'metadata-set' => 'saml20-idp-remote',
    'SingleSignOnService' =>
    array (
        0 =>
        array (
            'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
            'Location' => 'https://idp.example.org/idp/profile/Shibboleth/SSO',
        ),
        1 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'Location' => 'https://idp.example.org/idp/profile/SAML2/POST/SSO',
        ),
        2 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
            'Location' => 'https://idp.example.org/idp/profile/SAML2/POST-SimpleSign/SSO',
        ),
        3 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'Location' => 'https://idp.example.org/idp/profile/SAML2/Redirect/SSO',
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
            'Location' => 'https://idp.example.org:8443/idp/profile/SAML1/SOAP/ArtifactResolution',
            'index' => 1,
        ),
        1 =>
        array (
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
            'Location' => 'https://idp.example.org:8443/idp/profile/SAML2/SOAP/ArtifactResolution',
            'index' => 2,
        ),
    ),
    'keys' =>
    array (
        0 =>
        array (
            'encryption' => true,
            'signing' => true,
            'type' => 'X509Certificate',
            'X509Certificate' => '
MIIDJzCCAg+gAwIBAgIUdgbUVBymVAsqEZOb6MqW5Xi1L9kwDQYJKoZIhvcNAQEF
BQAwGjEYMBYGA1UEAxMPaWRwLmV4YW1wbGUub3JnMB4XDTEyMDcyODEzMjMzM1oX
DTMyMDcyODEzMjMzM1owGjEYMBYGA1UEAxMPaWRwLmV4YW1wbGUub3JnMIIBIjAN
BgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqBUNZTOiHLCJoQJy+mCvcpZEJAbK
8kbXx6kJIGidw8nQDq5G+fRY1js0tfVwM0qKcUhCiV/ybgO1SNCdYydS/MtcCaaK
monm1atG8GV1yuX/Cwa71TkZVkWoqBRcFpowi1ag/pdjdpZ0XPNX5OIPhaJBPQmi
jY9F91bW3EG76Ljl1VAk0EKCFscm0Vtg9L1oc1bzOZ6rHlXXxUlWRdWhu9eQ/w6+
QlPmtuDA4f/h5LI8rpBs3Vzfy5E0FJxLBSstcD7sT3f+b/Ul8RuHYbpvsd42od5Z
jTcdpTPoErMKbEoljKvBc68SnZpH9t46a2gVjfplRoV4/0IZRcv8AwGqRwIDAQAB
o2UwYzBCBgNVHREEOzA5gg9pZHAuZXhhbXBsZS5vcmeGJmh0dHBzOi8vaWRwLmV4
YW1wbGUub3JnL2lkcC9zaGliYm9sZXRoMB0GA1UdDgQWBBQ2Wx3VHq+ZJBESluo3
pTPUn1WMjDANBgkqhkiG9w0BAQUFAAOCAQEAmRmQTCNHsOrJjayn26nAzaiC5r4k
pHjbct3XeqKOceH9CXg3f0IPLkAdRBhf8UHvDa7fG+5YZTXRECO4v90IiqCWKmKl
OesFPUq9xcfZf6wur0gyAecLMnWmwARUxwExqqOTtPLlmgcFYAH+g82Kms9iuq7S
+PDVDKMLo0TJXbI6mLI31PH2Srlh13gwFBuWSznkxJTKEFvcjpiSz6JCYPsVuOUF
iLC5QkJG1V/W/8slZ/DPwDrL+g6PKvbXHrg6x1HmcY8BLV/hDioQGBA/qmd0xdsA
7SS45G+RiB+hrVGOi00xQGiXj/DG66/3f9cBWgDeotI/ErtI6dgpHFphfQ==

                    ',
        ),
    ),
    'scope' =>
    array (
        0 => 'example.org',
    ),
);


/*
 * Guest IdP. allows users to sign up and register. Great for testing!
 */
$metadata['https://openidp.feide.no'] = array(
	'name' => array(
		'en' => 'Feide OpenIdP - guest users',
		'no' => 'Feide Gjestebrukere',
	),
	'description'          => 'Here you can login with your account on Feide RnD OpenID. If you do not already have an account on this identity provider, you can create a new one by following the create new account link and follow the instructions.',

	'SingleSignOnService'  => 'https://openidp.feide.no/simplesaml/saml2/idp/SSOService.php',
	'SingleLogoutService'  => 'https://openidp.feide.no/simplesaml/saml2/idp/SingleLogoutService.php',
	'certFingerprint'      => 'c9ed4dfb07caf13fc21e0fec1572047eb8a7a4cb'
);


/*
 * Feide, the norwegian federation. Test and production metadata.
 */
$metadata['https://idp-test.feide.no'] = array(
	'name' => array(
		'en' => 'Feide Test environment',
		'no' => 'Feide testmiljø',
	),
	'description'                  => 'Feide test environment (idp-test.feide.no). Authenticate with your identity from a school or university in Norway.',

	'SingleSignOnService'          => 'https://idp-test.feide.no/simplesaml/saml2/idp/SSOService.php',
	'SingleLogoutService'          => 'https://idp-test.feide.no/simplesaml/saml2/idp/SingleLogoutService.php',

	'certFingerprint'              => 'fa982efdb69f26e8073c8f815a82a0c5885960a2',
	'hint.cidr'                    => '158.38.0.0/16',
);

$metadata['https://idp.feide.no'] = array(
	'name' => 'Feide',
	'description' => array(
		'en' => 'Authenticate with your identity from a school or university in Norway.',
		'no' => 'Logg inn med din identitet fra skolen eller universitetet du er tilknyttet (i Norge).',
	),
	'SingleSignOnService'          => 'https://idp.feide.no/simplesaml/saml2/idp/SSOService.php',
	'SingleLogoutService'          => 'https://idp.feide.no/simplesaml/saml2/idp/SingleLogoutService.php',
	'certFingerprint'              => 'cde69e332fa7dd0eaa99ee0ddf06916e8942ac53',
	'hint.cidr'                    => '158.38.0.0/16',
);



/*
 * Wayf, the danish federation metadata.
 */
$metadata['https://wayf.wayf.dk'] = array(
	'name' => array(
		'en' => 'DK-WAYF Production server',
		'da' => 'DK-WAYF Produktionsmiljøet',
	),
	'description'          => 'Login with your identity from a danish school, university or library.',
	'SingleSignOnService'  => 'https://wayf.wayf.dk/saml2/idp/SSOService.php',
	'SingleLogoutService'  => 'https://wayf.wayf.dk/saml2/idp/SingleLogoutService.php',
	'certFingerprint'      => 'c215d7bf9d51c7805055239f66b957d9a72ff44b'
);

$metadata['https://betawayf.wayf.dk'] = array(
	'name' => array(
		'en' => 'DK-WAYF Quality Assurance',
		'da' => 'DK-WAYF Quality Assurance miljøet',
	),
	'description'          => 'Login with your identity from a danish school, university or library.',
	'SingleSignOnService'  => 'https://betawayf.wayf.dk/saml2/idp/SSOService.php',
	'SingleLogoutService'  => 'https://betawayf.wayf.dk/saml2/idp/SingleLogoutService.php',
	'certFingerprint'      => 'c215d7bf9d51c7805055239f66b957d9a72ff44b'
);

$metadata['https://testidp.wayf.dk'] = array(
	'name' => array(
		'en' => 'DK-WAYF Test Server',
		'da' => 'DK-WAYF Test Miljøet',
	),
	'description'          => 'Login with your identity from a danish school, university or library.',
	'SingleSignOnService'  => 'https://testidp.wayf.dk/saml2/idp/SSOService.php',
	'SingleLogoutService'  => 'https://testidp.wayf.dk/saml2/idp/SingleLogoutService.php',
	'certFingerprint'      => '04b3b08bce004c27458b3e85b125273e67ef062b'
);
