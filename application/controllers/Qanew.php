<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class QaNew extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index(){
        $this->load->model('newsCategoryModel');
        $category = $this->newsCategoryModel->fetchRow(array('uri_path'=>'qanew/index'));
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
				$this->session->set_flashdata('session_qanew_success','true');
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
			$data['header_category'] = $category['name'];
			$data['top_article'] = top_article_expert($category['id'],0);
			$data['popular_topic'] = popular_topic();
			$data['ads_widget'] = ads_widget();
			//$data['fb_like_widget'] = '';
			$data['qa_widget_mobile'] = qa_widget_mobile();
			$data['fb_like_widget'] = fb_like_widget();
			$data['ask_expert_widget'] = ask_expert_widget();
			$data['fb_like_widget_data'] = fb_like_widget();
			$data['new_article'] = new_article(0,4,0,1);
			$data['list_kategori'] 	= selectlist2(array('table'=>'category','name'=>'title','where'=>"is_delete = 0"));
			if(!DEVELOPMENT_MEMBER){
				$data['qa_widget'] = qa_widget();
			}
			$user_sess_data = $this->session->userdata('MEM_SESS');
			if($user_sess_data){
				$data['is_login'] = 'hide';
				$data['is_login_form'] = '';
			} else {
				$data['is_login'] = '';
				$data['is_login_form'] = 'disabled';
			}
			render('article/article_category',$data);
			//render('article/article_category_qanew',$data);
		}
	}
    
	function sub_category($id_category){
		echo selectlist2(array('table'=>'sub_category','id'=>'code','name'=>'title','title'=>language('choose_one'),'where'=>array('category_id'=>$id_category)));
	}
	function thanks_page(){
		$user_sess_data = $this->session->flashdata('session_qanew_success');
		$user_sess_topic = $this->session->flashdata('session_qanew_topic');

		if($user_sess_data){
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article']= popular_article(0);
			$data['new_article']    = new_article(1,2,0,$user_sess_topic);
			$data['new_expert']    = new_expert(1,1);
			render('layout/ddi/qanew_thanks_page',$data);
		} else{
			redirect('tidakditemukan');
		}
	}
	function ask_expert(){
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
				$category = $post['id_category'];
				unset($post['topic']);
				$proses = $this->memberAskExpertModel->insert($post);
				if($proses){
					$this->load->model('registerModel');
					$data_now = date('Y-m-d H:i:s');
					$log_user_activity = array(
						'id_user'       =>  id_member(),
						'create_date' 	=>  $data_now,
						'last_date_read' 	=>  $data_now,
						'id_ask_expert' =>  $proses,
						'id_log_category'=>  36,
						'ipaddress' => $_SERVER['REMOTE_ADDR'],
						'ismobile' => $_SERVER['HTTP_USER_AGENT']
					);
					$query = $this->db->insert('user_activity_log', $log_user_activity);
					$status = 'success';
					$message = "Terima Kasih.";
					$this->session->set_flashdata('session_qanew_topic',convert_id_category_ask_expert($category));
					$this->session->set_flashdata('session_qanew_success','true');
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
	
		}else{
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article']= popular_article(0);
			$data['new_article']    = new_article(1,2);
			$data['new_expert']    = new_expert(1,1);
			
			$data['list_kategori'] 	= selectlist2(array('table'=>'category','title'=>language('choose_one'),'name'=>'title','where'=>"is_delete = 0"));
			$user_sess_data = $this->session->userdata('MEM_SESS');
			if($user_sess_data){
				$data['is_login'] = 'hide';
				$data['is_login_form'] = '';
				$data['disabled'] = '';
			} else {
				$data['is_login'] = '';
				$data['disabled'] = 'disabled';
				$data['is_login_form'] = 'disabledinput';
			}
			
			render('layout/ddi/ask_expert',$data);
		}
	}
}