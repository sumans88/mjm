<?php
class LogActivityModel extends  CI_Model{
	var $table = 'user_activity_log';
	var $tableAs = 'user_activity_log a';
	function __construct(){
	   parent::__construct();
	       
	}
	
	function findById($id){
		$where['a.id'] = $id;
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$where['is_delete'] = 0;
		$this->db->select('*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->table,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->table,$where)->result_array();
		}
	}
	function fetchRow($where) {
		return $this->findBy($where,1);
	}
	function tagsCounter($ids){
		if($ids){
			$this->db->query("update $this->table set tags_count = tags_count + 1 where id in($ids)");
		}
	}
	function getactiviylogmember($id_member,$page){

		$where['a.id_user'] = $id_member;
		//$this->db->select('a.*,b.news_title,b.uri_path,b.img,b.teaser,c.name as category,c.uri_path as uri_path_category');
		//$this->db->join('news b','b.id = a.id_article');
		//$this->db->join('news_category c','c.id = b.id_news_category');
		$this->db->select('a.*,b.description');
		$this->db->join('t_aegon_log_category b','b.id = a.id_log_category');
		if($page === 'all'){
			$this->db->where($where);
			$this->db->from($this->tableAs);
			return $this->db->count_all_results();
		}
		else{
			$this->db->limit(PAGING_PERPAGE_LOG,$page);
			$this->db->order_by('last_date_read','desc');
			$this->db->order_by('create_date','desc');
			return $this->db->get_where($this->tableAs,$where)->result_array();
		}

	}
 }
