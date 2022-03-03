<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_directory extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('Member_directory_model');
	}
	public function index()
	{
		$this->add();
	}
	public function add(){
		$data = $this->db->get('member_directory')->row_array();
		$data['name'] = anchor(site_url().'./file_upload/'.$data['name'], $data['name']);
		$data['judul']                    = 'Edit';
		$data['proses']                   = 'Update';
		$data['list_status_publish']   = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));

		$img_thumb											= image($data['banner'],'small');
		$imagemanager										= imagemanager('img1',$img_thumb,	200,110,'',$data['banner'],'banner');
		$data['img']						= $imagemanager['browse'];
		$data['imagemanager_config']		= $imagemanager['config'];
		render('apps/member_directory/add',$data,'apps');
	}
	function proses	($idedit=''){
		$this->layout 			= 'none';
		$post					= purify($this->input->post());
		
		$ret['error'] = 0;
		$ret['message'] = 'Success!';
		// image
		if ($post['imgDelete1'][0] == 1) {
			$post['banner'] = '';
		}else{
			$img = $post['img1'][0];
			if (!empty($img)) {
				$post['banner'] = $img;
			}
		}
		unset($post['imgDelete1'] , $post['img1']);
		// end image



		if (empty($post['name'])) {
			unset($post['name']);
		}else{
			$post['name'] = str_replace(' ','_',$post['name']);
			
			if (!$this->do_upload()) {
				$ret['error'] = 1;
				$ret['message'] = 'please contact Administrator!';
			}
		}
		$this->Member_directory_model->update($post,$idedit);
		echo json_encode($ret);
	}

	  private function do_upload()
        {
                $config['upload_path']          = FCPATH.'file_upload/';
                $config['allowed_types']        = '*';
                $config['max_size']             = 100000;
                $config['max_width']            = 1024;
                $config['max_height']           = 768;

				$this->load->library('upload', $config);
				
				return $this->upload->do_upload('userfile') ? true : false; 
        }
}

/* End of file news.php */
/* Location: ./application/controllers/apps/news.php */