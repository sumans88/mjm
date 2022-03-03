<?php
class frontendmenumodel extends  CI_Model{
	var $table = 'frontend_menu';
	var $tableAs = 'frontend_menu a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(), $isTotal=0){
		$alias['search_name'] 			= 'a.name';
		$alias['search_menu_position'] 	= 'a.id_menu_position';
		$alias['search_parent'] 		= 'a.id_parent';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as menu_position,c.name as parent,d.name as status_publish");
		$this->db->where('a.is_delete',0);
		$this->db->where('a.id_parent_lang is null');
		$this->db->join('menu_position b','b.id = a.id_menu_position');
		$this->db->join($this->table.' c','c.id = a.id_parent','left');
		$this->db->join('status_publish d','d.id = a.id_status_publish');
		$query = $this->db->get_where($this->tableAs,$where);

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
		$this->db->insert($this->table,$data);
		return $this->db->insert_id();
	}
	function update($data, $id){
		$where['id'] 			= $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table, $data, $where);
		return $id;
	}
	function updateKedua($data, $id){
		$where['id_parent_lang']	= $id;
		$data['user_id_modify'] 	= id_user();
		$data['modify_date'] 		= date('Y-m-d H:i:s');
		$this->db->update($this->table, $data, $where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function delete2($id){
		$data = array(
            'is_delete' => 1,
            'user_id_modify' => id_user(),
            'modify_date' => date('Y-m-d H:i:s'),
        );
        $this->db->where('id_parent_lang', $id);
        $this->db->update($this->table, $data);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['a.id_parent_lang'] = $id;
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a', $where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$where['a.id_status_publish'] = 2;
		$this->db->select('a.*,b.controller');
		$this->db->join('module b','b.id = a.id_module','left');
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

	function selectData($id,$is_single_row=0)
	{
		$this->db->where('is_delete',0);
		$this->db->where('id_parent_lang', $id);
		$this->db->or_where('id', $id);
		if($is_single_row==1){
			return 	$this->db->get_where($this->table)->row_array();
		}
		else{
			return 	$this->db->get_where($this->table)->result_array();
		}
		// return $this->db->get($this->table)->result_array();
	}
 }
