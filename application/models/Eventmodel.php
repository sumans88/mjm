<?php

class Eventmodel extends  CI_Model{

	var $table            = 'event';

	var $tableAs          = 'event a';

	var $view             = 'view_content_event';

	var $viewAs           = 'view_content_event a';

	var $tableParticipant = 'event_participant b';

	var $tableParticipantAs = 'event_participant';

    function __construct(){

       parent::__construct();

	   $this->load->model('model_user');

    }

	function records($where=array(),$isTotal=0){

		$alias['search_id']     = 'a.id';

		$alias['search_name']   = 'a.name';

		$alias['search_name_e'] = 'x.name';

		$alias['search_id_event_category'] = 'a.id_event_category';

		$alias['search_status_publish'] = 'd.id';

		// $alias['search_id_event_subcategory'] = 'a.id_event_subcategory';			

		$alias['search_speaker'] = 'a.speaker';



	 	query_grid($alias,$isTotal);

		$this->db->order_by('a.id', 'desc');

		$this->db->select('a.*, b.name as category, c.name as subcategory, d.name as status_publish,x.name as title_e');

		$this->db->join('event_category b',"b.id = a.id_event_category",'left');

		$this->db->join('event_category c',"c.id = a.id_event_subcategory",'left');

		$this->db->join('status_publish d','d.id = a.id_status_publish');

		$this->db->join('event x',"x.id_parent_lang = a.id",'left');

		$this->db->where('a.is_delete',0);

	

		// $this->db->where('a.id_event_subcategory != 0');

		// $this->db->where('a.id_parent_lang is null');

		$query = $this->db->get($this->tableAs);

		// print_r($this->db->last_query());exit;

		



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

		$this->db->select('a.*, b.name as category, b.uri_path as uri_path_category,z.name as subcategory,z.uri_path as uri_path_subcategory');

		$this->db->join('event_category b',"b.id = a.id_event_category",'left');

		$this->db->join('event_category z',"z.id = a.id_event_subcategory",'left');

		if($is_single_row==1){

			if ($where['a.is_close_1']) {

				unset($where['a.is_close_1']);



				return 	$this->db->where($where)->or_where('a.is_close','1')->get($this->tableAs)->row_array();

			}else{

				return 	$this->db->get_where($this->tableAs,$where)->row_array();

			}

		}

		else{

			if ($where['a.is_close_1']) {

				unset($where['a.is_close_1']);

				return 	$this->db->where($where)->or_where('a.is_close','1')->get($this->tableAs)->result_array();

			}else{

				return 	$this->db->get_where($this->tableAs,$where)->result_array();

			}

		}

	}

	

	function fetchRow($where) {

		return $this->findBy($where,1);

	}



