<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Midtrans extends CI_Controller {
	/*
	- custom_field1
	-- 1 ==  event 
	-- 2 == membership
	*/	 
	public function __construct()
	{
		parent::__construct();
		include APPPATH . 'third_party/veritrans/Veritrans.php';
		$this->load->model('eventprice_model');
		$this->load->model('paymentconfirmation_model');
		$this->load->model('eventmodel');
		$this->load->model('member_model');
		$this->load->model('company_model');
	}

	public function event_getSnapToken()
	{
		$post 		= $this->input->post();
		$price_data = $this->eventprice_model->findBy(array('id' => $post['id_ref_event_price']),1);

		$price_name   = $price_data['alias'] == '' ? $price_data['name'] : $price_data['alias'];
		$price_amount = $price_data['amount'];
		//Set Your server key
		Veritrans_Config::$serverKey = MIDTRANS_SERVER_KEY;

		// Uncomment for production environment
	    Veritrans_Config::$isProduction = true;

		// Enable sanitization
		Veritrans_Config::$isSanitized = true;

		// Enable 3D-Secure
		Veritrans_Config::$is3ds = true;
		// Required
		$order_id = 'E-'.rand(1000000000000,9999999999999);

		$transaction_details = array(
		  'order_id' => $order_id,
		  'gross_amount' => $price_amount, // no decimal allowed for creditcard
		);

		// Optional
		$item1_details = array(
		  'id' => 'ITEM1',
		  'price' => $price_amount,
		  'quantity' => 1,
		  'name' => $price_name
		);

		// Optional
		$item_details = array ($item1_details);

		
		// Fill transaction details
		$transaction = array(
		  'enabled_payments' => $enable_payments,
		  'transaction_details' => $transaction_details,
		  'customer_details' => $customer_details,
		  'item_details' => $item_details,
		  'custom_field1' => 1
		);

		$data['token'] = Veritrans_Snap::getSnapToken($transaction);

		// insert invoice 
		$data_insert_invoice['invoice_number']          = $order_id;
		$data_insert_invoice['id_ref_payment_type']     = 1;
		$data_insert_invoice['id_ref_payment_category'] = 1;
		$data_insert_invoice['price_id']                = $post['id_ref_event_price'] ;
		$data_insert_invoice['event_id']                = $post['event_id'] ;
		$this->paymentconfirmation_model->insert($data_insert_invoice);

		$insert_log['result']         = 'event registered';
		$insert_log['invoice_number'] = $order_id;
		$insert_log['payment_type']   = 'payment_online';
		$insert_log['status']         = 'registered';
	    $insert_log['process_date']    = date('Y-m-d H:i:s');		 
		$this->db->insert('midtrans_log', $insert_log);

		$data['event_id']       = $post['event_id'] ;
		$data['invoice_number'] = $order_id; 

		echo json_encode($data);
        exit();
	}

	public function membership_getSnapToken()
	{
		$post 		= $this->input->post();
		$member_category_data = $this->db->get_where('auth_member_category',array('id'=>$post['member']['member_category_id']))->row_array();

		$price_name   = $member_category_data['alias'] == '' ? $member_category_data['name'] : $member_category_data['alias'];
		$price_amount = $member_category_data['price'];
		//Set Your server key
		Veritrans_Config::$serverKey = MIDTRANS_SERVER_KEY;

		// Uncomment for production environment
		Veritrans_Config::$isProduction = true;

		// Enable sanitization
		Veritrans_Config::$isSanitized = true;

		// Enable 3D-Secure
		Veritrans_Config::$is3ds = true;
		// Required
		$order_id = 'M-'.rand(1000000000000,9999999999999);

		$transaction_details = array(
		  'order_id' => $order_id,
		  'gross_amount' => $price_amount, // no decimal allowed for creditcard
		);

		// Optional
		$item1_details = array(
		  'id' => 'ITEM1',
		  'price' => $price_amount,	
		  'quantity' => 1,
		  'name' => $price_name
		);
		$item1_details2 = array(
		  'id' => 'ITEM2',
		  'price' => $post['member']['member_category_id'] == 1 ? 900000 : 260000,
		  'quantity' => 1,
		  'name' => 'Convenience Fee'
		);

		// Optional
		$item_details = array ($item1_details,$item1_details2);

		
		// Fill transaction details
		$transaction = array(
		  'enabled_payments' => $enable_payments,
		  'transaction_details' => $transaction_details,
		  'customer_details' => $customer_details,
		  'item_details' => $item_details,
		  'custom_field1' => 2
		);

		$data['token'] = Veritrans_Snap::getSnapToken($transaction);
		// insert invoice 
		$data_insert_invoice['invoice_number']          = $order_id;
		$data_insert_invoice['id_ref_payment_category'] = 2;
		$data_insert_invoice['id_ref_payment_type'] = 1;
		$this->paymentconfirmation_model->insert($data_insert_invoice);

		$insert_log['result']         = 'membership	 registered';
		$insert_log['invoice_number'] = $order_id;
		$insert_log['payment_type']   = 'payment_online';
		$insert_log['status']         = 'registered';
		$insert_log['process_date']    = date('Y-m-d H:i:s');		 
		$this->db->insert('midtrans_log', $insert_log);

		$data['id_event']       = $post['event_id'] ;
		$data['price_id']       = $post['id_ref_event_price'] ;
		$data['price_id']       = $post['id_ref_event_price'] ;
		$data['invoice_number'] = $order_id;

		echo json_encode($data);
        exit();
	}

	public function notification(){
		$params = array('server_key' => MIDTRANS_SERVER_KEY, 'production' => MIDTRANS_IS_PRODUCTION);
		$this->load->library('veritrans');
		$this->veritrans->config($params);
		$this->load->helper('url');

		$json_result = file_get_contents('php://input');
		$notif       = json_decode($json_result);

		//notification handler sample
		$transaction      = $notif->transaction_status;
		$type             = $notif->payment_type;
		$order_id         = $notif->order_id;
		$gross_amount     = $notif->gross_amount;
		$transaction_time = $notif->transaction_time;
		$fraud            = $notif->fraud_status;
		$custom_field1    = $notif->custom_field1;

		if ($transaction == 'capture') {
		  // For credit card transaction, we need to check whether transaction is challenge by FDS or not
		  if ($type == 'credit_card'){
		    if($fraud == 'challenge'){
		      // TODO set payment status in merchant's database to 'Challenge by FDS'
		      // TODO merchant should decide whether this transaction is authorized or not in MAP
		      $insert_log["message"] = "Transaction Order ID: " . $order_id ." is challenged by FDS";
		      } 
		      else {
		      	// TODO set payment status in merchant's database to 'Success'
		      	
		      	// event
		      	if(!empty($custom_field1)){
			      	if ($custom_field1 == 1 ) {
						$this->payment_approve('event',$order_id);
			      	}
			      	//membership
			      	else{
						$this->payment_approve('membership',$order_id);
			      	}
		      	}

		      $insert_log["message"] = "Transaction Order ID: " . $order_id ." successfully captured using " . $type;
		      }
		    }
		  }
		else if ($transaction == 'settlement'){
		  // TODO set payment status in merchant's database to 'Settlement'
		  $insert_log['message'] = "Transaction Order ID: " . $order_id ." successfully transfered using " . $type;
		   // event 
		  if(!empty($custom_field1)){
		  	if ($custom_field1 == 1 ) {
		  		$this->payment_approve('event',$order_id);
		  	}
			      	//membership
		  	else{
		  		$this->payment_approve('membership',$order_id);
		  	}
		  }
		} 
		else if($transaction == 'pending'){
		  // TODO set payment status in merchant's database to 'Pending'
		  $insert_log["message"] = "Waiting customer to finish transaction Order ID: " . $order_id . " using " . $type;
		} 
		else if ($transaction == 'deny') {
		  // TODO set payment status in merchant's database to 'Denied'
		  $insert_log["message"] = "Payment using " . $type . " for transaction Order ID: " . $order_id . " is denied.";
		} else if($transaction == 'expire'){
		  // TODO set payment status in merchant's database to 'expire'
		  $insert_log["message"] = "Payment using " . $type . " for transaction Order ID: " . $order_id . " is expired. using " . $type;
		}
		$insert_log['result']         = json_encode($notif);
		$insert_log['invoice_number'] = $order_id;
		$insert_log['payment_type']   = $type;
		$insert_log['status']         = $transaction;
		$insert_log['process_date']    = date('Y-m-d H:i:s');		 
		$this->db->insert('midtrans_log', $insert_log);
/*
			// kirim email check json	
		if(!$config){
		  $config = $this->db->get('email_config')->row_array();
		}
		$this->load->library('email');
		$this->email->initialize(array(
		  'protocol' => 'smtp',
		  'smtp_host' => $config['smtp_host'],
		  'smtp_user' => $config['smtp_user'],
		  'smtp_pass' => $config['smtp_pass'],
		  'smtp_port' => $config['port'],
		  'crlf' => "\r\n",
		  'newline' => "\r\n",
		  'mailtype' => 'html', 
		  'charset' => 'iso-8859-1'
		));
		$this->email->from('no-reply@amcham.org', 'amcham');
		$this->email->to('amar.ronaldo.m@gmail.com');
		$this->email->subject('midtrans notif 2');
		$this->email->message(
			'JSON : '.json_encode($notif).
			'<br/> <br/>Query input : '. $notif->custom_field1.
			'<br/> <br/>Query input midtrans: '. $query2
		);

		$check_status_email_sent  = $this->email->send();
		exit;*/
	}

	function payment_cancel($type,$id_invoice){
		// untuk method transfer bank
		switch ($type) {
			case 'membership':
				//delete payment confirmation
				$id = $this->paymentconfirmation_model->findBy(array('invoice_number' => $id_invoice),1)['id'];
				$this->paymentconfirmation_model->delete($id);
				
				break;

			case 'event':
				//delete payment confirmation
				$payment_data = $this->paymentconfirmation_model->findBy(array('invoice_number' => $id_invoice),1);
				$id = $payment_data['id'];
				$this->paymentconfirmation_model->delete($id);

				//delete event participant
				$participant_id = $payment_data['member_id'];
				$this->eventmodel->deleteParticipant($participant_id);

				break;
		}
	}
	function payment_approve($type,$id_invoice){
		switch ($type) {
			case 'membership':
				$this->load->model('membership_model');
				//update payment to paid 
				$data_payment    = $this->paymentconfirmation_model->findBy(array('invoice_number' => $id_invoice),1);
				$data_membership_info = $this->membership_model->findBy(array('member_id'=>$data_payment['member_id']),1)['membership_code'];

				// kalau gak ada membership code 
				$data['is_paid'] = $data_membership_info ? '1':'2';

				$this->paymentconfirmation_model->update_frontend($data,$data_payment['id']);

				// update member status to paid 
				$update_member['status_payment_id'] = 1;
				$this->member_model->update_frontend($update_member,$data_payment['member_id']);

				$data_member = $this->member_model->findById($data_payment['member_id']);
				$data_company = $this->company_model->findById($data_member['company_id']);
				

		        $membership_category            = db_get_one('auth_member_category','name',array('id'=>$data_member['member_category_id']));
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
				$email_admin['link']            = base_url().'apps';

				sent_email_by_category(21,$email_admin,EMAIL_ADMIN_TO_SEND);

				break;

			case 'event':
				$data_payment    = $this->paymentconfirmation_model->findBy(array('invoice_number' => $id_invoice),1);

				event_approve($data_payment['event_id'],$data_payment['member_id'],'','nothing');

				break;
		}
	}

}

/* End of file veritrans.php */
/* Location: ./application/controllers/veritrans.php */