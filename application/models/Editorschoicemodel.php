<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EditorsChoiceModel extends CI_Model{
	
	var $table 		= 'news';
	var $tableAs 	= 'news a';
	
	function __construct(){
	   parent::__construct();
	   // $this->load->model('model_user');
	   // $user = $this->model_user->findById(id_user());
	   // $this->approvalLevelGroup = $user['approval_level'];
	}

	function records($where=array(),$isTotal=0){
		$grup = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
		$alias['search_uri_path'] 		= 'a.uri_path';
		$alias['search_status_publish'] = 'c.id';
		$alias['search_news_category'] 	= 'b.id';

		query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as news_category,c.name as status,d.username");
		//$this->db->where('a.is_delete',0)->where("(b.id=7 OR b.id=8 OR b.id=9)")->where("is_editor_choice = 1");
		$this->db->where('a.is_delete',0)->where("is_editor_choice = 1");
        $this->db->join('news_category b',"b.id = a.id_news_category",'left');
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		$this->db->order_by('a.publish_date desc');
		
		$query = $this->db->get($this->tableAs);
		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		return ddi_grid($data,$ttl_row);
	}

	function update($data,$id){
		$where['id'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}

	function fetchRow($where) {
		return $this->findBy($where,1);
	}

	function findBy($where,$is_single_row=0){
		$where['a.is_delete']         = 0;
		$where['a.is_editor_choice']  = 1;
		
		$this->db->select("a.*,b.name as news_category,c.name as status,d.username");
		//$this->db->where('a.is_delete',0)->where("(b.id=7 OR b.id=8 OR b.id=9)")->where("is_editor_choice = 1");
		// $this->db->where('a.is_delete',0)->where("is_editor_choice = 1");
        $this->db->join('news_category b',"b.id = a.id_news_category",'left');
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		$this->db->order_by('a.sort_is_editors_choice','asc');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}

	function get_all_news($isTotal=0,$id_lang=0){
		$n 	= 0;
		$alias['search_news_category'] = 'b.name';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as news_category,c.name as status,d.username");
		$this->db->join('news_category b',"b.id = a.id_news_category",'left');
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		// $this->db->where('a.id_lang',$id_lang);
		$this->db->where('a.is_delete',0);
		$this->db->where('a.id_lang',$id_lang);
		/*tambahan where*/
		$this->db->where('a.id_status_publish',2);
		$this->db->where('a.is_not_available',0);
		/*tambahan where*/
		$this->db->order_by('hits','desc');
		$this->db->where('a.publish_date <=' ,date('Y-m-d'));
		if($isTotal==0){
			$dt	= $this->db->get($this->tableAs)->result_array();
			foreach($dt as $dtx){
				$data[$n]['id']					= $dtx['id'];
				$data[$n]['news_title']			= $dtx['news_title'];
				$data[$n]['category']			= $dtx['news_category'];
				$data[$n]['top_sort']			= $dtx['top_sort'];
				$data[$n]['id_news_category']	= $dtx['id_news_category'];
				++$n;
			}
		}
		else{
			return $this->db->get($this->tableAs)->num_rows();
		}
		
		$ttl_row = $this->get_all_news(1,$id_lang);
		
		return ddi_grid($data,$ttl_row);
	}

}