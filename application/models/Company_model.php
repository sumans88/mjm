<?php
class company_model extends  CI_Model{
	var $table = 'company';
	var $tableAs = 'company a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		// $alias['search_exactor_name'] = 'CONCAT(e.nama,' ',a.nama) AS nama_produk"';
		$alias['search_fullname'] 		= 'CONCAT(b.prefix_name," ",b.firstname," ",b.lastname)';
		$alias['search_name_in']      = 'a.name_in';
		$alias['search_name_out']     = 'a.name_out';
		$alias['search_status']       = 'c.id';
		// $alias['search_tanggal_buat'] = 'a.create_date';
		$alias['search_membership']      = 'b.member_category_id';

		query_grid($alias,$isTotal);

		$this->db->select("a.*,a.create_date as tanggal_buat,b.prefix_name,b.firstname,b.lastname,b.status_payment_id,b.is_block ,b.id as member_id,c.name as status_payment,e.name as membership");
					/*,
					b.*,
					b.member_category_id = member_category_id,
					c.*,
					c.name as status_payment, and 
					a.create_date as tanggal_buat,a.id as id_company);*/
		$this->db->join('auth_member b', 'b.company_id = a.id ', 'left');
		$this->db->join('ref_status_payment c', 'c.id = b.status_payment_id', 'left');
		$this->db->join('auth_member_category e',"e.id = b.member_category_id",'left');		


		// $this->db->where('b.member_category_id in(1,2)');
		$this->db->where('(b.member_category_id != 3 and b.member_category_id != 2)');
		$this->db->group_by('id');

		$this->db->where('b.is_delete',0);
		// $this->db->order_by('c.id', 'desc');
		$this->db->order_by( 'name_in', 'asc');
		
		
		$query = $this->db->get_where($this->tableAs,$where);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		// print_r($this->db->last_query());exit;

		$ttl_row = $this->records($where,1);		return ddi_grid($data,$ttl_row);
	}

	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
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
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['a.is_delete'] = 0;
		$this->db->select('a.*,b.is_invis');
		$this->db->join('auth_member b', 'b.id = a.member_id_create', 'left');
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
