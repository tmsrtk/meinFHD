<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Peter Jaraczewski (PJ), <peter.jaraczewski@fh-duesseldorf.de>
 */

/**
 * Less Library
 *
 * This library is a wrapper class for lessphp
 * see github for further info: https://github.com/leafo/lessphps
 */
class Less {
	
	// setup paths to LESS and CSS directory
	private $lessDir = 'resources/less/';
	private $cssDir = 'resources/css/';
	
	/*
	 * Default constructor
	 */
	public function __construct() {
		// load lessphp file if it exists
		@require_once( 'lessphp/lessc.inc.php' );
	}
	
	/*
	 * Autocompiles given files from LESS to CSS
	 */
	public function auto_compile_less($lessFiles, $cssFiles) {
		// reset array index
		$i = 0;
		
		// loop through all files in array
		foreach( $lessFiles as $less_fname ) {
			
			// correct path for less_fname
			$less_fname = FCPATH . $this->lessDir . $less_fname;
			
			// load the cache
			$cache_fname = $less_fname.".cache";
			
			if (file_exists($cache_fname)) {
				$cache = unserialize(file_get_contents($cache_fname));
			} else {
				$cache = $less_fname;
			}
			
			// recreate cache
			$new_cache = lessc::cexecute($cache);
			if ( !is_array( $cache ) || $new_cache['updated'] > $cache['updated'] ) {
				file_put_contents( $cache_fname, serialize( $new_cache ) );
				file_put_contents( FCPATH . $this->cssDir . $cssFiles[$i], $new_cache['compiled']);
				
				// log message about performed compilation
				log_message('debug', 'edited LESS files compiled to a new CSS file' );
			}
			
			// increment array index
			$i++;
		}
	}
}

/* End of file Less.php */