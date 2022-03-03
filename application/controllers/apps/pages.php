<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('pagesModel');
		$this->load->model('languageModel');
	}
	function index()
	{
		// $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
		render('apps/pages/index', $data, 'apps');
	}
	public function add($id = '')
	{
		if ($id) {
			// $data = $this->pagesModel->findById($id);
			$datas 	= $this->pagesModel->selectData($id, 1);

			if (!$datas) {
				die('404');
			}
			$data 				= quote_form($datas);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';
			$data['id']			= $id;
			$data['page_name_data']	= $datas['page_name'];
			$data['dsp_main_content']	    = ($datas['is_main_content'] != 0) ? 'invis' : '';
			$data['required_main_content']	= ($datas['is_main_content'] != 0) ? 'false' : 'true';
			$data['json_people'] = json_encode(
				$this->db
					->order_by('sort', 'asc')
					->get_where('pages_profile', ['id_pages' => $id])
					->result_array()
			);
			$data['ttlPeople'] = $this->db->where('id_pages', $id)->from('pages_profile')->count_all_results();
		} else {
			$data['judul']				= 'Add';
			$data['proses']				= 'Save';
			$data['page_name_data']		= '';
			$data['uri_path']			= '';
			$data['teaser']				= '';
			$data['page_content']		= '';
			$data['id'] 				= '';
			$data['id_parent_lang']		= '';
			$data['seo_title']			= '';
			$data['meta_description'] 	= '';
			$data['meta_keywords']		= '';
			$data['dsp_main_content'] 	= '';
			$data['required_main_content'] 	= 'true';
			$data['json_people'] 		= '[]';
		}
		$img_thumb		= image($datas['img'], 'small');
		$imagemanager	= imagemanager('img', $img_thumb, 145, 195, '', '', 'title');
		$data['img']	= $imagemanager['browse'];

		$data['imagemanager_config']		= $imagemanager['config'];

		$this->db->where('id', '36');
		$data['list_parent'] = selectlist2(
			array(
				'table'=>'pages',
				'name'=>'page_name',
				'title'=>'Select Parent Page',
				'selected'=>$data['id_parent']
		));

		// $data['list_lang']	= $this->languageModel->langName();
		render('apps/pages/add', $data, 'apps');
	}
	public function view($id = '')
	{
		if ($id) {
			$datas 	= $this->pagesModel->selectData($id);
			// $data = $this->pagesModel->findById($id);
			if (!$datas) {
				die('404');
			}
			$data['list_lang']	= $this->languageModel->langName();
			foreach ($data['list_lang'] as $key => $value) {
				$data['list_lang'][$key]['invis'] 			= ($key == 0) ? '' : 'hide';
				$data['list_lang'][$key]['active'] 			= ($key == 0) ? 'active in' : '';
				$data['list_lang'][$key]['validation'] 		= ($key == 0) ? 'true' : 'false';

				$data['list_lang'][$key]['img_thumb'] 		= image($datas[$key]['img'], 'small');
				$data['list_lang'][$key]['img_ori'] 		= image($datas[$key]['img'], 'large');
				$data['list_lang'][$key]['page_name'] 		= $datas[$key]['page_name'];
				$data['list_lang'][$key]['page_content'] 	= $datas[$key]['page_content'];
				$data['list_lang'][$key]['teaser'] 			= $datas[$key]['teaser'];
				$data['list_lang'][$key]['nomor'] 			= $key + 1;
				$data['list_lang'][$key]['id_parent_lang'] 	= $datas[$key]['id_parent_lang'];
				$data['list_lang'][$key]['seo_title']		= $datas[$key]['seo_title'];
				$data['list_lang'][$key]['meta_description'] = $datas[$key]['meta_description'];
				$data['list_lang'][$key]['meta_keywords']	= $datas[$key]['meta_keywords'];
			}
			$data['list_lang2'] = $data['list_lang'];
		}
		render('apps/pages/view', $data, 'apps');
	}
	function records()
	{
		$data = $this->pagesModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['is_main_content'] = ($value['is_main_content'] == 1) ? 'invis' : '';
		}
		render('apps/pages/records', $data, 'blank');
	}

	function proses($idedit = '')
	{
		$id_user 				= id_user();
		$this->layout 			= 'none';
		$post					= purify($this->input->post());
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start();
		// $id_parent		= $this->languageModel->langId();
		// page data
		if (!$idedit) {
			$where['uri_path']			= $post['uri_path'][0];
			$unik 	= $this->pagesModel->findBy($where);
			$this->form_validation->set_rules('page_name', '"page Name"', 'required');
			$this->form_validation->set_rules('uri_path', '"Page URL"', 'required');
			$this->form_validation->set_rules('teaser', '"Teaser"', 'required');
			$this->form_validation->set_rules('seo_title', '"SEO Title"', 'required');
			$this->form_validation->set_rules('meta_description', '"Meta Description"', 'required');
			$this->form_validation->set_rules('meta_keyword', '"Meta Keyword"', 'required');
			if ($this->form_validation->run() == FALSE) {
				$ret['message']  	= validation_errors(' ', ' ');
			}
			if ($unik) {
				$ret['message']		= "Page URL " . $post['uri_path'][0] . " already taken";
			}
		}



		$data_save['page_name']			= $post['page_name'][0];
		$data_save['teaser']			= $post['teaser'][0];
		// $data_save['page_content'] 		= $post['page_content'][0];
		$data_save['page_content'] 		= htmlspecialchars_decode(urldecode($post['page_content']));
		$data_save['uri_path'] 			= $post['uri_path'][0];
		$data_save['id_lang']		 	= 1;
		$data_save['id_parent']		 	= $post['id_parent'][0];
		$data_save['id_parent_lang'] 	= $id_parent_lang;
		$data_save['seo_title']			= $post['seo_title'][0];
		$data_save['meta_description']	= htmlspecialchars_decode(urldecode($post['meta_description']));
		$data_save['meta_keywords']		= $post['meta_keywords'][0];

		if ($post['imgDelete'][0] == 0) {
			if ($idedit && $post['img'][0]) {
				$data_save['img']	= $post['img'][0];
			} elseif ($idedit) {
				$datas 				= $this->pagesModel->selectData($idedit, 1);
				$data_save['img']	= $datas['img'];
			} else {
				$data_save['img']	= $post['img'][0];
			}
		} else {
			$data_save['img'] = NULL;
		}

		if ($idedit) {

			auth_update();
			$ret['message'] = 'Update Success';
			$act			= "Update Pages";

			$iddata 		= $this->pagesModel->update($data_save, $idedit);
		} else {
			auth_insert();
			$ret['message'] = 'Insert Success';
			$act			= "Insert Pages";
			$iddata 		= $this->pagesModel->insert($data_save);
			// print_r($unik);
		}

		detail_log();
		insert_log($act);
		$this->db->trans_complete();
		set_flash_session('message', $ret['message']);
		$ret['error'] = 0;

		// person
		foreach ($post['list_id'] as $key => $value) {
			$person[$key] = [
				'id' => $value,
				'name' => $post['list_name'][$key],
				'position' => $post['list_position'][$key],
				'teaser' => $post['list_teaser'][$key],
				'description' => $post['list_description'][$key],
				'is_row' => $post['list_is_row'][$key] == 'on' ? true : false,
				'sort' => $key + 1,
				'id_pages' => $iddata
			];
			if(!empty($post['list_img'][$key])){
				$person[$key]['img'] = $post['list_img'][$key];
			}
			if (empty($post['list_img'][$key]) && !empty($post['is_delete_list_img'][$key]) ) {
				$person[$key]['img'] = null;
			}
		}
		$id_user = id_user();
		foreach ($person as $key => $value) {
			if ($value['id']) {
				// edit
				$person_id_update[] = $value['id'];
				$person[$key]['user_id_modify'] = $id_user;
				$person[$key]['modify_date'] = datetime_today();
				$update_person[] = $person[$key];
			} else {
				// tambah
				unset($person[$key]['id']);
				$person[$key]['user_id_create'] = $id_user;
				$person[$key]['create_date'] = datetime_today();
				$insert_person[] = $person[$key];
			}
		}
		if (!empty($person_id_update) || empty($post['list_id'])) {
			if (!empty($person_id_update)) {
				$this->db->where_not_in('id', $person_id_update);
			}
			$this->db->where('id_pages', $iddata);
			$this->db->delete('pages_profile');
		}
		if (!empty($update_person)) {
			$this->db->update_batch('pages_profile', $update_person, 'id');
		}
		if (!empty($insert_person)) {
			$this->db->insert_batch('pages_profile', $insert_person);
		}
		echo json_encode($ret);
	}
	function del()
	{
		auth_delete();
		$id = $this->input->post('iddel');
		$this->pagesModel->delete($id);
		$this->pagesModel->delete2($id);
		detail_log();
		insert_log("Delete Pages");
	}
	function select_page()
	{
		render('apps/pages/select_page', $data, 'blank');
	}
	function record_select_page()
	{
		$data = $this->pagesModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['page_name'] = quote_form($value['page_name']);
		}
		render('apps/pages/record_select_page', $data, 'blank');
	}
}

/* End of file pages.php */
/* Location: ./application/controllers/apps/pages.php */
