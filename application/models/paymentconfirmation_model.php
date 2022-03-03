<?php
class paymentconfirmation_model extends  CI_Model{
	var $table = 'payment_confirmation';
	var $tableAs = 'payment_confirmation a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_exactor_name'] 	    = 'b.firstname_or_b.lastname';
		// $alias['search_full_name'] 			= 'a.first_name_or_a.last_name';
		$alias['search_name_in'] 			= 'a.name_in';
		$alias['search_name_out'] 			= 'a.name_out';
		$alias['search_status'] 			= 'a.is_paid';
		$alias['search_tanggal_buat'] 	    = 'a.create_date';
		$alias['search_payment_type'] 	    = 'a.id_ref_payment_type';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		
		// $this->db->where('a.is_paid', $this->input->get('search_status'));

		$this->db->select("a.*,b.*,c.*,d.*, 
			(case when a.is_paid = 1 then 'Paid' when a.is_paid = 2 then 'Paid - Without Membership Id' else 'Not Paid' end
			)as status_payment,
			a.modify_date as tanggal_buat,a.id as id_payment,
			 e.name as payment_type");
		$this->db->join('auth_member b', 'b.id = a.member_id', 'left');
		$this->db->join('ref_status_payment c', 'c.id = b.status_payment_id', 'left');
		$this->db->join('ref_payment_type e', 'e.id = a.id_ref_payment_type', 'left');

		$this->db->join('company d', 'd.id = b.company_id', 'left');

		$this->db->where('a.is_delete',0);
		$this->db->where('b.is_delete',0);
		$this->db->where('a.id_ref_payment_category = 2');
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
	function records_event($where=array(),$isTotal=0){
		// $alias['search_exactor_name'] 	= 'b.firstname_or_b.lastname';
		// $alias['search_full_name'] 		= 'a.first_name_or_a.last_name';
		$alias['search_invoice_number'] 	= 'a.invoice_number';
		$alias['search_event_name'] 		= 'b.name';
		// $alias['search_name_out'] 		= 'a.name_out';
		$alias['search_status'] 			= 'a.is_paid';
		$alias['search_payment_type'] 			= 'c.id';
		$alias['search_tanggal_buat'] 	    = 'a.create_date';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
	 	// if ($this->input->get('search_status') === "1" ) {
	 	// $this->db->where('a.is_paid', $this->input->get('search_status'));
	 	// }

		$this->db->select("a.*,
			b.name as event_name,
			(case when a.is_paid = 1 then 'Paid' else 'Not Paid' end) as status_payment,
			a.modify_date as tanggal_buat,a.id as id_payment, c.name as payment_type");
		$this->db->join('event b', 'b.id = a.event_id', 'left');
		$this->db->join('ref_payment_type c', 'c.id = a.id_ref_payment_type', 'left');

		$this->db->where('a.is_delete',0);
		$this->db->where('b.is_delete',0);
		$this->db->where('a.id_ref_payment_category = 1');
		$query = $this->db->get_where($this->tableAs,$where);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		// print_r($this->db->last_query());exit;

		$ttl_row = $this->records_event($where,1);
		
		return ddi_grid($data,$ttl_row);
	}

	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table, array_filter($data));
		return $this->db->insert_id();
	}
	function insert_frontend($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		// $data['user_id_create'] = id_user();
		$this->db->insert($this->table, array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id'] 			= $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function update_frontend($data,$id){
		$where['id'] 			= $id;
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function delete_frontend($id){
		$data['is_delete'] = 1;
		$this->update_frontend($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['a.is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
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
 }
