<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contactus extends CI_Controller {

	function __construct(){

		parent::__construct();

		

	}

     function index(){

    	$post   = purify($this->input->post());

		$reload = false;

    	$this->data['recaptcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;

    	if($post){

    		$this->form_validation->set_rules('name', '"Name"', 'required'); 

    		$this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 

    		$this->form_validation->set_rules('phone_number', '"Phone"', 'required'); 

    		$this->form_validation->set_rules('message', '"Message"', 'required'); 

			if ($this->form_validation->run() == FALSE){

				 $message = validation_errors();

				 $status  = 'error';

			}else{
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
		    		$this->load->model('contactUsModel');

					// $post['topic_name'] = $this->db->select('name')->get_where('contact_us_topic',"id='$post[id_contact_us_topic]'")->row()->name;

					$post['contact_date'] = iso_date_custom_format(date('Y-m-d H:i:s'),'d-m-Y H:i:s');

					unset($post['g-recaptcha-response'], $post['action']);

		    		$proses               = $this->contactUsModel->insert($post);

		    		if($proses){

	            		sent_email_by_category(14,$post,'');

		    			$status  = 'success';

		    			$message = "Thanks, Your message  will be proses 5 day work.";

		    		}

		    		else{

		    			$status  = 'error';

		    			//error database

		    			$message = "Sorry, Please Try Again proses";

		    			$reload = true;

		    		}
		        } else {   

		        	$status  = 'error';

		        	//error post google 

			    	$message = "Sorry, Please Try Again";

		    		$reload = true;
		        }
			}

	        $data['message'] = "$message";

	        $data['status']  = $status;

	        $data['reload']  = $reload;

	        echo json_encode($data);

    	}

    	else{

    		$data['page_content'] = $this->parser->parse("layout/ddi/contact_us_form.html",$this->data,true); 



    		$data['hide_breadcrumb'] = 'hidden';

    		$data['page_name'] = 'Contact Us';



    		if($data['seo_title'] == ''){

    		    $data['seo_title'] = "MJM";

    		}



    		$data['amcham_committe_list'] = '';

    		$data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);

    		$data['banner_top']       = banner_top();

    		$data['widget_sidebar']   = widget_sidebar();


			$data['active_contactus'] = 'active';

    		render('contactus',$data); 

    	}

    }

}