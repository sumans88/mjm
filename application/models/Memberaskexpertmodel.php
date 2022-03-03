<?php
class MemberAskExpertModel extends  CI_Model{
	var $table = 'member_ask_expert';
	var $tableAs = 'member_ask_expert a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_category'] = 'b.title';
		$alias['search_namadepan'] = 'd.namadepan';
		$alias['search_datestart'] = 'a.datestart';
		$alias['search_dateend'] = 'a.dateend';
		$alias['search_email'] = 'd.email';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();
	
	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.title as category,c.title,d.namadepan, d.namabelakang, d.email");
		// $this->db->where('a.is_delete',0);
		$this->db->join('category b','b.id = a.id_category');
		$this->db->join('sub_category c','c.code = a.sub_category');
		$this->db->join('t_aegon_profile_member d',"d.id = a.id_user",'left');

		$query = $this->db->get($this->tableAs);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		
		$ttl_row = $this->records($where,1);
		
		 echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['id_user'] = id_member();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data,$id){
		$where['id'] = $id;
		$data['modify_id_user'] = id_user();
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
		// $where['is_delete'] = 0;
		$this->db->select("a.*,b.title as category,c.title");
		$this->db->join('category b','b.id = a.id_category');
		$this->db->join('sub_category c','c.code = a.sub_category');
		// $this->db->join('contact_us_topic b',"b.id = a.id_contact_us_topic");
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$this->db->select("a.*,b.title as category,c.title");
		$this->db->join('category b','b.id = a.id_category');
		$this->db->join('sub_category c','c.code = a.sub_category');
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
