<?php
class NewsCategoryModel extends  CI_Model{
	var $table = 'news_category';
	var $tableAs = 'news_category a';
	var $id_lang_default = 0;
    function __construct(){
       parent::__construct();
       $this->id_lang_default = default_lang_id();
    }

	function records($where=array(),$isTotal=0){
		$alias['search_name'] = 'a.name';
		$alias['search_uri_path'] = 'a.uri_path';
		$alias['search_parent_name'] = 'b.name';
		$alias['search_exactor_event_category'] = 'b.id_or_b.id_parent_category';
		// $alias['search_category'] = 'b.id';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as parent_name");
		$this->db->join('news_category b ', 'b.id = a.id_parent_category', 'left');
		// $this->db->select(".*");
		$this->db->where('a.is_delete',0);
		$this->db->where('a.id_lang',$this->id_lang_default);
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
 }
