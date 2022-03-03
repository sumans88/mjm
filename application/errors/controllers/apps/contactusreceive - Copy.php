<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ContactUsReceive extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('contactusreceiveModel');
		$this->load->model('languageModel');
	}
	function index(){
		$data['list_topic'] = selectlist2(array('table'=>'contact_us_topic','title'=>'All Topic','selected'=>$data['id_topic']));
		render('apps/contact_us_receive/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$datas 	= $this->contactusreceiveModel->selectData($id);
            
            if(!$datas){
				die('404');
			}

			$data 			= quote_form($datas);
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			
		}else{
			$data['judul']		= 'Add';
			$data['proses']		= 'Simpan';
            $data['id'] 		= '';
			$data['email'] 		= '';
			$data['id_topic'] 	= '';
		}

		$data['list_lang']	= $this->languageModel->langName();
		
		foreach ($data['list_lang'] as $key => $value){
			$data['list_lang'][$key]['invis'] 		= ($key==0) ? '' : 'hide';
			$data['list_lang'][$key]['active'] 		= ($key==0) ? 'active in' : '';
			$data['list_lang'][$key]['validation'] 	= ($key==0) ? 'true' : 'false';
			$data['list_lang'][$key]['nomor'] 		= $key;

			$data['list_lang'][$key]['id']			= $datas[$key]['id'];
			$data['list_lang'][$key]['email']		= $datas[$key]['email'];
			$data['list_lang'][$key]['list_topic']	= selectlist2(array('table'=>'contact_us_topic','title'=>'All Topic','selected'=>$datas[$key]['id_topic']));
		}
		$data['list_lang2']	= $data['list_lang'];

		render('apps/contact_us_receive/add',$data,'apps');
	}
	function records(){
		$data = $this->contactusreceiveModel->records();
		render('apps/contact_us_receive/records',$data,'blank');
	}
	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->contactusreceiveModel->delete($id);
		$this->contactusreceiveModel->delete2($id);
		$this->db->trans_complete();
	}
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		
        $this->form_validation->set_rules('email', '"Email"', 'required'); 

		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}else{
			$this->db->trans_start();   
			$id_parent_lang = NULL;
			foreach ($post['email'] as $key => $value){
				
				if($key==0){
					$id_topic 			= $post['id_topic'][$key];
					$idedit			 	= $post['id'][$key];
				}

				$data_save['email']			 	= $post['email'][$key];
				$data_save['id_lang'] 			= $post['id_lang'][$key];
				$data_save['id_topic']			= $id_topic;
				$data_save['id_parent_lang'] 	= $id_parent_lang;
				
				if($idedit){
					if($key==0){
					 	auth_update();
						$ret['message'] = 'Update Success';
						$act			= "Update Frontend menu";
						$iddata 		= $this->contactusreceiveModel->update($data_save,$idedit);
					}else{
						auth_update();
						$ret['message'] = 'Update Success';
						$act			= "Update Frontend menu";
						$iddata 		= $this->contactusreceiveModel->updateKedua($data_save,$idedit);
						// print_r($iddata);
					}
				}else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Frontend menu";
					$iddata 		= $this->contactusreceiveModel->insert($data_save);
				}

				if($key==0){
					$id_parent_lang = $iddata;
				}

				detail_log();
				insert_log($act);
				$this->db->trans_complete();
				set_flash_session('message', $ret['message']);
				$ret['error'] = 0;
			}
		}
		echo json_encode($ret);
	}
}

/* End of file contactusreceive.php */
/* Location: ./application/controllers/apps/contactusreceive.php */