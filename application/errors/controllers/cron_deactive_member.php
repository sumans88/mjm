<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cron_Deactive_Member extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('CronModel');
	}
	function delete_member(){
		//cron job untuk delete member yang sudah menonaktifkan selama lebih dari 30 hari
		$proses = $this->CronModel->delete_account();
		if($proses['status']==1){
			$status = 'success';
			$message = $proses['message'];	
		}
		else{
			$status = 'Terjadi Kesalahan';
			$message = $proses['message'];	
		}
		echo $message;
	}
}