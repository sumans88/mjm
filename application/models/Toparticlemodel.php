<?php
class TopArticleModel extends  CI_Model{
	var $table = 'news';
	var $tableAs = 'news a';
	function __construct(){
		parent::__construct();
		$this->load->model('model_user');
		$user = $this->model_user->findById(id_user());
		$this->approvalLevelGroup = $user['approval_level'];
	}
	function records($where=array(),$isTotal=0){
		$grup = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
		$alias['search_uri_path'] = 'a.uri_path';
		$alias['search_status_publish'] = 'c.id';
		$alias['search_news_category'] = 'b.id';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as news_category,c.name as status,d.username");
		$this->db->where('a.is_delete',0);
		$this->db->join('news_category b',"b.id = a.id_news_category",'left');
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		if($grup != 1){
			if($this->approvalLevelGroup > 0){
				$this->db->where("( a.approval_level = $this->approvalLevelGroup or  a.user_id_create = ".id_user()." or  a.user_id_editor = ".id_user()." or  a.user_id_publisher = ".id_user().")");
			}
			else{
				$this->db->where('a.user_id_create',id_user());
			}
		}

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
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_modify'] = id_user();
		$this->db->insert('top_article',array_filter($data));
		return $this->db->insert_id();
	}
	function delete($id){
		$data['is_delete'] = 1;
		$this->update($data,$id);
	}
	function findById($id){
		$where['a.id'] = $id;
		$where['a.is_delete'] = 0;
		$this->db->select('a.*, b.username');
		$this->db->join('auth_user b','b.id_auth_user = a.user_id_create');

		return 	$this->db->get_where($this->tableAs,$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$where['a.is_delete'] = 0;
		$this->db->select('a.*, b.name as category,b.uri_path as uri_path_category');
		$this->db->join('news_category b','b.id = a.id_news_category');
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

	
	function get_menu($where,$total='',$sidx='',$sord='',$mulai='',$end=''){
		$mulai = $mulai -1;
		if ($total==1){
			if($where){
				$data	= $this->db->get_where('news_category','is_delete!=0 '.$where)->num_rows();
			} else {
				$data	= $this->db->get('news_category',$where)->num_rows();	
			}
		}
		else{
			$dt	= $this->db->get('news_category')->result_array();
			$n 	= 0;
			foreach($dt as $dtx){
				$data[$n]['id']		= $dtx['id'];
				$data[$n]['name']	= $dtx['name'];
				$data[$n]['uri_path'] 	= $dtx['uri_path'];
				++$n;
			}
			$data[$n]['id']		= 0;
			$data[$n]['name']	= "All Category";
			$data[$n]['uri_path'] 	= "home";
		}
		return $data;
	}
	function get_top_news_by_category($id,$is_featured=0){
		$n 	= 0;
		if($is_featured!=0){
			$cek_exist	= db_get_one('top_article','is_featured',"id_category = '0'");
			if($cek_exist){
				$this->db->select("a.*,b.sort as top_sort");
				$this->db->where('a.is_delete',0);
				$this->db->where('b.id_category',0);
				$this->db->join('top_article b',"b.id_news = a.id",'left');
				$this->db->order_by('top_sort','asc');
			} else {
				$this->db->select("a.*,b.name as news_category,c.name as status,d.username");
				$this->db->where('a.is_delete',0);
				$this->db->join('news_category b',"b.id = a.id_news_category",'left');
				$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
				$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
				$this->db->order_by('hits','desc');
				$this->db->limit(7);
			}
		}else{
			$cek_exist	= db_get_one('top_article','is_featured',"id_category = '$id'");
			if($cek_exist){
				$this->db->select("a.*,b.sort as top_sort");
				$this->db->where('a.is_delete',0);
				$this->db->where('b.id_category',$id);
				$this->db->join('top_article b',"b.id_news = a.id",'left');
				$this->db->order_by('top_sort','asc');
			} else {
                $this->db->select("a.*,b.sort as top_sort");
                if($id==26){
                    $this->db->where("(a.is_experts = 1 and a.is_qa = 1) or a.id_news_category = $id");
                } else if($id==25){
                    $this->db->where("(a.is_experts = 1 and a.is_qa = 0) or a.id_news_category = $id");
                } else {
                    $this->db->where('a.id_news_category',$id);
                }
				$this->db->where('a.is_delete',0);
				$this->db->where('b.id_category',0);
				$this->db->join('top_article b',"b.id_news = a.id",'left');
				$this->db->order_by('top_sort','asc');
            }
			$this->db->limit(7);
		}
		$this->db->where('a.publish_date <=' ,date('Y-m-d'));
		$this->db->where('a.id_status_publish' ,2);
		$datax = $this->db->get($this->tableAs)->result_array();
        $ignore = 'a.id != 0 ';
		foreach($datax as $dtx){
			$data[$n]['id']		= $dtx['id'];
			$data[$n]['news_title']	= $dtx['news_title'];
			$data[$n]['top_sort']	= $dtx['top_sort'];
			$data[$n]['id_news_category']	= $dtx['id_news_category'];
            $ignore .="and a.id != $dtx[id] ";
			++$n;
		}
        $limit_tot =  (int)7-(int)$n;
        $limit_tot_count = abs((int)$limit_tot - 7);
        if($is_featured==0 and $limit_tot > 0){
            $this->db->select("a.*");
            $this->db->where('a.is_delete',0);
            $this->db->where('a.id_news_category',$id);
            $this->db->where($ignore);
	    $this->db->where('a.publish_date <=' ,date('Y-m-d'));
	    $this->db->where('a.id_status_publish' ,2);
			$this->db->order_by('a.hits','desc');
            $this->db->limit($limit_tot);
            $datax = $this->db->get($this->tableAs)->result_array();
            foreach($datax as $dtx){
                $data[$limit_tot_count]['id']		= $dtx['id'];
                $data[$limit_tot_count]['news_title']	= $dtx['news_title'];
                $data[$limit_tot_count]['top_sort']	= $dtx['top_sort'];
                $data[$limit_tot_count]['id_news_category']	= $dtx['id_news_category'];
                ++$limit_tot_count;
            }
        }
		return $data;
	}
	function get_all_news_by_category($id,$is_featured=0,$isTotal=0){
		$n 	= 0;
		$alias['search_news_category'] = 'b.name';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		
		if($is_featured!=0){
			$this->db->select("a.*,b.name as news_category,c.name as status,d.username");
			$this->db->where('a.is_delete',0);
			$this->db->join('news_category b',"b.id = a.id_news_category",'left');
			$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
			$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
			$this->db->order_by('hits','desc');
			
		}else{
			$this->db->select("a.*,b.name as news_category,c.name as status,d.username");
			$this->db->where('a.is_delete',0);
            if($id==26){
                $this->db->where("(a.is_experts = 1 and a.is_qa = 1) or a.id_news_category = $id");
            } else if($id==25){
                $this->db->where("(a.is_experts = 1 and a.is_qa = 0) or a.id_news_category = $id");
            } else {
                $this->db->where('a.id_news_category',$id);
            }
			$this->db->join('news_category b',"b.id = a.id_news_category",'left');
			$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
			$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
			$this->db->order_by('hits','desc');
			
		}
		$this->db->where('a.publish_date <=' ,date('Y-m-d'));
		if($isTotal==0){
			$dt	= $this->db->get($this->tableAs)->result_array();
			foreach($dt as $dtx){
				$data[$n]['id']		= $dtx['id'];
				$data[$n]['news_title']	= $dtx['news_title'];
				$data[$n]['category']	= $dtx['news_category'];
				$data[$n]['top_sort']	= $dtx['top_sort'];
				$data[$n]['id_news_category']	= $dtx['id_news_category'];
				++$n;
			}
		}
		else{
			return $this->db->get($this->tableAs)->num_rows();
		}
		
		$ttl_row = $this->get_all_news_by_category($id,$is_featured,1);
		
		return ddi_grid($data,$ttl_row);
	}
 }
