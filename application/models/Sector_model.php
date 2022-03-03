<?php
class Sector_model extends  CI_Model{
	var $table       = 'sector';
	var $tableAs     = 'sector a';
	var $tableview   = 'view_sector';
	var $tableviewAs = 'view_sector a';

  function __construct(){
       parent::__construct();
	   $this->load->model('model_user');
	   $user = $this->model_user->findById(id_user());
	   $this->approvalLevelGroup = $user['approval_level'];
  }
  
	function records($where=array(),$isTotal=0){
		$alias['search_name'] = 'a.name';

	 	query_grid($alias,$isTotal);
		$this->db->select('a.*');
		$this->db->where('((a.is_other = 0 and a.is_parent_other = 0) or (a.is_other = 1 and a.is_parent_other = 1))');
	
		$this->db->where('a.is_delete',0);
	
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
	function get_idotherparent(){
		$this->db->where('a.is_parent_other',1);
		$dataid = $this->db->get($this->tableAs)->row_array()['id'];
		return $dataid;
	}

	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function insert_frontend($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
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
	function update_frontend($data,$id){
		$where['id'] = $id;		
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
	function findviewBy($where,$is_single_row=0){
		// $where['a.is_delete'] = 0;
		// $this->db->select('a.*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableviewAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableviewAs,$where)->result_array();
		}
	}
	function committe_detail($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$this->db->select('a.*');
		$this->db->join('', 'table.column = table.column', 'left');
		if($is_single_row==1){
				return 	$this->db->get_where($this->tableview,$where)->row_array();
		}
		else{
				return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}

	function fetchRow($where) {
		return $this->findBy($where,1);
	}



 }
