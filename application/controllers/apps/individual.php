 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Individual extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('individual_model');
		$this->load->model('company_model');
		$this->load->model('member_model');
		$this->load->model('sector_model');
		$this->load->model('committee_model');
		$this->load->model('paymentconfirmation_model');
		$this->load->model('paymentconfirmationfiles_model');
		$this->load->model('auth_member_committee_model');
		$this->load->model('auth_member_sector_model');
		$this->load->model('membership_model');
	}

	function index(){
		$CI =& get_instance();
		$data['modal_invoice'] 	 = $this->parser->parse('apps/member/modal_invoice.html',$CI->data,TRUE);
		$data['list_membership'] = selectlist2(array('table'=>'auth_member_category','title'=>'Category'));
		$data['list_status']	 = selectlist2(array('table'=>'ref_status_payment','title'=>'Status'));
		render('apps/individual/index',$data,'apps');
	}
	
	function add($id=''){
		add_member_backend($id);
	}
	function view($is_company='',$id_company=""){
		if ($is_company == "Company") {
			view_company($id_company);
		}else{
			view_individual($is_company);
		}
	}
	function sent_invoice($draft){
		$post                      = $this->input->post();
		$member_id                 = $post['member_id'];
		$invoice_number            = $post['invoice_number'];
		
		$where_pc['member_id']     = $member_id;
		$where_pc['is_paid']       = 0;
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
		$data_member = $this->member_model->findById($member_id);

		$updatestatus = $data_member['status_payment_id'] == 1 || $data_member['status_payment_id'] == 4 ? 0: 1;
		if ($updatestatus && $draft) {
			$this->member_model->update(array('status_payment_id' => 3),$member_id); // draft
			detail_log();
			insert_log('member invoice draft');
		}else{
			if ($_FILES['file']['name'][0]) {
				$ext        = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);

				$fileRename = 'confirm_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($invoice_number, 0, 50))))."-".date("dMYHis").".".$ext;
				/*echo pathinfo($file, PATHINFO_EXTENSION); exit();*/
				fileToUpload($_FILES['file'],0,$fileRename);
				
			}else{
				$fileRename = $this->paymentconfirmationfiles_model->findBy(array('payment_confirmation_id'=>$id_payment,'type_id'=>1),1)['filename'];
			}
			//sent email to member
			$email_admin['name']           = full_name($data_member);
			$email_admin['invoice_number'] = $post['invoice_number'];
			//file attarch
			$email_admin['filename']       = $fileRename;
			$email_admin['path_file']      = 'file_upload';			
			$email_admin['link']           = base_url().'/en/member/payment_confirmation';

			$email_member = $data_member['email'];
			if (sent_email_by_category(7,$email_admin,$email_member)) {
				$ret['msg'] = 'Success Sent Invoice';
			}else{
				$ret['msg'] = 'Failed Sent Invoice';
			}

			$this->member_model->update(array('status_payment_id' => 4),$member_id); // replied
			detail_log();
			insert_log('sent member invoice');
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
		$ret['error'] = 0;
		$ret['close_modal'] = 'modal-id-member';
		echo json_encode($ret);
	}

	function records(){
		
		// $this->db->where('a.member_category_id', 2);
		$data = $this->member_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['full_name']       = full_name($value); 
			$data['data'][$key]['email']           = $value['email_member'];
			$data['data'][$key]['company']         = $value['name_in'];
			$data['data'][$key]['create_date']     = iso_date($value['create_date']);
			$data['data'][$key]['is_paid']         = $value['status_payment_id'] == 1 ? 'invis': '';
			$data['data'][$key]['dsp_btn_block']   = $value['is_block'] == 0?"":"invis";
			$data['data'][$key]['dsp_btn_unblock'] = $value['is_block'] == 0?"invis":"";
		}
		render('apps/individual/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		proses_member_backend($idedit);
	}

	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->individual_model->delete($id);
		$this->db->trans_complete();
	}
	function block(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->block($id);
		$this->db->trans_complete();
	}
	function unblock(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->unblock($id);
		$this->db->trans_complete();
	}

	function invoice_check($id){
		$where['member_id'] = $id;
		$where['is_paid']   = 0;
		$data_invoice       = $this->paymentconfirmation_model->findBy($where,1);

		$data_member = $this->member_model->findBy(array('id'=>$id),1);
		$ret['is_sent'] = ($data_member['status_payment_id'] == 1 || $data_member['status_payment_id'] == 4)? 1: 0;
		if ($data_invoice) {
			$ret['invoice_number']                 = $data_invoice['invoice_number'];
			$where_file['payment_confirmation_id'] = $data_invoice['id'];
			$where_file['type_id']                 = 1;
			$check_file                            = $this->paymentconfirmationfiles_model->findBy($where_file,1);
			$ret['filename']                       = (!empty($check_file)) ? $check_file['filename']:'';
		}else{
			$ret['invoice_number'] = '';
			$ret['filename']       = '';
		}

		echo json_encode($ret);
	}
			

	public function download(){
		$post  = purify($this->input->post(NULL,TRUE));
		$data  = $this->individual_model->download();
		

		$filename = 'list-individu.xls';
		// header('Content-type: application/excel');
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header('Content-Disposition: attachment; filename='.$filename);
// header("Content-Disposition: attachment; filename=abc.xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
		render('apps/member/export',$data,'blank');

	}
	
}

/* End of file individual.php */
/* Location: ./application/controllers/apps/individual.php */