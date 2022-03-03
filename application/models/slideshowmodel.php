<?php
class SlideshowModel extends  CI_Model{
	var $table = 'slideshow';
	var $tableAs = 'slideshow a';
    function __construct(){
       parent::__construct();
       $this->load->model('model_user');
       $user = $this->model_user->findById(id_user());
       $this->approvalLevelGroup = $user['approval_level'];

    }
	function records($where=array(),$isTotal=0){
		$grup = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
		$alias['search_uri_path'] = 'a.uri_path';
		$alias['search_status_publish'] = 'c.id';
		$alias['search_slideshow_title'] = 'a.slideshow_title';
		$alias['search_slideshow_title_e'] = 'x.slideshow_title';
		$alias['search_description'] = 'a.description';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,c.name as status,d.username, x.slideshow_title as slideshow_title_e");
		$this->db->where('a.is_delete',0);
		$this->db->where('a.id_parent_lang is null');
		// $this->db->where('a.id_slideshow_position = 0');
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		$this->db->join('slideshow x',"x.id_parent_lang = a.id",'left');
		
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
		if($this->check_publish($data,$id)) {
			$data['publish_date'] = date('Y-m-d');
		}
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function updateKedua($data,$id){
		$where['id_parent_lang'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		if($this->check_publish($data,$id)) {
			$data['publish_date'] = date('Y-m-d');
		}
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
		$where['a.is_delete'] = 0;
		$this->db->select('a.*,b.username');
		$this->db->join('auth_user b','b.id_auth_user = a.user_id_create');

		return 	$this->db->get_where($this->tableAs,$where)->row_array();
	}

	function fetchRow($where) {
		return $this->findBy($where,1);
	}
    function findBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$where['a.id_status_publish'] = 2;
		$this->db->select('a.*');
		$this->db->order_by('a.id','desc');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}

	function check_publish($data,$id){
		// print_r('dadwa');exit;
		if ($this->findById($id)['id_status_publish'] != 2 && $data['id_status_publish'] == 2) {
			return TRUE;
		}
		return FALSE;
	} 

 }
