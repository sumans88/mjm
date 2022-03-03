<?php
class GalleryModel extends  CI_Model{
	var $table = 'gallery';
	var $tableAs = 'gallery a';
	var $tableParticipant = 'event_detail b';
    function __construct(){
       parent::__construct();
    }
	function records($where=array(),$isTotal=0){
		$alias['search_gallery_name'] = 'a.name';
		$alias['search_gallery_name_e'] = 'x.name';
		$alias['search_description'] = 'a.description';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*, b.name as category_name, x.name as title_e");
		$this->db->where('a.is_delete',0);
		$this->db->where('b.is_delete',0);
		$this->db->where('a.id_parent_lang is null');
	
		$this->db->join('gallery_category b','b.id = a.id_gallery_category');
		$this->db->join('gallery x',"x.id_parent_lang = a.id",'left');
		$query = $this->db->get($this->tableAs);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		
		$this->db->where('a.id_gallery_category', 1);
		$ttl_row = $this->records($where,1);
		
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
	function records_video($where=array(),$isTotal=0){
		$alias['search_gallery_name'] = 'a.name';
		$alias['search_gallery_name_e'] = 'x.name';
		$alias['search_description'] = 'a.description';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*, b.name as category_name, x.name as title_e");
		$this->db->where('a.is_delete',0);
		$this->db->where('b.is_delete',0);
		$this->db->where('a.id_parent_lang is null');
	
		$this->db->join('gallery_category b','b.id = a.id_gallery_category');
		$this->db->join('gallery x',"x.id_parent_lang = a.id",'left');
		$query = $this->db->get($this->tableAs);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		$this->db->where('a.id_gallery_category', 4);
		$ttl_row = $this->records($where,1);
		
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
	function records_all_album($where=array(),$isTotal=0){
		$alias['search_gallery_name'] = 'a.name';
		$alias['search_gallery_name_e'] = 'x.name';
		$alias['search_description'] = 'a.description';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*, b.name as category_name, x.name as title_e");
		$this->db->where('a.is_delete',0);
		$this->db->where('b.is_delete',0);
		$this->db->where('a.id_parent_lang is null');
		$this->db->where('a.id_gallery_category', 1);
		$this->db->or_where('a.id_gallery_category', 4);
		$this->db->join('gallery_category b','b.id = a.id_gallery_category');
		$this->db->join('gallery x',"x.id_parent_lang = a.id",'left');
		$query = $this->db->get($this->tableAs);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function insert_migrasi($data){
		// $data['create_date'] 	= date('Y-m-d H:i:s');
		// $data['user_id_create'] = id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function selectData($id,$is_single_row=0){
		$this->db->select("a.*, b.name as category_name");
		$this->db->where('a.is_delete',0);
		$this->db->where('a.id',$id);
		$this->db->or_where('id_parent_lang',$id);
		$this->db->join('gallery_category b','b.id = a.id_gallery_category');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs)->row_array();
		}else{
			return 	$this->db->get_where($this->tableAs)->result_array();
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
		$where['is_delete'] = 0;
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

	function findByGalleryTags($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$this->db->select('a.*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}

	function findCatBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$this->db->select('a.*, b.name as category');
		$this->db->join('gallery_category b', 'b.id = a.id_gallery_category');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}

	/*yang ditambah*/
	function findByNews($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$this->db->select('a.*, b.name as category,b.uri_path as uri_path_category,a.is_experts,a.is_qa,a.teaser');
		$this->db->join('news_category b','b.id = a.id_news_category');
		if($is_single_row==1){
			return $this->db->get_where('news a',$where)->row_array();
		}
		else{
			return $this->db->get_where('news a',$where)->result_array();
		}
	}
	
	function fetchRow($where) {
		return $this->findBy($where,1);
		
	}
	
	function fetchRowCat($where) {
		return $this->findCatBy($where,1);
	}

	function records_participant($id,$where=array(),$isTotal=0){
		$alias['search_name'] = 'b.name';

	 	query_grid($alias,$isTotal);
		$this->db->select("b.*,a.id as gallery_id,a.id_parent_lang as parent_lang");
		$this->db->where('b.gallery_id',$id);
		$this->db->or_where('a.id_parent_lang',$id);
		$this->db->join($this->tableAs,'b.gallery_id = a.id');
		$query = $this->db->get($this->tableParticipant);


		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
	function selectDataParticipant($id,$is_single_row=0){
		$this->db->where('id',$id);
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableParticipant)->row_array();
		}else{
			return 	$this->db->get_where($this->tableParticipant)->result_array();
		}
	}

	function updateApprovaalParticipant($data,$id){
		$where['id'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->tableParticipant,$data,$where);
		return $id;
	}
 }
