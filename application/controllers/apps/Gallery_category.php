<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery_category extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('galleryModel');
		$this->load->model('gallery_category_model');
		$this->load->model('galleryImagesModel');
		$this->load->model('galleryDetailModel');
		$this->load->model('languagemodel');
		$this->load->model('tagsmodel');
		$this->load->model('newstagsmodel');
		$this->load->model('eventtagsmodel');
		$this->load->model('newstagsversionmodel');
		$this->load->model('gallerytagsmodel');
	}
	function index(){
		$data['list_status_publish']   = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		render('apps/gallery_category/index',$data,'apps');
	}
	public function add($id=''){
		if($id){
			$datas 	= $this->gallery_category_model->findById($id);

			if(!$datas){
				die('404');
			}
			$data 					= quote_form($datas);
			$data['judul']			= 'Edit';
			$data['proses']			= 'Update';
			$data['publish_date'] 	= iso_date($data['publish_date']);
			$data['id'] 			= $id;
		}else{
			$data['judul']            = 'Add';
			$data['proses']           = 'Save';
			$data['tags']             = '';
			$data['name']             = '';
			$data['id']               = '';
			$data['publish_date']     = '';
		}

		$tags_data = $this->newstagsmodel->records_tags_all();
		foreach ($tags_data as $key => $value_tags) {
		    $tags_data_val .=  ',"'.$value_tags['name'].'"';
		}
		$data['tags'] = substr($tags_data_val,1);

		$data['name']             = $datas['name'];
		$data['id_status_publish']        = $datas['id_status_publish'];
		$data['list_status_publish'] 	 = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$datas['id_status_publish']));

		/*untuk tag*/
				$this->db->select('a.*,b.name as tags,b.uri_path');
				$this->db->join('tags b','b.id = a.id_tags');
		$tags = $this->db->get_where('gallery_category_frontend_tags a',array('id_category'=>$datas['id']))->result_array();
		$tag = '';
		foreach ($tags as $k => $v){
			$tag .=  ','.$v['tags'];
		}
		$data['tags_data'] 				= substr($tag,1);
		/*end untuk tag*/

		render('apps/gallery_category/add',$data,'apps');
	}

	function records(){
		$data = $this->gallery_category_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['name'] = $value['name'];
			$data['data'][$key]['listtags'] 	= tags_records($value['list_tags']);
		}
		render('apps/gallery_category/records',$data,'blank');
	}
	function proses($idedit=''){
		$id_user 				= id_user();
		$this->layout 			= 'none';
		$post					= purify($this->input->post(NULL,TRUE));
		$ret['error']			= 1;
		$id_parent_lang 		= NULL;
		$this->db->trans_start(); 
		// $id_parent		= $this->languagemodel->langId();

		// foreach ($post['name'] as $key => $value){
			if(!$idedit){
				$this->form_validation->set_rules('name', '"Category Name"', 'required'); 
				if ($this->form_validation->run() == FALSE){
					$ret['message']  	= validation_errors(' ',' ');
				}
			}
			
			$data_save['name']                = $post['name'];
			$data_save['id_status_publish']   = $post['id_status_publish'];
			
			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Gallery Category";
				$iddata 		= $this->gallery_category_model->update($data_save,$idedit);
			}else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Gallery Category";
				$iddata 		= $this->gallery_category_model->insert($data_save);
				$idedit 		= $iddata;
			}

			detail_log();
			insert_log($act);
			$this->db->trans_complete();
			set_flash_session('message', $ret['message']);
			$ret['error'] = 0;


			/*untuk tags*/
			$tags = $post['tags'];
			foreach ($tags as $k => $v) {
				$tag = strtolower($v);

				/*untuk mengecek apakah tag sudah ada apa belum*/
				$whr['name']      = $tag;
				$whr['is_delete'] = 0;
				$cek = $this->db->get_where('tags',$whr)->row_array();

				if(!$cek){
					$t['name']           = $tag;
					$t['uri_path']       = url_title($tag);
					$t['create_date']    = date('Y-m-d H:i:s');
					$t['user_id_create'] = id_user();
					$this->db->insert('tags',array_filter($t));
					$idTags = $this->db->insert_id();
				}
				else{
					$idTags = $cek['id'];
				}
				/*end untuk mengecek apakah tag sudah ada apa belum*/

				/*untuk mengecek apakah tag sudah ada di event ini */
				$whr2['a.id_category'] = $idedit;
				$whr2['a.id_tags']     = $idTags;
									   $this->db->select('a.*');
				$cek2 				 = $this->db->get_where('gallery_category_frontend_tags a',$whr2)->row_array();

				if(!$cek2){
					$newsTags['id_category']    = $idedit;
					$newsTags['id_tags']        = $idTags;
					$newsTags['create_date']    = date('Y-m-d H:i:s');
					$newsTags['user_id_create'] = id_user();
					$this->db->insert('gallery_category_frontend_tags',array_filter($newsTags));
				}
				/*untuk mengecek apakah tag sudah ada di event ini */

				/*mengambil id tags dalam event*/
				$idGalleryTag[]	 = $idTags;
			}

			if ($idGalleryTag && $idedit) {
								   $this->db->where('id_category',$idedit);
								   $this->db->where_not_in('id_tags',$idGalleryTag);
				$deleteEventTags = $this->db->delete('gallery_category_frontend_tags');
			}
			/*end untuk tags*/
		// }
		echo json_encode($ret);
	}

	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->gallery_category_model->delete($id);
		detail_log();
		insert_log("Delete Gallery");
	}
}

/* End of file gallery.php */
/* Location: ./application/controllers/apps/gallery.php */