<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Xendit extends CI_Controller
{
	/*
	- custom_field1
	-- 1 ==  event 
	-- 2 == membership
	*/
	public function __construct()
	{
		parent::__construct();
		$this->load->model('eventprice_model');
		$this->load->model('paymentconfirmation_model');
		$this->load->model('eventmodel');
		$this->load->model('member_model');
		$this->load->model('company_model');
	}
	public function payment()
	{
		// $data = file_get_contents("php://input");
		$notif = file_get_contents("php://input");
		$notif       = json_decode($notif);
		if($notif->status == 'PAID'){
			$id_payment = $notif->external_id;
			$type = substr($id_payment,0,1);
			if ($type == 'M') {
				$this->payment_approve('membership', $id_payment);	
			} else if ($type == 'E') {
				$this->payment_approve('event', $id_payment);
			}
		}
	}


	function payment_cancel($type, $id_invoice)
	{
		// untuk method transfer bank
		switch ($type) {
			case 'membership':
				//delete payment confirmation
				$id = $this->paymentconfirmation_model->findBy(array('invoice_number' => $id_invoice), 1)['id'];
				$this->paymentconfirmation_model->delete($id);

				break;

			case 'event':
				//delete payment confirmation
				$payment_data = $this->paymentconfirmation_model->findBy(array('invoice_number' => $id_invoice), 1);
				$id = $payment_data['id'];
				$this->paymentconfirmation_model->delete($id);

				//delete event participant
				$participant_id = $payment_data['member_id'];
				$this->eventmodel->deleteParticipant($participant_id);

				break;
		}
	}
	function payment_approve($type, $id_invoice)
	{
		switch ($type) {
			case 'membership':
				$this->load->model('membership_model');
				//update payment to paid 
				$data_payment    = $this->paymentconfirmation_model->findBy(array('invoice_number' => $id_invoice), 1);
				$data_membership_info = $this->membership_model->findBy(array('member_id' => $data_payment['member_id']), 1)['membership_code'];

				// kalau gak ada membership code 
				$data['is_paid'] = $data_membership_info ? '1' : '2';

				$this->paymentconfirmation_model->update_frontend($data, $data_payment['id']);

				// update member status to paid 
				$update_member['status_payment_id'] = 1;
				$this->member_model->update_frontend($update_member, $data_payment['member_id']);

				$data_member = $this->member_model->findById($data_payment['member_id']);
				$data_company = $this->company_model->findById($data_member['company_id']);


				$membership_category            = db_get_one('auth_member_category', 'name', array('id' => $data_member['member_category_id']));
				$member_fullname                = full_name($data_member);

				$email_admin['category']        = $membership_category;
				$email_admin['is_company']      = $data_member['member_category_id'] == 1  ? 'hide' : '';

				$email_admin['name']            = $member_fullname;
				$email_admin['job']             = $data_member['job'];

				$email_admin['email']           = $data_member['email'];

				$email_admin['name_in']         = $data_company['name_in'];
				$email_admin['company_address'] = $data_company['address'];
				$email_admin['city']            = $data_company['city'];
				$email_admin['postal']          = $data_company['postal_code'];
				$email_admin['headquarters']    = $data_company['headquarters'];
				$email_admin['website']         = $data_company['website'];
				$email_admin['company_email']   = $data_company['email'];
				$email_admin['t_number']        = $data_company['t_number'];
				$email_admin['link']            = base_url() . 'apps';

				// sent_email_by_category(21, $email_admin, EMAIL_ADMIN_TO_SEND);

				break;

			case 'event':
				$data_payment    = $this->paymentconfirmation_model->findBy(array('invoice_number' => $id_invoice), 1);

				event_approve($data_payment['event_id'], $data_payment['member_id'], '', 'nothing');

				break;
		}
	}
}

/* End of file veritrans.php */
/* Location: ./application/controllers/veritrans.php */
