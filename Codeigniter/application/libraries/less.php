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
	public function auto_compile_less($files) {
		// loop through all files in array
		foreach( $files as $file ) {
			// generate proper file names & file paths
			$less_fname = FCPATH . $this->lessDir . $file;
			$css_fname = FCPATH . $this->cssDir  . basename($file, '.less') . '.css';
			
			$less = new lessc($less_fname);
			
			// load the cache
			$cache_fname = $less_fname.".cache";
			if (file_exists($cache_fname)) {
				$cache = unserialize(file_get_contents($cache_fname));
			} else {
				$cache = $less_fname;
				
				// log message if cache file is missing
				log_message('debug', "LESS Compiler: cache file for $file is missing and will be generated" );
			}
			
			// recreate the cache for comparsion
			$new_cache = $this->meinFHD_cexecute($cache);
			if ( !is_array($cache) || $new_cache['updated'] > $cache['updated'] ) {
				file_put_contents( $cache_fname, serialize( $new_cache ) );
				file_put_contents( $css_fname, $new_cache['compiled'] );
				
				// log message about performed compilation
				log_message('debug', 'LESS Compiler: edited LESS files compiled to a new CSS file' );
			}
		}
	}
	
	private function meinFHD_cexecute($in, $force = false) {
		// assume no root
		$root = null;

		if (is_string($in)) {
			$root = $in;
		} elseif (is_array($in) and isset($in['root'])) {
			if ($force or ! isset($in['files'])) {
				// If we are forcing a recompile or if for some reason the
				// structure does not contain any file information we should
				// specify the root to trigger a rebuild.
				$root = $in['root'];
			} elseif (isset($in['files']) and is_array($in['files'])) {
				foreach ($in['files'] as $fname => $ftime ) {
					if (!file_exists($fname) or filemtime($fname) > $ftime) {
						// One of the files we knew about previously has changed
						// so we should look at our incoming root again.
						$root = $in['root'];
						break;
					}
				}
			}
		} else {
			// TODO: Throw an exception? We got neither a string nor something
			// that looks like a compatible lessphp cache structure.
			return null;
		}
		
		if ($root !== null) {
			// custom formatter for minification
			$tabIndent = new lessc_formatter;
			$tabIndent->indentChar = "\t";
			
			// If we have a root value which means we should rebuild.
			$less = new lessc($root);
			
			// set our custom formatter
			$less->setFormatter('compressed');
			
			$out = array();
			$out['root'] = $root;
			$out['compiled'] = $less->parse();
			$out['files'] = $less->allParsedFiles();
			$out['updated'] = time();
			return $out;
		} else {
			// No changes, pass back the structure
			// we were given initially.
			return $in;
		}
		
	}
}

/* End of file Less.php */