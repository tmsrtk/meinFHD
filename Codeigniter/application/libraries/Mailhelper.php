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

		$this->CI->email->subject('[meinFHD] ' . $subject);
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

    /**
     * Sends an meinFHD Mail to multiple recipients at one time. Therefore the list of recipients needs to be passed
     * as an simple 1-dimensional array. The recipients will be put into the 'bcc'-field, so that they are
     * not visible for any person who recieves the mail.
     *
     * @access public
     * @param array $to Simple 1-dimensional array with all recipients
     * @param string $subject The subject of the email
     * @param string $message The message of the email
     * @return void
     */
    public function send_meinfhd_to_multiple_recipients($to = array(), $subject, $message){

        // generate recipient string
        $email_recipients = '';
        foreach($to as $single_recipient){
            $email_recipients .= $single_recipient . '; ';
        }

        $this->CI->email->from('efhade@gmx.de', 'meinFHD2 E-Mail - Service');
        $this->CI->email->bcc($email_recipients);
        $this->CI->email->subject('[meinFHD] ' . $subject);
        $this->CI->email->message($message);

        // writing mails for debug purposes in an simple .txt-file to do not spam around

        // open the file stream
        $filename_and_path = './resources/logs/email_log.txt';
        $file_to_write = fopen($filename_and_path, 'a+');
        // write the desired data to the file
        fwrite($file_to_write, "********************************************************************\n");
        fwrite($file_to_write, 'Datum: ' . date('d-m-y') . ' Uhrzeit: ' . date('H:i:s')  . "\n");
        fwrite($file_to_write, 'An: ' . $email_recipients . "\n");
        fwrite($file_to_write, 'Betreff: [meinFHD] ' . $subject . "\n");
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