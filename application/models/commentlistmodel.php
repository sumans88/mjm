<?php
class CommentListModel extends  CI_Model{
	var $table = 'comment_list';
	var $tableAs = 'comment_list a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_page_title'] = 'b.news_title';
		$alias['search_fullname'] = 'fullname';
		$alias['search_email'] = 'email';
		$alias['search_commentar'] = 'commentar';

		query_grid($alias,$isTotal);
		$this->db->select("b.news_title as page_title, fullname, email, commentar, c.name as page_cat, c.id as id_page, page_category");
		$this->db->where('page_category',1);
		$this->db->join('news b', 'b.id = a.id_parent', 'left');
		$this->db->join('news_category c', 'b.id_news_category = c.id', 'left');
		$query = $this->db->get($this->tableAs);

		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		//echo "<script>console.log('aop');</script>---d";exit();
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
	function findById($cat,$id){
		if($cat == 1){
			$this->db->select("c.name as category, b.news_title as title, fullname, email, commentar");
			$this->db->where('a.is_delete',0);
			$this->db->where('a.id',$id);
			//$this->db->or_where('id_parent_lang',$id);
			$this->db->join('news b','b.id = a.id_parent');
			$this->db->join('news_category c','c.id = b.id_news_category');
		} else if($cat == 2){
			$this->db->select('c.name as category, b.name as title, fullname, email, commentar');
			$this->db->where('a.is_delete',0);
			$this->db->where('a.id',$id);
			//$this->db->or_where('id_parent_lang',$id);
			$this->db->join('gallery b','b.id = a.id_parent');
			$this->db->join('gallery_category c','c.id = b.id_gallery_category');
		}
		$query = $this->db->get_where($this->tableAs)->row_array();
		//echo "<script>console.log('".var_dump($id)."');</script>---d";exit();
		return $query;
	}
	function findBy($where,$is_single_row=0){
		$where['is_delete'] = 0;
		$this->db->select('*');
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

	function selectData($cat,$id,$is_single_row=0){
		if($cat == 1){
			$this->db->select("c.name as category");
			$this->db->where('a.is_delete',0);
			$this->db->where('a.page_category',1);
			$this->db->where('b.id_news_category',$id);
			//$this->db->or_where('id_parent_lang',$id);
			$this->db->join('news b','b.id = a.id_parent');
			$this->db->join('news_category c','c.id = b.id_news_category');
		} else if($cat == 2){
			$this->db->select('c.name as category');
			$this->db->where('a.is_delete',0);
			$this->db->where('a.page_category',2);
			$this->db->where('b.id_gallery_category',$id);
			//$this->db->or_where('id_parent_lang',$id);
			$this->db->join('gallery b','b.id = a.id_parent');
			$this->db->join('gallery_category c','c.id = b.id_gallery_category');
		}
		
		if($is_single_row==1){
			$query = $this->db->get_where($this->tableAs)->row_array();
		}else{
			$query = $this->db->get_where($this->tableAs)->result_array();
		}

		return $query;
	}
	function recordsComment($cat,$id,$isTotal=0){
		
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

		/*$this->db->select("a.*,b.name as topic,a.message as komentar");
		$this->db->where('a.is_delete',0);
		$this->db->join('contact_us_topic b','b.id = a.id_contact_us_topic');*/

		if($cat == 1){
			$alias['search_page_title'] = 'b.news_title';
			$this->db->select("a.id as id, c.name as category, b.news_title as page_title, fullname, email, commentar");
			$this->db->where('a.is_delete',0);
			$this->db->where('a.page_category',1);
			$this->db->where('b.id_news_category',$id);
			//$this->db->or_where('id_parent_lang',$id);
			$this->db->join('news b','b.id = a.id_parent');
			$this->db->join('news_category c','c.id = b.id_news_category');
		} else if($cat == 2){
			$alias['search_page_title'] = 'b.name';
			$this->db->select('a.id as id, c.name as category, b.name as page_title, fullname, email, commentar');
			$this->db->where('a.is_delete',0);
			$this->db->where('a.page_category',2);
			$this->db->where('b.id_gallery_category',$id);
			//$this->db->or_where('id_parent_lang',$id);
			$this->db->join('gallery b','b.id = a.id_parent');
			$this->db->join('gallery_category c','c.id = b.id_gallery_category');
		}
		
		query_grid($alias,$isTotal);
		$query = $this->db->get_where($this->tableAs);
		
		if($isTotal==0){
			$data = $query->result_array();
		}
		else{
			return $query->num_rows();
		}

		$ttl_row = $this->records($where,1);
		//echo "<script>console.log('aop');</script>---d";exit();
		// echo $this->db->last_query();
		return ddi_grid($data,$ttl_row);
	}
 }
