<?php
class NewsImagesModel extends  CI_Model{
	var $table = 'news_images';
	var $tableAs = 'news_images a';
    function __construct(){
       parent::__construct();
	   
    }
	// function records($where=array(),$isTotal=0){
	// 	$alias['search_title'] = 'a.name';
	// 	// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	//  	query_grid($alias,$isTotal);
	// 	$this->db->select("a.*");
	// 	$this->db->where('a.is_delete',0);
	// 	$query = $this->db->get($this->tableAs);

	// 	if($isTotal==0){
	// 		$data = $query->result_array();
	// 	}
	// 	else{
	// 		return $query->num_rows();
	// 	}

	// 	$ttl_row = $this->records($where,1);
		
	// 	// echo $this->db->last_query();
	// 	return ddi_grid($data,$ttl_row);
	// }
	function insert($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = $data['user_id_create'] ? $data['user_id_create'] : id_user();
		$this->db->insert($this->table,array_filter($data));
		return $this->db->insert_id();
	}
	function update($data){
		// $where['id'] = $id;
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
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->tableAs,$where)->row_array();
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
	function findBydelete($where,$is_single_row=0){
		$where['a.is_delete'] = 1;
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
    /*function records_tags_all($where=array(),$isTotal=0){

		$this->db->select("a.*");
		$this->db->where('a.is_delete',0);
		$query = $this->db->get('tags a');

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}
		return $data;
	}

	function getTotalUpdateNewsByTagsId($id_tags){
		$date = date('Y-m-d');
		$tglBulanLalu = date( "Y-m-d", strtotime( "$date -1 month" ) ); 
		$this->db->where('a.id_tags',$id_tags);
		$this->db->where('b.id_status_publish',2);
		$this->db->where('b.is_delete',0);
		$this->db->where('b.approval_level',100);
		$this->db->join('news b','b.id = a.id_news');
		$this->db->where('b.publish_date >=' ,$tglBulanLalu);
		$this->db->where('b.publish_date <=' ,$date);
		// $this->db->order_by('b.publish_date','desc');
		$this->db->from($this->tableAs);
		return $this->db->count_all_results();
	}
	function getLatesNewsByTagsId($id_tags,$newsIds){
		$this->db->select('b.news_title,b.uri_path,b.publish_date,b.id,b.is_experts,b.is_qa');
		$this->db->where('a.id_tags',$id_tags);
		$this->db->where('b.id_status_publish',2);
		$this->db->where('b.is_delete',0);
		$this->db->where('b.approval_level',100);
		$this->db->join('news b','b.id = a.id_news');
		$this->db->order_by('b.publish_date','desc');
		if($newsIds){//kalo 1 news, tags nya lebih dari 1 dan semua tagsnya masuk top 10, biar ga muncul 2x
			$this->db->where_not_in('b.id',$newsIds);
		}
		$this->db->limit(3);
		$ret = $this->db->get($this->tableAs)->result_array();
		if($ret){
			return $ret;
		}
		else{ //kalo udah di exclude id_news malah jadi ga dapet datanya, biar dapet di null-in lagi newsIds nya
			return $this->getLatesNewsByTagsId($id_tags,null);
		}

	}

	function findByNotIn($idNews,$idTags){
		$where['is_delete'] = 0;
		$this->db->where('id_news',$idNews);
		$this->db->where_not_in('id_tags',$idTags);
		return 	$this->db->get_where($this->table,$where)->result_array();
	} */
 }
