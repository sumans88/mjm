<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Editors_Choice_Partners extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('AboutPartnersModel');
		$this->load->model('LanguageModel');
		$this->load->library('session');
	}
	function index(){
		$data['list_languages'] 		= selectlist2(array('table'=>'language','title'=>'Select Language','where'=>'is_delete = 0','name'=>'name'));
		render('apps/editors_choice_partners/index',$data,'apps');
	}

	function get_editor_choice(){
		$post 				= $this->input->post();

		$ret['error']		= 1;
		if ($post['id_lang']) {
			$id_lang_session	= array(
				'id_lang' => $post['id_lang'],
				);
			$this->session->set_userdata($id_lang_session);
			$this->db->order_by('a.sort_is_featured', 'asc');
			$ret 	= $this->AboutPartnersModel->findby(array('a.id_lang'=>$post['id_lang'],'a.is_featured' =>'1'));
			
			/*jika not available maka akan mengambl title dari news parent yang available*/
			foreach ($ret as $key => $value) {
				if($value['id_partners_category']){
					$ret[$key]['category_name'] = db_get_one('partners_category','name',array('id'=>$value['id_partners_category'])).' - ';
					// print_r($this->db->last_query());exit;
				}else{
					$ret[$key]['category_name'] = '';
				}
				// if ($value['is_not_available'] == 1 && $value['id_status_publish'] == 1) {

				// 	if ($value['id_parent_lang'] != Null) {
				// 		$whr_get['id'] = $value['id_parent_lang'];
				// 	} else {
				// 		$whr_get['id_parent_lang'] = $value['id'];
				// 	}
				// 	$get_news_available = $this->db->get_where('news',$whr_get)->row_array();

				// 	$ret[$key]['news_title'] = $get_news_available['news_title'].' (Only '.ucwords(db_get_one('language','name',array('id'=>$get_news_available['id_lang']))).' Language News)';
				// }
			}
			/*end jika not available maka akan mengambl title dari news parent yang available*/
			unset($ret['error']);
		}
		echo json_encode($ret);
	}

	function record_select_category(){
		$id_lang 	= $this->session->userdata('id_lang');
		$data 		= $this->AboutPartnersModel->get_all_partners(0,$id_lang);
		render('apps/editors_choice_partners/record_select_category',$data,'blank');
	}
	function select_category(){
		$id_lang = $this->input->post('id_lang');
		$this->session->set_userdata(array('id_lang'=>$id_lang));
		$data['list_partners_category'] = selectlist2(array('table'=>'partners_category','title'=>'Select Category','where'=>'is_delete = 0 and id_lang = '.$id_lang));
		render('apps/editors_choice_partners/select_category',$data,'blank');
	}

	function process(){
		$post 							= purify($this->input->post(Null,TRUE));
		$value							= ($post['value']) ? explode(',',$post['value']) : '';
		$id_lang						= $post['id_lang'];
		// $data_insert['id_lang'] 		= $id_lang;

		$sort = 0;
		$idin = array();
		if ($value) {
			for ($x = 0; $x < count($value); $x++) {
				$data 							= explode('_',$value[$x]);
				$id1 							= $data[0];
				$idin[]	= $id1;
				
				$id2    						= ($id_lang==1) ? db_get_one('partners_category','id',array('id_parent_lang'=>$id1)) : db_get_one('partners_category','id_parent_lang',array('id'=>$id1));
				$idin[]	= $id2;
				
				$idx = array($id1,$id2);
				
				$datins1['create_date']            = date('Y-m-d H:i:s');
				$datins1['user_id_create']         = id_user();
				$datins1['modify_date']            = date('Y-m-d H:i:s');
				$datins1['user_id_modify']         = id_user();
				$datins1['sort_is_featured'] = ++$sort;
				$datins1['is_featured']       = 1;
				// $data_insert['id_lang'] 				= $id_lang;
						  
						  // $this->db->where('id_status_publish',2);
						  // $this->db->where('is_not_available',0);
						  $this->db->where_in('id',$idx);
				$insert = $this->db->update('about_partners',$datins1);
				// popular_article_generate(1,$id_lang);
			}
		} else {
			$idin = '';
		}
		
		// print_r($idin);exit();
		// $this->db->where('id_lang',$id_lang);
		if ($idin) {
			$this->db->where_not_in('id',$idin);
		}
		$this->db->update('about_partners',array('is_featured' => 0, 'sort_is_featured'=>NULL));
		if($insert){
			$data['error_status'] = 'success';
			$data['msg'] = 'Success to save';
		}else{
			$data['error_status'] = 'error';
			$data['msg'] = "there's somethings wrong";
		}
		echo json_encode($data);
	}
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/editors_choice_partners.php */