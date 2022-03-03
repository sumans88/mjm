<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Upload_campaign extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('upload_campaignModel');
	}
	
	
	function index(){
		
		$data['list_status_campaign'] = selectlist2(array('table'=>'status_campaign','title'=>'All Status','selected'=>$data['status_campaign']));
		$data['list_category_campaign'] = selectlist2(array('table'=>'category_campaign','title'=>'All Category','selected'=>$data['id_category_campaign']));

		render('apps/upload_campaign/index',$data,'apps');
		
	}
	
	
	public function add($id=''){
		if($id){
			$data = $this->upload_campaignModel->findById($id);
			if(!$data){
			    die('404');
		    }
		    $data['judul']	= 'Edit';
		    $data['proses']	= 'Update';
			$data['edit_save']	= 'save';
		  
		    $data['publish_date'] = iso_date($data['publish_date']);
		  	$data = quote_form($data);
		    $data['edit_file'] = 'hidden';
		   
	    } else {	    
			$data['edit_file_stat']  = 'data-parsley-required="true"';
			$data['edit_save']	= 'save_with_file';
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['add'] = '';
			$data['edit_file'] = '';
			$data['title'] = '';
			$data['description']		= '';
			$data['publish_date']	= date('d-m-Y');
			$data['id'] 			= '';
			$data['url'] 			= '';
		}
		$data['list_status_campaign'] = selectlist2(array('table'=>'status_campaign','title'=>'All Status','selected'=>$data['status_campaign']));
		$data['list_category_campaign'] = selectlist2(array('table'=>'category_campaign','title'=>'All Category','selected'=>$data['id_category_campaign']));

		render('apps/upload_campaign/add',$data,'apps');
		
	}
		
		
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$file = $_FILES;
		$ret['error']			= 1;
		$this->form_validation->set_rules('title', '"title"', 'required'); 
		$this->form_validation->set_rules('description', '"Description"', 'required'); 
		$this->form_validation->set_rules('status_campaign', '"Status"', 'required'); 
		$this->form_validation->set_rules('id_category_campaign', '"Category Campaign"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		} else {   
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Campaign";
				$this->upload_campaignModel->update($post,$idedit);
				$ret['error'] = 0;
			} else {
				$file = $_FILES['file_name'];
				$fname = $file['name'];
				$data['title'] = '';

				if ($fname) {
					$maxFileSize = MAX_UPLOAD_SIZE_CHEETAH / 1000000;
					if(!is_writable(EXPERIAN_CAMPAIGN_DIR)){
						$ret['error'] = 1;
						$ret['message'] = "Directory is readonly";
					} else if($file['size'] >= MAX_UPLOAD_SIZE_CHEETAH){
						$ret['error'] = 1;
						$ret['message'] = "Max File size is ".$maxFileSize." MB";
					}else if($fname) {
					// if(!file_exists($folder)){
					// 	mkdir($folder);
					// }
					$upload_dir = EXPERIAN_CAMPAIGN_DIR;
					$allowed_types = 'pdf|doc|docx';
					$upload = upload_file('file_name','',$allowed_types,'',$upload_dir);
					$insert = array('file_name'=>$upload['file_name'],'file_size'=>$upload['file_size'],'file_type'=>$upload['file_type']);

					$insert = array_merge($insert,$post);
					$this->insertToDB($insert);
				
					$ret['message'] = 'success';
				}
			
				$this->session->set_flashdata('message',$ret['message']);
				$ret['error'] = 0;
				} else {
					$ret['message'] = 'error';
				}
			}
		}
		echo json_encode($ret);
	}
	
	function records(){
		$data = $this->upload_campaignModel->records();
		foreach($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] = iso_date($value['create_date']);
			if($value['process_date'] == NULL) {
				$data['data'][$key]['hidden'] = 'show';
			} else {
				$data['data'][$key]['hidden'] = 'hide';
			}
		}
		render('apps/upload_campaign/records',$data,'blank');
	}

	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->upload_campaignModel->delete($id);
		detail_log();
		insert_log("Delete Upload");
	}

	function insertToDB($data){
		return $this->upload_campaignModel->insert($data);
	}
}