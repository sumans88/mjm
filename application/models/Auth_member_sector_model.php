<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_member_sector_model extends  CI_Model{
	
	var $table 		= 'auth_member_sector';
	var $tableAs 	= 'auth_member_sector a';

    function __construct(){
       parent::__construct();
    }

	function records($where=array(),$isTotal=0){
		$grup = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
		$alias['search_uri_path'] = 'a.uri_path';
		$alias['search_status_publish'] = 'c.id';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*");
		// $this->db->select("a.*,c.name as status,d.username");
		$this->db->where('a.is_delete',0);
		// $this->db->where('a.id_parent_lang !=',0);
		// $this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		// $this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		
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
		// print_r($this->db->last_query());
		return $this->db->insert_id();
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
		// $data['user_id_modify'] = id_user();
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
		$where['a.is_delete'] = 0;
		$this->db->select('a.*');

		return 	$this->db->get_where($this->tableAs,$where)->row_array();
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
	function findByAll($where,$is_single_row=0){
		// $where['a.is_delete'] = 0;
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

/* End of file auth_member_sector_model.php */
/* Location: ./application/model/auth_member_sector_model.php */