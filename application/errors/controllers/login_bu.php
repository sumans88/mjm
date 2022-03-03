<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('loginmodel');
	}
    
    function resend_email_activation(){
    	$post = purify($this->input->post());
    	if($post){
    		$this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
			if ($this->form_validation->run() == FALSE){
				 $message = validation_errors();
				 $status = 'error';
			}
			else{
                $email = $post['email'];
	    		$proses = $this->loginmodel->resend_email_activation($post);
	    		if($proses['status']==1){
                    $status = 'success';
	    			$message = $proses['message'];
	    		}
	    		else{
	    			$status = 'Terjadi Kesalahan';
	    			$message = $proses['message'];	
	    		}
			}
	        $data['message'] 	=  "<div class='$status'> $message</div>";
	        $data['status'] 	=  $status;
	        echo json_encode($data);

    	}
    	else{
	        $data['fb_like_widget'] = fb_like_widget();
	        $data['ads_widget']     = ads_widget();
	        render('layout/ddi/member/resend_email_activation',$data);
    	}
    }
    function forgot_password(){
    	$post = purify($this->input->post());
    	if($post){
    		$this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
			if ($this->form_validation->run() == FALSE){
				 $message = validation_errors();
				 $status = 'error';
			}
			else{
			$email = $post['email'];
	    		$proses = $this->loginmodel->reset_password($post);
	    		if($proses['status']==1){
				$status = 'success';
	    			$message = $proses['message'];
	    		}
	    		else{
	    			$status = 'Terjadi Kesalahan';
	    			$message = $proses['message'];	
	    		}
			}
	        $data['message'] 	=  "<div class='$status'> $message</div>";
	        $data['status'] 	=  $status;
	        echo json_encode($data);

    	}
    	else{
	        $data['fb_like_widget'] = fb_like_widget();
	        $data['ads_widget']     = ads_widget();
	        render('layout/ddi/member/reset_password',$data);
    	}
    }
    function reset_password_code($activation_code){
        $post = purify($this->input->post());
	$process = $this->loginmodel->check_reset_due($activation_code);
    	if($process['status']==1){
            if($post){
                $this->form_validation->set_rules('pwd', '"Password"', 'required'); 
                if ($this->form_validation->run() == FALSE){
                     $message = validation_errors();
                     $status = 'error';
                }
                else{
                    $reset_process = $this->loginmodel->reset_password_code($post);
                    if($reset_process['status']==1){
                        $status = 'success';
                        $message = $reset_process['message'];
                    }else{
                        $status = 'Terjadi Kesalahan';
                        $message = $reset_process['message'];	
                    }
                }
                $data['message'] 	=  "<div class='$status'> $message</div>";
                $data['status'] 	=  $status;
                echo json_encode($data);
            } else {
                $data['reset_code'] = $activation_code;
                $data['fb_like_widget'] = fb_like_widget();
                $data['ads_widget']     = ads_widget();
                render('layout/ddi/member/reset_password_code',$data);
            }
        } else {
            die($process['message']);
        }
		
	}
    function login_process(){
        $post = purify($this->input->post());
    	if($post){
    		$this->form_validation->set_rules('modal_login_form_username', '"Username"', 'required');
            $this->form_validation->set_rules('modal_login_form_password', '"Password"', 'required'); 
			if ($this->form_validation->run() == FALSE){
				 $message = validation_errors();
				 $status = 'error';
			}
			else{
	    		$proses = $this->loginmodel->login($post);
	    		if($proses['status']==1){
                    $status = 'success';
	    		}
	    		else{
	    			$status = 'Terjadi Kesalahan';
	    			$message = $proses['message'];	
	    		}
			}
	        $data['message'] 	=  "<div class='$status'> $message</div>";
	        $data['status'] 	=  $status;
	        echo json_encode($data);
    	}
	}
    
}