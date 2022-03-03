<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Admin Class
 * @author agung iskandar
 * @version 2.1
 * @category Controller
 * @desc email configuration
 * 
 */
class Email_config extends CI_Controller
{
    //private $error = array();
    private $error = '';

    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Index Page for this controller.
     */
    public function index(){
       $data['data'] = $this->db->get('email_config')->row_array();
        render('apps/email_config/email_config',$data,'apps');
    }
    public function proses(){
        $post = $this->input->post();
        if($post['proses']=='saveemail'){
            unset($post['email'],$post['proses']);
            $this->db->update('email_config',$post);
            $ret['error'] = 0;
            $ret['message'] = 'Update Success';
        }
        else{
            $this->load->helper('mail');
            $email['to'] = $post['email'];
            $email['content'] = 'success sent at '.date('Y/m/d H:i:s');
            $email['subject'] = 'test configuration email';
            $ret = sent_mail($email,$post);
        }
        echo json_encode($ret);
        exit;
    }
}
/* End of file emaiL_config.php */
/* Location: ./application/controllers/admin.php */


