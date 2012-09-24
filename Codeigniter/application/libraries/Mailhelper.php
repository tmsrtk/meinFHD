<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * Helper class to send meinFHD2 E-Mails
 * 
 * 
 */
class Mailhelper {

	private $CI;

	public function __construct()
	{
		$this->CI =& get_instance();

		// Der E-Mail Versand (Ein Empfänger)
		$this->CI->load->library('email');
	}

	/**
	 * Sends a meinFHD2 Mail
	 *
	 * @param $to
	 * @param $subject
	 * @param $message
	 */
	public function send_meinfhd_mail($to, $subject, $message)
	{
		$this->CI->email->from('efhade@gmx.de', 'meinFHD2 E-Mail - Service');
		$this->CI->email->to($to);

		$this->CI->email->subject($subject);
		$this->CI->email->message($message);

		if ( ! $this->CI->email->send())
		{
			// error
		}
	}

}