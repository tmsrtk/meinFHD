<?php

$config = array(

    // This is a authentication source which handles admin authentication.
    'admin' => array(
        // The default is to use core:AdminPassword, but it can be replaced with
        // any authentication source.

        'core:AdminPassword',
    ),


    // Authsource for the Shibboleth / SAML Identity Provider of the FH Duesseldorf
    'meinfhd-sp' => array(
        'saml:SP',

        // The entity ID of this SP.
        // Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
        'entityID' => 'https://meinfhd.medien.fh-duesseldorf.de/',

        // The entity ID of the IdP this should SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => 'https://idp.fh-duesseldorf.de/idp/shibboleth',

        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => NULL,

        // Enabling security certificate
        'privatekey'    => 'meinfhd_key-n.pem',
        'certificate'   => 'meinfhd_cert.pem',

    ),
);
