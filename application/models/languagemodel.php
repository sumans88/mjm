<?php
class LanguageModel extends  CI_Model
{
	var $table = 'language';
	var $tableAs = 'language a';
    
    function __construct(){
       parent::__construct();
    
	   
    }

	function records($where=array(),$isTotal=0)
	{
		$alias['search_title'] = 'a.name';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*");
		$this->db->where('a.is_delete',0);
		$query = $this->db->get($this->tableAs);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}

	function insert($data)
	{
		$this->db->insert($this->table, array_filter($data));
		return $this->db->insert_id();
    }

	function update($data,$id){
		$where['id'] = $id;
		// $data['user_id_modify'] = id_user();
		// $data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] 		= 1;
		$data['status_lang'] 	= 0;
		$this->update($data,$id);
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
			return 	$this->db->get_where($this->tableAs, $where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs, $where)->result_array();
		}
	}
	
	function fetchRow($where)
	{
		return $this->findBy($where, 1);
	}

	function langId()
	{
		$this->db->select("id");
		$query = $this->db->get_where($this->table, array('status_lang' => 1, 'is_delete' => 0));
		return $query->row('id');
	}
	function langIdDefault($is_single_row='')
	{
		$this->db->select('id as id_lang');
		$query = $this->db->get_where($this->table, array('is_delete' => 0, 'status_lang' => 1));
		// return $query;
		if($is_single_row==1){
			return $query->row_array();
		}
		else{
			return $query->result_array();
		}
	}
	// function langId(){
	// 	// $this->db->select("id");
	// 	$this->db->order_by('status_lang', 'desc');
	// 	return $this->db->get_where('language', array('is_delete' => 0))->result_array();
	// }

	function langName(){
		$this->db->select("name as lang_name, id as lang_id");
		$this->db->order_by('status_lang', 'desc');
		return $this->db->get_where('language', array('is_delete' => 0))->result_array();
	}
	
	function langNameSelected($id){
		$this->db->select("name");
		return $this->db->get_where($this->table, array('is_delete' => 0, 'id' => $id))->result_array();
	}

 }
