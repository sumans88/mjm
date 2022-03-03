<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class I3 extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('I3Model');
	}

	function index()
	{
		$data['list_status_publish'] = selectlist2(array('table' => 'status_publish', 'title'=>'All Status', 'selected'=>$data['id_status_publish']));
		render('apps/i3/index',$data, 'apps');
	}

	public function add($id='')
	{
		if($id)
		{
			$data = $this->I3Model->findById($id);
			
            if(!$data)
            {
				die('404');
			}
			
			$data['judul']	= 'Edit';
			$data['proses']	= 'Update';
			$data = quote_form($data);
			$data['publish_date'] = iso_date($data['publish_date']);
		}
		else
		{
			$data['judul']			 = 'Add';
			$data['proses']			 = 'Simpan';
			$data['title']			 = '';
			$data['url'] 			 = '';
			$data['publish_date']	 = date('d-m-Y');
			$data['teaser'] 		 = '';
			$data['page_content']		 = '';
			$data['id'] 			 = '';
		}

		$img_thumb					 = image($data['img'], 'small');
		$imagemanager				 = imagemanager('img', $img_thumb, 750, 186);
		$data['img']				 = $imagemanager['browse'];
		$data['imagemanager_config'] = $imagemanager['config'];

		$data['list_status_publish'] = selectlist2(array('table' => 'status_publish', 'title' => 'Select Status', 'selected' => $data['id_status_publish']));
		$data['list_category'] = selectlist2(array('table' => 'category_i3', 'title' => 'Select Category', 'selected' => $data['id_category']));

		render('apps/i3/add', $data, 'apps');
	}

	function records()
	{
		$data = $this->I3Model->records();
		foreach ($data['data'] as $key => $value)
		{
			$data['data'][$key]['title'] 		  = quote_form($value['title']);
			$data['data'][$key]['publish_date']	  = iso_date($value['publish_date']);
			$data['data'][$key]['approval_level'] = $approval;
		}
		render('apps/i3/records', $data, 'blank');
	}
	
	
	function proses($idedit='')
	{
		$id_user 		=  id_user();
		$this->layout 	= 'none';
		$post 			= purify($this->input->post());
		$ret['error']	= 1;
		
		$this->form_validation->set_rules('id_status_publish', '"Status Publish"', 'required'); 
		$this->form_validation->set_rules('id_category', '"Category News"', 'required');
		$this->form_validation->set_rules('title', '"Title News"', 'required');
		$this->form_validation->set_rules('url', '"URL News"', 'required');
		$this->form_validation->set_rules('teaser', '"Teaser News"', 'required');
		$this->form_validation->set_rules('page_content', '"Content News"', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$ret['message']  = validation_errors(' ', ' ');
		}
		else
		{   
			$this->db->trans_start();   
			$post['publish_date'] = iso_date($post['publish_date']);
			if($idedit)
			{
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update News";
				if(!$post['img'])
				{
					unset($post['img']);
				}
				$this->I3Model->update($post, $idedit);
			}
			else
			{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert News";
				$idedit 		= $this->I3Model->insert($post);
			}
			$this->db->trans_complete();
			set_flash_session('message', $ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}

	function del()
	{
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->I3Model->delete($id);
		$this->db->trans_complete();
	}
	
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */