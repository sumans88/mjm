<?php
class web_configuration_model extends  CI_Model{
	var $table            = 'web_config';
	var $tableAs          = 'web_config a';
    function __construct(){
       parent::__construct();
	   $this->load->model('model_user');
	   $user = $this->model_user->findById(id_user());
	   $this->approvalLevelGroup = $user['approval_level'];
    }
	function records($where=array(),$isTotal=0){
		// $alias['search_name'] = 'a.name';
		// $alias['search_category'] = 'b.type_name';

	 	query_grid($alias,$isTotal);
		$this->db->select('a.*');
		// $this->db->join('web_config_type b', 'b.id = a.type_id', 'left');
		$this->db->where_in('a.id',array(1,2,5,7,9,11,18));
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
	function update($data,$id){
		$where['id'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function updateKedua($data,$id){
		$where['id_parent_lang'] 	= $id;
		$data['user_id_modify'] 	= id_user();
		$data['modify_date'] 		= date('Y-m-d H:i:s');
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
	function findById($id){
		$where['a.id'] = $id;
		$where['a.is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		// $this->db->select('a.*');
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