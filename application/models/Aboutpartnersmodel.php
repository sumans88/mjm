<?php
class aboutpartnersmodel extends  CI_Model{
	var $table = 'about_partners';
	var $tableAs = 'about_partners a';
	function __construct(){
		parent::__construct();

	}
	function records($where=array(),$isTotal=0){
		$lang = default_lang_id();
		$alias['search_title'] = 'a.title';
		$alias['search_url'] = 'a.url';
		$alias['search_partners_category'] = 'b.id';
		$alias['search_status_publish'] = 'c.id';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

		query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as partners_category, c.name as status_publish");
		$this->db->join('partners_category b', 'b.id = a.id_partners_category', 'left');
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->where('a.is_delete',0);
		// $this->db->where('a.id_parent_lang !=',0);
		$this->db->where('a.id_lang',$lang);
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
	function updateKedua($data,$id){
		$where['id_parent_lang'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
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
	function selectData($id,$is_single_row=0){
		$this->db->where('is_delete',0);
		$this->db->where('id',$id);
		$this->db->or_where('id_parent_lang',$id);
		if($is_single_row==1){
			return 	$this->db->get_where($this->table)->row_array();
		}else{
			return 	$this->db->get_where($this->table)->result_array();
		}
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
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	
	function fetchRow($where) {
		return $this->findBy($where,1);
	}

	function get_all_partners($isTotal=0,$id_lang=0){
		$alias['search_title']             = 'a.title';
		$alias['search_partners_category'] = 'b.id';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();
		$id_lang = $this->session->userdata('id_lang');

		query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as partners_category");
		$this->db->join('partners_category b', 'b.id = a.id_partners_category', 'left');
		$this->db->where('a.is_delete',0);
		$this->db->where('b.id_lang',$id_lang);
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

}
