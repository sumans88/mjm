<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sector extends CI_Controller{



	function __construct(){

		parent::__construct();

		$this->load->model('Sector_model');

	}



	function index()

	{

		render('apps/sector/index',$data,'apps');

	}



	public function add($id=''){

		if($id){

			$data = $this->Sector_model->findById($id);

			if(!$data){

				die('404');

			}

			$data 					= quote_form($data);

			$data['judul']			= 'Edit';

			$data['proses']			= 'Update';

		}

		else{

			$data['judul']			= 'Add';

			$data['proses']			= 'Save';

			$data['name'] 			= '';

			$data['uri_path']		= '';

			$data['teaser']			= '';

			$data['page_content']	= '';

			$data['id'] 			= '';

		}

		$img_thumb						= image($data['img'],'small');

		$imagemanager					= imagemanager('img',$chair_img_thumb);

		$data['img']					= $imagemanager['browse'];

		$data['imagemanager_config']	= $imagemanager['config'];

		$data['list_status_publish'] = selectlist2(
			array(
				'table'=>'status_publish',
				'selected'=>$data['id_status_publish']
				)
			);

		render('apps/sector/add',$data,'apps');

	}



	public function view($id=''){

		if($id){

			$data = $this->Sector_model->findById($id);

			$data['img_thumb'] 	= image($data['img'],'small');

			$data['img_ori'] 	= image($data['img'],'large');

			if(!$data){

				die('404');

			}

			$data['page_name'] 	= quote_form($data['page_name']);

			$data['teaser'] 	= quote_form($data['teaser']);

		}

		render('apps/sector/view',$data,'apps');

	}



	function records(){
		$data = $this->Sector_model->records();

		render('apps/sector/records',$data,'blank');

	}



	function proses($idedit=''){

		$this->layout 		= 'none';

		$post 				= purify($this->input->post());
		$post['page_content']= htmlspecialchars_decode(urldecode($post['page_content']));
	
		$ret['error']		= 1;

		$where['uri_path'] 	= $post['uri_path'];

		if($idedit){

			$where['id !='] = $idedit;

		}


		$unik 					= $this->Sector_model->findBy($where);

		$this->form_validation->set_rules('name', '"page Name"', 'required');

		$this->form_validation->set_rules('uri_path', '"Page URL"', 'required');

		/*$this->form_validation->set_rules('teaser', '"Teaser"', 'required');*/

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

				$act			= "Update Sector";


				if(!$post['img']){

					unset($post['img']);

				}

				$this->Sector_model->update($post,$idedit);

			}

			else{

				auth_insert();

				$ret['message'] = 'Insert Success';

				$act			= "Insert Sector";

				$this->Sector_model->insert($post);

			}

			detail_log();

			insert_log($act);

			$this->db->trans_complete();

			$this->session->set_flashdata('message',$ret['message']);

			$ret['error'] = 0;

		}

		echo json_encode($ret);

	}



	function del(){

		auth_delete();

		$id 	= $this->input->post('iddel');

		$data 	= $this->Sector_model->delete($id);

		detail_log();

		insert_log("Delete Sector");

	}



	function select_page(){

		render('apps/sector/select_page',$data,'blank');



	}



	function record_select_page(){

		$data = $this->Sector_model->records();

		foreach ($data['data'] as $key => $value) {

			$data['data'][$key]['page_name'] = quote_form($value['page_name']);

		}

		render('apps/sector/record_select_page',$data,'blank');

	}



}



/* End of file pages.php */

/* Location: ./application/controllers/apps/committee.php */

