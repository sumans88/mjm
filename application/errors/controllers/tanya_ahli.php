<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tanya_ahli extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index(){
    	$post = purify($this->input->post());
    	if($post){
    		$post['id_contact_us_topic'] =  $post['topic'];
    		$this->form_validation->set_rules('id_category', '"Kategori"', 'required'); 
    		$this->form_validation->set_rules('sub_category', '"Sub Kategori"', 'required'); 
			if ($this->form_validation->run() == FALSE){
				 $message = validation_errors();
				 $status = 'error';
			}
			else if(!id_member()){
				$message = 'notlogin';
				$status = 'error';
			}
			else{
	    		$this->load->model('memberAskExpertModel');
                $email = $post['email'];
	    		unset($post['topic']);
	    		$proses = $this->memberAskExpertModel->insert($post);
	    		if($proses){
	    			$status = 'success';
	    			$message = "Terima Kasih.";
                    // sent_email_by_category(2,$data, $email);
	    		}
	    		else{
	    			$status = 'error';
	    			$message = "Maaf, silakan ulangi kembali";	
	    		}
			}

	        $data['message'] 	=  "<div class='$status'> $message</div>";
	        $data['message_text'] 	=  $message;
	        $data['status'] 	=  $status;
	        echo json_encode($data);

    	}
    	else{
	        $data['fb_like_widget'] = fb_like_widget();
	        $data['ads_widget']     = ads_widget();
	        $data['list_kategori'] 	= selectlist2(array('table'=>'category','name'=>'title','where'=>"is_delete = 0"));
	        render('tanya_ahli',$data);
    	}
    }
    function sub_category($id_category){
	    echo selectlist2(array('table'=>'sub_category','id'=>'code','name'=>'title','where'=>array('category_id'=>$id_category)));
    }
}