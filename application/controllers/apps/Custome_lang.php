<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Custome_lang extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('customeLangModel');
		$this->load->model('languageModel');
	}
	function index(){
		render('apps/custome_lang/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			// $data = $this->customeLangModel->findById($id);
			$datas 	= $this->customeLangModel->selectData($id);

			if(!$datas){
				die('404');
			}

			$data 				= quote_form($datas);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';
		}else{
			$data['judul']				= 'Add';
			$data['proses']				= 'Save';
			$data['page_name_data']		= '';
			$data['uri_path']			= '';
			$data['teaser']				= '';
			$data['page_content']		= '';
			$data['id'] 				= '';
			$data['id_parent_lang']		= '';
		}

		$data['list_lang']	= $this->languageModel->langName();

		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 			= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 			= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 		= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 			= $key;

			$data['list_lang'][$key]['id']				= $datas[$key]['id'];
			$data['list_lang'][$key]['code_array']		= $datas[$key]['code'];
			$data['list_lang'][$key]['value'] 			= $datas[$key]['value'];
			$data['list_lang'][$key]['description']		= $datas[$key]['description'];
		}
		$data['list_lang2'] 	= $data['list_lang'];
		render('apps/custome_lang/add',$data,'apps');
	}

	public function view($id=''){
		if($id){
			$datas 	= $this->customeLangModel->selectData($id);
			// $data = $this->customeLangModel->findById($id);
			if(!$datas){
				die('404');
			}
			$data['list_lang']	= $this->languageModel->langName();
			foreach ($data['list_lang'] as $key => $value){
				$data['list_lang'][$key]['invis'] 			= ($key==0) ? '' : 'hide';
				$data['list_lang'][$key]['active'] 			= ($key==0) ? 'active in' : '';
				$data['list_lang'][$key]['validation'] 		= ($key==0) ? 'true' : 'false';
				$data['list_lang'][$key]['nomor'] 			= $key;

				$data['list_lang'][$key]['code_array']		= $datas[$key]['code'];
				$data['list_lang'][$key]['value'] 			= $datas[$key]['value'];
				$data['list_lang'][$key]['description']		= $datas[$key]['description'];
				$data['list_lang'][$key]['id_parent_lang']	= $datas[$key]['id_parent_lang'];
				$data['list_lang'][$key]['nomor'] 			= $key+1;
			}
			$data['list_lang2'] = $data['list_lang'];
		}
		render('apps/custome_lang/view',$data,'apps');
	}
	function records(){
		$data = $this->customeLangModel->records();
		foreach ($data['data'] as $key => $value){
			$data['data'][$key]['code_array']		= $data[$key]['code'];
			$data['data'][$key]['value'] 			= $data[$key]['value'];
			$data['data'][$key]['description']		= $data[$key]['description'];
		}
		render('apps/custome_lang/records',$data,'blank');
	}	
	function generate(){
		$this->layout 		= 'none';
		$ret['error']		= 1;
		$id_parent_lang 	= NULL;
		$this->db->trans_start();

		$list_lang	= $this->languageModel->langName();
		foreach ($list_lang as $key => $value){
			$id_lang 		= $list_lang[$key]['lang_id'];
			$name_lang 		= $list_lang[$key]['lang_name'];
			$data 			= '';
			$custome_lang 	= $this->customeLangModel->customeData($value['lang_id']);

			foreach ($custome_lang as $key1 => $value1){
				$code 		 = $custome_lang[$key1]['code'];
				$content 	 = $custome_lang[$key1]['value'];
				$data 		.= "$"."lang['$code'] 	= '$content'".";\n";
			}
			$data 			= "<?php"."\n".$data."?>";
			$generate 				= generate_custome_lang_file($name_lang,$data);
		}
		
		$ret['message'] 	= 'Regenerate Success';
		$this->db->trans_complete();
		set_flash_session('message', $ret['message']);
		$ret['error'] = 0;
		echo json_encode($ret);
		redirect('apps/custome_lang');



		// $data = "asd"

		// $bahasa = select from language where is_delete=0
		// foreach($bahasa){
		// 	$custom = select custom_lang where id_lang = id_bahasa and is_delete = 0
		// 	$data = "";
		// 	foreach($custom){
		// 		$data .= "$lang['ftp_no_connection']			= 'Unable'; /n";
		// 	}
		// 	generate_custome_lang_file($nama_bahasa, $data);
		// }

		// pake dot
		// $data hasilnya asd$lang====

		// gk pake dot
		// $data hasilnya $lang
	}
	function proses($idedit=''){
		$id_user 			= id_user();
		$this->layout 		= 'none';
		$post				= purify($this->input->post());
		$ret['error']		= 1;
		$id_parent_lang 	= NULL;
		$this->db->trans_start();

		foreach ($post['code'] as $key => $value){
			$this->form_validation->set_rules('code', '"Code"', 'required'); 
			$this->form_validation->set_rules('value', '"Value"', 'required'); 
			$this->form_validation->set_rules('description', '"Description"', 'required'); 
			
			if($this->form_validation->run() == FALSE){
				$ret['message']  = validation_errors(' ', ' ');
			}
			
			if($key==0){
				$idedit		= $post['id'][$key];
				$code 		= $post['code'][$key];
			}
				
			$data_save['code']				= $code;
			$data_save['value']				= $post['value'][$key];
			$data_save['description'] 		= $post['description'][$key];
			$data_save['id_lang']		 	= $post['id_lang'][$key];
			$data_save['id_parent_lang'] 	= $id_parent_lang;

			if($idedit){
				if($key==0){
					auth_update();
					$ret['message'] 	= 'Update Success';
					$act				= "Update Pages";
					$iddata 			= $this->customeLangModel->update($data_save,$idedit);
				}else{
					auth_update();
					$ret['message']		= 'Update Success';
					$act				= "Update Pages";
					$iddata 			= $this->customeLangModel->updateKedua($data_save,$idedit);
				}
			}else{
				auth_insert();
				$ret['message'] 	= 'Insert Success';
				$act				= "Insert Pages";
				$iddata 			= $this->customeLangModel->insert($data_save);
			}

			if($key==0){
				$id_parent_lang	= $iddata;
			}

			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			set_flash_session('message', $ret['message']);
			$ret['error'] = 0;

		}
		echo json_encode($ret);
	}
	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->customeLangModel->delete($id);
		$this->customeLangModel->delete2($id);
		detail_log();
		insert_log("Delete Pages");
	}
	function select_page(){
		render('apps/custome_lang/select_page',$data,'blank');

	}
	function record_select_page(){
		$data = $this->customeLangModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/custome_lang/record_select_page',$data,'blank');
	}
}

/* End of file custome_lang.php */
/* Location: ./application/controllers/apps/custome_lang.php */