<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Language extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('languageModel');
	}
	function index(){
		// $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
		render('apps/language/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->languageModel->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data['name_laguage']	= $data['name'];
			$data = quote_form($data);
		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['name_laguage']		= '';
			$data['uri_path']		= '';
			$data['teaser']			= '';
			$data['page_content']	= '';
			$data['id'] 			= '';
		}
		// $data['img']				= image($data['img'],'small','thumb');
		// $imagemanager				= imagemanager();
		// $data['img']				= $imagemanager['browse'];
		// $data['imagemanager_config']= $imagemanager['config'];

		render('apps/language/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$data = $this->languageModel->findById($id);
			$data['img_thumb'] = image($data['img'],'small');
			$data['img_ori'] = image($data['img'],'large');
			if(!$data){
				die('404');
			}
			$data['page_name'] = quote_form($data['page_name']);
			$data['teaser'] = quote_form($data['teaser']);
		}
		render('apps/language/view',$data,'apps');
	}
	function records(){
		$data = $this->languageModel->records();
		foreach ($data['data'] as $key => $value) {
			$status_lang = $value['status_lang'];
			
			if ($status_lang != 0) {
				$data['data'][$key]['status_lang'] = 'Default';
			}
			else{
				$data['data'][$key]['status_lang'] = '<a href="javascript:void(0)" class="chg_stat" data-lang="'.$value['id'].'">Change Default</a>';
			}
		}
		render('apps/language/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		// $where['uri_path']		= $post['uri_path'];
		// if($idedit){
		// 	$where['id !=']		= $idedit;
		// }
		// $unik 					= $this->languageModel->findBy($where);
		$this->form_validation->set_rules('name', '"Language"', 'required'); 
		// $this->form_validation->set_rules('uri_path', '"Page URL"', 'required'); 
		// $this->form_validation->set_rules('teaser', '"Teaser"', 'required'); 
		// $this->form_validation->set_rules('page_content', '"Content"', 'required'); 
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
					$act			= "Update Pages";
					// if(!$post['img']){
					// 	unset($post['img']);
					// }
					$this->languageModel->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Pages";
					$this->languageModel->insert($post);
				}
			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->languageModel->delete($id);
		detail_log();
		insert_log("Delete Pages");
	}
	function select_page(){
		render('apps/language/select_page',$data,'blank');

	}
	function record_select_page(){
		$data = $this->languageModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/language/record_select_page',$data,'blank');
	}
	function change_stat(){
		$id = $this->input->post('data');

		$cek_data_default = $this->db->get_where('language', array('status_lang' => 1))->num_rows();
		if ($cek_data_default > 0) {
			$this->db->where('status_lang', 1);
			$this->db->update('language', array('status_lang'=>0));
		}

		$this->db->where('id',$id);
		$this->db->update('language',array('status_lang'=>1));
	}
}

/* End of file pages.php */
/* Location: ./application/controllers/apps/language.php */