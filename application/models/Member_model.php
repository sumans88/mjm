<?php
class Member_model extends  CI_Model{
	var $table = 'auth_member';
	var $tableAs = 'auth_member a';
	var $tableview = 'view_auth_member';
	var $tableviewAs = 'view_auth_member a';
    function __construct(){
       parent::__construct();
       // $this->load->model('model_user');
       $this->load->model('company_model');
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
		$alias['search_email']          = 'a.email';


		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);

		$this->db->select("a.*,a.email as email_member, b.*,a.id as member_id, c.name as membership, e.name as status,f.expired_date as expired_date,f.is_renew as is_renew_self ,f.is_expired as is_expired,
			(case when g.invoice_number 
			is null then '' else g.invoice_number end)
			as invoice_number, g.id as payment_id");
		$this->db->join('company b',"b.id = a.company_id",'left');
		$this->db->join('auth_member_category c',"c.id = a.member_category_id",'left');		
		$this->db->join('ref_status_payment e',"e.id = a.status_payment_id",'left');
		$this->db->join('membership_information f',"f.member_id = a.id",'left');
		$this->db->join('payment_confirmation g',"g.member_id = a.id and is_paid = 0",'left');

		// $this->db->order_by('a.create_date', 'desc');
		$this->db->order_by('firstname', 'asc');
		// $this->db->where('a.member_category_id', 1);

		$this->db->where('a.is_delete', 0);

		$query = $this->db->get_where($this->tableAs,$where);
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
	function records_representative($where=array(),$isTotal=0){
		$alias['search_fullname']    = "concat(a.prefix_name,' ',a.firstname,' ',a.lastname)";		
		$alias['search_job']         = 'a.job';
		$alias['search_email']       = 'a.email';
		$alias['search_citizenship'] = 'a.citizenship';
		$alias['search_company']     = 'b.name_in_or_b.name_out';
		$alias['search_membership']  = 'c.id';
		$alias['search_status']      = 'a.status_payment_id';
		
        
	 	query_grid($alias,$isTotal);

		$this->db->select("a.*,a.email as m_email,b.*,a.id as member_id, c.name as membership, e.name as status");
		$this->db->join('company b',"b.id = a.company_id",'left');
		$this->db->join('auth_member_category c',"c.id = a.member_category_id",'left');		
		$this->db->join('ref_status_payment e',"e.id = a.status_payment_id",'left');

		// $this->db->order_by('a.create_date', 'desc');
		$this->db->order_by('name_in', 'asc');

		// $this->db->where('(a.member_category_id', 3);
		// $this->db->or_where('a.member_category_id = 1 )');
		$this->db->where('a.is_delete', 0);
		$query = $this->db->get_where($this->tableAs,$where);
		// print_r($this->db->last_query());exit;
		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		$ttl_row = $this->records_representative($where,1);

        $custom_url = current_controller('function').'/'.$where['company_id'];
        return ddi_grid($data,$ttl_row,5,$custom_url);
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
	function update_frontend($data,$id)
	{
		$where['id'] 			= $id;
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] = 1;
		// $data['status_payment_id'] = 6;
		$this->update($data,$id);
	}
	function delete_company($id){
		$data['is_delete'] = 1;
		$member_company = $this->findBy(array('company_id'=>$id));
		foreach ($member_company as $key => $value) {
			// $data['status_payment_id'] = 6;
			$this->update($data,$value['id']);
		}		
		$this->company_model->update($data, $id);
	}
	function block($id){
		$data['is_block'] = 1;
		$this->update($data,$id);
	}
	function block_company($id){
		$member_company = $this->findBy(array('company_id'=>$id));
		foreach ($member_company as $key => $value) {
			$data['is_block'] = 1;
			$this->update($data,$value['id']);
		}		
	}
	function unblock($id){
		$data['is_block'] = 0;
		$this->update($data,$id);
	}
	function unblock_company($id){
		$member_company = $this->findBy(array('company_id'=>$id));
		foreach ($member_company as $key => $value) {
			$data['is_block'] = 0;
			$this->update($data,$value['id']);
		}		
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
	function cekStatusKirim($id_membership,$tanggal_kirim){
		$where['membership_information_id'] = $id_membership;
		$where['is_manual']                 = 0;
		$where['sent']                      = $tanggal_kirim;
		return 	$this->db->get_where('membership_information_sent_report',$where)->row_array();
	}
	function getDataUserExpiredReminder($date){
		$where['expired_date'] = $date; 
		$this->db->select('a.*');
		return 	$this->db->get_where('membership_information a',$where)->result_array();
	}
	function simpanDataLaporanTerkirim($insert){
		$this->db->insert('membership_information_sent_report',$insert);
		return $this->db->insert_id();
	}
	function list_representative($id_company,$is_single_row=0){
		$this->db->select("a.*,a.email as m_email,b.*,a.id as member_id, c.name as membership, e.name as status");
		$this->db->join('company b',"b.id = a.company_id",'left');
		$this->db->join('auth_member_category c',"c.id = a.member_category_id",'left');		
		$this->db->join('ref_status_payment e',"e.id = a.status_payment_id",'left');

		$this->db->order_by('a.create_date', 'desc');

		$this->db->where('(a.member_category_id', 3);
		$this->db->or_where('a.member_category_id = 1 )');
		$this->db->where('a.company_id', $id_company);
		$this->db->where('a.is_delete', 0);
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}

	function list_individual($id_company,$is_single_row=0){
		$this->db->select("a.*,a.email as m_email,b.*,a.id as member_id, c.name as membership, e.name as status");
		$this->db->join('company b',"b.id = a.company_id",'left');
		$this->db->join('auth_member_category c',"c.id = a.member_category_id",'left');		
		$this->db->join('ref_status_payment e',"e.id = a.status_payment_id",'left');

		$this->db->order_by('a.create_date', 'desc');

		$this->db->where('(a.member_category_id', 2);
		$this->db->where('a.company_id', $id_company);
		$this->db->where('a.is_delete', 0);
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}

 	}

 }
