<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ref_logframe extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('ref_logframe_model','model');
	}
	function index(){
		// $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
		render('apps/ref_logframe/ref_logframe',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->model->findById($id);
			if(!$data){
				die('404');
			}
			$data['judul']	= 'Ubah';
			$data['proses']	= 'Ubah';
		}
		else{
			$data['judul']	= 'Tambah';
			$data['proses']	= 'Simpan';
			$data['name']	= '';
			$data['id'] 	= '';
		}
		render('apps/ref_logframe/add',$data,'apps');
	}
	public function view($id=''){
		if($id){
			$data = $this->model->findById($id);
			if(!$data){
				die('404');
			}
			$data['parent_kode'] = db_get_one('ref_logframe','kode',array('id'=>$data['parent_kode']));
		}
		render('apps/ref_logframe/view',$data,'apps');
	}
	
	function records(){
		$data = $this->model->records();
		render('apps/ref_logframe/records',$data,'blank');
	}	
	
	function proses($idedit=''){
		$this->layout 			= 'none';
		$post 					= $this->input->post();
		$ret['error']			= 1;
		$where['name']			= $post['name'];
		if($idedit){
			$where['id !=']		= $idedit;
		}
		$unik 					= $this->model->findBy($where);
		$this->form_validation->set_rules('name', '"name"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else if($unik){
			$ret['message']	= "Logframe title $post[title] already taken";
		}
		else{   
			$this->db->trans_start();
			$post['tanggal_awal'] = iso_date($post['tanggal_awal']);
			$post['tanggal_akhir'] = iso_date($post['tanggal_akhir']);   
				if($idedit){
					auth_update();
					$ret['message'] = 'Update Success';
					$act			= "Update Master Logframe";
					$this->model->update($post,$idedit);
				}
				else{
					auth_insert();
					$ret['message'] = 'Insert Success';
					$act			= "Insert Master Logframe";
					$this->model->insert($post);
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
		$this->model->delete($id);
		detail_log();
		insert_log("Delete Master Logframe");
	}
}

/* End of file ref_logframe.php */
/* Location: ./application/controllers/apps/ref_logframe.php */