<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Route_url extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('RouteUrlModel');
		$this->load->model('languagemodel');
	}
	function index(){
		render('apps/route_url/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			// $data = $this->RouteUrlModel->findById($id);
			$datas 	= $this->RouteUrlModel->selectData($id);

			if(!$datas){
				die('404');
			}

			$data 				= quote_form($datas);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';
		}else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['title']			= '';
			$data['page_name_data']		= '';
			$data['controller']		= '';
			$data['value']			= '';
			$data['slug']			= '';
			$data['description'] 	= '';
			$data['id_lang']		= '';
			$data['id']				= '';
		}

		$data['list_lang']	= $this->languagemodel->langName();

		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 			= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 			= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 		= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 			= $key;

			$data['list_lang'][$key]['id']				= $datas[$key]['id'];
			$data['list_lang'][$key]['title']			= $datas[$key]['code'];
			$data['list_lang'][$key]['controller']		= $datas[$key]['controller'];
			$data['list_lang'][$key]['slug'] 			= $datas[$key]['slug'];
			$data['list_lang'][$key]['value'] 			= $datas[$key]['value'];
			$data['list_lang'][$key]['description']		= $datas[$key]['description'];
		}

		$data['list_lang2'] 	= $data['list_lang'];
		render('apps/route_url/add',$data,'apps');
	}
	// public function save_routes(){
 //        // Ambil data permalink dari database
 //        $routes = $this->PermalinkModel->getdata_permalink();

 //        // Membuat content yang akan ditulis pada file permalink.php
 //        foreach( $routes as $value ){
 //            $data[] = '$route["' . $value['slug'] . '"] = "' . $value['value'] .'";';
 //        }

 //        $output = implode("\n", $data);

 //        $this->load->helper('file');
 //        write_file(APPPATH . "config/custom_routes.php", $output);
 //    }
	public function checkTitle(){
		$title = $post['title'];
		$this->RouteUrlModel->selectDataAda($title);
	}
	public function view($id=''){
		if($id){
			$datas 	= $this->RouteUrlModel->selectData($id);
			// $data = $this->RouteUrlModel->findById($id);
			if(!$datas){
				die('404');
			}
			$data['list_lang']	= $this->languagemodel->langName();
			foreach ($data['list_lang'] as $key => $value){
				$data['list_lang'][$key]['invis'] 			= ($key==0) ? '' : 'hide';
				$data['list_lang'][$key]['active'] 			= ($key==0) ? 'active in' : '';
				$data['list_lang'][$key]['validation'] 		= ($key==0) ? 'true' : 'false';
				$data['list_lang'][$key]['nomor'] 			= $key;

				$data['list_lang'][$key]['title']			= $datas[$key]['code'];
				$data['list_lang'][$key]['controller'] 		= $datas[$key]['controller'];
				$data['list_lang'][$key]['value'] 			= $datas[$key]['value'];
				$data['list_lang'][$key]['slug'] 			= $datas[$key]['slug'];
				$data['list_lang'][$key]['description']		= $datas[$key]['description'];
				$data['list_lang'][$key]['id_parent_lang']	= $datas[$key]['id_parent_lang'];
				$data['list_lang'][$key]['nomor'] 			= $key+1;
			}
			$data['list_lang2'] = $data['list_lang'];
		}
		render('apps/route_url/view',$data,'apps');
	}
	function records(){
		$data = $this->RouteUrlModel->records();
		foreach ($data['data'] as $key => $value){
			$data['data'][$key]['title']			= $data[$key]['code'];
			$data['data'][$key]['controller']		= $data[$key]['controller'];
			$data['data'][$key]['value'] 			= $data[$key]['value'];
			$data['data'][$key]['description']		= $data[$key]['description'];
			$data['data'][$key]['slug'] 			= $data[$key]['slug'];
			$data['data'][$key]['id_parent_lang']	= $data[$key]['id_parent_lang'];

		}
		render('apps/route_url/records',$data,'blank');
	}	
	function generate(){
		$this->layout 		= 'none';
		$ret['error']		= 1;
		$id_parent_lang 	= NULL;
		$this->db->trans_start();

		$route_url 	= $this->RouteUrlModel->customeData();

		foreach ($route_url as $key => $value){
			$slug 		 = $route_url[$key]['slug'];
			$content 	 = $route_url[$key]['value'];
			$controller	 = $route_url[$key]['controller'];
			$data 		.= "$"."route['$slug'] 	= '$controller'".";\n";
		}
		$data 			= "<?php"."\n".$data."?>";
		$generate 		= generate_route_url_file($data);

		$ret['message'] 	= 'Regenerate Success';
		$ret['error'] 		= 0;
		$this->db->trans_complete();
		set_flash_session('message', $ret['message']);
		echo json_encode($ret);
		redirect('apps/route_url');
	}
	function proses($idedit=''){
		$id_user 			= id_user();
		$this->layout 		= 'none';
		$post				= purify($this->input->post());
		$ret['error']		= 1;
		$id_parent_lang 	= NULL;
		$this->db->trans_start();

		foreach ($post['title'] as $key => $value){
			$this->form_validation->set_rules('title', '"Title"', 'required'); 
			$this->form_validation->set_rules('controller', '"Controller"', 'required'); 
			$this->form_validation->set_rules('value', '"Value"', 'required'); 
			$this->form_validation->set_rules('description', '"Description"', 'required'); 
			
			$title 	= $post['title'][$key];
			$ada 	= $this->RouteUrlModel->selectDataAda($title);

			if($ada){
				$ret['message']		= 'Title $value already exist';
			}elseif($this->form_validation->run() == FALSE){
				$ret['message']  	= validation_errors(' ', ' ');
			}else{
				if($key==0){
					$idedit		= $post['id'][$key];
				}
					
				$data_save['code']				= $post['title'][$key];
				$data_save['controller']		= $post['controller'][$key];
				$data_save['value']				= $post['value'][$key];
				$data_save['description'] 		= $post['description'][$key];
				$data_save['slug'] 				= $post['slug'][$key];
				$data_save['id_lang']		 	= $post['id_lang'][$key];
				$data_save['id_parent_lang'] 	= $id_parent_lang;
				

				if($idedit){
					if($key==0){
						auth_update();
						$ret['message'] 	= 'Update Success';
						$act				= "Update Pages";
						$iddata 			= $this->RouteUrlModel->update($data_save,$idedit);
					}else{
						auth_update();
						$ret['message']		= 'Update Success';
						$act				= "Update Pages";
						$iddata 			= $this->RouteUrlModel->updateKedua($data_save,$idedit);
					}
				}else{
					auth_insert();
					$ret['message'] 	= 'Insert Success';
					$act				= "Insert Pages";
					$iddata 			= $this->RouteUrlModel->insert($data_save);
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
			$this->db->trans_complete();
			set_flash_session('message', $ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}
	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->RouteUrlModel->delete($id);
		$this->RouteUrlModel->delete2($id);
		detail_log();
		insert_log("Delete Pages");
	}
	function select_page(){
		render('apps/route_url/select_page',$data,'blank');

	}
	function record_select_page(){
		$data = $this->RouteUrlModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/route_url/record_select_page',$data,'blank');
	}
}

/* End of file route_url.php */
/* Location: ./application/controllers/apps/route_url.php */