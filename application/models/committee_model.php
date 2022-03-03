<?php
class Committee_model extends  CI_Model{
	var $table            = 'committee';
	var $tableAs          = 'committee a';
	var $view             = 'view_content_event';
	var $viewAs           = 'view_content_event a';
	var $tableParticipant = 'event_participant b';
    function __construct(){
       parent::__construct();
	   $this->load->model('model_user');
	   $user = $this->model_user->findById(id_user());
	   $this->approvalLevelGroup = $user['approval_level'];
    }
	function records($where=array(),$isTotal=0){
		$alias['search_name'] = 'a.name';
		$alias['search_chair'] = 'a.chair';
		$alias['search_co_chair'] = 'a.co_chair';
	

	 	query_grid($alias,$isTotal);
		$this->db->select('a.*');
	
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
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
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
		$this->db->select('a.*');
		$this->db->distinct();
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

	function getArtikelTerkait($id_tags,$id_lang=1){
		$where['a.is_delete'] 			= 0;
		$where['a.id_status_publish']	= 2;
		$where['a.publish_date <='] 	= date('Y-m-d');
		$where['b.id_committee']		= $id_tags;
		$where['b.is_delete'] 			= 0;
		$where['a.approval_level']		= 100;
		$this->db->distinct();
		$this->db->select('a.news_title,a.id,a.img,a.uri_path,a.teaser,a.publish_date,b.id_tags');
		$this->db->join('committee_tags b','b.id_committee = a.id');
		$this->db->order_by('a.publish_date','desc');
		$this->db->group_by('a.id'); 
		return $this->db->get_where('news a',$where);
	}



 }
