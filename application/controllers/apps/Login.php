<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->layout = 'none';
		
	}
    function index(){
        //method untuk mengambil segmentasi pada URL pada saat error
        $err            = $this->uri->segment(4);
        $this->data['recaptcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
        //notif error
        $error_login    = $this->session->flashdata('error_login') != '' ? $this->session->flashdata('error_login') : '';
        //data array untuk menampung password dan error login
        $data           = array('base_url' => base_url(), 'login'=>'', 'password'=>'', 'error_login'=>$error_login);
        //notif function error yang di hide
	    $data['error_login_hide']    = $this->session->flashdata('error_login') != '' ? '' : 'hide';
        //load view dan data 
        render('login',$data,'blank');
    }
    function cek_login(){
        //load mode Auth_model
        /*$this->load->model('Auth_model');*/
        //input username dan password untuk dibawa ke model Auth_model method check_login
        /*$this->Auth_model->check_login($this->input->post('username'),$this->input->post('password'));*/
       $post                   = purify(null_empty($this->input->post()));
       
       $action = $_POST['action'];
       $response = $_POST['g-recaptcha-response'];
          $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => GOOGLE_CAPTCHA_SECRET_KEY, 'response' => $response)));
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       $Response = curl_exec($ch);
       curl_close($ch);
       $arrResponse = json_decode($Response, true);
       if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
            $this->load->model('auth_model');
            unset($post['g-recaptcha-response']);
            $this->auth_model->check_login($post['username'],$post['password']);
       } else {
            $this->session->set_flashdata('error_login','Sorry, Please Try Again');
            redirect('apps/login');
       }

    }

    function logout(){
        
        $data['ip'] 		    = $_SERVER['REMOTE_ADDR'];
    	$data['activity']       = "Logout";
    	$data['id_auth_user']   = $this->data['id_auth_user'];
    	$data['log_date'] =  date('Y-m-d H:i:s');
        $this->db->insert('access_log',$data);
        $this->session->sess_destroy();
        $this->load->model('LoginTransactionModel');
        $this->LoginTransactionModel->update($data['id_auth_user'],array('lock_date'=>$data['log_date'],'is_active'=>2),array('ip_address'=>$data['ip']));
        redirect('apps/login');
    }
	
	function login_trouble(){
		$data = array('base_url' => base_url());
		$this->parser->parse('login_trouble.html', $data);
	}
}