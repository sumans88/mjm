<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Register extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('registermodel');
	}
    function index(){
    	$post = purify($this->input->post());
    	if($post){
    		$post['id_contact_us_topic'] =  $post['topic'];
    		$this->form_validation->set_rules('namadepan', '"Nama Depan"', 'required'); 
    		$this->form_validation->set_rules('pwd', '"Password"', 'required'); 
    		//$this->form_validation->set_rules('hp', '"Handphone"', 'required'); 
    		$this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
			if ($this->form_validation->run() == FALSE){
				 $message = validation_errors();
				 $status = 'error';
			}
			else{
                $email = $post['email'];
	    		$proses = $this->registermodel->register($post);
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
	        $data['list_provinsi'] 	= selectlist2(array('table'=>'propinsi','order'=>'propinsi','title'=>'Pilih Provinsi','name'=>'propinsi'));
	        render('layout/ddi/member/register',$data);
    	}
    }
    function get_town(){
        $id = $this->input->post('id');
        if($id){
            $list 			 = $this->db->query('select kota,id_propinsi,type_kota from master_kd_pos where id_propinsi='.$id.' group by kota,id_propinsi,type_kota order by kota asc')->result_array();
            $opt 			 = $conf['no_title'] ? '' : "<option value=''>Pilih Kota</option>";
            foreach($list as $l){
		if($l['kota'] == $this->input->post('kota')){
			$opt 	.= "<option value='$l[kota]' selected> $l[type_kota] $l[kota]</option>";
		} else {
			$opt 	.= "<option value='$l[kota]'> $l[type_kota] $l[kota]</option>";
		}
            }
            echo $opt;
        } else {
            echo '';
        }
    }
    function get_kecamatan(){
        $id = $this->input->post('id');
        if($id){
            $list 			 = $this->db->query("select kecamatan,kota from master_kd_pos where kota='".$id."' group by kecamatan,kota order by kecamatan asc")->result_array();
            $opt 			 = $conf['no_title'] ? '' : "<option value=''>Pilih Kecamatan</option>";
            foreach($list as $l){
		if($l['kecamatan'] == $this->input->post('kecamatan')){
			$opt 	.= "<option value='$l[kecamatan]' selected> $l[kecamatan]</option>";
		} else {
			$opt 	.= "<option value='$l[kecamatan]'> $l[kecamatan]</option>";
		}
            }
            echo $opt;
        } else {
            echo '';
        }
    }
    function get_kelurahan(){
        $id = $this->input->post('id');
        if($id){
            $list 			 = $this->db->query("select kelurahan,kecamatan from master_kd_pos where kecamatan='".$id."' group by kecamatan,kelurahan order by kelurahan asc")->result_array();
            $opt 			 = $conf['no_title'] ? '' : "<option value=''>Pilih Kecamatan</option>";
            foreach($list as $l){
		if($l['kelurahan'] == $this->input->post('kelurahan')){
			$opt 	.= "<option value='$l[kelurahan]' selected> $l[kelurahan]</option>";
		} else {
			$opt 	.= "<option value='$l[kelurahan]'> $l[kelurahan]</option>";
		}
            }
            echo $opt;
        } else {
            echo '';
        }
    }
    public function social_signup($provider)
	{
		log_message('debug', "controllers.HAuth.login($provider) called");

		try
		{
			log_message('debug', 'controllers.HAuth.login: loading HybridAuthLib');
			$this->load->library('HybridAuthLib');

			if ($this->hybridauthlib->providerEnabled($provider))
			{
				log_message('debug', "controllers.HAuth.login: service $provider enabled, trying to authenticate.");
				$service = $this->hybridauthlib->authenticate($provider);

				if ($service->isUserConnected())
				{
					log_message('debug', 'controller.HAuth.login: user authenticated.');

					$user_profile = $service->getUserProfile();

					log_message('info', 'controllers.HAuth.login: user profile:'.PHP_EOL.print_r($user_profile, TRUE));

					$data['user_profile'] = $user_profile;
					$process = $this->registermodel->inserting_data($data, $provider);
                    if($process == 1){
                        redirect('/dashboard');
                    } else {
                        redirect('/dashboard');
                    }
					//$this->load->view('hauth/done',$data);
				}
				else // Cannot authenticate user
				{
					show_error('Cannot authenticate user');
				}
			}
			else // This service is not enabled.
			{
				log_message('error', 'controllers.HAuth.login: This provider is not enabled ('.$provider.')');
				show_404($_SERVER['REQUEST_URI']);
			}
		}
		catch(Exception $e)
		{
			$error = 'Unexpected error';
			switch($e->getCode())
			{
				case 0 : $error = 'Unspecified error.'; break;
				case 1 : $error = 'Hybriauth configuration error.'; break;
				case 2 : $error = 'Provider not properly configured.'; break;
				case 3 : $error = 'Unknown or disabled provider.'; break;
				case 4 : $error = 'Missing provider application credentials.'; break;
				case 5 : log_message('debug', 'controllers.HAuth.login: Authentification failed. The user has canceled the authentication or the provider refused the connection.');
				         //redirect();
				         if (isset($service))
				         {
				         	log_message('debug', 'controllers.HAuth.login: logging out from service.');
				         	$service->logout();
				         }
				         show_error('User has cancelled the authentication or the provider refused the connection.');
				         break;
				case 6 : $error = 'User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.';
				         break;
				case 7 : $error = 'User not connected to the provider.';
				         break;
			}

			if (isset($service))
			{
				$service->logout();
			}

			log_message('error', 'controllers.HAuth.login: '.$error);
			show_error('Error authenticating user.');
		}
	}
	
	public function endpoint()
	{
		
		log_message('debug', 'controllers.HAuth.endpoint called.');
		log_message('info', 'controllers.HAuth.endpoint: $_REQUEST: '.print_r($_REQUEST, TRUE));

		if ($_SERVER['REQUEST_METHOD'] === 'GET')
		{
			log_message('debug', 'controllers.HAuth.endpoint: the request method is GET, copying REQUEST array into GET array.');
			$_GET = $_REQUEST;
		}

		log_message('debug', 'controllers.HAuth.endpoint: loading the original HybridAuth endpoint script.');
		require_once APPPATH.'/third_party/hybridauth/index.php';

	}
	function active_member($activation_code){
		$process = $this->registermodel->active_member($activation_code);
		if($process['status']==1){
				$this->session->set_flashdata('success_login',$process['message']);
				redirect('/dashboard');
		}else{
				echo $process['message'];
		}
		
	}
}