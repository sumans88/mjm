<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('member_model');
		$this->load->model('company_model');
		$this->load->model('membership_model');
		$this->load->model('individual_model');
		$this->load->model('paymentconfirmation_model');
		$this->load->model('paymentconfirmationfiles_model');
		$this->load->model('committee_model');
		$this->load->model('sector_model');
		$this->load->model('auth_member_committee_model');
		$this->load->model('auth_member_sector_model');
	} 

	function index(){
		$CI =& get_instance();
		$data['list_membership'] = selectlist2(array('table'=>'auth_member_category','title'=>'Membership Category'));
		$data['modal_invoice'] = $this->parser->parse('apps/member/modal_invoice.html',$CI->data,TRUE);
		$data['list_status']     = selectlist2(array('table'=>'ref_status_payment','title'=>'Status'));
		$data['page_name']     = "Company";
		render('apps/company/index',$data,'apps');
	}
	
	function add($id=''){
		add_member_backend($id);
	}
	public function view_representative($id=''){
		if($id){
			$datas 	= $this->company_model->findById($id);
			if(!$datas){
				die('404');
			} else {
				// if ($datas['name_in'] && $datas['name_out']) {
				// 	$data['name'] = $datas['name_in']. " / ".$datas['name_out'];
				// }else if ($datas['name_in'] && !$datas['name_out']) {
					$data['name'] = $datas['name_in'];
				// }else{
				// 	$data['name'] = $datas['name_out'];
				// }
				$data['is_paid'] = $datas['status_payment_id'] ==1 ? "":"invis";
			}
		}
		$data['id_company'] = $id;

		// $data_representative = $this->member_model->list_representative($id);
		// $id_representative = $this->member_model->list_representative($id,1)['member_id'];
		
		render('apps/company/view_representative',$data,'apps');
	}
	public function view_subdiary($id=''){
		if($id){
			$datas 	= $this->company_model->findBy(array('id_parent_company'=> $id));
			if(!$datas){
				die('404');
			} else {
				// if ($datas['name_in'] && $datas['name_out']) {
				// 	$data['name'] = $datas['name_in']. " / ".$datas['name_out'];
				// }else if ($datas['name_in'] && !$datas['name_out']) {
					$data['name'] = db_get_one('company','name_in', array('id'=> $id));
				// }else{
				// 	$data['name'] = $datas['name_out'];
				// }
				$data['is_paid'] = $datas['status_payment_id'] ==1 ? "":"invis";
				$data['list_membership'] = selectlist2(array('table'=>'auth_member_category','title'=>'Membership Category'));
				$data['modal_invoice'] = $this->parser->parse('apps/member/modal_invoice.html',$CI->data,TRUE);
				$data['list_status']     = selectlist2(array('table'=>'ref_status_payment','title'=>'Status'));
				$data['page_name']     = "Company Subdiary";
			}
		}
		$data['id_company'] = $id;

		// $data_representative = $this->member_model->list_representative($id);
		// $id_representative = $this->member_model->list_representative($id,1)['member_id'];
		
		render('apps/company/view_subdiary',$data,'apps');
	}


	function view($id=''){
		if($id){
			view_individual($id);
		}else{
			redirect('apps/member');
		}
	}
	function sent_invoice($draft){
		$post           = $this->input->post();		
		$member_id      = $post['member_id'];
		$invoice_number = $post['invoice_number'];
		
		$where_pc['member_id'] = $member_id;
		$check_pc = $this->paymentconfirmation_model->findBy($where_pc,1);
			$pc_save['invoice_number'] = $invoice_number;
			$pc_save['member_id']      = $member_id;

		if ($check_pc) {
			//update payment confirmation
			$id_payment                  = $this->paymentconfirmation_model->update($pc_save,$check_pc['id']);
			$action                      = 'update';
			$ret['msg'] = 'Update Success';
			
		}else{
			//save payment confirmation
			$id_payment                = $this->paymentconfirmation_model->insert($pc_save);
			$action                    = 'save';
			$ret['msg'] = 'Insert Success';
		}
		$data_member = $this->member_model->findById($member_id);

		$updatestatus = $data_member['status_payment_id'] == 1 || $data_member['status_payment_id'] == 4 ? 0: 1;
		if ($updatestatus && $draft) {
			$this->member_model->update(array('status_payment_id' => 3),$member_id); // draft
		}else{
			$this->member_model->update(array('status_payment_id' => 4),$member_id); // replied
		}

		if ($_FILES['file']['name'][0]) {
			$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);

			$fileRename               = 'invoice_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($invoice_number, 0, 50))))."-".date("dMYHis").".".$ext;
			/*echo pathinfo($file, PATHINFO_EXTENSION); exit();*/
			fileToUpload($_FILES['file'],0,$fileRename);
			
		}else{
			$fileRename = $this->paymentconfirmationfiles_model->findBy(array('payment_confirmation_id'=>$id_payment,'type_id'=>1),1)['filename'];
		}
		detail_log();

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

		if ($draft) {
			insert_log('member invoice draft');
		}else{
			//sent email to member
			$email_admin['name']           = full_name($data);
			$email_admin['invoice_number'] = $post['invoice_number'];
			//file attarch
			$email_admin['filename']       = $fileRename;
			$email_admin['path_file']      = 'file_upload';

			$email_member = $data_member['email'];
			if (sent_email_by_category(7,$email_admin,$email_member)) {
				$ret['msg'] = 'Success Sent Invoice';
			}else{
				$ret['msg'] = 'Failed Sent Invoice';
			}

			insert_log('sent member invoice');
		}
		$ret['error']       = 0;
		$ret['close_modal'] = 'modal-id-member';
		echo json_encode($ret);
	}

	function records(){
		$data = $this->company_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['full_name']                 = ($value) ? full_name($value) : 'No owner'; 
			$data['data'][$key]['tanggal_buat']              = iso_date($value['create_date']);
			$data['data'][$key]['status']                    = $value['status'] ? $value['status'] : 'new';
			$data['data'][$key]['is_paid']                   = $value['status_payment_id'] == 1 ? 'invis': '';
			// $data['data'][$key]['member_id']              = $value['member_id_create'];

			$data['data'][$key]['dsp_btn_subdiary']          = db_get_one('company','id',array('id_parent_company'=>$value['id'])) != ""? "":"invis";
			// $data['data'][$key]['dsp_btn_representative'] = count($this->db->get_where('auth_member',array('company_id'=>$value['id']))->result_array()) >1? "":"invis";
			$data['data'][$key]['dsp_btn_representative']    = "";
			$data['data'][$key]['dsp_btn_block']   	         = $value['is_block'] == 0?"":"invis";
			$data['data'][$key]['dsp_btn_unblock']           = $value['is_block'] == 0?"invis":"";
		}
		render('apps/company/records',$data,'blank');
	}
	function records_subdiary($id){
		$data = $this->company_model->records(array('id_parent_company'=> $id));
		
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['full_name']                = ($value) ? full_name($value) : 'No owner'; 
			$data['data'][$key]['tanggal_buat']             = iso_date($value['create_date']);
			$data['data'][$key]['status']                   = $value['status'] ? $value['status'] : 'new';
			$data['data'][$key]['is_paid']                  = $value['status_payment_id'] == 1 ? 'invis': '';
			// $data['data'][$key]['member_id']             = $value['member_id_create'];

			$data['data'][$key]['dsp_btn_subdiary']         = db_get_one('company','id',array('id_parent_company'=>$value['id'])) != ""? "":"invis";
			$data['data'][$key]['dsp_btn_representative']   = count($this->db->get_where('auth_member',array('company_id'=>$value['id']))->result_array()) >1? "":"invis";
			$data['data'][$key]['dsp_btn_block']   	        = $value['is_block'] == 0?"":"invis";
			$data['data'][$key]['dsp_btn_unblock']          = $value['is_block'] == 0?"invis":"";
		}
		render('apps/company/records',$data,'blank');
	}
	function records_representative($id){
		$data                                               = $this->member_model->records_representative(array('company_id'=>$id));
		$get_member_create_company                          = $this->company_model->findBy(array('id'=>$id),1)['member_id_create'];
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['id']                       = $value['member_id']; 
			$data['data'][$key]['full_name']                = ($value['firstname'] !="") ? full_name($value) : "-"; 
			$data['data'][$key]['create_date']              = iso_date($value['create_date']);			
			$data['data'][$key]['status_payment']           = $value['status'] ? $value['status'] : 'new';
			$data['data'][$key]['change_status_individual'] = $get_member_create_company == 0 && $value['is_block'] == 0 && $value['status_payment_id'] == 2 ? '<a href="'.base_url().'change_company/'.$value['company_id'].'/'.$value['member_id'].'" title="View Detail" class="fa fa-lock action-form-icon"></a>' : '' ;
			$data['data'][$key]['is_paid']                  = $value['status_payment_id'] == 1 ? 'invis': '';
			$data['data'][$key]['dsp_btn_block']            = $value['is_block'] == 0?"":"invis";
			$data['data'][$key]['dsp_btn_unblock']          = $value['is_block'] == 0?"invis":"";
		}
		render('apps/company/records_representative',$data,'blank');
	}
	
	
	function proses($idedit=''){
		proses_member_backend($idedit);
	}

	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->delete_company($id);
		$this->db->trans_complete();
	}
	function block(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->block_company($id);
		$this->db->trans_complete();
	}
	function unblock(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->unblock_company($id);
		$this->db->trans_complete();
	}
	function del_member(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->delete($id);
		$this->db->trans_complete();
	}
	function block_member(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->block($id);
		$this->db->trans_complete();
	}
	function unblock_member(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->unblock($id);
		$this->db->trans_complete();
	}

	function invoice_check($id){
		$where['member_id']      = $id;
		$data_invoice            = $this->paymentconfirmation_model->findBy($where,1);

		$data_member = $this->member_model->findBy(array('id'=>$id),1);
		$ret['is_sent'] = ($data_member['status_payment_id'] == 1 || $data_member['status_payment_id'] == 4)? 1: 0;
		if ($data_invoice) {
			$ret['invoice_number'] = $data_invoice['invoice_number'];

			$where_file['payment_confirmation_id'] = $data_invoice['id'];
			$where_file['type_id']                 = 1;
			$check_file            = $this->paymentconfirmationfiles_model->findBy($where_file,1);
			$ret['filename']       = (!empty($check_file)) ? $check_file['filename']:'';
		}else{
			$ret['invoice_number'] = '';
			$ret['filename']       = '';
		}

		echo json_encode($ret);
	}

	function add_representative($idcompany,$id){
		if($id){
			$data  = $this->individual_model->findBy(array('id'=> $id),1);
            if(!$data){
				die('404');
			}else{
				$data['checked_gratis']      = $data['is_invis']?"checked":'';
				$data['email_user']          = $data['email'];
				$data['m_t_number_profile']  = $data['m_t_number'];
				$data_company                = $this->company_model->findById($data['company_id']);
				$data['name_in']             = $data_company['name_in'];
				$data['city']                = $data_company['city'];
				$data['address']             = $data_company['address'];
				$data['postal_code']         = $data_company['postal_code'];
				$data['headquarters']        = $data_company['headquarters'];
				$data['website']             = $data_company['website'];
				$data['m_t_number_company']  = $data_company['t_number'];
				$data['description']         = $data_company['description'];
				$data['company_id']          = $data_company['id'];
				$data['member_code']         = $this->membership_model->findBy(array('member_id'=>$id),1)['membership_code'];
			}
			$data['judul']              = 'Add';
			$data['proses']             = 'Update';
			// print_r($data);exit;
			
		} else{
			$data['judul']              = 'Add';
			$data['proses']             = 'Save';
			$data['name']               = '';
			$data['id']                 = '';
			$data['firstname']          = '';
			$data['lastname']           = '';
			$data['prefix_name']        = '';
			$data['citizenship']        = '';		
			$data['job']                = '';
			// $data['linkedin_id']        = '';
			$data['m_t_number_profile'] = '';
			// $data['m_m_number_profile'] = '';
			$data['m_t_number_company'] = '';
			// $data['m_m_number_company'] = '';
			$data['email']              = '';
			$data['uri_path']           = '';
			$data['name_in']            = '';
			// $data['name_out']           = '';
			$data['address']            = '';
			$data['website']            = '';
			$data['headquarters']       = '';
			$data['description']        = '';
			$data['postal_code']        = '';
			$data['city']               = '';
			$data['company_id']         = '';
			$data['checked_gratis']     = '';
			$data['email_user']         = '';
			$data['email_company']      = '';		
			$data['member_code']        = '';		
			$data['email_user']         = '';
			$data['m_t_number_profile'] = '';
			$data['name_in']            = '';
			$data['city']               = '';
			$data['address']            = '';
			$data['postal_code']        = '';
			$data['headquarters']       = '';
			$data['website']            = '';
			$data['m_t_number_company'] = '';
			$data['description']        = '';
			$data['company_id']         = '';
			$data['member_code']        = '';
			$data['address_member']        = '';
		}

		$data['list_member_category'] = selectlist2(
			array(
				'table'=>'auth_member_category',
				'selected'=>'3',
				'where' => 'id = 3'
				)
			);

		$data['list_company'] = selectlist2(
		array(
			'table'=>'company',
			'selected'=>$data['company_id'],
			'where' => array('is_delete' => 0),
			'name' => 'name_in'
			)
		);

		$data['list_status_payment'] = selectlist2(
		array(
			'table'=>'ref_status_payment',
			'selected'=>$data['status_payment_id'],
			'where' => 'id = 1 or id = 2',
			'name' => 'name'
			)
		);
		
		$data['img']					= imageProfile($data['img'],'individu');
		$data['img_company'] 			= imageProfile($data['img_company'],'company');
		$data['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['id_status_publish']));
		$data['list_languages'] 		= selectlist2(array('table'=>'language','title'=>'All Languages','selected'=>$data['id_lang']));

		$company = $this->company_model->findById($data['company_id']);
		
		$this->db->order_by('a.name','asc');
		$datas = $this->committee_model->findBy();
		if ($id) {
			$cekTagsNews = $this->auth_member_committee_model->findBy(array('member_id'=>$id));
			
			foreach ($cekTagsNews as $key => $value) {
			$a[] .= $value['committee_id'];
			}
		}else{
			$a[] = "";
		}

		foreach ($datas as $key => $value) {
			
			if (in_array($value['id'], $a, true)) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
		
			$ret_committee .= '<div class="col-sm-6">
	          <label class="checkbox-committee"><input type="checkbox" '.$checked.' value="'.$value['id'].'" id="'.$value['id'].'" class="committee" name="id_committee[]" >'.$value['name'].'</label>
	        </div>';
		}

		//sector
		$this->db->order_by('a.name','asc');
		$this->db->where('is_other = 0');
		$datas2 = $this->sector_model->findBy();
		$count_datas	 = count($datas2); 

		// print_r($this->db->last_query());exit;
		// print_r($cekSector);exit;
		list($list_sector_1,$list_sector_2 )= array_chunk($datas2, (ceil(count($datas2) / 2)+3));
		$this->db->where('is_parent_other != 0');
		$other_opt = $this->sector_model->findBy();
		$list_sector_2[] = $other_opt[0];

		if ($company['id']) {
			$this->db->where('a.is_delete_tag = 0');
			$this->db->where('a.company_id',$company['id']);
			$cekSector = $this->sector_model->findviewBy();
			foreach ($cekSector as $key => $value) {
				$b[] .= $value['id'];
				if ($value['is_other'] == 1 && $value['is_parent_other'] == 1) {
					$data['check_other'] = 1;
					$data['check_other_value'] = "";
				}else if ($value['is_other'] == 1) {
					$data['check_other'] = 1;
					$data['check_other_value'] = $value['name'];
				}else{
					$data['check_other'] = 0;
					$data['check_other_value'] = "";
				}
			}
		}
		$first_word1   = strtolower(substr($list_sector_1[0]['name'],0,1));
		$first_word2   = strtolower(substr($list_sector_2[0]['name'],0,1));
	
		foreach ($list_sector_1 as $key => $value) {
			if (in_array($value['id'], $b, true)) {
				$checked1 = 'checked';
			} else {
				$checked1 = '';
			}
			$word_first1 = strtolower(substr($value['name'],0,1));    

			if ($word_first1 != $first_word1 ) {
				$ret_sector1 .= '<hr class="line-content">';
				$first_word1 = $word_first1;
			}
			$ret_sector1 .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
	          <label class="checkbox-sector"><input type="checkbox" '.$checked1.' value="'.$value['id'].'" id="'.$value['id'].'" class="sector" name="id_sector[]" >'.$value['name'].'</label>
	        </div>';
		}	
		foreach ($list_sector_2 as $key => $value) {
			if (in_array($value['id'], $b, true)) {
				$checked2 = 'checked';
			} else {
				$checked2 = '';
			}
			$word_first2 = strtolower(substr($value['name'],0,1));    

			if ($word_first2 != $first_word2 ) {
				$ret_sector2 .= '<hr class="line-content">';
				$first_word2 = $word_first2;
			}
			$ret_sector2 .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
	          <label class="checkbox-sector"><input type="checkbox" '.$checked2.' value="'.$value['id'].'" id="'.$value['id'].'" class="sector" name="id_sector[]" >'.$value['name'].'</label>
	        </div>';
		}	
		$data['list_sector_1'] 	.= $ret_sector1;
		$data['list_sector_2'] 	.= $ret_sector2;

		$data['list_committee'] .= $ret_committee;

		$data['category_view']   	= '3';
		$data['member_category_id'] = '3';
		$data['company_id'] = $idcompany;
		$data['page_name'] = "Representative";
		$data['url_back'] = "view_representative/".$idcompany;


		render('apps/member/add',$data,'apps');
	}

	function proses_add_representative($idcompany,$id_member){
		$this->layout               = 'none';
		$post                       = purify($this->input->post());
		
		$data['company_id']         = $idcompany;
		$data['firstname']          = $post['firstname'];
		$data['lastname']           = $post['lastname'];
		$data['prefix_name']        = $post['prefix_name'];
		$data['job']                = $post['job'];
		$data['email']              = $post['email'];
		$data['linkedin_id']        = $post['linkedin_id'];
		
		$data['m_m_number'] 		= $post['m_m_number'];
		$data['m_t_number'] 		= $post['m_t_number'];
		
		$committee          		= $post['id_committee'];
		$company 					= $this->company_model->findById($data['company_id']);

		if(!$_FILES['img']['name']){
			unset($post['img']);
		} else {
			$ext                   = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
			$fileRename            = 'representative'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['img'], 0, 50))))."-".date("dMYHis").".".$ext;;
			fileToProfileImage($_FILES['img'],0,$fileRename,'representative');
			$data['img'] 			= $fileRename;
		}

		if ($id_member) {
			auth_update();
			unset($post['id_committee']);
			$re_data                    = $this->member_model->findById($id_member);
			$data['member_category_id'] = $re_data['member_category_id'];
			$ret['message']             = 'Update Success';
			$act                        = "Update Profile Representative";
			$member_id                  = $this->member_model->update($data,$id_member);			
			
		}else{
			auth_insert();
			unset($post['id_committee']);
			$data['status_payment_id']  = 2;
			$data['member_category_id'] = 3;
			$ret['message'] = 'Insert Success';
			$act            = "Insert Profile Representative";
			$member_id      = $this->member_model->insert($data);	

			//insert membership
			$membership_save['member_id']             = $member_id;
			$membership_save['company_id']            = $idcompany;
			$membership_save['first_registered_date'] = date("Y-m-d");
			$membership_id                            = $this->membership_model->insert($membership_save);

			// update member membership id
			$this->member_model->update(array('membership_information_id'=>$membership_id),$member_id);

			$data_member = $this->member_model->findById($membership_id);
				debugvar($data_member);
				exit();
			$password               = generatePassword();
			//send email to user
			$email_user['name']     = full_name($data_member);
			$email_user['category'] = db_get_one('auth_member_category','name',array('id'=>3));
			$email_user['username'] = $data_member['email'];
			$email_user['password'] = $password;
			$email_user['link']     = base_url().'member';
			sent_email_by_category(2,$email_user,$data_member['member_email']);
		}

		#committee
		foreach ($committee as $key => $value) {
		if($value){
			$cek = $this->committee_model->fetchRow(array('id'=>$value));//liat tags name di tabel ref
			if(!$cek){//kalo belom ada
			}
			else{
				$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
			}
			$cekTagsNews = $this->auth_member_committee_model->fetchRow(array('committee_id'=>$id_tags,'member_id'=>$id_member)); //liat di tabel news tags, (utk edit)

			if(!$cekTagsNews){//kalo blm ada ya di insert
				$tag['committee_id'] = $id_tags;
				$tag['member_id'] = $id_member;
				$id_news_tags = $this->auth_member_committee_model->insert($tag);
			}
			else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
				$id_news_tags = $cekTagsNews['id'];
			}
			$del_tags_news[] = $id_news_tags;

			}
		}

		$this->db->where_not_in('a.id',$del_tags_news); 
		$delete = $this->auth_member_committee_model->findBy(array('a.member_id'=>$company['member_id_create'])); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)
		
		foreach ($delete as $key => $value) {
			$this->auth_member_committee_model->delete($value['id']);
		}
		#end_committee
		detail_log();
		insert_log("Update Profie Representative");

		echo json_encode($ret);
	}

	function change_individual($id_company,$member_id){
	
		$update_member['member_category_id'] = 2;
		$this->member_model->update($update_member,$member_id);
	
		$update_company['member_id_create'] =  $member_id;
		$this->company_model->update($update_company,$id_company);

		render('apps/company/index',$data,'apps');

	}

	function change_company($id_company,$member_id){
	
		$update_member['member_category_id'] = 1;
		$this->member_model->update($update_member,$member_id);
	
		$update_company['member_id_create'] =  $member_id;
		$this->company_model->update($update_company,$id_company);
		
		render('apps/company/index',$data,'apps');
	}

	public function download(){
		$post  = purify($this->input->post(NULL,TRUE));
		$this->db->where('a.member_category_id !=3');
		$data  = $this->individual_model->download();
		// print_r($data);exit;
		$nomor = 0;

		foreach ($data as $key => $value) {
			$data['data'][] = array(
				'nomor' 			   => ++$nomor,
				'name'             => full_name($data[$key],1),
				'prefix_name'      => $data[$key]['prefix_name'],
				'job'              => $data[$key]['job'],
				't_number'         => $data[$key]['m_t_number'],
				'category'         => $data[$key]['membership'],
				'subsidiary'       => db_get_one('company','name_in',array('id' => $data[$key]['id_parent_company'])),
				'email'            => $data[$key]['email_member'],
				'status'           => $data[$key]['status'],
				'company_name'     => $data[$key]['name_in'],
				'company_address'  => $data[$key]['address'],
				'city'             => $data[$key]['city'],
				'postal_code'      => $data[$key]['postal_code'],
				'headquarters'     => $data[$key]['headquarters'],
				'website'          => $data[$key]['website'],
				't_number_company' => $data[$key]['t_number'],
				'is_invis'         => $data[$key]['is_invis'] ? "yes" : 'no',
				'membership_code' => $data[$key]['membership_code']
			);
		}

		render('apps/member/export',$data,'blank');
		$filename = 'list-company.xls';
		header('Content-type: application/excel');
		header('Content-Disposition: attachment; filename='.$filename);
	}
}

/* End of file member.php */
/* Location: ./application/controllers/apps/company.php */