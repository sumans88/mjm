<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*************************************
  * Created : Sept 27 2011
  * Created by : fatty
  * Email : ihate.haters@yahoo.com
  * Content : Authgroup_model
  * Project : 
  * CMS ver : CI ver.2
*************************************/


class Authgroup_model extends CI_Model {
	var $table = 'auth_user_grup';
	var $tableAs = 'auth_user_grup a';
	function __construct()
	{
		parent::__construct();
	}
	
	function ListGroup($perpage=null,$limit=null)
	{
		if ($limit && $perpage) $this->db->limit($perpage,$limit);
		$this->db->order_by('id_auth_user_grup', 'asc');
		$query = $this->db->get('auth_user_grup');
		return $query;
	}
	
	function GetGroupById($Id)
	{
		$this->db->where('id_auth_user_grup',$Id);
		$this->db->order_by('id_auth_user_grup','asc');
		$this->db->limit(1);
		$query = $this->db->get('auth_user_grup');
		return $query;
	}
	
	function InsertGroup($data)
	{
		$this->db->insert('auth_user_grup', $data);
	}
	
	function UpdateGroup($Id,$data)
	{
		$this->db->where('id_auth_user_grup',$Id);
		$this->db->update('auth_user_grup', $data);	
	}
	
	function DeleteGroup($Id)
	{
		$this->db->where('id_auth_user_grup',$Id);
		$this->db->delete('auth_user_grup'); 
	}
	
	
	
	function GetMenuByParent($Parent_id)
	{
		$this->db->where('id_parents_menu_admin',$Parent_id);
		$query = $this->db->get('ref_menu_admin');
		return $query;
	}
	
	function GetMenuByRef($id_ref_menu,$id_group)
	{
		$this->db->where('id_ref_menu_admin',$id_ref_menu);
		$this->db->where('id_auth_user_grup',$id_group);
		$query = $this->db->get('auth_pages');
		return $query;
	}

	function findById($id){
		$where['a.id_auth_user_grup'] = $id;
		$where['a.is_delete'] = 0;
		return 	$this->db->get_where($this->tableAs,$where)->row_array();
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
?>