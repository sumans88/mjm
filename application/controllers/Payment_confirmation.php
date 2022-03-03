<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payment_confirmation extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('member_model');
		$this->load->model('membership_model');
		$this->load->model('company_model');
		$this->load->model('LoginModel');
		$this->load->model('paymentconfirmation_model');
		$this->load->model('paymentconfirmationfiles_model');
	}
	function index(){
		
		$data['invoice_number'] = $this->uri->segment(3);
		$data['bank_name']      = "";
		$data['bank_account']   = "";
		$data['payment_date']   = "";
		$data['amount']         = "";
		$data['note']           = "";
		$data['filename']       = "";
		$data['filename_src']   = "";
		
 		$data['page_heading']    = 'Payment Confirmation';
		$data['banner_top']      = banner_top(); // pake banner top
		$data['widget_sidebar']  = widget_sidebar(); //pake sidebar
		$data['seo_title']       = "AMCHAM INDONESIA";
		$data['hide_breadcrumb'] = 'hide';
		$this->data['recaptcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;

		render("member/payment_confirmation",$data);
	}

	function payment_confirmation_proses2(){		
		$post                 = purify($this->input->post());
		$post['payment_date'] = iso_date_custom_format($post['payment_date'], 'Y-m-d');
		$user_sess_data       = $this->session->userdata('MEM_SESS');
		$data_member          = $this->member_model->findById($user_sess_data['id']);
		$data_payment         = $this->paymentconfirmation_model->findBy(array('member_id'=>$user_sess_data['id'],'is_paid' => 0),1);
		$data_membership      = $this->membership_model->findBy(array('member_id'=>$user_sess_data['id']),1);
		
		$where_paid['member_id']     = $user_sess_data['id'];
		$where_paid['is_paid']       = 1;
		$data_invoice_paid      = $this->paymentconfirmation_model->findBy($where_paid,1);

		$where['invoice_number'] = $post['invoice_number'];
		$where['member_id']      = $user_sess_data['id'];
		$where['is_paid']        = 0;
		$data_invoice            = $this->paymentconfirmation_model->findBy($where,1);
		// print_r($this->db->last_query());exit;
		if (!$data_invoice) {
			$ret['error'] = 1;
			$ret['msg'] = 'Invoice Number Not Found, Please check your email to find your Invoice Number.';
			echo json_encode($ret);exit;
		}
		$check_file = $this->paymentconfirmationfiles_model->findBy(array('payment_confirmation_id'=>$data_invoice['id'],'type_id'=>2),1);
		if (!$check_file) {
			// upload file
			if ($_FILES['file']['name'][0]) {
				$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
				$fileRename               = 'invoice_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['invoice_number'], 0, 50))))."-".date("dMYHis").".".$ext;
				fileToUpload($_FILES['file'],0,$fileRename);

				$invoice_save['filename'] = $fileRename;
			}else{
				$invoice_save['filename'] = "";
			}
			//

			$invoice_save['member_id']               = $user_sess_data['id'];
			$invoice_save['payment_confirmation_id'] = $data_invoice['id'];
			$invoice_save['type_id']                 = 2;
			$this->paymentconfirmationfiles_model->member_insert($invoice_save);
		}else{
			if ($_FILES['file']['name'][0]) {
				$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
				$fileRename               = 'invoice_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['invoice_number'], 0, 50))))."-".date("dMYHis").".".$ext;
				fileToUpload($_FILES['file'],0,$fileRename);

				$invoice_save['filename'] = $fileRename;
			}else{
				$invoice_save['filename'] = $check_file['filename'];
			}
			//

			$invoice_save['member_id']               = $user_sess_data['id'];
			$invoice_save['payment_confirmation_id'] = $data_invoice['id'];
			$invoice_save['type_id']                 = 2;
			$this->paymentconfirmationfiles_model->member_update($invoice_save,$check_file['id']);
		}
		//update payment_confirmation
		unset($post['file']);
		$this->paymentconfirmation_model->update_frontend($post,$data_invoice['id']);
		detail_log();

		//sent email to admin 
			$email_admin['name']            = full_name($user_sess_data);
			$email_admin['invoice_number']  = $post['invoice_number'];
			$email_admin['account_name']    = $post['bank_name'];
			$email_admin['payment_date']    = ($post['payment_date'] != '0000:00:00')?iso_date($post['payment_date']):'-';
			$email_admin['amount']          = $post['amount'];
			$email_admin['notes']           = $post['note'];
			$email_admin['user_id']         = $data_member['email'];
			// $email_admin['membership_code'] = $data_membership['membership_code'];


			$email_admin['link']           = base_url().'apps/login';
			//file attarch
			$email_admin['filename']       = $fileRename;
			$email_admin['path_file']      = 'file_upload';
		//
		if($data_invoice_paid){
			sent_email_by_category(5,$email_admin,EMAIL_ADMIN_TO_SEND);
			insert_frontend_log('Member Send Payment Confirmation');
		}else{
			sent_email_by_category(5,$email_admin,EMAIL_ADMIN_TO_SEND);
			insert_frontend_log('Member Send Payment Confirmation');
		}

		$ret['error']     = 0;
		$ret['modalname'] = 'myModalThanks';
		echo json_encode($ret);exit;
		
	}
	function payment_confirmation_proses(){		
		$post                 = purify($this->input->post());
		$post['payment_date'] = iso_date_custom_format($post['payment_date'], 'Y-m-d');
		$data_payment         = $this->paymentconfirmation_model->findBy(array('invoce_number'=>$post['invoice_number'],'is_paid' => 0),1);
			
		$is_membership 		  = empty($data_payment['event_id']) ? true : false;
		$data_member          = $is_membership ? $this->member_model->findById($data_payment['member_id']):$this->EventModel->selectDataParticipant($data_payment['member_id'],1);
		
		$where['invoice_number'] = $post['invoice_number'];
		$where['is_paid']        = 0;
		$data_invoice            = $this->paymentconfirmation_model->findBy($where,1);
		
		if (!$data_invoice) {
			$ret['error'] = 1;
			$ret['msg'] = 'Invoice Number Not Found, Please check your email to find your Invoice Number.';
			echo json_encode($ret);exit;
		}

		$check_file = $this->paymentconfirmationfiles_model->findBy(array('payment_confirmation_id'=>$data_invoice['id'],'type_id'=>2),1);
		if (!$check_file) {
			// upload file
			if ($_FILES['file']['name'][0]) {
				$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
				$fileRename               = 'invoice_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['invoice_number'], 0, 50))))."-".date("dMYHis").".".$ext;
				fileToUpload($_FILES['file'],0,$fileRename);

				$invoice_save['filename'] = $fileRename;
			}else{
				$invoice_save['filename'] = "";
			}
			//

			$invoice_save['member_id']               = $data_payment['id'];
			$invoice_save['payment_confirmation_id'] = $data_invoice['id'];
			$invoice_save['type_id']                 = 2;
			$this->paymentconfirmationfiles_model->member_insert($invoice_save);
		}else{
			if ($_FILES['file']['name'][0]) {
				$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
				$fileRename               = 'invoice_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['invoice_number'], 0, 50))))."-".date("dMYHis").".".$ext;
				fileToUpload($_FILES['file'],0,$fileRename);

				$invoice_save['filename'] = $fileRename;
			}else{
				$invoice_save['filename'] = $check_file['filename'];
			}
			//

			$invoice_save['member_id']               = $data_payment['id'];
			$invoice_save['payment_confirmation_id'] = $data_invoice['id'];
			$invoice_save['type_id']                 = 2;
			$this->paymentconfirmationfiles_model->member_update($invoice_save,$check_file['id']);
		}
		//update payment_confirmation
		unset($post['file']);
		$this->paymentconfirmation_model->update_frontend($post,$data_invoice['id']);
		detail_log();

		if ($is_membership) {
			//sent email to admin 
				$email_admin['name']            = full_name($data_member);
				$email_admin['invoice_number']  = $post['invoice_number'];
				$email_admin['account_name']    = $post['bank_name'];
				$email_admin['payment_date']    = ($post['payment_date'] != '0000:00:00')?iso_date($post['payment_date']):'-';
				$email_admin['amount']          = $post['amount'];
				$email_admin['notes']           = $post['note'];
				$email_admin['user_id']         = $data_member['email'];


				$email_admin['link']           = base_url().'apps/login';
				//file attarch
				$email_admin['filename']       = $fileRename;
				$email_admin['path_file']      = 'file_upload';
			//
				sent_email_by_category(5,$email_admin,EMAIL_ADMIN_TO_SEND);
				insert_frontend_log('Member Send Payment Confirmation');
		}else{

				if ($data_participant['fullname'] != '') {
					$participant_name = $data_member['fullname'] ; 
				}else if($data_member['firstname'] != ''){
					$participant_name = $data_member['firstname'] ; 
				}else{
					$participant_name = $data_member['lastname'] ; 

				}

		                // email
				if ($data_member['email_1'] != '') {
					$participant_email = $data_member['email_1'];
				}else if ($data_member['email_2'] != '') {
					$participant_email = $data_member['email_2'];
				}else{
					$participant_email = '';
				}


				$email_admin['event_name']      = db_get_one('event','name',array('id'=>$data_payment['event_id'])); 
				$email_admin['participant_name']  = $participant_name; 
				$email_admin['invoice_number']  = $post['invoice_number'];
				$email_admin['note']           = $post['note'];


				$email_admin['link']           = base_url().'apps/login';
				//file attarch
				$email_admin['filename']       = $fileRename;
				$email_admin['path_file']      = 'file_upload';

				sent_email_by_category(18,$email_admin,$participant_email);
				insert_frontend_log('Participant Event Send Payment Confirmation');
		}

		$ret['error']     = 0;
		$ret['modalname'] = 'myModalThanks';
		echo json_encode($ret);exit;
		
	}
	
}

/* End of file member.php */
/* Location: ./application/controllers/apps/payment_confirmation.php */