<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reminder extends CI_Controller {
	function __construct(){
		parent::__construct();
		$today					= date('Y-m-d');
		$this->today			= $today;
		$this->h_min60			= add_date($today,60);
		$this->h_min30			= add_date($today,30);
		$this->h_min15			= add_date($today,15);
		$this->h_min14			= add_date($today,14);
		$this->h_min7			= add_date($today,7);
		$this->h_min3			= add_date($today,3);
		$this->h_min1			= add_date($today,1);
		$this->h_H				= add_date($today,0);
		$this->dates_reminder_user_expired        = array(30=>$this->h_min30,0=>$this->h_H);
		$this->dates_reminder_user_status_expired = array(0=>$this->h_H);
		$this->load->model('member_model');
		$this->load->model('company_model');
		$this->load->model('membership_model');
	}

	// 60 H
	function index(){ 
		foreach($this->dates_reminder_user_expired as $id=>$date){
			$this->load->helper('mail');

			$data = $this->member_model->getDataUserExpiredReminder($date); 
			if ($id == 30){
				$keterangan_reminder = "30 hari lagi";
			}  else if ($id == 0){
				$keterangan_reminder = "hari ini";
			} else{
				$keterangan_reminder = " ";
			}
	
			foreach($data as $dt){
				$cek                            = $this->member_model->cekStatusKirim($dt['id'],$this->today);
				
				$data_member                    = $this->member_model->findViewById($dt['member_id']);
				$get_member                     = $this->member_model->findBy(array('id'=> $data_member['member_id']),1);
				$get_company                    = $this->company_model->findBy(array('id'=>$data_member['company_id']),1);
				
				$email_admin['expired_date']    = $data_member['membership_expired'];
				$email_admin['name']            = full_name($get_member);
				$email_admin['job']             = $get_member['job'];
				// $email_admin['citizenship']     = $get_member['citizenship'];
				// $email_admin['linkedin_id']     = $get_member['linkedin_id'];
				$email_admin['email']           = $get_member['email'];
				// $email_admin['name_out']        = $get_company['name_out'];
				$email_admin['name_in']         = $get_company['name_in'];
				$email_admin['company_address'] = $get_company['address'];
				$email_admin['city']            = $get_company['city'];
				$email_admin['postal']          = $get_company['postal_code'];
				$email_admin['headquarters']    = $get_company['headquarters'];
				$email_admin['website']         = $get_company['website'];
				$email_admin['company_email']   = $get_company['email'];
				$email_admin['t_number']        = $get_company['t_number'];
				// $email_admin['m_number']        = $get_company['m_number'];
				
				$superadmin                     = CRON_EMAIL_ADMIN;

				$conf['subject'] 			= $mail_tpt['judul'].' - '.$dt['laporan_ke'].' '.$data_program['mitra'].' jatuh tempo '.$keterangan_reminder ;

				#updateexpiredmembership
				if ($id == 0 ){
					$update_membership['is_expired'] = 1; 
					$id_membership                   = $data_member['membership_id'];
					$this->membership_model->update($update_membership,$id_membership);
					
					$member_id                          = $data_member['member_id'];
					$update_member['status_payment_id'] = 5;
					$this->member_model->update($update_member,$member_id);
				}

				#sentmail
				if(!$cek){
					$email_admin['link'] = base_url_lang().'/member/';
					sent_email_by_category(9,$email_admin,$data_member['member_email']);

					$email_admin['link'] = base_url().'apps/login';
					sent_email_by_category(11,$email_admin,$superadmin);
					// echo "kirim berhasil";

					$insert['sent']                      = date('Y-m-d');
					$insert['membership_information_id'] = $data_member['member_id'];
					$insert['is_manual']                 = 0;
					$this->member_model->simpanDataLaporanTerkirim($insert);
				} else {
					echo "Sorry, User Expired Reminder has been sent";
				}
			}
		}	
	}


}