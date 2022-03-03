<?php
class ContactUsModel extends  CI_Model{
	var $table = 'contact_us';
	var $tableAs = 'contact_us a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		// $alias['search_title'] = 'a.name';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		/*$this->db->select("a.*,b.name as topic,a.message as komentar");
		$this->db->where('a.is_delete',0);
		$this->db->join('contact_us_topic b','b.id = a.id_contact_us_topic');*/
		$this->db->select("a.*,a.name as fullname, a.phone_number as phone, a.message as komentar");
		$this->db->where('a.is_delete',0);
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
		$data['user_id_create'] = null;

		// $user_sess_data = $this->session->userdata('MEM_SESS');
		
		sent_email_by_category(14,$data, '');
		unset($data['topic_name'],$data['contact_date']);
		$insert_id = $this->db->insert($this->table,array_filter($data));
		// $insert_id = $this->db->insert_id();
		// if($user_sess_data){
			// $this->load->model('registermodel');
			// $log_user_activity = array(
				// 'id_user'          =>  $user_sess_data['id'],
				// 'process_date' =>  date('Y-m-d H:i:s'),
				// 'id_log_category'   =>  41,
				// 'id_contact_us' => $id_news
			// );
			// $this->registermodel->log_user_activity($log_user_activity);
		// }
		return $insert_id;
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
		$where['is_delete'] = 0;
		/*$this->db->select('a.*,b.name as topic');
		$this->db->join('contact_us_topic b',"b.id = a.id_contact_us_topic");*/
		$this->db->select("a.*,a.name as fullname, a.phone_number as phone, a.message as komentar");
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$where['is_delete'] = 0;
		$this->db->select("a.*,a.name as fullname, a.phone_number as phone, a.message as komentar");
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
