<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * SAML Authentication Library
 *
 * This library is a wrapper to integrate simpleSAMLphp into the CI-Framework.
 * Moreover it supplies methods to realize the authentication via an identity provider.
 */

class Samlauthentication {

    private $authSource;

    /**
     * default constructor: loads the library and initializes the authentication source
     */
    public function __construct() {
        // load the simplesamlphp library if it exists
        @require_once('simplesamlphp/lib/_autoload.php');

        // select and create the authentication source
        $this->authSource = new SimpleSAML_Auth_Simple('default-sp');
    }


}
/* End of file Samlauthentication.php */