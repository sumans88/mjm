<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_Buy extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('productbuyModel');
	}
	function index(){
        $data['list_city'] = selectlist2(array('table'=>'city','title'=>'Select City','selected'=>$data['city'],'order'=>'id'));
		render('apps/product_buy/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$data = $this->productbuyModel->findById($id);
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
			$data['nama']		= '';
			$data['id'] 			= '';
			$data['email']			= '';
			$data['mobile']	= '';
			$data['city']		= '';
		}
        $data['list_city'] = selectlist2(array('table'=>'city','title'=>'Select City','selected'=>$data['city'],'order'=>'id'));

		render('apps/product_buy/add',$data,'apps');
	}
	
	function records(){
		$data = $this->productbuyModel->records();
		foreach ($data['data'] as $key => $value) {
			
            $data['data'][$key]['create_date'] 	= iso_date_time($value['create_date']);			
			$data['data'][$key]['nama'] 		= quote_form($value['nama']);
		}
		render('apps/product_buy/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user =  id_user();
		$this->layout 			= 'none';
		$post 					= purify($this->input->post());
		$ret['error']			= 1;
		$where['a.uri_path']		= $post['uri_path'];
		$where['a.id !=']		= ($idedit) ? $idedit : '';
		$this->form_validation->set_rules('nama', '"Name"', 'required');
        $this->form_validation->set_rules('mobile', '"Mobile"', 'required');
        $this->form_validation->set_rules('city', '"City"', 'required');
        $this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
		// $this->form_validation->set_rules('id_status_publish', '"Status"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		
		else{   
			$this->db->trans_start();   
		
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update News";
				if(!$post['img']){
					unset($post['img']);
				}
				$this->productbuyModel->update($post,$idedit);
			}
			else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert News";
				$idedit = $this->productbuyModel->insert($post);
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
		$this->productbuyModel->delete($id);
		$this->db->trans_complete();
	}
	
	

}

/* End of file news.php */
/* Location: ./application/controllers/apps/product.php */