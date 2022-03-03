<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cron_Action_Member extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('CronModel');
	}
	function delete_member_nonactive(){
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
	
	function delete_member_not_confirmation_email(){
		//cron job untuk delete member register yang belum melakukan konfirmasi melalui selama lebih dari hari maksimal
		$proses = $this->CronModel->delete_account_not_activate_email();
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
	
	function migrate_member_fr($order){
		//cron job untuk delete member register yang belum melakukan konfirmasi melalui selama lebih dari hari maksimal
		$proses = $this->CronModel->delete_account_fr($order);
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