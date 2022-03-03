<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payment_confirmation extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('member_model');
		$this->load->model('membership_model');
		$this->load->model('company_model');
		$this->load->model('paymentconfirmation_model');
		$this->load->model('paymentconfirmationfiles_model');
	}

	function index(){
		$CI =& get_instance();
		$data['modal_download']   = $this->parser->parse('apps/payment_confirmation/modal_download.html',$CI->data,TRUE);
		$data['modal_invoice']   = $this->parser->parse('apps/payment_confirmation/modal_invoice.html',$CI->data,TRUE);
		$data['list_membership'] = selectlist2(array('table'=>'auth_member_category','title'=>'Membership Category'));
		$data['list_payment_type'] = selectlist2(array('table'=>'ref_payment_type','title'=>'Select'));
		$data['list_status']     = "<option value=''>Select Status Invoice</option>
                                    <option value='0'>Not Paid </option>
                                    <option value='1'>Paid </option>
                                    <option value='2'>Paid - Without M
                                    embership Id </option>
                                    ";
		render('apps/payment_confirmation/index',$data,'apps');
	}
	
	function add($id=''){
		if($id){
			$data = $this->member_model->findById($id);

            if(!$data){
				die('404');
			}
			$data 				= quote_form($data);
			$data['judul']		= 'Edit';
			$data['proses']		= 'Update';

			if($data['is_invis']=='1'){
				$data['checked_gratis'] = 'checked';
			}

		}
		else{
			$data['judul']			= 'Add';
			$data['proses']			= 'Simpan';
			$data['name_in'] 		= '';
            $data['name_out'] 		= '';
            $data['address'] 		= '';
            $data['headquarters']	= '';
            $data['description']	= '';
            $data['city']			= '';
            $data['website']		= '';
            $data['email'] 			= '';
            $data['img'] 			= '';
			$data['id'] 			= '';
		}


		$img_thumb 		= image($data['img'],'small');
		$imagemanager	= imagemanager('img',$img_thumb,750,186);
		$data['img']	= $imagemanager['browse'];
		$data['imagemanager_config'] 	= $imagemanager['config'];
		$data['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['id_status_publish']));
		$data['list_languages'] 		= selectlist2(array('table'=>'language','title'=>'All Languages','selected'=>$data['id_lang']));



		render('apps/payment_confirmation/add',$data,'apps');
	}
	function view($id=''){
		$CI =& get_instance();
		if($id){
			$data                     = $this->member_model->findById($id);			
			$p_email                  = $data['email'];
			$CI->data['is_sent']      = $data['status_payment_id'] == 4 ? 'invis': '';
			// $data['is_paid']       = $data['status_payment_id'] == 1 ? 'invis': '';
			$data['dsp_button_sent']  = $data['is_paid'] == 1 ? 'invis':'';
			
			$data2                    = $this->company_model->findById($data['company_id']);
			$data                     = array_merge($data,$data2);
			
			// $data['modal_invoice'] = $this->parser->parse('apps/payment_confirmation/modal_invoice.html',$CI->data,TRUE);
			
			$data['full_name']        = full_name($data);
			$data['create_date']      = iso_date($data['create_date']);
			$data['status']           = $data['status'] ? $data['status'] : 'new';
			$data['is_individu']      = ($data['member_category_id'] == 1 ) ? 'hide' : '';
			$data['is_company']       = ($data['member_category_id'] == 1 ) ? '' : 'hide';
			$data['c_email']          = $data2['email'];
			$data['p_email']          = $p_email;
			$data['c_address']        = $data2['address'];
			$data['membership']       = db_get_one('auth_member_category', 'name',array('id' => $data['member_category_id']));

			render('apps/payment_confirmation/view',$data,'apps');
		}else{
			redirect('apps/payment_confirmation');
		}
	}
	function approve(){
		$post               = $this->input->post();
		$no_anggota         = $post['no_anggota'];
		$member_id          = $post['member_id'];
		$data_member        = $this->member_model->findById($member_id);

		
		$where['member_id'] = $post['member_id'];
		// $this->db->where('is_paid', 0);
		// $this->db->or_where('is_paid', 2);
		$data_invoice       = $this->paymentconfirmation_model->findBy($where,1);
		// print_r($this->db->last_query());exit;

		$where_paid['member_id']               = $member_id;
		$where_paid['is_paid']                 = 1;
		$data_invoice_paid                     = $this->paymentconfirmation_model->findBy($where_paid,1);

		$where_file['payment_confirmation_id'] = $data_invoice['id'];
		$where_file['type_id']                 = 2;
		$data_invoice_file                     = $this->paymentconfirmationfiles_model->findBy($where_file,1);
		

		//check no anggota input
		$where_anggota['membership_code'] = $no_anggota;
		$where_anggota['member_id !=']    = $member_id;

		$check_no_anggota = $this->membership_model->findBy($where_anggota,1);
		if ($check_no_anggota) {
			$ret['error'] = 1;
			$ret['msg']   = 'Membership Code already taken ';
			echo json_encode($ret);
			return false;
		}
		$data_update_payment['is_paid'] = '1';

		$this->paymentconfirmation_model->update($data_update_payment,$data_invoice['id']);
		if (empty($data_invoice_paid)) { // notexpired
			//update membership info 

			$membership_save['membership_code'] = $no_anggota;
			$membership_save['registered_date'] = date("Y-m-d");
			$membership_save['expired_date']    = date_expired();
			$this->membership_model->update($membership_save,$data_member['membership_information_id']);
			// print_r($this->db->last_query());exit;
			
			//update company no_anggota
			// $this->company_model->update(array('nomor_anggota'=>$no_anggota),$data_member['company_id']);
			// $this->member_model->update(array('password'=>md5($no_anggota)),$data_member['id']);

			//update user_password dengan no_anggota
			$this->member_model->update(array('password'=>md5($no_anggota),'status_payment_id'=>1),$data_member['id']);

			//sent email user (new password)
			$email_user['full_name'] = full_name($data_member);
			$email_user['password']  = $no_anggota;
			$email_user['email']     = $data_member['email'];
			$email_user['link']      = base_url_lang()."member";
			sent_email_by_category(4,$email_user,$data_member['email']);
			
		}else{ // extend expired
			//update membership info 
			$membership_save['expired_date']    = date_expired();
			$membership_save['is_expired']      = 0;
			$membership_save['membership_code'] = $no_anggota;
			$this->membership_model->update($membership_save,$data_member['membership_information_id']);

			// update auth_member
			$update_member['status_payment_id'] = 1;
			$this->member_model->update($update_member,$member_id);

			//sent email user (new password)
			$email_user['full_name'] = full_name($data_member);
			$email_user['email']     = $data_member['email'];
			$email_user['link']      = base_url_lang()."member";
			sent_email_by_category(12,$email_user,$data_member['email']);
		}
		/* update payment file  */
		if ($_FILES['file']['name'][0]) {
			$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);

			$fileRename               = 'confirm_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($invoice_number, 0, 50))))."-".date("dMYHis").".".$ext;
			fileToUpload($_FILES['file'],0,$fileRename);
		}else{
			$fileRename =$data_invoice_file['filename'];
		}

		// update status payment || invoice
		$payment_update['is_paid']      = "1";
		$payment_update['bank_name']    = $post['bank_name'];
		$payment_update['bank_account'] = $post['bank_account'];
		$payment_update['payment_date'] = iso_date_custom_format($post['payment_date'],'Y-m-d');
		$payment_update['amount']       = $post['amount'];
		$payment_update['note']         = $post['note'];

		$this->paymentconfirmation_model->update($payment_update,$data_invoice['id']);



		// if insert file payment 
		$insert_payment['filename']                = $fileRename;
		$insert_payment['member_id']               = $post['member_id'];
		$insert_payment['payment_confirmation_id'] = $data_invoice['payment_id'];
		$insert_payment['type_id']                 = 2;

		//insert file gambar
		if ($data_invoice['payment_id']) {
			$this->paymentconfirmationfiles_model->update($insert_payment,$data_view_member['payment_id']);
		}else{
			$this->paymentconfirmationfiles_model->insert($insert_payment);
		}
		/* end update payment file */

		$ret['error']       = 0;
		$ret['msg']         = 'Success';
		$ret['close_modal'] = 'modal-send-invoice';
		echo json_encode($ret);
	}

	function sent_invoice($draft){
		$post                      = $this->input->post();
		$member_id                 = $post['member_id'];
		$invoice_number            = $post['invoice_number'];
		$data_member 			   = $this->member_model->findById($member_id);
		
		$where_pc['member_id'] 	   = $member_id;
		$where_pc['is_paid']  	   = 0;
		
		$check_pc                  = $this->paymentconfirmation_model->findBy($where_pc,1);
		$pc_save['invoice_number'] = $invoice_number;
		$pc_save['member_id']      = $member_id;

		if ($check_pc) {
			//update payment confirmation
			$id_payment = $this->paymentconfirmation_model->update($pc_save,$check_pc['id']);
			$action     = 'update';
			$ret['msg'] = 'Update Success';
			
		}else{
			//save payment confirmation
			$id_payment = $this->paymentconfirmation_model->insert($pc_save);
			$action     = 'save';
			$ret['msg'] = 'Insert Success';
		}

		// upload file
		if ($_FILES['file']['name'][0]) {
			$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);

			$fileRename               = 'invoice_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($invoice_number, 0, 50))))."-".uniqid().".".$ext;
			/*echo pathinfo($file, PATHINFO_EXTENSION); exit();*/
			fileToUploadInvoiceImage($_FILES['file'],0,$fileRename);
		}else{
			$fileRename = $this->paymentconfirmationfiles_model->findBy(array('payment_confirmation_id'=>$id_payment,'type_id'=>1),1)['filename'];
		}
			$invoice_save['filename']                = $fileRename;
			$invoice_save['member_id']               = $member_id;
			$invoice_save['payment_confirmation_id'] = $id_payment;
			$invoice_save['type_id']                 = 1;			

		if ($action == 'update') {
			$where['payment_confirmation_id'] = $id_payment;
			$where['member_id']               = $member_id;
			$id_pc                            = $this->paymentconfirmationfiles_model->findBy($where,1)['id'];
			$this->paymentconfirmationfiles_model->update($invoice_save,$id_pc);
		}else{
			$this->paymentconfirmationfiles_model->insert($invoice_save);
		}

		/* check bila di post draft || status payment id 1 / 4*/
		$updatestatus = in_array($data_member['status_payment_id'], array(1,4)) ? 0: 1;

		if ($updatestatus && $draft) {
			$this->member_model->update(array('status_payment_id' => 3),$member_id); // draft
			detail_log();
			insert_log('member invoice draft');
		}else{
			$this->member_model->update(array('status_payment_id' => 4),$member_id); // sent invoice
			detail_log();
			insert_log('sent member invoice');

			//sent invoice to member
			$email_admin['name']           = full_name($data_member);
			$email_admin['invoice_number'] = $post['invoice_number'];
			//file attarch
			$email_admin['filename']       = $fileRename;
			$email_admin['path_file']      = 'file_upload';

			// $email_member = 'amar.ronaldo.m@gmail.com';
			$email_member = $data_member['email'];
			// ($data_member['member_category_id'] == 1) ?  : ;			
			if (sent_email_by_category(7,$email_admin,$email_member)) {
				$ret['msg'] = 'Success Sent Invoice';
			}else{
				$ret['msg'] = 'Failed Sent Invoice';
			}
		}
		$ret['error'] = 0;
		$ret['close_modal'] = 'modal-id-member';
		echo json_encode($ret);
	}

	function records(){
		$data = $this->paymentconfirmation_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['full_name']    = full_name($value); 
			$data['data'][$key]['create_date'] = iso_date($value['create_date']);
			// $data['data'][$key]['is_paid']      = $value['status_payment_id'] == 1 ? 'invis':'';

			$data['data'][$key]['dsp_button_sent']      = !in_array($value['status_payment_id'], array('1','4','5')) ? 'invis':'';
			// $this->db->where('type_id', 2);
			$img = $this->paymentconfirmationfiles_model->findBy(
																array('payment_confirmation_id' => $value['id_payment'], 'type_id'=> 2),1)['filename'];
			// $data['data'][$key]['preview_image'] = ($img) ? '<a href="'.base_url().'file_upload/'.$img.'" target="_BLANK"><img src="'.base_url().'file_upload/'.$img.'" alt="Confirmation-preview" width="150px"></a>':'';
			$data['data'][$key]['preview_image'] = ($img) ? '<a href="'.base_url().'file_upload/'.$img.'" target="_BLANK">preview</a>':'';
			// $value['img_name'];
			
			// $data['data'][$key]['status']      = $value['status'] ? $value['status'] : 'new';
			// $data['data'][$key]['approval_level'] 	= $approval;
		}
		render('apps/payment_confirmation/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user 		= id_user();
		$this->layout 	= 'none';
		$post 			= purify($this->input->post());
		$ret['error']	= 1;
		$this->db->trans_start();

		$this->form_validation->set_rules('name_in', '"Name Company"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			$ret['message']  = validation_errors(' ',' ');
		}
		else{
			// print_r($post);

			$post['img'] = $post['img'][0];

			if($idedit){
				auth_update();
				$ret['message'] = 'Update Success';
				$act			= "Update Company";
				$this->company_model->update($post,$idedit);
			}
			else{
				auth_insert();
				$ret['message'] = 'Insert Success';
				$act			= "Insert Company";
				$iddata 		= $this->company_model->insert($post);
			}

			$this->db->trans_complete();
			set_flash_session('message',$ret['message']);
			$ret['error'] = 0;
		}
		echo json_encode($ret);
	}

	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->delete($id);
		$this->db->trans_complete();
	}

	function invoice_check($id,$id_payment,$is_event){
		// $where['member_id']     = $id;
		// $where['is_paid']       = 0;
		$data_invoice           = $this->paymentconfirmation_model->findById($id_payment);
		if (!empty($is_event)) {
			$ret['event_id'] = $data_invoice['event_id'];
			$ret['event_participant_id'] = $data_invoice['member_id'];
		}
		$data_invoice           = $this->paymentconfirmation_model->findById($id_payment);
		$data_member            = $this->member_model->findBy(array('id'=>$id),1);
		$data_membership        = $this->membership_model->findBy(array('member_id'=>$id),1);

		// untuk hide button draft
		$ret['is_sent']         = ( in_array($data_member['status_payment_id'], array(1,4,5) ) ) ? 1: 0;
		// kalau member sudah aktif tidak bisa di confirm lagi 
		$ret['dsp_btn_approve'] = $data_invoice['is_paid'] == 1 ? true: false;

		// $ret['dsp_btn_approve'] = ( in_array($data_member['status_payment_id'], array(1) ) ) ? "invis": "";

		// cari invoice yang sudah dibayar , kalau sudah ada berarti ini untuk extend membership 
		$where['member_id']     = $id;
		$where['is_paid']       = 1;
		$data_invoice_paid      = $this->paymentconfirmation_model->findBy($where,1);

		// // mendisable no_anggota bila expired
		// $ret['disable_expired'] = empty($data_invoice_paid)? false :true;
		// // mendisable + hide expired date bila invoice pertama kali 
		// $ret['dsp_expired_date'] = empty($data_invoice_paid)? true :false;
		// $ret['expired_date'] = !empty($data_invoice_paid)  ?date_expired():iso_date($data_membership['expired_date']);


		if ($data_invoice) {
			$ret['invoice_number']                 = $data_invoice['invoice_number'];
		
			$where_file['payment_confirmation_id'] = $data_invoice['id'];
			$where_file['type_id']                 = 2;
			$where_file['member_id']               != $id;
			$check_file                            = $this->paymentconfirmationfiles_model->findBy($where_file,1);

			$ret['filename']                       = (!empty($check_file)) ? base_url().'file_upload/'.$check_file['filename']:'';
			$ret                                   = array_merge($ret,$data_invoice);
			$ret['payment_date'] =iso_date($ret['payment_date']);
		}else{
			$ret['invoice_number'] = '';
			$ret['filename']       = '';
		}
		$ret['is_bank_transfer'] = $data_invoice['id_ref_payment_type'] == 2 ? true : false ;
		$ret['user_id']       = $data_member['email'];		
		$ret['no_anggota'] = $this->membership_model->findBy(array('member_id'=>$id),1)['membership_code'];
		$ret['payment_active']  =  $data_invoice['is_paid'];

		echo json_encode($ret);
	
}
	function approve_extend(){
		$post        = $this->input->post();
		$member_id   = $post['member_id'];

		$data_view_member = $this->member_model->findViewBy(array('member_id'=>$member_id),1);
		if ($_FILES['file']['name'][0]) {
			$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);

			$fileRename               = 'confirm_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($invoice_number, 0, 50))))."-".date("dMYHis").".".$ext;
			/*echo pathinfo($file, PATHINFO_EXTENSION); exit();*/
			fileToUpload($_FILES['file'],0,$fileRename);
		}else{
			$fileRename =$data_view_member['payment_file'];
		}
		$insert_payment['filename']                = $fileRename;
		$insert_payment['member_id']               = $data_view_member['member_id'];
		$insert_payment['payment_confirmation_id'] = $data_view_member['payment_id'];
		$insert_payment['type_id']                 = 2;

		//insert file gambar
		if ($data_view_member['payment_id']) {
			$this->paymentconfirmationfiles_model->update($insert_payment,$data_view_member['payment_id']);
		}else{
			$this->paymentconfirmationfiles_model->insert($insert_payment);
		}

		//update membership info 
		$membership_save['expired_date']    = date_expired();
		$membership_save['is_expired']      = 0;
		$this->membership_model->update($membership_save,$data_view_member['membership_id']);

		// update auth_member
		$update_member['status_payment_id'] = 1;
		$this->member_model->update($update_member,$member_id);
		
		//sent email user (new password)
		$email_user['full_name'] = full_name($data_view_member);
		$email_user['email']     = $data_view_member['member_email'];
		$email_user['link']      = base_url_lang()."member";
		sent_email_by_category(12,$email_user,$data_view_member['member_email']);
	
		$ret['error']       = 0;
		$ret['msg']         = 'Success';
		$ret['close_modal'] = 'modal-send-invoice-extend';
		echo json_encode($ret);
	}
	function download($value='')
	{
		$post  = purify($this->input->post());
		$this->load->model('individual_model');

		$from =iso_date_custom_format( $post['from_create_date'],'Y-m-d');
		$to =iso_date_custom_format( $post['to_create_date'],'Y-m-d');

		if($post['from_create_date'] != '' && $post['to_create_date'] == '') {
			$this->db->where("a.create_date between '$from' and '$to'");
		} else if($post['from_create_date'] != '' && $post['to_create_date'] == '') {
			$to = iso_date_custom_format(date('Y-m-d'),'Y-m-d');
			$this->db->where("a.create_date between '$from' and '$to'");
		}
		$this->db->select('g.invoice_number');
		$this->db->join('payment_confirmation g', 'g.member_id = a.id', 'left');
		$this->db->where('g.invoice_number !=', "");
		$this->db->where('g.id_ref_payment_category', "2");

		$this->db->group_by('member_id');
		$data  = $this->individual_model->download();
		$data['data'] = empty($data['data']) ? [] : $data['data'];
		
		render('apps/member/export',$data,'blank');
		$filename = 'list-individu.xls';
		header('Content-type: application/excel');
		header('Content-Disposition: attachment; filename='.$filename);
	}
	
}

/* End of file member.php */
/* Location: ./application/controllers/apps/payment_confirmation.php */