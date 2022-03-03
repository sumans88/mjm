<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_Tags extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('tagsmodel');
	}
	function index(){
		render('apps/news_tags/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->tagsmodel->findById($id);
            if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data = quote_form($data);
		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Save';
			$data['page_content']	= '';
            $data['id'] 			= '';
            $data['name']	= '';
            $data['uri_path']	= '';
			$data['seo_title']			= '';
			$data['meta_description']	= '';
			$data['meta_keywords']		= '';
		}
 
		render('apps/news_tags/add',$data,'apps');
	}
	function records(){
		$data = $this->tagsmodel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] 	= iso_date($value['create_date']);
		}
		render('apps/news_tags/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		if($idedit){
			$where['id !=']	= $idedit;
		}
		$this->form_validation->set_rules('name', '"Name"', 'required');
		$this->form_validation->set_rules('uri_path', '"Uri Path"', 'required');
		$where['uri_path']		= $post['uri_path'];
		$unik 					= $this->tagsmodel->findBy($where);

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else if($unik){
			$ret['message']	= "Page URL $post[uri_path] already taken";
		}
		else{   
			$this->db->trans_start();
				if($idedit){
                    auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update News Tags";
					$this->tagsmodel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert News Tags";
					$idedit = $this->tagsmodel->insert($post);
				}
			$this->db->trans_complete();
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->tagsmodel->delete($id);
		$this->db->trans_complete();
	}
	
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */