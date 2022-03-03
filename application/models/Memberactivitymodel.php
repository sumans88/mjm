<?php
class memberActivityModel extends  CI_Model{
	var $table = 'user_activity_log';
	var $tableAs = 'user_activity_log a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_activity'] = 'b.description';
		$alias['search_activity_id'] = 'b.id';
		$alias['search_datestart'] = 'a.datestart';
		$alias['search_dateend'] = 'a.dateend';
	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.description as activity, c.namadepan, c.namabelakang, c.process_time, c.email");
		$this->db->join('t_aegon_log_category b','b.id = a.id_log_category');
		$this->db->join('t_aegon_profile_member c','c.id = a.id_user');

		$query = $this->db->get($this->tableAs);
		echo $this->db->last_query();
		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		return ddi_grid($data,$ttl_row);
	}
	
	function fetchRow($where) {
		return $this->findBy($where,1);
	}

	function export_to_excel($where,$is_single_row=0){
		$this->db->select("a.*,b.description as activity, c.namadepan, c.namabelakang, c.process_time, c.email");
		$this->db->join('t_aegon_log_category b','b.id = a.id_log_category');
		$this->db->join('t_aegon_profile_member c','c.id = a.id_user');
		if($is_single_row==1){
			return $this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return $this->db->get_where($this->tableAs,$where)->result_array();
		}
		
	}

	function records_tags_all($where=array(),$isTotal=0){

		$this->db->select("a.*");
		$query = $this->db->get('t_aegon_log_category a');

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		return $data;
	}
	
 }
