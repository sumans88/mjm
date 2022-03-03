<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function sent_mail($email,$config){
    $CI=& get_instance();
  if(!$config){
      $config = $CI->db->get('email_config')->row_array();
  }
   // Set SMTP Configuration
   // $emailConfig = [
   //     'protocol' => 'smtp', 
   //      // 'mailpath' => '/usr/sbin/sendmail',
   //     'smtp_host' => $config['smtp_host'], 
   //     'smtp_port' => $config['port'], 
   //     'smtp_user' => $config['smtp_user'], 
   //     'smtp_pass' => $config['smtp_pass'], 
   //     // 'smtp_port' => 2525,
   //     'crlf' => "\r\n",
   //     'newline' => "\r\n",
   //     // 'smtp_crypto' =>  'ssl',
   //     'mailtype' => 'html', 
   //     'charset' => 'iso-8859-1'
   // ];
   // Set your email information
   $from = [
       'email' => $config['smtp_user_alias'],
       'name' =>  $config['smtp_user_from']
   ];
  
   $email_to = $email['to'];
   //Load CodeIgniter Email library
   $CI->load->library('email');
   $CI->email->initialize(array(
                      'protocol' => 'smtp',
                      'smtp_host' => $config['smtp_host'],
                      'smtp_user' => $config['smtp_user'],
                      'smtp_pass' => $config['smtp_pass'],
                      'smtp_port' => $config['port'],
                      'smtp_crypto' =>  'ssl',
                      'crlf' => "\r\n",
                      'newline' => "\r\n",
                      'mailtype' => 'html', 
                      'charset' => 'UTF-8'
                    ));
   
   // Sometimes you have to set the new line character for better result
   // $CI->email->set_newline("\r\n");
   // Set email preferences
   $check_delimiter = strpos($email['to'],',');
   if ($check_delimiter) {
      $arr_email_to = array_unique(explode(',', $email['to']));

       if (isset($email['filename']) && isset($email['path_file'])) {    
         $CI->email->attach($email['path_file'].'/'.$email['filename']);
       }
       // print_r($email['path_file'].'/'.$email['filename']);exit;
       // print_r($arr_email_to);exit;
       
      foreach ($arr_email_to as $key => $value) {
       $CI->email->clear();
       $CI->email->from($from['email'], $from['name']);
       $CI->email->to( $value);
       $CI->email->subject($email['subject']);
       $CI->email->message($email['content']);


        $check_status_email_sent  = $CI->email->send();
      }
   }else{
     $CI->email->from($from['email'], $from['name']);
     $CI->email->to($email_to);
     $CI->email->subject($email['subject']);
     $CI->email->message($email['content']);
     
     if (isset($email['filename']) && isset($email['path_file'])) {    
        $CI->email->attach($email['path_file'].'/'.$email['filename']);
     }
    $check_status_email_sent  = $CI->email->send();
    
   }
     // Ready to send email and check whether the email was successfully sent
   if (!$check_status_email_sent) {
       // Raise error message         
      $ret['error'] = 1;
      $ret['message'] = show_error($CI->email->print_debugger());;
      print_r(
        show_error($CI->email->print_debugger())
      );exit;
   } else {
      // Show success notification or other things here
      $ret['error']   = 0;
      $to = is_array($email['to']) ? implode(',',$email['to']) : $email['to'];
      $ret['message'] = "Success send email to $to";
   }


   return $ret;
}

function mail_tpt($code,$replace=array()){
   $CI          = & get_instance();
   $tpt         = $CI->db->get_where('email_tmp',"code = '$code'")->row_array();
   $data['subject']     = $tpt['subject'];
   $data['content']     = $tpt['page_content'];
   foreach($replace as $id=>$val){
      $data['subject']  = str_replace('{'.$id.'}',$val,$data['subject']);
      $data['content']  = str_replace('{'.$id.'}',$val,$data['content']);
   }
   //print_r($data);
   //die();
   return $data;
}
