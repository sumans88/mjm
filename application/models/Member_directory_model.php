<?php
class Member_directory_model extends  CI_Model{
	var $table = 'member_directory';
	var $tableAs = 'member_directory a';
	function __construct(){
	   parent::__construct();
	}

	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = $data['user_id_create'] ? $data['user_id_create'] : id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data, $id){
		$where['id'] 			= $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table, $data, $where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['a.is_delete'] = 0;
		$this->db->select('a.*, b.username, c.name as category,c.uri_path as uri_path_category');
		$this->db->join('auth_user b','b.id_auth_user = a.user_id_create');
		$this->db->join('news_category c','c.id = a.id_news_category');

		return 	$this->db->get_where($this->tableAs,$where)->row_array();
	}
	function findBy($where,$is_single_row=0,$page=0){
		$where['a.is_delete'] = 0;
		$this->db->select('a.*');

		if($is_single_row==1){
			return $this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return $this->db->get_where($this->tableAs,$where)->result_array();
		}
	} 
 }