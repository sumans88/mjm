<?php

class EmailDefaultModel extends  CI_Model{

	var $table = 'ref_email_category';

	var $tableAs = 'ref_email_category a';

    function __construct(){

       parent::__construct();

       $this->load->model('model_user');

       $user = $this->model_user->findById(id_user());

       // $this->approvalLevelGroup = $user['approval_level'];



    }

	function records($where=array(),$isTotal=0){

		$alias['search_name'] = 'c.template_name';

		// $ttl_row = $this->db->get($this->tableAs)->num_rows();



	 	query_grid($alias,$isTotal);

		$this->db->select("a.*,c.template_name, d.username");

		$this->db->where('a.is_delete',0);

		$this->db->join('email_tmp c',"c.id = a.id_email_tmp",'left');

		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');

		

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

		$this->db->select('a.*,b.username');

		$this->db->join('auth_user b','b.id_auth_user = a.user_id_create');



		return 	$this->db->get_where($this->tableAs,$where)->row_array();

	}



	function fetchRow($where) {

		return $this->findBy($where,1);

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

    function insert_email_log($data){

		$data['process_date'] 	= date('Y-m-d H:i:s');

		$this->db->insert('t_aegon_log_email',array_filter($data));

		return $this->db->insert_id();

	}

 }

