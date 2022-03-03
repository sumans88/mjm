<?php
class gallery_category_model extends  CI_Model{
	var $table = 'gallery_category_frontend';
	var $tableAs = 'gallery_category_frontend a';
    function __construct(){
       parent::__construct();
    }
	function records($where=array(),$isTotal=0){
		$alias['search_name'] = 'a.name';
		$alias['search_listtags'] = "c.name";

		$alias['search_status'] = 'd.id';

	 	query_grid($alias,$isTotal);

		$this->db->select("a.*, group_concat(c.name SEPARATOR ' // ') as list_tags, d.name as status");
		$this->db->where('a.is_delete',0);

		$this->db->join('gallery_category_frontend_tags b','b.id_category = a.id','LEFT');
		$this->db->join('tags c','c.id = b.id_tags','LEFT');
		$this->db->join('status_publish d', 'd.id = a.id_status_publish', 'left');
		
		$this->db->group_by('a.id');
		
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

	function findBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$this->db->select('a.*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	
	function fetchRow($where) {
		return $this->findBy($where,1);
		
	}
 }
