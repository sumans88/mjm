<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * helper sent mail using smpt or direct sent
 * @author Agung Iskandar
 * @param $conf
 *   An associative array containing:
 *   - subject: judul email.
 *   - content: isi email.
 *   - to: email to.
 *   - to_name: (optional) nama kepada nya ex agung@deptechdigital, agung iskandar.
 *	  - from: (optional) default from config.
 *	  - from_name:(optional) default from config.
 *	  @example
 *	   $this->load->helper('mail');
		$conf['subject'] 	= 'test sent mail from helper using smtp';
		$conf['content'] 	= '<b> isi email</b><br><p>lorem ipsum dolor sit amet</p>';
		$conf['to'] 		= 'agung_doanks@yahoo.co.id';
		$conf['from'] 		= 'agung_doanks@yahoo.co.id';
		$conf['from_name']= 'agg';
		sent_mail($conf);
 */
function sent_mail($conf){
	 $CI = & get_instance();
	 $CI->load->helper('email');
	 $error = 'sent mail configuration error : ';
	 if(!$conf['subject']){
		  die("$error subject is empty!");
	 }
	 else if(!$conf['content']){
		  die("$error content is empty!");
	 }
	 else if(!$conf['to']){
		  die("$error to is empty!");
	 }	 
	 else if (!valid_email($conf['to'])){
		  die("$error $conf[to] is not valid email!");
	 }
	 
	 $path = str_replace("system/","application/helpers/",BASEPATH);
	 require_once $path.'mail/class.phpmailer.php';
	 //$config				= $CI->db->get("ref_email_config")->row();
	 //$ssl				= ($config['ssl'] == 'y') ? 'ssl://' : '';
	 $nama_pengirim 	= ($conf['from_name']) ? $conf['from_name'] : $config->name;
	 $email_pengirim 	= ($conf['from'])  ? $conf['from']  : $config->email;
	 
	 //if($config->type=='SMTP'){
		  //error_reporting(E_STRICT);
		  date_default_timezone_set('Asia/Jakarta');
		  $mail             = new PHPMailer();
		  $mail->IsSMTP(); // telling the class to use SMTP
		  $mail->Host       = 'smtp.gmail.com';//$config->smtp_server; // SMTP server
		  $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing) // 1 = errors and messages// 2 = messages only
		  $mail->SMTPAuth   = true;                  // enable SMTP authentication
		  $mail->Port       = 587;         //587           // set the SMTP port for the GMAIL server
		  $mail->Username   = 'appsmail2@gmail.com';//$config->email ; // SMTP account username
		  $mail->Password   = '1qaZXsw2!';//$config->password;     // SMTP account password
		  //$mail->SMTPSecure = ($config->ssl == 'y') ? "tls" : '';
		  $mail->SMTPSecure = "tls";
		  
		  $mail->SetFrom($email_pengirim,$nama_pengirim); //buat replynya
		  //$mail->AddReplyTo('agungiskandar@gmail.com','agungs');
		  $mail->Subject    = $conf['subject'];
		  $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		  $mail->MsgHTML($conf['content']);
		  $mail->AddAddress($conf['to'], $conf['to_name']);
		  
		  if(!$mail->Send()) {
				//echo "Error|" . $mail->ErrorInfo;
				exit;
		  }
	 //}
	 //else {
		  //die('under construction');
		//$config['protocol'] = 'sendmail';
		//$config['mailpath'] = '/usr/sbin/sendmail';
		//$config['charset'] = 'iso-8859-1';
		//$config['wordwrap'] = TRUE;
		//$config['mailtype'] = 'html';
		//$CI->load->library('email');
		//$CI->email->initialize($config);
		//$CI->email->from($from, 'Administrator CRM Axindo');
		//$CI->email->to($recipients);
		//$CI->email->subject($subject);
		//$CI->email->message($content_message);
		//$CI->email->send();
		//echo $CI->email->print_debugger();
	  //}
}
