<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Contactus extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index(){
    	$post = purify($this->input->post());
    	if($post){
    		$post['id_contact_us_topic'] =  $post['topic'];
    		$this->form_validation->set_rules('topic', '"Topik"', 'required'); 
    		$this->form_validation->set_rules('message', '"Komentar"', 'required'); 
    		$this->form_validation->set_rules('fullname', '"Nama"', 'required'); 
    		$this->form_validation->set_rules('hp', '"Handphone"', 'required'); 
    		$this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
    		$this->form_validation->set_rules('city', '"Kota"', 'required'); 
			if ($this->form_validation->run() == FALSE){
				 $message = validation_errors();
				 $status = 'error';
			}
			else{
	    		$this->load->model('contactUsModel');
                $email = $post['email'];
				$post['topic_name'] = $this->db->select('name')->get_where('contact_us_topic',"id='$post[id_contact_us_topic]'")->row()->name;
				$post['contact_date'] = iso_date_custom_format(date('Y-m-d H:i:s'),'d-m-Y H:i:s');
	    		unset($post['topic']);
	    		$proses = $this->contactUsModel->insert($post);
	    		if($proses){
	    			$status = 'success';
	    			$message = "Terima Kasih untuk saran & masukkan Anda. Permintaan yang kami terima akan kami proses dalam 5 hari kerja.";
	    		}
	    		else{
	    			$status = 'error';
	    			$message = "Maaf, silakan ulangi kembali";
	    		}
			}

	        $data['message'] 	=  "<div class='$status'> $message</div>";
	        $data['status'] 	=  $status;
	        echo json_encode($data);

    	}
    	else{
    		$this->lang->load("general",LANGUAGE);
        	$lang = $this->lang->language;
	        $data['fb_like_widget'] = fb_like_widget();
	        $data['ads_widget']     = ads_widget();
	        $data['list_topic'] 	= selectlist2(array('table'=>'contact_us_topic','order'=>'id','title'=>$lang['lang_choose_topic']));
	        render('contactus',$data);
    	}
    }
}