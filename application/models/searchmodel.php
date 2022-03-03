<?php
class SearchModel extends  CI_Model{
	var $table = 'view_search';
	var $tableAs = 'view_search a';
	var $viewAs = 'view_search a';
    function __construct(){
       parent::__construct();
       $this->load->model('model_user');
    }
	function records($where=array(),$isTotal=0){
		$alias['search_uri_path'] = 'a.uri_path';
		$alias['search_status_publish'] = 'c.id';
		$alias['search_news_category'] = 'b.id';
		$tags = $_GET['search_tags'];
		unset($_GET['search_tags']);
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as news_category,c.name as status,d.username");
		$this->db->where('a.id_status_publish',2);
		$this->db->where('a.approval_level',100);
		$this->db->where('a.is_delete',0);
		$this->db->join('news_category b',"b.id = a.id_news_category",'left');
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		if($tags){
			$this->db->join('news_tags e',"e.id_news = a.id and e.id_tags = $tags");
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

	function fetchRow($where) {
		return $this->findBy($where,1);
	}
	function findBy($post,$page=0){
		
		$where['lang'] 	= 'en';
		$keyword 		= $post['keyword'];
		unset($post['keyword']);
		if($keyword){
			$this->db->where("( name like '%".$keyword."%' OR description like '%".$keyword."%' OR content like '%".$keyword."%')");
			// $this->db->where("(name like '%$keyword%' or description like '%$keyword%' or content like '%$keyword%')");
		}
		/*if($post['tgl'] && $post['bln'] && $post['thn']){
			$this->db->like('publish_date',"-$post[bln]-$post[tgl]");
		}*/
		$tgl = $post['tgl'] ? zero_first($post['tgl'],2) : '';
        $bln = $post['bln'] ? zero_first($post['bln'],2) : '';
        $thn = $post['thn'];
        // echo $tgl.'-'.$bln.'-'.$thn;
        // $nama_bln = $bulan[(int)$bln];
	        if($thn && !$bln && !$tgl){
	            // $tanggal = "Tahun : <strong>$thn</strong>";
				// $this->db->like('publish_date',"$thn%");
				$this->db->where("publish_date like '$thn-%'");
	        }
	        else if($thn && $bln && !$tgl){
	            // $tanggal = "Bulan <strong>".$nama_bln.' '. $thn.'</strong>';
				// $this->db->like('publish_date',"$thn-$bln%");
				$this->db->where("publish_date like '$thn-$bln%'");
	        }
	        else if($thn && $bln && $tgl){
	            // $tanggal = "Tanggal :<strong>$tgl $nama_bln $thn</strong>";
				$this->db->where("publish_date = '$thn-$bln-$tgl'");
				// $this->db->like('publish_date',"$thn-$bln-$tgl%");
	        }
	        else if($tgl && !$bln && !$thn){
	            // $tanggal = "Tanggal :<strong> $tgl</strong>";
				// $this->db->like('publish_date',"%-$tgl");
				$this->db->where("publish_date like '%-$tgl'");
	        }
	        else if(!$tgl && $bln && !$thn){
	            // $tanggal = "Bulan <strong>$nama_bln</strong>";
				$this->db->where("publish_date like '%-$bln-%'");
				// $this->db->like('publish_date',"%-$bln-%");
	        }
	        else if($tgl && $bln && !$thn){
				$this->db->where("publish_date like '%-$bln-$tgl'");
				// $this->db->like('publish_date',"%-$bln-%");
	        }

        /*if($thn && $bln && $tgl){
            $tanggal = "Tanggal :<strong>$tgl $nama_bln $thn</strong>";
			$this->db->like('publish_date',"$thn-$bln-$tgl");
        }
        else if($thn && $bln && !$tgl){
			$this->db->like('publish_date',"$thn-$bln%");
            // $tanggal = "Bulan <strong>".$nama_bln.' '. $thn.'</strong>';
        }
        else if($thn && !$bln && !$tgl){
			$this->db->like('publish_date',"$thn-%");
            // $tanggal = "Tahun : <strong>$thn</strong>";
        }
        else if(!$thn && $bln && $tgl){
            // $tanggal = "Tanggal $tgl $nama_bln";
			$this->db->like('publish_date',"%-$bln-$tgl");
        }
        else if(!$thn && !$bln && $tgl){
            // $tanggal = "Tanggal $tgl";
			// $this->db->like('publish_date',"%-$tgl");
			$this->db->where("publish_date like '%-$tgl'");
        }
        else if(!$thn && $bln && !$tgl){
            // $tanggal = "Bulan $nama_bln";
			$this->db->like('publish_date',"%-$bln-%");
        }
        else if($thn && !$bln && $tgl){
			$this->db->like('publish_date',"$thn-%%-$tgl");
            // $tanggal = "Tanggal $tgl Tahun $thn";
        }


		$tags = $post['tags'];
		if($tags){
			$this->db->join('news_tags e',"e.id_news = a.id and e.id_tags = $tags");
		}*/

		$this->db->select('a.*');
		// $this->db->select('a.*, b.name as category,b.uri_path as uri_path_category, a.name as nama');
		// $this->db->join('news_category b','b.id = a.id_news_category');
		// return $this->db->get_where($this->tableAs,$where)->result_array();

		if($page === 'all'){
			$this->db->where($where);
			$this->db->from($this->tableAs);
			return $this->db->count_all_results();

		}
		else{
			$this->db->limit(PAGING_PERPAGE,$page);
			$this->db->order_by('a.publish_date','desc');
			return $this->db->get_where($this->tableAs,$where)->result_array();
		}

	} 

	function findViewBy($where,$is_single_row=0,$post){
		// $where['a.is_delete'] = 0;
		$this->db->select('a.*, a.name as news_title');

		$keyword = !empty($post['keyword']) ? query_kutip($post['keyword']): '' ;
		$bln     = !empty($post['month']) ? zero_first($post['month'],2) : '';
		$thn     = !empty($post['year']) ? $post['year'] : '';

        if ($keyword) {
        	/*$this->db->like('a.name',$keyword);
        	$this->db->or_like('a.description',$keyword);
        	$this->db->or_like('a.content',$keyword);*/
        	$this->db->where('(name like "%'.$keyword.'%" OR description like "%'.$keyword.'%" OR content like "%'.$keyword.'%")');
        }
        
        if($thn && !$bln){
			$this->db->where('YEAR(a.publish_date)="'.$thn.'" ');
        }
        else if($thn && $bln){
			$this->db->where('MONTH(a.publish_date)="'.$bln.'" and YEAR(a.publish_date)="'.$thn.'"');
        }
        else if($bln && !$thn){
			$this->db->where('MONTH(a.publish_date)="'.$bln.'" ');
        }

        $this->db->order_by('publish_date', 'desc');


		if($is_single_row==1){
			return $this->db->get_where($this->viewAs,$where)->row_array();
		}
		else{
			return $this->db->get_where($this->viewAs,$where)->result_array();
		}
	} 
 }
