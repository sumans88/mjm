<?php
class dashboardmodel extends  CI_Model{
	var $table = 't_aegon_profile_member';
	var $tableAs = 't_aegon_profile_member a';
    function __construct(){
       parent::__construct();
	   
    }
	function records($where=array(),$isTotal=0){
		$alias['search_title'] = 'a.name';
		// $ttl_row = $this->db->get($this->tableAs)->num_rows();

	 	query_grid($alias,$isTotal);
		$this->db->select("a.*,b.name as topic,a.message as komentar");
		$this->db->where('a.is_delete',0);
		$this->db->join('contact_us_topic b','b.id = a.id_contact_us_topic');
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
		$data['type_login'] = 'aegon';
		$data['process_time'] 	= date('Y-m-d H:i:s');
		//$data['user_id_create'] = null;#visitor
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
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table.' a',$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		//$where['is_active'] = 1;
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
    function inserting_data($data1, $author){
        $where['id_social_media'] = (string)$data1['user_profile']->identifier;
        if($author == 'Facebook'){
            $author = 'fb';
        } else if($author == 'Twitter'){
            $author = 'twitter';
        }
        $check_id_social = $this->db->select('*')->get_where($this->tableAs,$where)->row();
        if($data1['user_profile']->birthYear and $data1['user_profile']->birthMonth and $data1['user_profile']->birthDay){
            $birthdate = $data1['user_profile']->birthYear.'-'.$data1['user_profile']->birthMonth.'-'. $data1['user_profile']->birthDay;
        } else {
            $birthdate = '';
        }
		if(!$check_id_social){
            $array = array(
                'image' => $data1['user_profile']->photoURL,	
                'screen_name' => $data1['user_profile']->displayName,		
                'namadepan' => $data1['user_profile']->firstName,		
                'namabelakang' => $data1['user_profile']->lastName,		
                'jeniskelamin' => $data1['user_profile']->gender,		
                'tgllahir' => $birthdate,		
                'email' => $data1['user_profile']->email,		
                'nohp' => $data1['user_profile']->phone,		
                'alamat' => $data1['user_profile']->address,		
                'kota' => $data1['user_profile']->city,		
                'kodepos' => $data1['user_profile']->zip,
                'type_login' => $author,
                'process_time' => date('Y-m-d H:i:s'),
                'termscondition' => 1,
                'newsletter' => 1,
                'is_active' => 1,
                'id_social_media' => $data1['user_profile']->identifier,
                'idrenderpage' => base64_encode(date('Y-m-d H:i:s').'#'.$data1['user_profile']->email)
            );
            $query = $this->db->insert($this->table, $array);
            if($query){
                $array_social = array(
                    'id' => $this->db->insert_id(),
                    'id_social_media' => $data1['user_profile']->identifier,
                    'nama_depan' => $data1['user_profile']->firstName,		
                    'nama_belakang' => $data1['user_profile']->lastName,
                    'email' => $data1['user_profile']->email,
                    'image' => $data1['user_profile']->photoURL,
                    'type_login' => $author,
                    
                );
                $query = $this->db->insert('t_aegon_profile_social_media', $array_social);
            }
            return 2;
        } else {
            $member_sess = array();
            $member_sess = array(
                'member_email'          =>  $check_id_social->email,
                'member_namadepan'      =>  $check_id_social->namadepan,
                'member_namabelakang'   =>  $check_id_social->namabelakang,
                'idrenderpage'          =>  $check_id_social->idrenderpage,
                'id_social_media'       =>  $check_id_social->id_social_media,
            );
            $this->session->set_userdata('MEM_SESS',$member_sess);
	    $this->session->unset_userdata('ADM_SESS');
            return 1;
        }
        
    }
	function get_child($id_member){
		$where['a.id_member'] = $id_member;
		$this->db->select('a.*');
		return $this->db->get_where('t_aegon_member_child a',$where)->result_array();	
	}
 }
