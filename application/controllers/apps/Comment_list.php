<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comment_List extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('commentListModel');
	}
	function index(){
		// $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
		render('apps/comment_list/index',$data,'apps');
	}
	function records(){
		$data = $this->commentListModel->records();
		// echo $this->db->last_query();exit();		
		/*
		foreach ($data['data'] as $key => $value) {
			if($value['page_category'] == 1){
				$whrParent['id'] = $value['id_parent'];
				$get_data_parent = $this->db->get_where('news',$whrParent)->row_array();
				$whrParentCat['id'] = $get_data_parent['id_news_category'];
				$get_data_page_cat = $this->db->get_where('news_category',$whrParentCat)->row_array();
				$data['data'][$key]['page_cat'] = $get_data_page_cat['name'];
				$data['data'][$key]['id_page'] = $get_data_page_cat['id'];
			} else if($value['page_category'] == 2){
				$whrParent['id'] = $value['id_parent'];
				$get_data_parent = $this->db->get_where('gallery',$whrParent)->row_array();
				$data['data'][$key]['parent_title'] = $get_data_parent['id_gallery_category'];	
				$data['data'][$key]['page_cat'] = "Infographic";
				$data['data'][$key]['id_page'] = 5;
			}
		} */
		//echo var_dump($data);exit();
		//echo var_dump($data);
		//echo "<script>console.log('aaa');</script>---d";exit();
		render('apps/comment_list/records',$data,'blank');
	}
	function detail($id){
		$id_cat = $this->session->userdata('commentlist_cat');
		$data = $this->commentListModel->findById($id_cat,$id);
		//echo "<script>console.log('".var_dump($data)."');</script>---d";exit();
		render('apps/comment_list/detail',$data,'blank');
	}
	public function view($cat='',$pageid=''){
		if($cat && $pageid){
			$data 	= $this->commentListModel->selectData($cat,$pageid,1);

			if(!$data){
				die('404');
			} 
			$data['page_category'] = $cat;
			$data['id_page'] = $pageid;
			$this->session->set_userdata('commentlist_cat', $cat);
			$this->session->set_userdata('commentlist_page', $pageid);
			render('apps/comment_list/view',$data,'apps');
		}
		else{
			die('404');
		}
	}
	function recordscomment(){
		$id_cat = $this->session->userdata('commentlist_cat');
		$id_page = $this->session->userdata('commentlist_page');
		$data = $this->commentListModel->recordsComment($id_cat, $id_page);

		render('apps/comment_list/recordscomment',$data,'blank');
	}
	function export_to_excel(){
		$post = $this->input->post();

		$alias['search_title'] = 'a.name';
		$alias['search_topic'] = 'b.name';
		where_grid($post, $alias);
		$data['data'] = $this->contactUsModel->export_to_excel();
		$i=1;
		foreach ($data['data'] as $key => $value) {
			$message = substr($value['komentar'],0,150);
			if(strlen($value['komentar']) > 150){
				$message .= ' ...';
			}
			$data['data'][$key]['komentar'] = $message;
			$data['data'][$key]['nomor'] = $i++;
		}
		render('apps/contactus/export_to_excel',$data,'blank');
		export_to('Contact Us.xls');
	}
}

/* End of file contactus.php */
/* Location: ./application/controllers/apps/contactus.php */