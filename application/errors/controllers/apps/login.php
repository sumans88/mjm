<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->layout = 'none';
		
	}
    function index(){
        //method untuk mengambil segmentasi pada URL pada saat error
        $err            = $this->uri->segment(4);
        //notif error
        $error_login    = get_flash_session('error_login') != '' ? get_flash_session('error_login') : '';
        //data array untuk menampung password dan error login
        $data           = array('base_url' => base_url(), 'login'=>'', 'password'=>'', 'error_login'=>$error_login);
        //notif function error yang di hide
	    $data['error_login_hide']    = get_flash_session('error_login') != '' ? '' : 'hide';
        //load view dan data 
        render('login',$data,'blank');
    }
    function cek_login(){
        //load mode Auth_model
        $this->load->model('Auth_model');
        //input username dan password untuk dibawa ke model Auth_model method check_login
        $this->Auth_model->check_login($this->input->post('username'),$this->input->post('password'));
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