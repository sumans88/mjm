<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Backup_conf extends CI_Controller { 
	function index(){
		session_start();
		$data 						= $this->db->get('backup_conf')->row_array();
		$data['status_not_active']	= ($data['status'] == '0') ? 'checked' :'';
		
		for($n=1;$n<=30;++$n){
			$selected = ($data['hari'] == $n) ? 'selected' : '';
			$list_hari .= "<option $selected>$n</option>";
		}
		$data['list_hari']			= $list_hari;
        render('apps/backup/conf',$data,'apps');
	}
	function proses(){
		$post = $this->input->post();
		$this->db->update('backup_conf',$post);
		$file =  BACKUP_DIR.'backupDB.sh';
		if($post['status']== '1'){
			$data = $this->db->get('backup_conf')->row_array();
			$content = str_replace('JML_HARI',$data['hari'],$data['scipt_backup']);
			$content = str_replace('DB_NAME',$data['db_name'],$content);
			$content = str_replace('DB_USER',$data['db_user'],$content);
			$content = str_replace('DB_PASS',$data['db_pass'],$content);
		}
		else{
			$content = '';
		}
		//echo $content;
		$this->load->helper('file');
		if(write_file($file, $content)){
			echo 'Update Success';
		}
		else{
			echo 'error';
		}
		
	}
}