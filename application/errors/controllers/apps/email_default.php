<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class email_default extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('EmailDefaultModel');
	}
	function index(){
		render('apps/email_default/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->EmailDefaultModel->findById($id);
            if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data = quote_form($data);
		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
            $data['id'] 			= '';
		}
        $img_thumb					= image($data['img'],'small');//is_file_exsist(UPLOAD_DIR.'small/',$data['img']) ? ($this->baseUrl.'uploads/small/'.$data['img']) : '';
		$imagemanager				= imagemanager('img',$img_thumb,750,186);
		$data['img']				= $imagemanager['browse'];
		$data['imagemanager_config']= $imagemanager['config'];

		$data['list_email_template'] = selectlist2(array('table'=>'email_tmp','name'=>'template_name','where'=>array("id_ref_email_category"=>$id),'title'=>'Select Email Template','selected'=>$data['id_email_tmp']));

		render('apps/email_default/add',$data,'apps');
	}
	function records(){
		$data = $this->EmailDefaultModel->records();
		render('apps/email_default/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		
        $this->form_validation->set_rules('id_email_tmp', '"Email Template"', 'required'); 

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{   
			$this->db->trans_start();   
				if($idedit){
                    auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Email Template";
					$this->EmailDefaultModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert News";
					$idedit = $this->EmailDefaultModel->insert($post);
				}
			$this->db->trans_complete();
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */