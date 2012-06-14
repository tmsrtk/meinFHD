<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Adminhelper {

	/* creates a random pw with a length of 10 chars - jochens function */
	function passwort_generator() 
	{
	
		$laenge = 10;
		$string = md5((string)mt_rand() . $_SERVER["REMOTE_ADDR"] . time());
		  
		$start = rand(0,strlen($string)-$laenge);
		 
		$password = substr($string, $start, $laenge);
		 
		return md5($password);
	}

}









/* End of file Adminhelper.php */
/* Location: ./application/libraries/Adminhelper.php */