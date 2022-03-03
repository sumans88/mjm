<?php
class HomeAdminModel extends  CI_Model{
	var $table = 'comment';
	var $tableAs = 'comment a';
	function __construct(){
	   parent::__construct();
	   $this->load->model('model_user');
	   $user = $this->model_user->findById(id_user());
    
	}
	function count_new_article($where=array()){
		$query = $this->db->query('SELECT count(id_news) as count_news FROM comment where is_delete=2 and flag > 0')->row_array();
		
		return $query['count_news'];
	}
	/**
	 * Get log editor choice
	 * @return mixed;
	 * @param array $where  Optional conditions;
	 * @param string $isTotal  default 0;
	 */
	function log_editor_choice($where=array(),$isTotal=0){
		$alias['search_uri_path'] = 'e.uri_path';
		$alias['search_status_publish'] = 'c.id';
		$alias['search_id'] = 'e.id';
		$alias['search_news_category'] = 'b.id';
		

	 	query_grid($alias,$isTotal);
		$this->db->select("e.*,a.is_delete as is_active, b.name as news_category,c.name as status,d.username, a.create_date as date_group");
		$this->db->where('e.is_delete',0);
		$this->db->join('news e',"e.id = a.id_news",'left');
		$this->db->join('news_category b',"b.id = e.id_news_category",'left');
		$this->db->join('status_publish c',"c.id = e.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = e.user_id_create",'left');
		$query = $this->db->get_where('editors_choice_log a',$where);
		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->log_editor_choice($where,1);
		
		return ddi_grid($data,$ttl_row);
	}
	/**
	 * Get log top contents
	 * @return mixed;
	 * @param array $where  Optional conditions;
	 * @param string $isTotal  default 0;
	 */
	function log_top_content($where=array(),$isTotal=0){
		$alias['search_uri_path'] = 'e.uri_path';
		$alias['search_status_publish'] = 'c.id';
		$alias['search_id'] = 'e.id';
		$alias['search_news_category'] = 'b.id';
		

	 	query_grid($alias,$isTotal);
		$this->db->select("e.*,a.is_delete as is_active, b.name as news_category,c.name as status,d.username, a.create_date as date_group, a.id_category as category_top");
		$this->db->where('e.is_delete',0);
		$this->db->join('news e',"e.id = a.id_news",'left');
		$this->db->join('news_category b',"b.id = e.id_news_category",'left');
		$this->db->join('status_publish c',"c.id = e.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = e.user_id_create",'left');
		$query = $this->db->get_where('top_article_log a',$where);
		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->log_top_content($where,1);
		
		return ddi_grid($data,$ttl_row);
	}
 }