	function records_participant($id,$where=array(),$isTotal=0){

		$alias['search_name'] = 'b.firstname';

		$alias['search_invoice_number'] = 'c.invoice_number';



	 	query_grid($alias,$isTotal);

		$this->db->select("b.*,a.id as event_id,a.id_parent_lang as parent_lang,c.invoice_number");

		$this->db->where('b.event_id',$id);

		$this->db->join($this->tableAs,'b.event_id = a.id');

		$this->db->join('payment_confirmation c ','c.event_id = b.event_id and c.member_id = b.id','LEFT');

		$query = $this->db->get($this->tableParticipant);



		if($isTotal==0){

			$data = $query->result_array();

		}

		else{

			return $query->num_rows();

		}



		$ttl_row = $this->records_participant($id,$where,1);

		

		// echo $this->db->last_query();

		$grid = ddi_grid($data,$ttl_row);

		return $grid;

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

	function updateFrontendApprovaalParticipant($data,$id){

		$where['id'] = $id;

		$data['modify_date'] 	= date('Y-m-d H:i:s');

		$this->db->update($this->tableParticipant,$data,$where);

		return $id;

	}

	function update_status($data,$id){

		$this->db->where('id', $id);

		$this->db->or_where('id_parent_lang', $id);

		$this->db->update($this->table,$data);	

		return $id;

		// echo $this->db->last_query();

	}



	function findViewById($id){

		$where['a.id'] = $id;

		$where['a.is_delete'] = 0;

		return 	$this->db->get_where($this->view.' a',$where)->row_array();

	}



	function findViewBy($where,$is_single_row=0){

		$where['a.is_delete'] = 0;

		$this->db->select('a.*, a.content_title as name');

		if($is_single_row==1){

			return 	$this->db->get_where($this->viewAs,$where)->row_array();

		}

		else{

			return 	$this->db->get_where($this->viewAs,$where)->result_array();

		}

	}

	

	function fetchViewRow($where) {

		return $this->findViewBy($where,1);

	}



	function insertParticipant($data){

		$data['create_date'] 	= date('Y-m-d H:i:s');

		$data['user_id_create'] = 1;

		$this->db->insert($this->tableParticipantAs,$data);

		return $this->db->insert_id();

	}

	function deleteParticipant($id){

		$data['is_delete'] = 1;

		$this->db->where('id', $id);

		$this->db->update('event_participant',$data);

		// return $this->db->insert_id();

	}

	function download($id){

		$this->db->select("a.*");

		$this->db->where('id',$id);

		$this->db->where('a.is_delete',0);

		$this->db->where('a.id_parent_lang is null');

		

		if ($where) {

			$this->db->where($where);

		}



		$this->db->order_by('a.id','desc');



		$query = $this->db->get($this->tableAs);

		return $query->result_array();

	}

	function findByParticipant($where,$is_single_row=0){

		$this->db->order_by('start_time', 'desc');

		$this->db->select('a.*, b.name as category, c.name as subcategory, d.name as status_publish,x.name as title_e');

		$this->db->join('event_category b',"b.id = a.id_event_category",'left');

		$this->db->join('event_category c',"c.id = a.id_event_subcategory",'left');

		$this->db->join('status_publish d','d.id = a.id_status_publish');

		$this->db->join('event x',"x.id_parent_lang = a.id",'left');

		$this->db->join('event_participant e',"e.event_id = x.id",'left');

		$this->db->where('a.is_delete',0);



		if($is_single_row==1){

			return $this->db->get_where($this->tableAs,$where)->row_array();

		}

		else{

			return $this->db->get_where($this->tableAs,$where)->result_array();

		}

	} 



	function data_amcham_event()

    {

        $this->db->select("a.*");  

        $this->db->join('event_category b',"b.id = a.id_event_category",'left');

        $this->db->where('id_event_category', 26);

				$this->db->order_by('a.start_date','desc');



        $data = $this->db->get('event a')->result_array();

        return $data;



    }


  

    function data_amcham()

    {
    	$today = date('Y-m').'-01';
    	$data = $this->db->query("
    		SELECT a.*,
			       a.content_title AS name
			FROM (view_content_event a)
			WHERE a.id_lang = '1'
			  AND a.is_not_available = 0
			  AND a.id_status_publish = 2
			  AND a.is_delete = 0
			  AND a.id_event_category IN ('40',
			                              '26')
			UNION ALL
			SELECT a.*,
			       a.content_title AS name
			FROM (view_content_event a)
			WHERE a.id_lang = '1'
			  AND a.is_not_available = 0
			  AND a.id_status_publish = 2
			  AND a.is_delete = 0
			  AND a.id_event_category IN ('28')
			  AND a.start_date >= '".$today."' order by start_date DESC")->result_array();

    //     $this->db->select("a.*");  

    //     $this->db->join('event_category b',"b.id = a.id_event_category",'left');

    //     $this->db->where('a.is_delete', 0);

				// $this->db->order_by('a.start_date','desc');



    //     $data = $this->db->get('event a')->result_array();

        return $data;



    }



  function data_nonamcham_event()

    {

        $this->db->select("a.*");  

        $this->db->join('event_category b',"b.id = a.id_event_category",'left');

        $this->db->where('id_event_category', 28);

				$this->db->order_by('a.start_date','desc');



        $data = $this->db->get('event a')->result_array();

        return $data;



    }



 }

