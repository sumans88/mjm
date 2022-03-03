<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('member_model');
		$this->load->model('company_model');
		$this->load->model('paymentconfirmation_model');
		$this->load->model('paymentconfirmationfiles_model');
		$this->load->model('individual_model');
		$this->load->model('sector_model');
		$this->load->model('committee_model');
		$this->load->model('auth_member_committee_model');
		$this->load->model('auth_member_sector_model');
		$this->load->model('membership_model');
	}

	function index(){
		$CI =& get_instance();
		// $datap['base_url'] = base_url();
		// $this->parser->parse('apps/member/modal_invoice.html',$datap,TRUE);
		$data['modal_invoice'] = $this->parser->parse('apps/member/modal_invoice.html',$CI->data,TRUE);
		$data['list_membership'] = selectlist2(array('table'=>'auth_member_category','title'=>'Membership Category'));
		$data['list_status']     = selectlist2(array('table'=>'ref_status_payment','title'=>'Status'));
		// $data['list_languages'] 		= selectlist2(array('table'=>'language','title'=>'All Languages','selected'=>$data['id_lang']));
		render('apps/member/index',$data,'apps');
	}
	
	function add($id=''){
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
			$data['sort']               = '';
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
		}

		$data['list_member_category'] = selectlist2(
			array(
				'table'=>'auth_member_category',
				'selected'=>$data['member_category_id']
				,'where' => 'id = 3 '
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

		$data['list_committee'] = $ret_committee;
		$data['member_category_id'] = '3';

		render('apps/member/add',$data,'apps');
	}
	function view($id=''){
		$CI =& get_instance();
		if($id){
			view_individual($id);
		}else{
			redirect('apps/member');
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
			detail_log();
			insert_log('member invoice draft');
		}else{
			//sent email to member
			$email_admin['name']           = full_name($data_member);
			$email_admin['invoice_number'] = $post['invoice_number'];
			//file attarch
			$email_admin['filename']       = $fileRename;
			$email_admin['path_file']      = 'file_upload';			
			$email_admin['link']           = base_url();

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

		if ($_FILES['file']['name'][0]) {
			$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);

			$fileRename               = 'confirm_'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($invoice_number, 0, 50))))."-".date("dMYHis").".".$ext;
			/*echo pathinfo($file, PATHINFO_EXTENSION); exit();*/
			fileToUpload($_FILES['file'],0,$fileRename);
			
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
		$ret['error'] = 0;
		$ret['close_modal'] = 'modal-id-member';
		echo json_encode($ret);
	}

	function records(){
		$data = $this->member_model->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['full_name']   = full_name($value); 
			$data['data'][$key]['company']     = $value['name_in'];
			$data['data'][$key]['create_date'] = iso_date($value['create_date']);
			
			$data['data'][$key]['is_paid'] = $value['status_payment_id'] == 1 ? 'invis': '';
		}
		render('apps/member/records',$data,'blank');
	}
	
	
	function proses($idedit=''){
		$id_user 		= id_user();
		$this->layout 	= 'none';
		$post 			= purify($this->input->post());
		// print_r($post);exit;

		$ret['error']	= 1;
		$this->db->trans_start();

		// #check unique email
		// while ($this->member_model->findBy(array('email' => $post['email_user']),1)) {
		// 	$ret['message'] = "Email has Registered Before";
		// 	// set_flash_session('message',$ret['message']);
		// 	echo json_encode($ret);
		// 	exit;
		// }

		$committee 		= $post['id_committee'];
		$sector 		= $post['id_sector'];
		unset($post['id_committee'],$post['id_sector']);
		$member_data = $this->member_model->findById($idedit);
		// $company     = $this->company_model->findById($member_data['company_id']);

		if (!$post['company_id']){
			unset($post['company_id']);
		} 

		//generate password
		$password = generatePassword();
		if($post['other-sector-name']){
			$other_sector_name = $post['other-sector-name'];
		}


		if(!$_FILES['img']['name']){
			unset($post['img']);
		} else {
			$ext        = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
			$fileRename = 'individu'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['img'], 0, 50))))."-".date("dMYHis").".".$ext;;
			fileToProfileImage($_FILES['img'],0,$fileRename,'individu');
			$data_profile['img']               = $fileRename;
		}

		$data_profile['firstname']             = $post['firstname'];
		$data_profile['lastname']              = $post['lastname'];
		$data_profile['prefix_name']           = $post['prefix_name'];
		$data_profile['job']                   = $post['job'];
		$data_profile['sort']                  = $post['sort'] ? $post['sort'] : NULL;
		$data_profile['is_invis']              = $post['is_invis'];
			// $data_profile['linkedin_id']    = $post['linkedin_id'];
			// $data_profile['citizenship']    = $post['citizenship'];
			// $data_profile['m_m_number']     = $post['m_m_number_profile'];
		$data_profile['m_t_number']            = $post['m_t_number_profile'];
		$data_profile['email']                 = $post['email_user'];
		$data_profile['uri_path']              = generate_url_member(full_name($data_profile));
		$data_profile['member_category_id']    = $post['member_category_id'];
		$data_profile['status_payment_id']     = $post['status_payment_id'];
		if ($post['status_payment_id'] == 1){
			$data_profile['password']          = md5($post['member_code']);
		} else if ($idedit) { // bila bila update dan status payment 0
			unset($data_profile['password']);
		}else{
			$data_profile['password']          = md5($password);
		}

		// data member
		if ($idedit) {
			auth_update();
			$iddata  = $this->member_model->update($data_profile,$idedit);	
			$act            = "Update Member";
		}else{
			auth_insert();
			$iddata  = $this->member_model->insert($data_profile);
			$act            = "Insert Member";
		}
		detail_log();	
		insert_log($act);

		//JikaRepresentative
		if ($post['company_id'] && $data_profile['member_category_id'] == 3){
			$data_company = $this->company_model->findBy(array('id'=>$post['company_id']),1);
			$id_company   = $post['company_id'];

		} else { 
			if(!$_FILES['img_company']['name']){
				unset($post['img_company']);
			} else {
				$ext         = pathinfo($_FILES['img_company']['name'], PATHINFO_EXTENSION);
				$fileRename  = 'company'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['img_company'], 0, 50))))."-".date("dMYHis").".".$ext;;
				fileToProfileImage($_FILES['img_company'],0,$fileRename,'company');
				$data_company['img'] 			= $fileRename;
			}

			$data_company['name_in']            = $post['name_in']; 
			$data_company['uri_path_name_out']  = generate_url_company($post['name_in']); 
				// $data_company['name_out']    = $post['name_out'];
			$data_company['city']               = $post['city'];
			$data_company['address']            = $post['address'];
			$data_company['headquarters']       = $post['headquarters'];
			$data_company['website']            = $post['website'];
				// $data_company['m_number']    = $post['m_m_number_company'];
			$data_company['t_number']           = $post['m_t_number_company'];
				// $data_company['email']       = $post['email_company'];
			$data_company['description']        = $post['description'];
			$data_company['postal_code']        = $post['postal_code'];
			$data_company['member_id_create']   = $iddata;

			if ( $data_profile['member_category_id'] == 4) {// subdiary
				$data_company['id_parent_company']  = $post['company_id'];
			}else{
				$data_company['id_parent_company']  = 0; // jaga jaga bila user ganti category dari subdiary
			}

			if ($member_data['member_category_id'] == $post['member_category_id'] && $idedit) { // check bila category sebelumnya sama
				$id_company = $this->company_model->update($data_company,$member_data['company_id']);
				$act            = "Update Company Member";
			}else{
				$id_company = $this->company_model->insert($data_company);
				$act            = "Insert Company Member";	
			}
				detail_log();	
				insert_log($act);
		}
		//update member company_id
			$update_member['company_id']            = $id_company;
			$this->member_model->update($update_member,$iddata);

		//membership
			if ($idedit) {
				// cari id membership untuk update
				$id_membership = $this->membership_model->findBy(array('member_id' => $idedit),1)['id'];
			}

			if ($member_data['status_payment_id'] != $post['status_payment_id'] && $idedit) { // status edit dan ganti ke active
				$membership_save['membership_code']    = $post['member_code'];
				$membership_save['registered_date']    = date("Y-m-d");
				$membership_save['expired_date']       = date_expired();
				$this->membership_model->update($membership_save,$id_membership);
			}else if ($idedit) { // update
				$membership_save['membership_code']    = $post['member_code'];
				$this->membership_model->update($membership_save,$id_membership);

			}else{ // insert pertama kali
				$membership_save['member_id']              = $iddata;
				$membership_save['company_id']             = $id_company;
				$membership_save['first_registered_date']  = date("Y-m-d");
				if ($post['status_payment_id'] == 1){
					$membership_save['membership_code']    = $post['member_code'];
					$membership_save['registered_date']    = date("Y-m-d");
					$membership_save['expired_date']       = date_expired();
				}
				// insert membership_information dan update member untuk simpan idnya
				$membership_id                             = $this->membership_model->insert($membership_save);
				$membership_update['membership_information_id'] = $membership_id;
				$this->member_model->update($membership_update,$iddata);
			}
		//
		
		// untuk members yang dari new diedit active dan pertamakali insert
		if(($member_data['status_payment_id'] != $post['status_payment_id'] && $idedit) || !$idedit) {
			
			//send email to user
			$email_user['name']     		= full_name($data_profile);
			$email_admin['company_email']   = $data_company['email'];
			$email_user['category'] 		= db_get_one('auth_member_category','name',array('id'=>$data_profile['member_category_id']));
			$email_user['username'] 		= $post['email_user'];

			if ($post['status_payment_id'] == 1){
				$email_user['password'] = $post['member_code'];
			} else {
				$email_user['password'] = $password;
			}
			$email_user['link']     = base_url().'member';
			sent_email_by_category(2,$email_user,$post['email_user']);
			//
		}
		// saat pertamakali insert
		if (!$idedit) {
			//send email to admin 
			$email_admin['category']        = db_get_one('auth_member_category','name',array('id'=>$data_profile['member_category_id']));
			$email_admin['is_company']      = $data_profile['member_category_id'] == 1  ? 'hide' : '';

			$email_admin['name']            = full_name($data_profile);
			$email_admin['job']             = $data_profile['job'];
				// $email_admin['citizenship']     = $data_profile['citizenship'];
				// $email_admin['linkedin_id']     = $data_profile['linkedin_id'];
			$email_admin['email']           = $data_profile['email'];
				// $email_admin['name_out']        = $data_company['name_out'];
			$email_admin['name_in']         = $data_company['name_in'];
			$email_admin['company_address'] = $data_company['address'];
			$email_admin['city']            = $data_company['city'];
			$email_admin['postal']          = $data_company['postal_code'];
			$email_admin['headquarters']    = $data_company['headquarters'];
			$email_admin['website']         = $data_company['website'];
				// $email_admin['company_email']   = $data_company['email'];
			$email_admin['t_number']        = $data_company['t_number'];
				// $email_admin['m_number']        = $data_company['m_number'];
			$email_admin['link']            = base_url().'apps';

			sent_email_by_category(6,$email_admin,EMAIL_ADMIN_TO_SEND);
			//
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
					$cekTagsNews = $this->auth_member_committee_model->fetchRow(array('committee_id'=>$id_tags,'member_id'=>$iddata)); //liat di tabel news tags, (utk edit)


					if(!$cekTagsNews){//kalo blm ada ya di insert
						$tag['committee_id'] = $id_tags;
						$tag['member_id'] = $iddata;
						$id_news_tags = $this->auth_member_committee_model->insert($tag);
					}
					else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
						$id_news_tags = $cekTagsNews['id'];
					}
					$del_tags_news[] = $id_news_tags;

				}
			}

			$this->db->where_not_in('a.id',$del_tags_news); 
			$delete = $this->auth_member_committee_model->findBy(array('a.member_id'=>$iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)
			
			foreach ($delete as $key => $value) {
				$this->auth_member_committee_model->delete($value['id']);
			}
		#end_committee

		#sector
			$id_parent_sector = $this->sector_model->get_idotherparent();
			if (in_array($id_parent_sector, $sector)) {
				if ($other_sector_name) { // name other sector
					$insert_sector['name']              = $other_sector_name;
					$insert_sector['is_other']          = 1;
					$insert_sector['uri_path']          = generate_url($other_sector_name);
					$insert_sector['id_status_publish'] = 2;
					$checksector                        = $this->sector_model->findBy(array('uri_path'=>generate_url($other_sector_name)),1);

					if ($checksector) {
						$insert_sector_id          = $this->sector_model->update_frontend($insert_sector,$checksector['id']);
					}else{
						$insert_sector_id          = $this->sector_model->insert_frontend($insert_sector);
					}

					$key_update                = array_search($id_parent_sector, $sector);

					$sector[$key_update] = $insert_sector_id ; // set sector id with new sector
				}
			} 
			
			foreach ($sector as $key => $value) {
				if($value){
					$cek = $this->sector_model->fetchRow(array('id'=>$value));//liat tags name di tabel ref
					if($cek){
						$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
					}
					// $cekTagsNews = $this->auth_member_sector_model->fetchRow(array('sector_id'=>$id_tags,'member_id'=>$iddata)); //liat di tabel news tags, (utk edit)
					$cekTagsNews = $this->auth_member_sector_model->fetchRow(array('sector_id'=>$id_tags,'company_id'=>$id_company)); //liat di tabel news tags, (utk edit)

					if(!$cekTagsNews){//kalo blm ada ya di insert
						$tags['sector_id']  = $id_tags;
						$tags['company_id'] = $id_company;
						$id_news_tags = $this->auth_member_sector_model->insert($tags);
					}
					else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
						$id_news_tags = $cekTagsNews['id'];
					}
					$del_tags_news[] = $id_news_tags;
				}
			}
			$this->db->where_not_in('a.id',$del_tags_news); 
			// $delete = $this->auth_member_sector_model->findBy(array('a.member_id'=>$iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)

			$delete = $this->auth_member_sector_model->findBy(array('a.company_id'=>$id_company)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)
			
			foreach ($delete as $key => $value) {
				$this->auth_member_sector_model->delete($value['id']);
			}
		#end_sector

		$this->db->trans_complete();
		// set_flash_session('message',$ret['message']);
		$ret['message'] = 'Insert Success';
		$ret['error'] = 0;
		echo json_encode($ret);
	}

	function del(){
		$this->db->trans_start();   
		$id = $this->input->post('iddel');
		$this->member_model->delete($id);	
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
	
}

/* End of file member.php */
/* Location: ./application/controllers/apps/member.php */