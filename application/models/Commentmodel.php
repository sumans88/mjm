<?php
class CommentModel extends  CI_Model{
	var $table = 'comment';
	var $tableAs = 'comment a';
    function __construct(){
       parent::__construct();
       $this->load->model('model_user');
       $user = $this->model_user->findById(id_user());

    }
	function records($where=array(),$isTotal=0){
		$grup = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
		$alias['search_status_publish'] = 'c.id';
		$alias['search_author'] = 'namadepan';
		$alias['search_flag'] = 'flag';
		$alias['search_create_date'] = 'a.create_date';
		$alias['search_is_delete'] = 'a.is_delete';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,c.name as status_delete,d.namadepan + ' ' + d.namabelakang as author, e.news_title");
		$this->db->join('status_publish c',"c.id = a.is_delete",'left');
		$this->db->join('t_aegon_profile_member d',"d.id = a.user_id_create",'left');
		$this->db->join('news e',"e.id = a.id_news",'left');

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
		$data['is_delete'] = 2;
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$id_news = $data['id_news'];
		$id_user_create = $data['user_id_create'];
		$this->db->insert($this->table,array_filter($data));
		$id_comment =  $this->db->insert_id();
		if($data['is_admin']==0){
			$this->load->model('registermodel');
			$log_user_activity = array(
				'id_user'          =>  $id_user_create,
				'process_date' =>  date('Y-m-d H:i:s'),
				'id_log_category'   =>  37,
				'id_comment'   =>  $id_comment,
				'id_article' => $id_news
			);
			$this->registermodel->log_user_activity($log_user_activity);
		}
		return $id_comment;
	}
	function update($data,$id){
		$where['id'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table,$data,$where);
		return $id;
	}
	function delete($id, $user_id_modify){
		$data['is_delete'] = 1;
		$data['user_id_modify'] = $user_id_modify;
		if($data['is_admin']==0){
			$this->load->model('registermodel');
			$log_user_activity = array(
				'id_user'          =>  $user_id_modify,
				'process_date' =>  date('Y-m-d H:i:s'),
				'id_log_category'   =>  38,
				'id_comment'   =>  $id,
				'id_article' => $this->findBy(array('a.id'=>$id),1)['id_news']
			);
			$this->registermodel->log_user_activity($log_user_activity);
		}
		$this->update($data,$id);
	}
	function findbyadmin($where, $is_admin = 0){
		if($is_admin==0){
			$where['a.is_delete'] = 2;
		}
		$this->db->select('a.*, d.username as namadepan');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		return 	$this->db->get_where($this->tableAs,$where)->row_array();
	}
	function flag($id){
		$where['id'] = $id;
		$this->db->select('a.*');
		$data =  $this->db->get_where($this->tableAs,$where)->row_array();
		if($data['flag'] == '' or $data['flag'] == 0){
			$data_update['flag'] = 1;
		} else {
			$data_update['flag'] = $data['flag'] +=1;
		}
		
		$this->update($data_update,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['a.is_delete'] = 2;
		$this->db->select('a.*,b.username');
		$this->db->join('auth_user b','b.id_auth_user = a.user_id_create');

		return 	$this->db->get_where($this->tableAs,$where)->row_array();
	}

	function fetchRow($where) {
		return $this->findBy($where,1);
	}
	function findBy($where,$is_single_row=0){
		$where['a.is_delete'] = 2;
		$this->db->select('a.*,d.namadepan,d.namabelakang,d.image, e.news_title');
		$this->db->join('t_aegon_profile_member d',"d.id = a.user_id_create",'left');
		$this->db->join('news e',"e.id = a.id_news",'left');
		$this->db->order_by('id','desc');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
	function findBynumrow($where){
		$where['a.is_delete'] = 2;
		$this->db->select('a.*,d.namadepan,d.namabelakang,d.image');
		$this->db->join('t_aegon_profile_member d',"d.id = a.user_id_create",'left');
		return $this->db->get_where($this->tableAs,$where)->num_rows();
	} 
	
	function comment_like($data){
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$this->db->insert('comment_like',array_filter($data));
		return $this->db->insert_id();
	}
	function comment_unlike($post){
		$data['is_delete'] = 1;
		$where['id_news'] = $post['id_news'];
		$where['id_comment'] = $post['id_comment'];
		$where['user_id_create'] = $post['user_id_create'];
		$this->db->update('comment_like',$data,$where);
	}
	function findBycommentlike($where,$nums_row=0){
		$where['is_delete'] = 0;
		$this->db->select('*');
		if($nums_row==1){
			return 	$this->db->get_where('comment_like',$where)->num_rows();
		}
		else{
			return 	$this->db->get_where('comment_like',$where)->result_array();
		}
	}
	function findByEdit($where,$is_single_row=0){
		$this->db->select('a.*,d.namadepan,d.namabelakang,d.image, e.news_title');
		$this->db->join('t_aegon_profile_member d',"d.id = a.user_id_create",'left');
		$this->db->join('news e',"e.id = a.id_news",'left');
		$this->db->order_by('id','desc');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	}
 }
