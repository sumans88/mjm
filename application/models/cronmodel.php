<?php
class CronModel extends  CI_Model{
	var $table = 't_aegon_member_deactive';
	var $tableAs = 't_aegon_member_deactive a';
	function __construct(){
	   parent::__construct();
	       
	}
	function delete_account(){
		$data_now = date('Y-m-d');
		$check_deactive_member= $this->db->select('*')->get_where($this->table,"is_active=1 and due_date<='$data_now'")->result_array();
		if($check_deactive_member){
			foreach ($check_deactive_member as $key => $data) {
				
				$member = $this->db->select('*')->get_where('t_aegon_profile_member',"id = '$data[id_member]'")->row_array();

				$this->insert_log_delete_member($member, 1);
				$this->delete_all_data_member($data['id_member'], $data['id_social_media'], $data);

			}
			
			
			$proses['status'] = 1;
			$proses['message'] = 'Akun telah berhasil di delete.';
		} else {
			$proses['status'] = 0;
			$proses['message'] = 'Reactive Code tidak ditemukan.';
		}
		return $proses;
	}
	function delete_account_not_activate_email(){
		$data_now = date('Y-m-d');
		$max_data_exp = EXP_DATE_ACTIVATION_EMAIL;
		$due_date = date("Y-m-d",strtotime("$data_now + $max_data_exp days"));
		
		$check_deactive_member= $this->db->select('*')->get_where('t_aegon_profile_member',"is_active=0 and send_activation_date > '$due_date'")->result_array();

		if($check_deactive_member){
			foreach ($check_deactive_member as $key => $data) {
				$id_member = $data['id'];
				
				$this->insert_log_delete_member($data, 2);
				$this->delete_all_data_member($id_member, $data['id_social_media'], $data);
				
			}
			
			$proses['status'] = 1;
			$proses['message'] = 'Akun telah berhasil di delete.';
		} else {
			$proses['status'] = 0;
			$proses['message'] = 'Tidak ada member di delete karena telah jatuh tempo aktivasi kode.';
		}
		return $proses;
	}
	
	function delete_account_fr($order='desc'){
		ini_set("max_execution_time", 0);


		$this->db->limit(1000);
		$check_deactive_member= $this->db->select('*')->get_where('t_aegon_profile_member',"is_old_member is null order by id $order")->result_array();
		if($check_deactive_member){
			foreach ($check_deactive_member as $key => $data) {
				$id_member = $data['id'];
				echo $data['email'].' <br>';
				$this->insert_log_delete_member($data, 3);
				$this->delete_all_data_member($id_member, $data['id_social_media'], $data);
				
			}
			$this->delete_account_fr();
			$proses['status'] = 1;
			$proses['message'] = 'Akun telah berhasil di delete.';
		} else {
			$proses['status'] = 0;
			$proses['message'] = 'Tidak ada member di delete karena telah jatuh tempo aktivasi kode.';
		}
		return $proses;
	}
	
	function delete_all_data_member($id_member, $id_social_media, $data){
		
		$this->load->model('mailchimpModel');
		if($data['newsletter']==1){
			//$this->mailchimpModel->unsubscribe_mailchimp('3b6703b579',$data['email']);
		}				
		if($data['marketing']==1){
			//$this->mailchimpModel->unsubscribe_mailchimp('dff617b903',$data['email']);
		}
		
		$this->db->delete('t_aegon_keep_login', array('userid' => $id_member));
		$this->db->delete('t_aegon_log_login', array('userid' => $id_member));
		$this->db->delete('user_activity_log', array('id_user' => $id_member));
		$this->db->delete('user_article_log', array('id_user' => $id_member));
		$this->db->delete('t_aegon_member_child', array('id_member' => $id_member));
		$this->db->delete('t_aegon_profile_social_media', array('id_social_media' => $data['id_social_media']));
		$this->db->delete('t_aegon_profile_member', array('id' => $id_member));
		$this->db->delete('t_aegon_member_deactive', array('id_member' => $id_member));
		$this->db->delete('t_aegon_member_change_email', array('userid' => $id_member)); 
	}
	
	function insert_log_delete_member($data){
		$data_delete['email'] = $data['email'];
		$data_delete['process_date'] 	= date('Y-m-d H:i:s');
		$this->db->insert('t_aegon_log_delete_not_activation_email',array_filter($data_delete));
		
		//copy data information
		$this->db->insert('t_aegon_profile_member_deleted',array_filter($data));
		
		
	}
 }
