<?php
class Model_user extends  CI_Model{
	var $table = 'auth_user';
	var $tableAs = 'auth_user a';
    function __construct(){
       parent::__construct();
    }
	 function user($where,$total='',$sidx='',$sord='',$mulai='',$end=''){
		$mulai = $mulai -1;
                  // if($this->session->userdata['ADM_SESS']['admin_id_auth_user_group']!=1){
                  //     $where="AND a.company_id=".  company_id();
                  // }
		if ($total==1){
			$sql	= "SELECT count(*) ttl 
					   FROM auth_user a
						LEFT JOIN auth_user_grup b ON a.id_auth_user_grup = b.id_auth_user_grup
						where a.is_delete = 0 $where";
			$data	= $this->db->query($sql)->row()->ttl;
		}
		else{
                            
			$sql	= "SELECT a.id_auth_user as id, a.id_auth_user_grup,grup, a.userid, a.username,a.email,a.phone,profil_mitra_id
						FROM auth_user a
						LEFT JOIN auth_user_grup b ON a.id_auth_user_grup = b.id_auth_user_grup
						where a.is_delete = 0 $where";
			$dt	= $this->db->query($sql)->result_array();
			$n 	= 0;
			foreach($dt as $dtx){
				$data[$n]['id']	= $dtx['id'];
				$data[$n][1]	= edit_grid($dtx['id']);
				$data[$n][] 	= delete_row_grid($dtx['id'],'apps/user/delete');
				$data[$n][] 	= $dtx['userid'];
				$data[$n][] 	= $dtx['username'];
				$data[$n][] 	= $dtx['grup'];
				$data[$n][] 	= $dtx['id_auth_user_grup'];
				$data[$n][] 	= $dtx['email'];
				++$n;
			}
		}
		return $data;
    }
    
	   function insert($data){
			$data['user_id_create'] = id_user();
			$this->db->insert($this->table,null_empty($data));
			detail_log();
			return $this->db->insert_id();
		}
		function update($data,$id){
			$where['id_auth_user'] = $id;
			$data['user_id_modify'] = id_user();
			$data['modify_date'] 	= date('Y-m-d H:i:s');
			$this->db->update($this->table,null_empty($data),$where);
			detail_log();
			return $id;
		}
		function delete($id){
			$data['is_delete'] = 1;
			$this->update($data,$id);
		}



	function findById($id){
		$where['a.id_auth_user'] = $id;
		$where['a.is_delete'] = 0;
		$this->db->select('a.*,b.approval_level');
		$this->db->join('auth_user_grup b','a.id_auth_user_grup = b.id_auth_user_grup');
		return 	$this->db->get_where($this->tableAs,$where)->row_array();
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

 }
