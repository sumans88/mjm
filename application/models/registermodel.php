<?php
class RegisterModel extends  CI_Model{
	var $table = 'auth_member';
	var $tableAs = 'auth_member a';
	function __construct(){
	   parent::__construct();
	       
	}
	function records($where=array(),$isTotal=0){
		$alias['search_title'] = 'a.name';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as topic,a.message as komentar");
		$this->db->where('a.is_delete',0);
		$this->db->join('contact_us_topic b','b.id = a.id_contact_us_topic');
		$query = $this->db->get($this->tableAs);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
	function insert($data){
		$data['type_login'] = 'aegon';
		$data['process_time'] 	= date('Y-m-d H:i:s');
		//$data['user_id_create'] = null;#visitor
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where_email['email'] = $data['email'];
		$where_email['id !='] = $id;
		$check_email = $this->db->select('*')->get_where($this->tableAs,$where_email)->row();
		if($check_email){
			//return 'Email Telah terdaftar, silahkan masukan email yang lain.';
			$data['status'] = 0;
			if($check_email->is_active == 0){
					$data['message'] = language('email_not_confirmation');
			} else {
					$data['message'] = language('email_already_registered');
			}
			return $data;
		} else {
			if($data['tgllahir']){
				$data['tgllahir'] = iso_date($data['tgllahir'],'/');
			}
			if($data['tgllahir_pasangan']){
				$data['tgllahir_pasangan'] = iso_date($data['tgllahir_pasangan'],'/');
			}
			$data_now = date('Y-m-d H:i:s');
			$log_user_activity = array(
			    'id_user'          =>  $id,
			    'process_date' =>  $data_now,
			    'id_log_category'   =>  26,
			);
			$this->log_user_activity($log_user_activity);
				if($data['kota']){
					$data['kodepos'] = $this->db->query("select kode_pos from master_kd_pos where kota='".$data['kota']."'")->row()->kode_pos;
				}
				$where['id'] = $id;
				$data['update_time'] 	= $data_now;
				$this->db->update($this->table,$data,$where);
			$proses['status'] = 1;
			$proses['message'] = language('success_change_profile');
			return $proses;
		}
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$where['is_delete'] = 0;
		
		$this->db->select('*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	
	function fetchRow($where) {
		return $this->findBy($where,1);
	}
	function register($data){
		$data_now = date('Y-m-d H:i:s');
		$where['email'] = $data['email'];
		$check_email = $this->db->select('*')->get_where($this->tableAs,$where)->row();
		if($check_email and !check_block_ip()){
		    //return 'Email Telah terdaftar, silahkan masukan email yang lain.';
			$data['status'] = 0;
			if($check_email->is_active == 0){
					$data['message'] = language('email_not_confirmation');
			} else {
					$data['message'] = language('email_registered');
			}
			return $data;
		} else {
			$this->load->model('mailchimpModel');
			if($data['newsletter']==1){
				$this->mailchimpModel->subscribe_mailchimp('3b6703b579',$data['email'],$data['namadepan'],$data['namabelakang']);
			}				
			if($data['marketing']==1){
				$this->mailchimpModel->subscribe_mailchimp('dff617b903',$data['email'],$data['namadepan'],$data['namabelakang']);
			}
		    $data['type_login'] = 'aegon';
		    $data['is_active'] = 0;
		    $data['is_complete_data'] = 1;
		    $data['process_time'] 	= $data_now;
		    $data['send_activation_date'] 	= $data_now;
		    $data['idrenderpage'] = md5($data_now.'#'.$data['email']);
		    $data['activation_code'] = md5($data_now.'#'.$data['email'].'#activation');
		    $pwd_user = $data['pwd'];
		    unset($data['pwd'],$data['confirm_pwd']);
		    $this->db->insert($this->table,array_filter($data));
		    $id_user = $this->db->insert_id();
		    $data_password['pwd'] = md5($pwd_user);
		    $data_password['id_member'] = $id_user;
		    $data_password['create_time'] 	= $data_now;
		    $data_password['is_active'] 	= 1;
		    $this->db->insert('t_aegon_member_password',array_filter($data_password));
		    $data['status'] = 1;
		    $data['message'] = 'Terima Kasih telah registrasi.';
		    $data['activation_code'] = base_url().'member/activemember/'.$data['activation_code'];
		    $data['base_url'] = base_url();
			sent_email_by_category(3,$data, $data['email']);
			unset($data['base_url']);
		    $data_log_registration = array(
				'id_user' => $id_user,
				'process_date' => $data_now,
				'type_reg' => 'aegon',
			    );
		    $this->log_registration($data_log_registration);
		    $log_user_activity = array(
			'id_user'          =>  $id_user,
			'process_date' =>  $data_now,
			'id_log_category'   =>  12,
		    );
		    $this->log_user_activity($log_user_activity);
		    return $data;
		}
	}
	function inserting_data($data1, $author){
		$data_now = date('Y-m-d H:i:s');
		$where['id_social_media'] = (string)$data1['user_profile']->identifier;
		$register_via = $author;
		if($author == 'Facebook'){
		    $author = 'fb';
		} else if($author == 'Twitter'){
		    $author = 'twitter';
		}
		$check_id_social = $this->db->select('*')->get_where($this->tableAs,$where)->row();
		if($data1['user_profile']->birthYear and $data1['user_profile']->birthMonth and $data1['user_profile']->birthDay){
		    $birthdate = $data1['user_profile']->birthYear.'-'.$data1['user_profile']->birthMonth.'-'. $data1['user_profile']->birthDay;
		} else {
		    $birthdate = '';
		}
		if(!$check_id_social){
		    $array = array(
			'image' => $data1['user_profile']->photoURL,	
			'screen_name' => $data1['user_profile']->displayName,		
			'namadepan' => $data1['user_profile']->firstName,		
			'namabelakang' => $data1['user_profile']->lastName,		
			'jeniskelamin' => $data1['user_profile']->gender,		
			'tgllahir' => $birthdate,		
			'email' => $data1['user_profile']->email,
			'username' => $data1['user_profile']->displayName,
			'nohp' => $data1['user_profile']->phone,		
			'alamat' => $data1['user_profile']->address,		
			'kota' => $data1['user_profile']->city,		
			'kodepos' => $data1['user_profile']->zip,
			'type_login' => $author,
			'process_time' => $data_now,
			'termscondition' => 1,
			'is_active' => 0,
			'id_social_media' => $data1['user_profile']->identifier,
			'idrenderpage' => md5($data_now.'#'.$data1['user_profile']->email)
		    );
		    $data_member_email = $array;
		    $query = $this->db->insert($this->table, $array);
		    $id_user_member = $this->db->insert_id();
		    if($query){
			$array_social = array(
			    'id' => $id_user_member,
			    'id_social_media' => $data1['user_profile']->identifier,
			    'nama_depan' => $data1['user_profile']->firstName,		
			    'nama_belakang' => $data1['user_profile']->lastName,
			    'email' => $data1['user_profile']->email,
			    'image' => $data1['user_profile']->photoURL,
			    'type_login' => $author,
			    
			);
			$query = $this->db->insert('t_aegon_profile_social_media', $array_social);
		    }
		    $data_log_registration = array(
			'id_user' => $id_user_member,
			'process_date' => $data_now,
			'type_reg' => $author,
		    );
		    $this->log_registration($data_log_registration);
		    $data_member_email['base_url'] = base_url();
		    $data_member_email['activation_code'] = base_url().'member/activemember/'.$data_member_email['activation_code'];
		    sent_email_by_category(4,$data_member_email, $data_member_email['email']);
		    $log_login_data = array(
			'userid'          =>  $id_user_member,
			'process_date' =>  $data_now,
			'type_login'   =>  $author,
		    );
		    $this->log_login($log_login_data);
		    $log_user_activity = array(
			'id_user'          =>  $id_user_member,
			'process_date' =>  $data_now,
			'id_log_category'   =>  12,
		    );
		    $this->log_user_activity($log_user_activity);
		    $log_user_activity = array(
			'id_user'          =>  $id_user_member,
			'process_date' =>  $data_now,
			'id_log_category'   =>  10,
		    );
		    $this->log_user_activity($log_user_activity);
		    $member_sess = array();
		    $member_sess = array(
			'member_email'          =>  $data_member_email['email'],
			'member_namadepan'      =>  $data_member_email['namadepan'],
			'member_namabelakang'   =>  $data_member_email['namabelakang'],
			'idrenderpage'          =>  $data_member_email['idrenderpage'],
			'id_social_media'       =>  $data_member_email['id_social_media'],
			'id'       				=>  $id_user_member,
		    );
		    $this->session->set_userdata('MEM_SESS',$member_sess);
		    $this->session->unset_userdata('ADM_SESS');
		    $this->session->set_flashdata('success_login',language('success_register_via')." $register_via. ".language('complete_data'));
		    return 2;
		} else {
		    $this->login_member($check_id_social);
		    $log_login_data = array(
			'userid'          =>  $check_id_social->id,
			'process_date' =>  $data_now,
			'type_login'   =>  $author,
		    );
		    $this->log_login($log_login_data);
		    $log_user_activity = array(
			'id_user'          =>  $check_id_social->id,
			'process_date' =>  $data_now,
			'id_log_category'   =>  10,
		    );
		    $this->log_user_activity($log_user_activity);
		    $data['message'] = language('success_login');
			//$this->session->set_flashdata('success_login',$data['message']);
		    return 1;
		}
		
	    }
	    function login_member($data,$remember_me=0){ //row array	    	
			$member_sess = array();
			$member_sess = array(
				'member_email'        =>  $data['email'],
				'member_namadepan'    =>  $data['firstname'],
				'member_namabelakang' =>  $data['lastname'],
				// 'member_namatengah'   =>  $data['middlename'],
				'full_name'           =>  full_name($data),
				'firstname'           =>  $data['firstname'],
				'lastname'            =>  $data['lastname'],
				'prefix_name'         =>  $data['prefix_name'],
				'company_id'          =>  $data['company_id'],
				'member_category_id'  =>  $data['member_category_id'],
				'is_invis'            =>  $data['is_invis'],
				'is_paid'             =>  $data['is_paid'],
				'id'                  =>  $data['id'],
				'remember_me'         =>  $remember_me,
				'status'         =>  $data['status_payment_id']
			);
			$this->load->model('membership_model');

			$membership_id                  = $this->membership_model->findBy(array('member_id'=>$data['id']),1)['id'];
			$data_save['last_visited_date'] = date('Y-m-d H:i:s');
			$this->membership_model->update($data_save,$membership_id);
			

			if ($remember_me) {
				setcookie('username', base64_encode($data->email), time() + (86400 * 30), "/"); // 86400 = 1 day
				setcookie('password', base64_encode($data->password), time() + (86400 * 30), "/"); // 86400 = 1 day
			}
			$this->session->set_userdata('MEM_SESS',$member_sess);
			$this->session->unset_userdata('ADM_SESS');
	    }

	    function log_registration($data){
		//to table t_aegon_log_simple_registration
		$array_log_registration = array(
		    'id_user' => $data['id_user'],
		    'process_date' => $data['process_date'],
		    'type_reg' => $data['type_reg'],
		);
		$query = $this->db->insert('t_aegon_log_simple_registration', $array_log_registration);
	    }
	    function log_login($data, $log_last_login=1){
		if($log_last_login==1){
			$where['id'] = $data['userid'];
			$data_log['islogin'] = 1;
			$data_log['last_login'] 	= $data['process_date'];
			$this->db->update($this->table,$data_log,$where);
		}
		//to table t_aegon_log_login
		$array_log_login = array(
		    'userid' => $data['userid'],
		    'ipaddress' => $_SERVER['REMOTE_ADDR'],
		    'process_date' => $data['process_date'],
		    'ismobile' => $_SERVER['HTTP_USER_AGENT'],
		    'id_log_category' => 10,
		    'type_login' => $data['type_login'],
		);
		$query = $this->db->insert('t_aegon_log_login', $array_log_login);
	    }
	function log_user_activity($data){
	    //to table user_activity_log
	    $array_log_login = array(
		'id_user' => $data['id_user'],
		'create_date' => $data['process_date'],
		'id_log_category' => $data['id_log_category'],
		'last_date_read' => $data['process_date'],
		'ipaddress' => $_SERVER['REMOTE_ADDR'],
		'ismobile' => $_SERVER['HTTP_USER_AGENT']
	    );
	    $query = $this->db->insert('user_activity_log', $array_log_login);
	}
	function active_member($activation_code,$email=0){
		$data_now = date('Y-m-d H:i:s');
		$where['activation_code'] = $activation_code;
		$check_activation_code = $this->db->select('*')->get_where($this->tableAs,$where)->row();
		if($check_activation_code and !check_block_ip()){
		    if($check_activation_code->is_active==0){
			$data_create_date = $check_activation_code->send_activation_date;
			$max_data_exp = EXP_DATE_ACTIVATION_EMAIL;
			if(date('Y-m-d') <= date("Y-m-d",strtotime("$data_create_date + $max_data_exp days"))){
				$data['status'] = 1;
				$data['email'] = $email;
				$data['check_activation_code'] = $activation_code;
			} else {
				$data['status'] = 0;
				if($email==0){		
					$data['message'] = language('exp_to_activation_email');		
				} else{
					$data['message'] = language('exp_to_activation_email_new');	
				}
			}
		    }
		} else {
		    $data['status'] = 0;
			if($email==0){		
				$data['message'] = language('failed_to_activation_email');		
			} else{
				$data['message'] = language('failed_to_activation_email_new_active');	
			}
		}
		return $data;
	}
	function process_activation($activation_code, $email=0){
		$data_now = date('Y-m-d H:i:s');
		$where['activation_code'] = $activation_code;
		$check_activation_code = $this->db->select('*')->get_where($this->tableAs,$where)->row();
		if($check_activation_code and !check_block_ip()){
		    if($check_activation_code->is_active==0){
			$data_create_date = $check_activation_code->send_activation_date;
			$max_data_exp = EXP_DATE_ACTIVATION_EMAIL;
			if(date('Y-m-d') <= date("Y-m-d",strtotime("$data_create_date + $max_data_exp days"))){
				$where['id'] = $check_activation_code->id;
				$data['is_active'] = 1;
				$data['update_time'] 	= $data_now;
				$data['activation_code'] 	= '';
				$this->db->update($this->table,$data,$where);
				$log_user_activity = array(
					'id_user'         	=>  $check_activation_code->id,
					'process_date' 		=>  $data_now,
					'id_log_category'   =>  24,
				);
				$this->log_user_activity($log_user_activity);
				$this->login_member($check_activation_code);
				$log_login_data = array(
					'userid'          =>  $check_activation_code->id,
					'process_date' =>  $data_now,
					'type_login'   =>  language('activation_email'),
				);
				$this->log_login($log_login_data,0);
				
				$where_data['id'] = $check_activation_code->id;
				$check_activation_code_array = $this->db->select('*')->get_where($this->tableAs,$where_data)->row_array();
				$check_activation_code_array['base_url'] = base_url();		
				if($email==0){				
					//please change id to 12 on server
					sent_email_by_category(12,$check_activation_code_array, $check_activation_code_array['email']);
					$data['status'] = 1;
					$data['message'] = language('success_to_activation_email');
				}else{
					sent_email_by_category(18,$check_activation_code_array, $check_activation_code_array['email']);
					$data['status'] = 1;
					$data['message'] = language('success_to_activation_email_new');
				}
			} else {
				$data['status'] = 0;
				if($email==0){		
					$data['message'] = language('exp_to_activation_email');		
				} else{
					$data['message'] = language('exp_to_activation_email_new');	
				}
			}
		    }
		} else {
		    $data['status'] = 0;
			if($email==0){		
				$data['message'] = language('failed_to_activation_email');		
			} else{
				$data['message'] = language('failed_to_activation_email_new');	
			}
		}
		return $data;
	}
	function change_password($data,$id_member=''){
		$data_now = date('Y-m-d H:i:s');
		if($id_member){
			$user_sess_data['id'] = $id_member;
		} else {
			$user_sess_data = $this->session->userdata('MEM_SESS');
		}
		$check_current_pass = $this->db->select('pwd')->get_where('t_aegon_member_password',"id_member='$user_sess_data[id]' and pwd='".md5($data['current_pwd'])."' and is_active=1")->row();
		if($check_current_pass and !check_block_ip()){
			$where_reset['id_member'] = $user_sess_data['id'];
			$data_reset['is_active'] = 0;
			$this->db->update('t_aegon_member_password',$data_reset,$where_reset);
			$data_password['pwd'] = md5($data['pwd']);
			$data_password['id_member'] = $user_sess_data['id'];
			$data_password['create_time'] 	= $data_now;
			$data_password['is_active'] 	= 1;
			$this->db->insert('t_aegon_member_password',array_filter($data_password));
			$log_user_activity = array(
			    'id_user'          =>  $user_sess_data['id'],
			    'process_date' =>  $data_now,
			    'id_log_category'   =>  40,
			);
			$this->log_user_activity($log_user_activity);
			$data_msg['status'] = 1;
			$data_msg['message'] = language('password_changed'); 
		}else{
			$data_msg['status'] = 0;
			$data_msg['message'] = language('wrong_password_old');  
		}
		return $data_msg;
	}
	function change_subscriber($data,$id_member=''){
		$this->load->model('mailchimpModel');
		$data_now = date('Y-m-d H:i:s');
		if($id_member){
			$user_sess_data['id'] = $id_member;
		} else {
			$user_sess_data = $this->session->userdata('MEM_SESS');
		}
		$check_current_data= $this->db->select('newsletter,marketing')->get_where($this->table,"id='$user_sess_data[id]'")->row();
		unset($data['newsletter_check'],$data['marketing_check']);
		if($check_current_data and !check_block_ip()){
			if($check_current_data->newsletter!=$data['newsletter']){
				if($data['newsletter']==1){
					$id_log_category_newsletter = 30;
					$this->mailchimpModel->subscribe_mailchimp('3b6703b579',$user_sess_data['member_email'],$user_sess_data['member_namadepan'],$user_sess_data['member_namabelakang']);
				}else{
					$data['newsletter'] = 0;
					$id_log_category_newsletter = 28;
					$this->mailchimpModel->unsubscribe_mailchimp('3b6703b579',$user_sess_data['member_email']);
				}
				$log_user_activity = array(
					'id_user'          =>  $user_sess_data['id'],
					'process_date' =>  $data_now,
					'id_log_category'   =>  $id_log_category_newsletter,
				);
				$this->log_user_activity($log_user_activity);				
			} else if($check_current_data->marketing!=$data['marketing']){
				if($data['marketing']==1){
					$id_log_category = 31;
					$this->mailchimpModel->subscribe_mailchimp('dff617b903',$user_sess_data['member_email'],$user_sess_data['member_namadepan'],$user_sess_data['member_namabelakang']);
				}else{
					$data['marketing'] = 0;
					$id_log_category = 29;
					$this->mailchimpModel->unsubscribe_mailchimp('dff617b903',$user_sess_data['member_email']);
				}
				$log_user_activity = array(
					'id_user'          =>  $user_sess_data['id'],
					'process_date' =>  $data_now,
					'id_log_category'   =>  $id_log_category,
				);
				$this->log_user_activity($log_user_activity);	
			}
			$where['id'] = $user_sess_data['id'];
			$data_update = $this->db->update($this->table,$data,$where);
			$data_msg['status'] = 1;
			$data_msg['message'] = language('subscriber_changed');
		}else{
			$data_msg['status'] = 0;
			$data_msg['message'] = language('something_error').', '.language('something_error_call');  
		}
		return $data_msg;
	}
	function complete_data($data){
		$data_now = date('Y-m-d H:i:s');
		$where['email'] = $data['email'];
		$where['id !='] = $data['id'];
		$check_email = $this->db->select('*')->get_where($this->tableAs,$where)->row();
		if(!$check_email and !check_block_ip()){
			$log_user_activity = array(
			    'id_user'          =>  $data['id'],
			    'process_date' =>  $data_now,
			    'id_log_category'   =>  26,
			);
			$this->log_user_activity($log_user_activity);
			$data_password['pwd'] = md5($data['pwd']);
			$data_password['id_member'] = $data['id'];
			$data_password['create_time'] 	= $data_now;
			$data_password['is_active'] 	= 1;
			$this->db->insert('t_aegon_member_password',array_filter($data_password));
			$this->load->model('mailchimpModel');
			$where_save['id'] = $data['id'];
			if($data['newsletter']==1){
				$this->mailchimpModel->subscribe_mailchimp('3b6703b579',$data['email'],$data['namadepan'],$data['namabelakang']);
			}				
			if($data['marketing']==1){
				$this->mailchimpModel->subscribe_mailchimp('dff617b903',$data['email'],$data['namadepan'],$data['namabelakang']);
			}
			$data['is_complete_data'] = 1;
			$data['is_active'] = 0;
			$data['update_time'] 	= $data_now;
			$data['activation_code'] = md5($data_now.'#'.$data['email'].'#activation');;
			$data['send_activation_date'] 	= $data_now;
			$data['activation_code'] = base_url().'member/activemember/'.$data['activation_code'];
			$data['base_url'] = base_url();	
			sent_email_by_category(3,$data, $data['email']);
			
			unset($data['id'],$data['pwd'],$data['confirm_pwd']);
			$this->db->update($this->table,$data,$where_save);
			$proses['status'] = 1;
			$proses['message'] = language('complete_data_success');
		} else {
			$proses['status'] = 0;
			$proses['message'] = language('email_registered');
		}
		return $proses;
	}
	function deactivate_account($id_member){
		$data_now = date('Y-m-d H:i:s');
		$check_current_data= $this->db->select('id')->get_where($this->table,"id='$id_member'")->row();
		if($check_current_data and !check_block_ip()){
			$where_save['id_member'] = $id_member;
			$data['is_active'] = 0;
			$this->db->update('t_aegon_member_deactive',$data,$where_save);
			
			$where_member['id'] = $id_member;
			$data_is_active['is_active'] = 2;
			$data_is_active['activation_code'] = md5($data_now.'#'.$data['email'].'#deactive_account');
			$this->db->update($this->table,$data_is_active,$where_member);
			
			$data_current = $this->db->select('*')->get_where($this->table,"id='$id_member'")->row_array();
			$data_current['base_url'] = base_url();	
			$data_current['idrenderpage'] = base_url().'member/reactivateaccount/'.$data_current['idrenderpage'];
			//please change id to 13 on server
			sent_email_by_category(13,$data_current, $data_current['email']);
			
			
			$data_password['due_date'] = date("Y-m-d",strtotime("$data_now + 30 days"));
			$data_password['id_member'] = $id_member;
			$data_password['create_time'] 	= $data_now;
			$data_password['is_active'] 	= 1;
			$this->db->insert('t_aegon_member_deactive',array_filter($data_password));
			
			$log_user_activity = array(
				'id_user'         	=>  $id_member,
				'process_date' 		=>  $data_now,
				'id_log_category'   =>  32,
			);
			$this->log_user_activity($log_user_activity);
			
			$proses['status'] = 1;
			$proses['message'] = language('deactive_data_success');
		} else {
			$proses['status'] = 0;
			$proses['message'] = language('email_registered');
		}
		return $proses;
	}
	function reactivate_account($idrenderpage){
		$data_now = date('Y-m-d H:i:s');
		$check_current_data= $this->db->select('*')->get_where($this->table,"idrenderpage='$idrenderpage' and is_active=2")->row();
		if($check_current_data and !check_block_ip()){
			$id_member = $check_current_data->id;
			$check_deactive_member= $this->db->select('*')->get_where('t_aegon_member_deactive',"id_member='$id_member' and is_active=1")->row();
			if($check_deactive_member){
				$where_save['id_member'] = $id_member;
				$data['is_active'] = 0;
				$this->db->update('t_aegon_member_deactive',$data,$where_save);
				
				$where_member['id'] = $id_member;
				$data_is_active['is_active'] = 1;
				$this->db->update($this->table,$data_is_active,$where_member);
				
				$log_user_activity = array(
					'id_user'       =>  $id_member,
					'process_date' 	=>  $data_now,
					'id_log_category'   =>  33,
				);
				
				$data_current = $this->db->select('*')->get_where($this->table,"id='$id_member'")->row_array();
				$data_current['base_url'] = base_url();	
				//plase Change To 15 on server
				sent_email_by_category(15,$data_current, $data_current['email']);
				
				$this->log_user_activity($log_user_activity);
				 $this->login_member($check_current_data);
				$proses['status'] = 1;
				$proses['message'] = language('reactive_success');
			} else {
				$proses['status'] = 0;
				$proses['message'] = language('reactive_code_not_found');
			}
		} else {
			$proses['status'] = 0;
			$proses['message'] = language('reactive_code_not_found');
		}
		return $proses;
	}
	function insert_child($nama='',$dob_child='',$jeniskelamin='',$umur_anak='',$id_member=''){
		$data_now = date('Y-m-d H:i:s');
		if($id_member){
			$user_sess_data['id'] = $id_member;
		} else {
			$user_sess_data = $this->session->userdata('MEM_SESS');
		}
		if($user_sess_data['id']){
			if($dob_child){
				$data['dob_child'] = iso_date($dob_child,'/');
			}
			$data['nama'] = $nama;
			$data['jeniskelamin'] = $jeniskelamin;
			$data['umur_anak'] = $umur_anak;
			$data['id_member'] = $user_sess_data['id'];
			$data['create_date'] = $data_now;
			$data['modify_date'] = $data_now;
			$this->db->insert('t_aegon_member_child',array_filter($data));
		}
	}
	function delete_all_child($id_member=''){
		$data_now = date('Y-m-d H:i:s');
		if($id_member){
			$user_sess_data['id'] = $id_member;
		} else {
			$user_sess_data = $this->session->userdata('MEM_SESS');
		}
		if($user_sess_data['id']){
			$this->db->delete('t_aegon_member_child', array('id_member' => $user_sess_data['id'])); 
		}
	}
	
	function change_new_email($email){
		$data_now = date('Y-m-d H:i:s');
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if($user_sess_data and !check_block_ip()){
			$where_save['userid'] = $user_sess_data['id'];
			$data_save['is_active'] = 0;
			$this->db->update('t_aegon_member_change_email',$data_save,$where_save);
			
			$activation_code = md5($data_now.'#'.$user_sess_data['member_email'].'#change_new_email');
			
			$data_change['from_data'] = $user_sess_data['member_email'];
			$data_change['to_data'] 	= $email;
			$data_change['activation_code'] = $activation_code;
			$data_change['is_active'] 	= 1;
			$data_change['process_date'] 	= $data_now;
			$data_change['userid'] 	= $user_sess_data['id'];
			$this->db->insert('t_aegon_member_change_email',array_filter($data_change));
			
			$log_user_activity = array(
				'id_user'       =>  $user_sess_data['id'],
				'process_date' 	=>  $data_now,
				'id_log_category'   =>  34,
			);
			$this->log_user_activity($log_user_activity);
			
			$id_member = $user_sess_data['id'];
			$data_current = $this->db->select('*')->get_where($this->table,"id='$id_member'")->row_array();
			$data_current['activation_code'] = $activation_code;
			$data_current['base_url'] = base_url();	
			$data_current['activation_code'] = base_url().'member/confirmationchangeemail/'.$data_current['activation_code'];			
			//please change id to 14 on server
			sent_email_by_category(14,$data_current, $user_sess_data['member_email']);
		}
	}
	function change_new_email_confirmed($activation_code){
		$data_now = date('Y-m-d H:i:s');
		$where['activation_code'] = $activation_code;
		$where['is_active'] = 1;
		$check_activation_code = $this->db->select('*')->get_where('t_aegon_member_change_email',$where)->row();
		if($check_activation_code and !check_block_ip()){
			$data_create_date = $check_activation_code->process_date;
			$max_data_exp = EXP_CHANGE_EMAIL_MEMBER;
			if(date('Y-m-d') <= date("Y-m-d",strtotime("$data_create_date + $max_data_exp days"))){
				$data['status'] = 1;
			} else {
				$data['status'] = 0;
				$data['message'] = language('change_email_code_exp');		
			}
		} else {
		    $data['status'] = 0;
		    $data['message'] = language('change_email_code_not_found');				
		}
		return $data;
	}
	function change_new_email_confirmed_process($activation_code){
		$data_now = date('Y-m-d H:i:s');
		$where['activation_code'] = $activation_code;
		$where['is_active'] = 1;
		$check_activation_code = $this->db->select('*')->get_where('t_aegon_member_change_email',$where)->row();
		if($check_activation_code and !check_block_ip()){
			$data_create_date = $check_activation_code->process_date;
			$max_data_exp = EXP_CHANGE_EMAIL_MEMBER;
			if(date('Y-m-d') <= date("Y-m-d",strtotime("$data_create_date + $max_data_exp days"))){
				$id_member = $check_activation_code->userid;
				
				$data_current = $this->db->select('*')->get_where($this->table,"id='$id_member'")->row_array();
				$where_save['userid'] = $data_current['id'];
				$data_save['is_active'] = 0;
				$this->db->update('t_aegon_member_change_email',$data_save,$where_save);
				
				$where_email['email'] = $check_activation_code->to_data;
				$check_email = $this->db->select('*')->get_where($this->tableAs,$where_email)->row();
				if($check_email){
					//return 'Email Telah terdaftar, silahkan masukan email yang lain.';
					$data['status'] = 0;
					$data['message'] = language('email_registered_change_email');
				} else {
					$activation_code_new = md5($data_now.'#'.$data_current['email'].'#activation');
					$where_update['id'] = $data_current['id'];
					$data_update['is_active'] = 0;
					$data_update['email'] = $check_activation_code->to_data;
					$data_update['send_activation_date'] 	= $data_now;
					$data_update['activation_code'] = $activation_code_new;
					$this->db->update($this->table,$data_update,$where_update);
					$data_current['base_url'] = base_url();	
					$data_current['activation_code'] = $activation_code_new;
					$data_current['activation_code'] = base_url().'member/activeemailmember/'.$data_current['activation_code'];			
					sent_email_by_category(17,$data_current, $check_activation_code->to_data);
					
					$log_user_activity = array(
						'id_user'       =>  $data_current['id'],
						'process_date' 	=>  $data_now,
						'id_log_category'   =>  35,
					);
					$this->log_user_activity($log_user_activity);
					
					$data['status'] = 1;
					$data['message'] = language('change_email_success');
				}
			} else {
				$data['status'] = 0;
				$data['message'] = language('change_email_code_exp');		
			}
		} else {
		    $data['status'] = 0;
		    $data['message'] = language('change_email_code_not_found');				
		}
		return $data;
	}
	function reactivate_account_check($idrenderpage){
		$data_now = date('Y-m-d H:i:s');
		$check_current_data= $this->db->select('*')->get_where($this->table,"idrenderpage='$idrenderpage' and is_active=2")->row();
		if($check_current_data and !check_block_ip()){
			$id_member = $check_current_data->id;
			$check_deactive_member= $this->db->select('*')->get_where('t_aegon_member_deactive',"id_member='$id_member' and is_active=1")->row();
			if($check_deactive_member){
				$proses['status'] = 1;
			} else {
				$proses['status'] = 0;
				$proses['message'] = language('reactive_code_not_found');
			}
		} else {
			$proses['status'] = 0;
			$proses['message'] = language('reactive_code_not_found');
		}
		return $proses;
	}
 }
