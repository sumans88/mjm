<?php
class loginmodel extends  CI_Model{
	var $table = 'auth_member';
	var $tableAs = 'auth_member a';
    function __construct(){
       parent::__construct();
	   
    }
	function resend_email_activation($data){
        $data_now = date('Y-m-d H:i:s');
        $where['email'] = $data['email'];
        $check_email = $this->db->select('*')->get_where($this->tableAs,$where)->row_array();
        if($check_email){
            //return 'Email Telah terdaftar, silahkan masukan email yang lain.';		
            if($check_email['is_active'] == 0){
		$check_email['activation_code'] = base_url().'member/activemember/'.$check_email['activation_code'];
		$check_email['base_url'] = base_url();
                sent_email_by_category(5,$check_email, $check_email['email']);
                $data_msg['status'] = 1;
                $data_msg['message'] = language('success_to_send_email_activation');
            } else {
                $data_msg['status'] = 0;
                $data_msg['message'] = language('success_to_confirmation_email');    
            }
        } else {
            $data_msg['status'] = 0;
            $data_msg['message'] = language('email_not_exist');  
        }
        return $data_msg;    
    }
    function reset_password($data){
        $data_now = date('Y-m-d H:i:s');
        $where['email'] = $data['email'];
        $check_email = $this->db->select('*')->get_where($this->tableAs,$where)->row_array();
        if($check_email){
            $reset_code = md5($data_now.'#'.$check_email['email'].'#reset_password');
            $where['id'] = $check_email['id'];
	    $exp_password_days = EXP_RESET_PASSWORD_MEMBER;
            $data['reset_date_due'] = date('Y-m-d H:i:s',strtotime("+$exp_password_days day", strtotime($data_now)));
            $data['reset_code'] 	= $reset_code;
            $this->db->update($this->table,$data,$where);
		$check_email['reset_code'] = base_url().'member/resetpasswordcode/'.$reset_code;
		$check_email['base_url'] = base_url();
            sent_email_by_category(6,$check_email, $check_email['email']);
            $data_msg['status'] = 1;
            $data_msg['message'] = language('reset_password_email'); 
        } else {
            $data_msg['status'] = 0;
            $data_msg['message'] = language('email_not_exist');  
        }
        return $data_msg;    
    }
    function check_reset_due($activation_code){
		$data_now = date('Y-m-d H:i:s');
		$where['reset_code'] = $activation_code;
		$check_activation_code = $this->db->select('*')->get_where($this->tableAs,$where)->row();
        if($check_activation_code){
                if(date('Y-m-d') < $check_activation_code->reset_date_due){
                    $data['status'] =1;
                } else {
                    $data['status'] = 0;
                    $data['message'] = language('reset_password_expired');
                }
        } else {
                $data['message'] = language('reset_password_code_not_valid');
				$data['status'] = 0;
        }
		return $data;
	}
    function reset_password_code($data){
        $data_now = date('Y-m-d H:i:s');
        $where['reset_code'] = $data['reset_code'];
        $check_code = $this->db->select('*')->get_where($this->tableAs,$where)->row_array();
        if($check_code and !check_block_ip()){
            $this->load->model('registermodel');
            $where_reset['id_member'] = $check_code['id'];
            $data_reset['is_active'] = 0;
            $this->db->update('t_aegon_member_password',$data_reset,$where_reset);
            $data_password['pwd'] = md5($data['pwd']);
            $data_password['id_member'] = $check_code['id'];
            $data_password['create_time'] 	= $data_now;
            $data_password['is_active'] 	= 1;
            $this->db->insert('t_aegon_member_password',array_filter($data_password));
            $log_user_activity = array(
                'id_user'          =>  $check_code['id'],
                'process_date' =>  $data_now,
                'id_log_category'   =>  25,
            );
            $this->registermodel->log_user_activity($log_user_activity);
            $data_msg['status'] = 1;
            $data_msg['message'] = language('reset_password_success_activation'); 
            $where_reset_password['id'] = $check_code['id'];
            $data_reset_password['reset_code'] = '';
            $data_reset_password['reset_date_due'] = '';
            $this->db->update('auth_member',$data_reset_password,$where_reset_password);
        } else {
            $data_msg['status'] = 0;
            $data_msg['message'] = language('reset_password_code_not_valid');  
        }
        return $data_msg;    
    }
    function login($data){
	$data_now = date('Y-m-d H:i:s');
        $this->db->select("a.*");
        $this->db->where('b.is_active',1);
        $this->db->where('a.email', $data['modal_login_form_username']);
	$this->db->join('t_aegon_member_password b',"b.id_member = a.id",'left');
        $this->db->where('b.pwd', md5($data['modal_login_form_password']));
	$check_user = $this->db->get($this->tableAs)->row();
        if($check_user){
            if($check_user->is_active==1){
                $this->load->model('registermodel');
                if($data['remember_me'] ==1){
                    $array_keep_login = array(
                        'userid' => $check_user->id,
                        'ipaddress' => $_SERVER['REMOTE_ADDR'],
                        'process_date' => $data_now,
                    );
			setcookie('email', base64_encode($data['modal_login_form_username']), time() + (86400 * 30), "/"); // 86400 = 1 day
			setcookie('password', base64_encode($data['modal_login_form_password']), time() + (86400 * 30), "/"); // 86400 = 1 day
		    $this->input->cookie('user',TRUE);
		    $this->input->cookie('password',TRUE);
                    $query_keep_login = $this->db->insert('t_aegon_keep_login', $array_keep_login);
		    $this->registermodel->login_member($check_user,1);
                }else{
			$this->registermodel->login_member($check_user);
		}
                $log_login_data = array(
                    'userid'          =>  $check_user->id,
                    'process_date' =>  $data_now,
                    'type_login'   =>  'Aegon',
                );
                $this->registermodel->log_login($log_login_data);
                $log_user_activity = array(
                    'id_user'          =>  $check_user->id,
                    'process_date' =>  $data_now,
                    'id_log_category'   =>  10,
                );
		
		$where_data_log['email'] = $data['modal_login_form_username'];
		$data_log['is_active'] = 0;
		$this->db->update('t_aegon_log_max_login',$data_log,$where_data_log);
		
                $this->registermodel->log_user_activity($log_user_activity);
                $data['status'] =1;
                $data['message'] = language('success')." ".language('login');
                //$this->session->set_flashdata('success_login',$data['message']);
            } else if($check_user->is_active==2) {
                $data['status'] = 0;
                $data['message'] = language('deactive_account_via_login'). ' <form id="data_agreement_active">
					<label class="text-left error-login-margin">
                            <input class="col-sm-1 checkbox-error-login" type="checkbox" id="termscondition_data" name="termscondition_data" value="1" style="height: auto;"> 
			    '.language('agreement_register').'
					</label>
					<label class="errorTxt"></label>
					<button class="btn btn-default" id="active_account_agree" data_redirect="'.base_url().'member/reactivateaccountprocess/'.$check_user->idrenderpage.'">
					'.language('reactive_account').'</button>
					</form>
					<script>
					$("#data_agreement_active").validate({
							rules: {
								termscondition_data: {required: true},
							},
							errorElement : "label",
							errorLabelContainer: ".errorTxt"
						});
						
					$("#active_account_agree").click(function(){
						if($("#data_agreement_active").validate().form()){
							loading();
							window.location.href= $("#active_account_agree").attr("data_redirect");
						}
						return false;
					})
					</script>
					
					';
	} else {
		$data['status'] = 0;
                $data['message'] = language('account_not_active_yet');
	    }
        } else {
		$this->db->select("a.*");
		$this->db->where('a.is_active',1);
		$this->db->where('a.email', $data['modal_login_form_username']);
		$check_user_data_exist = $this->db->get($this->tableAs)->row();
				
		if($check_user_data_exist){
			$this->load->model('maxloginmodel');
			$data_max_login = $this->maxloginmodel->findBy(array('email'=>$data['modal_login_form_username'],'is_active' => 1),1);
			$max_data_exp_time = EXP_MAX_TIME_FAILED_LOGIN;
			$max_data_exp_count = EXP_MAX_COUNT_FAILED_LOGIN;
			$data_now = date('Y-m-d H:i:s');
			$data_create_date = $data_max_login['process_date'];
			if($data_max_login['count'] >= $max_data_exp_count and date('Y-m-d h:i:s') <= date("Y-m-d h:i:s",strtotime("$data_create_date + $max_data_exp_time minutes")) ){
				$data['message'] = language('wrong_password_max') . ' ' .language('please_reset_password');
				$data['status'] = 2;
				$where_data_log['id'] = $data_max_login['id'];
				$data_log['last_try'] = $data_now;
				$data_log['count'] 	= $data_max_login['count'] += 1;
				$this->db->update('t_aegon_log_max_login',$data_log,$where_data_log);
			} else {
				if(date('Y-m-d H:i:s') <= date("Y-m-d H:i:s",strtotime("$data_create_date + $max_data_exp_time minutes")) and $data_max_login){
					$where_data_log['id'] = $data_max_login['id'];
					$data_log['last_try'] = $data_now;
					$data_log['count'] 	= $data_max_login['count'] += 1;
					$this->db->update('t_aegon_log_max_login',$data_log,$where_data_log);
				}else{
					$data_active['userid'] = $check_user_data_exist->id;
					$data_active['process_date'] 	= $data_now;
					$data_active['last_try'] 	= $data_now;
					$data_active['count'] 	= 1;
					$data_active['is_active'] 	= 1;
					$data_active['email'] 	= $check_user_data_exist->email;
					$this->db->insert('t_aegon_log_max_login',array_filter($data_active));
					
				}
				$data['message'] = language('wrong_user_password');
				$data['status'] = 0;
			}

		} else{
			$data['message'] = language('wrong_user_password_all');
			$data['status'] = 0;
		}
		
                
        }
		return $data;
	}
	function remember_me_login($data){
		$user_sess_data = $this->session->userdata('MEM_SESS');
		$this->load->model('registermodel');
		if(isset($_COOKIE['username']) and isset($_COOKIE['password'])){
			$this->db->select("a.*");
			$this->db->where('a.is_delete',0);
            $this->db->where('a.email', base64_decode($_COOKIE['username']));
			$this->db->where('a.password', md5(base64_decode($_COOKIE['password'])));
			// $this->db->join('t_aegon_member_password b',"b.id_member = a.id",'left');
			// $this->db->where('b.pwd', md5(base64_decode($_COOKIE['password'])));
			$check_user = $this->db->get($this->tableAs)->row();
			if($check_user){
				$this->registermodel->login_member($check_user,1);
			}
		}
		return true;
	}
}
