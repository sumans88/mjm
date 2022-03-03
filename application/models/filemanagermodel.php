<?php
class FileManagerModel extends  CI_Model{
	var $table = 'file_manager';
	var $tableAs = 'file_manager a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
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
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where=array(),$field='*',$is_single_row=0){
		$this->db->select($field);
		$this->db->where('is_delete',0);
		$this->db->order_by('id','desc');
		if($is_single_row==1){
			return 	$this->db->get_where($this->table,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->table,$where)->result_array();
		}
	} 
	/**
	 * Get total data
	 * @return integer;
	 * @param array $where  An associative array to be passed;
	 * @param string $field  Default *;
	 */
	function getTotal($where=array(), $field='*') {
		$this->db->select($field);
		$this->db->where('is_delete',0);
		$this->db->order_by('id','desc');
		return 	$this->db->get_where($this->table,$where)->num_rows();
	}
	/**
	 * Get all data
	 * @return array;
	 * @param array $where  An associative array to be passed;
	 * @param integer $perPage  Data limit;
	 * @param integer $offset  Data offset;
	 */
	function getAll($where=array(), $perPage, $offset){
		$this->db->select('*');
		$this->db->where('is_delete',0);
		$this->db->order_by('id','desc');
		$this->db->limit($perPage, $offset);
		return 	$this->db->get_where($this->table,$where)->result_array();
	}
	/**
	 * Search data
	 * @return mixed(optional);
	 * @param array $where  An associative array to be passed;
	 */
	function searchPic($where=array()){
		$this->db->select('*');
		$this->db->where('is_delete',0);
		$this->db->order_by('id','desc');
		return 	$this->db->get_where($this->table,$where);
	}
 }
