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

        // writing mails for debug purposes in an simple .txt-file to do not spam around

        // open the file stream
        $filename_and_path = './resources/logs/email_log.txt';
        $file_to_write = fopen($filename_and_path, 'a+');
        // write the desired data to the file
        fwrite($file_to_write, "********************************************************************\n");
        fwrite($file_to_write, 'Datum: ' . date('d-m-y') . ' Uhrzeit: ' . date('H:i:s')  . "\n");
        fwrite($file_to_write, 'An: ' . $to . "\n");
        fwrite($file_to_write, 'Betreff: ' . $subject . "\n");
        fwrite($file_to_write, $message . "\n");
        // close the filestream afterwards
        fclose($file_to_write);

        /*
		if ( ! $this->CI->email->send())
		{
			// error
		}
        */
	}

}