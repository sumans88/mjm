<?php
class Individual_model extends  CI_Model{
	var $table = 'auth_member';
	var $tableAs = 'auth_member a';
	var $tableview = 'view_auth_member';
	var $tableviewAs = 'view_auth_member a';
    function __construct(){
       parent::__construct();
       // $this->load->model('model_user');
       // $user = $this->model_user->findById(id_user());
       // $this->approvalLevelGroup = $user['approval_level'];

    }
	function records($where=array(),$isTotal=0){
		// $grup = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
		$alias['search_full_name']       = 'a.full_name';
		$alias['search_prefix_name']     = 'a.prefix_name';
		$alias['search_job']             = 'a.job';
		$alias['search_citizenship']     = 'a.citizenship';
		$alias['search_country']         = 'a.country';
		$alias['search_company'] 		 = 'b.name_in_or_b.name_out';
		$alias['search_membership']      = 'c.id';
		$alias['search_status']          = 'a.status_payment_id';


		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);

		$this->db->select("a.*,b.*,a.id as member_id, c.name as membership, e.name as status");
		$this->db->join('company b',"b.id = a.company_id",'left');
		$this->db->join('auth_member_category c',"c.id = a.member_category_id",'left');		
		$this->db->join('ref_status_payment e',"e.id = a.status_payment_id",'left');

		$this->db->order_by('a.create_date', 'desc');

		$this->db->where('a.is_delete', 0);
		$this->db->where('a.member_category_id', 2);
		$query = $this->db->get($this->tableAs);
		// print_r($this->db->last_query());exit;
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
		return 	$this->db->get_where($this->tableAs,$where)->row_array();
	}

	function fetchRow($where) {
		return $this->findBy($where,1);
	}
	function findBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	function findViewById($id){
		$where['a.member_id'] = $id;	
		return 	$this->db->get_where($this->tableviewAs,$where)->row_array();
	}

	function fetchViewRow($where) {
		return $this->findViewBy($where,1);
	}
	function findViewBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;		
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableviewAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableviewAs,$where)->result_array();
		}
	}
	function findViewByIdCommittee($id){
		$where['a.id_committee'] = $id;	
		return 	$this->db->get_where($this->tableviewAs,$where)->row_array();
	} 

 }
