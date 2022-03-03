<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Member extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('DashboardModel');
		$this->load->model('RegisterModel');
		$this->load->model('LoginModel');
		$this->load->model('company_model');
		$this->load->model('member_model');
		$this->load->model('paymentconfirmation_model');
		$this->load->model('paymentconfirmationfiles_model');
		$this->load->model('membership_model');
		$this->load->model('eventModel');
		$this->load->model('committee_model');
		$this->load->model('sector_model');
		$this->load->model('auth_member_committee_model');
		$this->load->model('auth_member_sector_model');
		$this->load->model('model_log_updated');
		/*
		
		 please remove when you want to use member (module)
		if(DEVELOPMENT_MEMBER){
			redirect('/');
		}
		*/
	}
	function index()
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');

		/*=====  cek login  ======*/
		/*
		if($user_sess_data){
			// kalau set remember me 
			if($user_sess_data['remember_me'] == 1){
			    $this->LoginModel->remember_me_login();
				redirect('member/profile','refresh');
		    }
		}else{
		 */
		$this->load->model('pagesmodel');
		$this->load->model('membership_category_model');

		$data['page_heading']    = 'Membership';
		$data['banner_top']      = banner_top(); // pake banner top
		$data['widget_sidebar']  = widget_sidebar(); //pake sidebar
		$data['seo_title']       = "AMCHAM INDONESIA";
		$this->db->select('page_content as membership_content, alias as membership_alias');
		$data['data_membership'] = $this->membership_category_model->findBy(array('id !=' => '3'));

		$data['pages_member_benefit'] = $this->pagesmodel->findBy(array('uri_path' => 'member-benefit'), 1)['page_content'];
		$data['pages_member_privileges'] = $this->pagesmodel->findBy(array('uri_path' => 'member-teaser-privilages'), 1)['page_content'];
		$data['pages_membership_category'] = $this->pagesmodel->findBy(array('uri_path' => 'membership-category'), 1)['page_content'];
		$temp_pages_membership_login = $this->pagesmodel->findBy(array('uri_path' => 'membership-login'), 1);
		$data['pages_membership_login_teaser'] = $temp_pages_membership_login['teaser'];
		$data['pages_membership_login_content'] = $temp_pages_membership_login['page_content'];
		$data['hide_breadcrumb'] = 'hide';
		$this->data['recaptcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
		render("member/index", $data);
		//}
	}
	function privileges()
	{
		$data['page_heading']    = 'Membership Privileges';
		$data['banner_top']      = banner_top(); // pake banner top
		$data['widget_sidebar']  = widget_sidebar(); //pake sidebar
		$data['seo_title']       = "AMCHAM INDONESIA";
		$data['hide_breadcrumb'] = 'hide';

		$this->load->model('pagesmodel');
		$where['uri_path']                = 'member-privilages';
		$pagesteaser                      = $this->pagesmodel->fetchRow($where);

		$data['member_privileges_head']   = $pagesteaser['name'];
		$data['member_privileges_teaser'] = $pagesteaser['page_content'];
		$this->load->model('membership_privileges_category_model');

		$this->db->select('name as name_privileges ,id as sort_data,id as id_category_privileges');
        $this->db->order_by('name', 'asc');
		$data['list_category_privileges'] = $this->membership_privileges_category_model->findBy(['a.id_status_publish'=>2]);

		render("member/privilages", $data);
	}

	function list_privileges()
	{
		$post = $this->input->post();
		$this->load->model('membership_privileges_model');
		$this->db->select('name as privileges_name,address as privileges_address, number as privileges_number, email as privileges_email, website as privileges_website, page_content as privileges_description,img as img');
		$this->db->order_by('status_member', 'asc');
		$this->db->order_by('name', 'asc');
		
		$check = $this->membership_privileges_model->findBy(array('id_status_publish' => 2, 'id_category' => $post['id']));
		$ret='';
		if (empty(array_filter($check))) {
			$ret .= '<div class="widget-white pt30 pb0 mb30">';
			$ret .=   '<p>Sorry Data Not Found</p>';
			$ret .= '</div>';
		} else {
			foreach ($check as $key => $value) {
				$ret .=	'<div class="widget-white pt30 pb0 mb30">';
				$ret .=	  '<div class="row">';
				$ret .=	    '<div class="col-sm-6 col-md-6 col-xs-12 text-right">';
				$ret .=	      '<div class="thumbnail-amcham thumb-logo-privilages">' . getImg($value['img'], 'small') . '</div>';
				$ret .=	    '</div>';
				$ret .=	    '<div class="col-sm-6 col-md-6 col-xs-12">';
				$ret .=	      '<div class="title-event-list">' . $value['privileges_name'] . '</div>';
				$ret .=	      '<div class="media media-listmember">';
				$ret .=	        '<div class="media-left">';
				$ret .=	          '<div class="icon-listmember il-dtlpart"><img src="asset/images/maps.png"></div>';
				$ret .=	        '</div>';
				$ret .=	        '<div class="media-body">' . $value['privileges_address'] . '</div>';
				$ret .=	      '</div>';
				$ret .=	      '<div class="media media-listmember">';
				$ret .=	        '<div class="media-left">';
				$ret .=	          '<div class="icon-listmember il-dtlpart"><img src="asset/images/phone.png"></div>';
				$ret .=	        '</div>';
				$ret .=	        '<div class="media-body">' . $value['privileges_number'] . '</div>';
				$ret .=	      '</div>';
				$ret .=	      '<div class="media media-listmember">';
				$ret .=	        '<div class="media-left">';
				$ret .=	          '<div class="icon-listmember il-dtlpart"><img src="asset/images/envalop.png"></div>';
				$ret .=	        '</div>';
				$ret .=	        '<div class="media-body">' . $value['privileges_email'] . '</div>';
				$ret .=	      '</div>';
				$ret .=	      '<div class="media media-listmember">';
				$ret .=	        '<div class="media-left">';
				$ret .=	          '<div class="icon-listmember il-dtlpart"><img src="asset/images/link-website.png"></div>';
				$ret .=	        '</div>';
				$ret .=	        '<div class="media-body">' . '<a href="//'. $value['privileges_website'].'" target="_blank">'. $value['privileges_website'].'</a>'. '</div>';
				$ret .=	      '</div>';
				$ret .=	    '</div>';
				$ret .=	  '</div>';
				$ret .=	  '<hr class="line-content">';
				$ret .=	    $value['privileges_description'];
				$ret .=	'</div>';
			}
		}

		echo json_encode($ret);
	}
	
	function profile()
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if ($user_sess_data) {
			$data_view                  = $this->member_model->findViewById($user_sess_data['id']);
			//member info 
			$data_member                = $this->member_model->findById($data_view['member_id']);

			$temp['member_id']          = $data_member['id'];
			$temp['member_name']        = full_name($data_member, 1);
			$temp['member_job']         = $data_member['job'];
			$temp['member_email']       = $data_member['email'];
			// $temp['member_linkedin_id'] = $data_member['linkedin_id'];
			$temp['member_uri_path']    = $data_member['uri_path'];

			$path                       = ($data_member['member_category_id'] == 2) ? 'individu' : 'representative';
			$img_member                 = imageProfile($data_member['img'], $path);

			$temp['member_img']         = $img_member;

			//company
			$data_company               = $this->company_model->findById($data_view['company_id']);
			// print_r($data_company);exit;
			$temp['member_t_number']    = $data_member['m_t_number'];
			// $temp['member_m_number']    = $data_member['m_m_number'];
			$temp['member_address']     = $data_company['address'];
			$temp['member_website']     = $data_company['website'];

			$data['data_member'][]      = $temp;
			// end member info
			unset($temp);


			//company
			$temp['company_name']        = $data_company['name_in'];
			$temp['company_name_out']    = $data_company['id'];
			// $data_company['uri_path_name_out'] ;
			$temp['company_address']     = $data_company['address'];
			$temp['company_t_number']    = $data_company['t_number'];

			$this->db->select('group_concat(sector_id) as sector_id_concat')->group_by('company_id');
			$id_sector =  $this->auth_member_sector_model->findBy(array('company_id' => $data_company['id']), 1)['sector_id_concat'];
			if (!empty($id_sector)) {
				$this->db->select("group_concat(a.name) as sector_group");
				$this->db->where_in('a.id', explode(',', $id_sector));
				$temp['company_sector'] = $this->sector_model->findBy()[0]['sector_group'];
			} else {
				$temp['company_sector'] = "";
			}

			// $temp['company_sector']      = $this->auth_member_sector_model->findBy(array('company_id'=>$data_company['id']));
			$temp['company_website']     = $data_company['website'];
			// $img_member                 = imageProfile($data_member['img'],$path);
			// $img_company                 = ($data_view['company_img'] != '') ? base_url().'images/member/company/'.$data_view['company_img'] : '';	
			$temp['company_img']         = imageProfile($data_company['img'], 'company');
			$temp['company_description'] = $data_company['description'];

			$data['data_company'][]      = $temp;
			unset($temp);

			//membership
			$data_membership = $this->membership_model->findById($data_view['membership_id']);

			$temp['membership_code']       = $data_membership['membership_code'];
			$temp['membership_registered'] = iso_date_custom_format($data_membership['registered_date'], 'Y-m-d');
			$temp['membership_expired']    = iso_date_custom_format($data_membership['expired_date'], 'Y-m-d');
			$temp['membership_lastvisit']  = iso_date_custom_format($data_membership['last_visited_date'], 'Y-m-d');
			$data['data_membership'][]     = $temp;
			unset($temp);
			if ($data_membership['registered_date'] && $data_membership['is_expired'] == 0) {
				$data['is_membership']  = '';
				$data['not_membership'] = 'hide';
			} else {
				$data['is_membership']  = 'hide';
				$data['not_membership'] = '';
			}

			// login parser by category
			$CI = &get_instance();
			$data['is_representative']  = "";

			$data  = array_merge($data, $CI->data);
			if (check_is_company($data_view['category_id'])) { //company
				unset($data['data_member']);
				$data_representative = $this->member_model->findBy(array('company_id' => $data_view['company_id'], 'member_category_id' => 3));
				$data_user_company = $this->member_model->findBy(array('company_id' => $data_view['company_id'], 'member_category_id' => 1));
				$data_representative = array_merge($data_user_company, $data_representative);
				if (!empty($data_representative)) {
					foreach ($data_representative as $key => $value) {
						$data_member_r                 = $this->member_model->findById($value['id']);
						$temp['re_id']                 = $data_member_r['id'];

						$temp['re_hide_representatif'] = $data_member_r['member_category_id'] == 3 ? 'hide' : '';

						$temp['re_name']               = full_name($data_member_r, 1);
						$temp['re_url']                = $data_member_r['uri_path'];
						$temp['re_job']                = $data_member_r['job'];
						$temp['re_email']              = $data_member_r['email'];
						// $temp['re_linkedin_id']        = $data_member_r['linkedin_id'];
						$img_member                    = imageProfile($data_member_r['img'], 'representative');
						$temp['re_img']                = $img_member;

						$data_company_r                = $this->company_model->findById($value['company_id']);
						$temp['re_t_number']           = $data_member_r['m_t_number'];
						$temp['re_address']            = empty($data_member_r['address_member']) ? '': $data_member_r['address_member']; 
						// $temp['re_m_number']           = $data_member_r['m_m_number'];
						$data['data_re'][]             = $temp;
						unset($temp);
					}
				} else {
					$data['no_reprepesentative'] = 'hide';
					$data['data_re'][] = '';
				}
				$data['content_top'] = $this->parser->parse('member/company_profile.html', $data, 1);
			} else { //individu
				$data['is_representative']  = $data_view['category_id'] == 3  ? "hidden" : "";
				$data['content_top'] = $this->parser->parse('member/individu_profile.html', $data, 1);
			}
			// else{//reprentative
			// 	$data['content_top'] = $this->parser->parse('member/representative_profile.html',$data,1);
			// }


			$data['paging_load_more'] = PAGING_PERPAGE;
			//upcoming

			$today                = date('Y-m-d');
			$where_up['a.end_date >=']  = $today;
			$where_up['a.id_status_publish'] = 2; //berpengaruh karena id status 1 290 record
			$where_up['a.publish_date <=']  = $today;
			$this->db->limit(PAGING_PERPAGE, 0);
			$data_events_up       = $this->eventModel->findViewBy($where_up);

			$id_cat_amcham_event  = id_child_news(26, 1);

			// print_r($data_events);exit;
			
			foreach ($data_events_up as $key => $value) {
				unset($temp);
				$temp['upcoming_cat']  = ($value['subcategory'] != '') ? $value['subcategory'] . ' event' : '';
				$temp['upcoming_link'] = site_url('event/detail/' . $value['uri_path']);
				$temp['upcoming_name'] = $value['name'];
				if ($value['uri_path'] == 'annual-golf-turnament') {
					$temp['upcoming_date'] = event_date($value['publish_date'], '', '', '-', 1);
				} else {
					$temp['upcoming_date'] = event_date($value['start_date'], $value['start_time'], $value['end_time']);
				}
				$temp['upcoming_address'] = $value['location_name'];
				$temp['upcoming_teaser']  = character_limiter($value['teaser'], 140, '...');
				$temp['upcoming_color']   = in_array($value['id_event_category'], $id_cat_amcham_event) ? 'widget-blue-left' : ' widget-red-left';
				$temp['upcoming_img']     = getImg($value['img'], 'small');
				$data['upcoming_event'][] = $temp;
				// $temp['']
			}
			$data['dsp_event_up'] = !$data_events_up ? 'hide' : '';

			// buat load more
			$where_up['a.end_date >=']  = $today;
			$this->db->limit(PAGING_PERPAGE, PAGING_PERPAGE_MORE);
			$data_events_up_more       = $this->eventModel->findViewBy($where_up);

			$data['dsp_load_more_upcoming_events'] = !$data_events_up_more ? 'hide' : '';

			//pass event 
			$id_event_category 		= id_child_event(26, 1, 1);

			$where_past['a.end_date <='] = $today;
			$this->db->where_in('a.id_event_category', $id_event_category);
			$this->db->where('a.id_status_publish = 2');
			$this->db->limit(PAGING_PERPAGE, 0);
			$this->db->order_by('start_date', 'desc');
			$data_events_past       = $this->eventModel->findViewBy($where_past);

			$id_cat_amcham_eventpast = id_child_news(26, 1);
			foreach ($data_events_past as $key => $value) {
				unset($temp);
				$temp['past_cat']  = ($value['subcategory'] != '') ? $value['subcategory'] . ' event' : '';
				$temp['past_link'] = site_url('event/detail/' . $value['uri_path']);
				$temp['past_name'] = $value['name'];
				if ($value['uri_path'] == 'annual-golf-turnament') {
					$temp['past_date'] = event_date($value['publish_date'], '', '', '-', 1);
				} else {
					$temp['past_date'] = event_date($value['start_date'], $value['start_time'], $value['end_time']);
				}
				$temp['past_address'] = $value['location_name'];
				$temp['past_teaser']  = character_limiter($value['teaser'], 140, '...');
				$temp['past_color']   = in_array($value['id_event_category'], $id_cat_amcham_eventpast) ? 'widget-blue-left' : ' widget-red-left';
				$temp['past_img']     = getImg($value['img'], 'small');
				//past_material
				$this->load->model('eventFilesModel');
				$materials = $this->eventFilesModel->listFiles(array('id_event' => $value['id']));
				foreach ($materials as $key => $value) {
					$materialsfile['material_file'] = $value['filename'];
					$materialsfile['mat_idx']       = md5plus($value['idFile']);
					$temp['past_material'][]        = $materialsfile;
				}
				$temp['past_dsp_material']      = empty($materials) ? 'hidden' : '';

				$data['past_event'][] = $temp;
			}
			// buat load more
			$where_past['a.end_date <='] = $today;
			$this->db->limit(PAGING_PERPAGE, PAGING_PERPAGE_MORE);
			$this->db->where_in('a.id_event_category', $id_event_category);
			$this->db->where('a.id_status_publish = 2');
			$this->db->order_by('start_date', 'desc');
			$data_events_past_more      = $this->eventModel->findViewBy($where_past);

			$data['dsp_load_more_past_events'] = !$data_events_past_more ? 'hide' : '';

			$data['is_paid']         = ($data_view['status_id'] == 1) ? '' : 'hide';
			$data['is_not_paid']     = ($data_view['status_id'] == 1) ? 'hide' : '';
			$data['have_not_paid_invoice']     = db_get_one('payment_confirmation', 'id', array('is_paid' => 0, 'member_id' => $user_sess_data['id'])) ? '' : 'hide';
			if ($data_view['status_id'] == 1) { } else 
			if ($data_view['status_id'] == 5) {
				$data['membership_information_description']     = "<p>Your AmCham membership has expired, please contact our staff if you would like to renew your membership.</p>";
			} else {
				$data['membership_information_description']     = "<p>Your registration not finish yet, please pay your invoice and confirm your payment by clicking the payment confirmation button.</p>";
			}
			$data['is_company']      = (check_is_company($data_view['category_id'])) ? '' : 'hide';
			$data['is_not_company']  = (check_is_company($data_view['category_id'])) ? 'hide' : '';

			$data['page_heading']    = 'Membership';
			$data['banner_top']      = banner_top(); // pake banner top
			$data['widget_sidebar']  = widget_sidebar(); //pake sidebar
			$data['seo_title']       = "AMCHAM INDONESIA";
			$data['hide_breadcrumb'] = 'hide';

			$data['hidden_list']     = ($data_view['status_id'] == 1) ? '' : 'hidden';

			$this->db->order_by('a.name', 'asc');

			$datas = $this->committee_model->findBy();


			$cekTagsNews = $this->auth_member_committee_model->findBy(array('member_id' => $user_sess_data['id']));
			$a = [];
			foreach ($cekTagsNews as $key => $value) {
				$a[] .= $value['committee_id'];
			}
			$ret_committee='';
			foreach ($datas as $key => $value) {
				if (in_array($value['id'], $a, true)) {
					$checked = 'checked';
				} else {
					$checked = '';
				}
				$ret_committee .= '<div class="checkbox cb-amcham cb-amcham-grey 12 {hidden_list}">
		           <label  class="checkbox-committee"><input type="checkbox" ' . $checked . ' value="' . $value['id'] . '" id="' . $value['id'] . '" class="committee" name="id_committee[]" ><span><i class="glyphicon glyphicon-ok"></i></span>' . $value['name'] . $data['is_paid'] . '</label>
		        </div>';
			}
			$data['list_committee'] .= $ret_committee;



			$this->db->order_by('a.name', 'asc');
			$datas = $this->sector_model->findBy();

			$count_datas	 = count($datas);

			$cekSector = $this->auth_member_sector_model->findBy(array('company_id' => $user_sess_data['company_id']));
			$b = [];
			foreach ($cekSector as $key => $value) {
				$b[] .= $value['sector_id'];
			}
$ret_sector ='';
			foreach ($datas as $key => $value) {

				if (in_array($value['id'], $b, true)) {
					$checked = 'checked';
				} else {
					$checked = '';
				}

				$ret_sector .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
		          <label class="checkbox-sector"><input type="checkbox" ' . $checked . ' value="' . $value['id'] . '" id="' . $value['id'] . '" class="sector" name="id_sector[]" ><span><i class="glyphicon glyphicon-ok"></i></span>' . $value['name'] . '</label>
		        </div>';
			}
			$data['list_sector'] 	.= $ret_sector;

			$data['list_year']  = list_year($y, 10);
			$data['list_month'] = list_month($m);

			$user_sess_data    = $CI->session->userdata('MEM_SESS');
			$data['firstname'] = $user_sess_data['member_namadepan'];
			$data['lastname']  = $user_sess_data['member_namabelakang'];

			render("member/profile", $data);
		} else {
			redirect('member', 'refresh');
		}
	}
	function payment_confirmation()
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if ($user_sess_data) {
			if ($user_sess_data['remember_me'] == 1) {
				$this->LoginModel->remember_me_login();
			}
			$where['member_id'] = $user_sess_data['id'];
			$where['is_paid']   = 0;
			$data_invoice       = $this->paymentconfirmation_model->findBy($where, 1);
			// if ($data_invoice['bank_account'] != "" or !is_null($data_invoice['bank_account'])) {
			$data['invoice_number'] = $data_invoice['invoice_number'];
			$data['bank_name']      = $data_invoice['bank_name'];
			$data['bank_account']   = $data_invoice['bank_account'];
			$data['payment_date']   = $data_invoice['payment_date'];
			$data['amount']         = $data_invoice['amount'];
			$data['note']           = $data_invoice['note'];

			$check_file = $this->paymentconfirmationfiles_model->findBy(
				array('type_id' => 2, 'payment_confirmation_id' => $data_invoice['id']),
				1
			);
			if ($check_file) {
				$data['filename'] = $check_file['filename'];
				$data['filename_src'] = base_url() . 'file_upload/' . $check_file['filename'];
			} else {
				$data['filename']       = "";
				$data['filename_src']   = "";
			}
			// $data['filename'] = $data_invoice['filename'];
			// }else{
			// 	$data['invoice_number'] = "";
			// 	$data['bank_name']      = "";
			// 	$data['bank_account']   = "";
			// 	$data['payment_date']   = "";
			// 	$data['amount']         = "";
			// 	$data['note']           = "";
			// 	$data['filename']       = "";
			// 	$data['filename_src']   = "";
			// }

			$data['page_heading']    = 'Payment Confirmation';
			$data['banner_top']      = banner_top(); // pake banner top
			$data['widget_sidebar']  = widget_sidebar(); //pake sidebar
			$data['seo_title']       = "AMCHAM INDONESIA";
			$data['hide_breadcrumb'] = 'hide';

			render("member/payment_confirmation", $data);
			// $this->session->set_userdata('MEM_SESS',array('remember_me' => 1));
			// print_r(base64_decode($this->input->cookie('password')));exit;
		} else {
			redirect('member', 'refresh');
		}
	}
	function payment_confirmation_proses()
	{
		$this->load->model('EventModel');
		$post                 = purify($this->input->post());
		$post['payment_date'] = iso_date_custom_format($post['payment_date'], 'Y-m-d');
		$data_payment         = $this->paymentconfirmation_model->findBy(array('invoice_number' => $post['invoice_number'], 'is_paid' => 0), 1);
		$is_membership 		  = empty($data_payment['event_id']) ? true : false;
		$data_member          = $is_membership ? $this->member_model->findById($data_payment['member_id']) : $this->EventModel->selectDataParticipant($data_payment['member_id'], 1);

		$where['invoice_number'] = $data_payment['invoice_number'];
		$where['is_paid']        = 0;
		$data_invoice            = $this->paymentconfirmation_model->findBy($where, 1);

		if (!$data_invoice) {
			$ret['error'] = 1;
			$ret['msg'] = 'Invoice Number Not Found, Please check your email to find your Invoice Number.';
			echo json_encode($ret);
			exit;
		}
		$secret   = GOOGLE_CAPTCHA_SECRET_KEY;
        $response = $_POST['g-recaptcha-response'];
        $ip       = $_SERVER['REMOTE_ADDR'];
        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$response."&remoteip=".$ip);
        $Return   = json_decode($Response);
       
       if($Return->success){
			$check_file = $this->paymentconfirmationfiles_model->findBy(array('payment_confirmation_id' => $data_invoice['id'], 'type_id' => 2), 1);
			if (!$check_file) {
				// upload file
				if ($_FILES['file']['name'][0]) {
					$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
					$fileRename               = 'invoice_' . preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['invoice_number'], 0, 50)))) . "-" . date("dMYHis") . "." . $ext;
					fileToUpload($_FILES['file'], 0, $fileRename);

					$invoice_save['filename'] = $fileRename;
				} else {
					$invoice_save['filename'] = "";
				}
				//

				$invoice_save['member_id']               = $data_payment['id'];
				$invoice_save['payment_confirmation_id'] = $data_invoice['id'];
				$invoice_save['type_id']                 = 2;
				$this->paymentconfirmationfiles_model->member_insert($invoice_save);
			} else {
				if ($_FILES['file']['name'][0]) {
					$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
					$fileRename               = 'invoice_' . preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['invoice_number'], 0, 50)))) . "-" . date("dMYHis") . "." . $ext;
					fileToUpload($_FILES['file'], 0, $fileRename);

					$invoice_save['filename'] = $fileRename;
				} else {
					$invoice_save['filename'] = $check_file['filename'];
				}
				//

				$invoice_save['member_id']               = $data_payment['id'];
				$invoice_save['payment_confirmation_id'] = $data_invoice['id'];
				$invoice_save['type_id']                 = 2;
				$this->paymentconfirmationfiles_model->member_update($invoice_save, $check_file['id']);
			}
			//update payment_confirmation
			unset($post['file'],$post['g-recaptcha-response']);
			$this->paymentconfirmation_model->update_frontend($post, $data_invoice['id']);
			detail_log();

			if ($is_membership) {
				//sent email to admin 
				$email_admin['name']            = full_name($data_member);
				$email_admin['invoice_number']  = $post['invoice_number'];
				$email_admin['account_name']    = $post['bank_name'];
				$email_admin['payment_date']    = ($post['payment_date'] != '0000:00:00') ? iso_date($post['payment_date']) : '-';
				$email_admin['amount']          = $post['amount'];
				$email_admin['notes']           = $post['note'];
				$email_admin['user_id']         = $data_member['email'];


				$email_admin['link']           = base_url() . 'apps/login';
				//file attarch
				$email_admin['filename']       = $fileRename;
				$email_admin['path_file']      = 'file_upload';
				//
				// sent_email_by_category(5, $email_admin, EMAIL_ADMIN_TO_SEND);
				insert_frontend_log('Member Send Payment Confirmation');
			} else {

				if ($data_participant['fullname'] != '') {
					$participant_name = $data_member['fullname'];
				} else if ($data_member['firstname'] != '') {
					$participant_name = $data_member['firstname'];
				} else {
					$participant_name = $data_member['lastname'];
				}

				// email
				if ($data_member['email_1'] != '') {
					$participant_email = $data_member['email_1'];
				} else if ($data_member['email_2'] != '') {
					$participant_email = $data_member['email_2'];
				} else {
					$participant_email = '';
				}


				$email_admin['event_name']      = db_get_one('event', 'name', array('id' => $data_payment['event_id']));
				$email_admin['participant_name']  = $participant_name;
				$email_admin['invoice_number']  = $post['invoice_number'];
				$email_admin['note']           = $post['note'];


				$email_admin['link']           = base_url() . 'apps/login';
				//file attarch
				$email_admin['filename']       = $fileRename;
				$email_admin['path_file']      = 'file_upload';

				// sent_email_by_category(18, $email_admin, EMAIL_ADMIN_TO_SEND);
				insert_frontend_log('Participant Event Send Payment Confirmation');
			}

			$ret['error']     = 0;
			$ret['modalname'] = 'myModalThanks';
			echo json_encode($ret);
			exit;
		} else {
            $ret['error'] = 1;
			$ret['msg'] = 'Sorry, Please Try Again';
			$ret['redirect'] = base_url() . 'payment_confirmation/'.$post['invoice_number'];
			echo json_encode($ret);exit;
       }
	}
	function payment_confirmation_proses_back()
	{
		$post                 = purify($this->input->post());
		$post['payment_date'] = iso_date_custom_format($post['payment_date'], 'Y-m-d');
		$user_sess_data       = $this->session->userdata('MEM_SESS');
		$data_member          = $this->member_model->findById($user_sess_data['id']);
		$data_payment         = $this->paymentconfirmation_model->findBy(array('member_id' => $user_sess_data['id'], 'is_paid' => 0), 1);
		$data_membership      = $this->membership_model->findBy(array('member_id' => $user_sess_data['id']), 1);

		$where_paid['member_id']     = $user_sess_data['id'];
		$where_paid['is_paid']       = 1;
		$data_invoice_paid      = $this->paymentconfirmation_model->findBy($where_paid, 1);

		$where['invoice_number'] = $post['invoice_number'];
		$where['member_id']      = $user_sess_data['id'];
		$where['is_paid']        = 0;
		$data_invoice            = $this->paymentconfirmation_model->findBy($where, 1);
		// print_r($this->db->last_query());exit;
		if (!$data_invoice) {
			$ret['error'] = 1;
			$ret['msg'] = 'Invoice Number Not Found, Please check your email to find your Invoice Number.';
			echo json_encode($ret);
			exit;
		}
		$check_file = $this->paymentconfirmationfiles_model->findBy(array('payment_confirmation_id' => $data_invoice['id'], 'type_id' => 2), 1);
		if (!$check_file) {
			// upload file
			if ($_FILES['file']['name'][0]) {
				$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
				$fileRename               = 'invoice_' . preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['invoice_number'], 0, 50)))) . "-" . date("dMYHis") . "." . $ext;
				fileToUpload($_FILES['file'], 0, $fileRename);

				$invoice_save['filename'] = $fileRename;
			} else {
				$invoice_save['filename'] = "";
			}
			//

			$invoice_save['member_id']               = $user_sess_data['id'];
			$invoice_save['payment_confirmation_id'] = $data_invoice['id'];
			$invoice_save['type_id']                 = 2;
			$this->paymentconfirmationfiles_model->member_insert($invoice_save);
		} else {
			if ($_FILES['file']['name'][0]) {
				$ext                      = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);
				$fileRename               = 'invoice_' . preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['invoice_number'], 0, 50)))) . "-" . date("dMYHis") . "." . $ext;
				fileToUpload($_FILES['file'], 0, $fileRename);

				$invoice_save['filename'] = $fileRename;
			} else {
				$invoice_save['filename'] = $check_file['filename'];
			}
			//

			$invoice_save['member_id']               = $user_sess_data['id'];
			$invoice_save['payment_confirmation_id'] = $data_invoice['id'];
			$invoice_save['type_id']                 = 2;
			$this->paymentconfirmationfiles_model->member_update($invoice_save, $check_file['id']);
		}
		//update payment_confirmation
		unset($post['file']);
		$this->paymentconfirmation_model->update_frontend($post, $data_invoice['id']);
		detail_log();

		//sent email to admin 
		$email_admin['name']            = full_name($user_sess_data);
		$email_admin['invoice_number']  = $post['invoice_number'];
		$email_admin['account_name']    = $post['bank_name'];
		$email_admin['payment_date']    = ($post['payment_date'] != '0000:00:00') ? iso_date($post['payment_date']) : '-';
		$email_admin['amount']          = $post['amount'];
		$email_admin['notes']           = $post['note'];
		$email_admin['user_id']         = $data_member['email'];
		// $email_admin['membership_code'] = $data_membership['membership_code'];


		$email_admin['link']           = base_url() . 'apps/login';
		//file attarch
		$email_admin['filename']       = $fileRename;
		$email_admin['path_file']      = 'file_upload';
		//
		if ($data_invoice_paid) {
			// sent_email_by_category(5, $email_admin, EMAIL_ADMIN_TO_SEND);
			insert_frontend_log('Member Send Payment Confirmation');
		} else {
			// sent_email_by_category(5, $email_admin, EMAIL_ADMIN_TO_SEND);
			insert_frontend_log('Member Send Payment Confirmation');
		}

		$ret['error']     = 0;
		$ret['modalname'] = 'myModalThanks';
		echo json_encode($ret);
		exit;
	}
	function edit($type)
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if (!$user_sess_data) {
			redirect('member');
			return false;
		}

		//committee
		$this->db->order_by('a.name', 'asc');
		$datas = $this->committee_model->findBy();

		$cekTagsNews = $this->auth_member_committee_model->findBy(array('member_id' => $user_sess_data['id']));
		$a=[];
		foreach ($cekTagsNews as $key => $value) {
			$a[] .= $value['committee_id'];
		}
		list($list_committee_1, $list_committee_2) = array_chunk($datas, ceil(count($datas) / 2));

		foreach ($list_committee_1 as $key => $value) {
			if (in_array($value['id'], $a, true)) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
			$ret_committee1 .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
	           <label  class="checkbox-committee"><input type="checkbox" ' . $checked . ' value="' . $value['id'] . '" id="' . $value['id'] . '" class="committee" name="id_committee[]" ><span><i class="glyphicon glyphicon-ok"></i></span>' . $value['name'] . $data['is_paid'] . '</label>
	        </div>';
		}
		foreach ($list_committee_2 as $key => $value) {
			if (in_array($value['id'], $a, true)) {
				$checked2 = 'checked';
			} else {
				$checked2 = '';
			}
			$ret_committee2 .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
	           <label  class="checkbox-committee"><input type="checkbox" ' . $checked2 . ' value="' . $value['id'] . '" id="' . $value['id'] . '" class="committee" name="id_committee[]" ><span><i class="glyphicon glyphicon-ok"></i></span>' . $value['name'] . $data['is_paid'] . '</label>
	        </div>';
		}
		$data['list_committee_1'] .= $ret_committee1;
		$data['list_committee_2'] .= $ret_committee2;


		//sector
		$this->db->order_by('a.name', 'asc');
		$this->db->where('is_other = 0');
		$datas2          = $this->sector_model->findBy();
		$count_datas	 = count($datas2);

		$this->db->where('a.is_delete_tag = 0');
		$this->db->where('a.company_id', $user_sess_data['company_id']);
		$cekSector = $this->sector_model->findviewBy();
		list($list_sector_1, $list_sector_2) = array_chunk($datas2, (ceil(count($datas2) / 2) + 3));

		$this->db->where('is_parent_other != 0');
		$other_opt = $this->sector_model->findBy();
		$list_sector_2[] = $other_opt[0];
		$b=[];
		foreach ($cekSector as $key => $value) {
			$b[] .= $value['id'];
			if ($value['is_other'] == 1 && $value['is_parent_other'] == 1) {
				$data['check_other'] = 1;
				$data['check_other_value'] = "";
			} else if ($value['is_other'] == 1) {
				$data['check_other'] = 1;
				$data['check_other_value'] = $value['name'];
			}
		}
		if (!isset($data['check_other'])) {
			$data['check_other'] = 0;
			$data['check_other_value'] = "";
		}

		$first_word1   = strtolower(substr($list_sector_1[0]['name'], 0, 1));
		$first_word2   = strtolower(substr($list_sector_2[0]['name'], 0, 1));
		$ret_sector1 ='';
		foreach ($list_sector_1 as $key => $value) {
			if (in_array($value['id'], $b, true)) {
				$checked1 = 'checked';
			} else {
				$checked1 = '';
			}
			$word_first1 = strtolower(substr($value['name'], 0, 1));

			if ($word_first1 != $first_word1) {
				$ret_sector1 .= '<hr class="line-content">';
				$first_word1 = $word_first1;
			}
			$ret_sector1 .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
	          <label class="checkbox-sector"><input type="checkbox" ' . $checked1 . ' value="' . $value['id'] . '" id="' . $value['id'] . '" class="sector" name="id_sector[]" ><span><i class="glyphicon glyphicon-ok"></i></span>' . $value['name'] . '</label>
	        </div>';
		}
		$ret_sector2 = '';
		foreach ($list_sector_2 as $key => $value) {
			if (in_array($value['id'], $b, true)) {
				$checked2 = 'checked';
			} else {
				$checked2 = '';
			}
			$word_first2 = strtolower(substr($value['name'], 0, 1));

			if ($word_first2 != $first_word2) {
				$ret_sector2 .= '<hr class="line-content">';
				$first_word2 = $word_first2;
			}
			$ret_sector2 .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
	          <label class="checkbox-sector"><input type="checkbox" ' . $checked2 . ' value="' . $value['id'] . '" id="' . $value['id'] . '" class="sector" name="id_sector[]" ><span><i class="glyphicon glyphicon-ok"></i></span>' . $value['name'] . '</label>
	        </div>';
		}
		$data['list_sector_1'] 	.= $ret_sector1;
		$data['list_sector_2'] 	.= $ret_sector2;

		$data['page_heading']    = 'Edit' . $type;
		$data['banner_top']      = banner_top(); // pake banner top
		$data['widget_sidebar']  = widget_sidebar(); //pake sidebar
		$data['seo_title']       = "AMCHAM INDONESIA";
		$data['hide_breadcrumb'] = 'hide';

		render("member/" . $type, $data);
	}

	function edit_profile()
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if ($user_sess_data) {
			if ($user_sess_data['remember_me'] = 1) {
				$this->load->model('loginModel');
				$this->loginModel->remember_me_login();
			}
			$data = $this->DashboardModel->fetchRow(array('id' => $user_sess_data['id']));
			//if($data['is_complete_data']==1 and $data['is_active']==1){
			if ($data['is_active'] == 1) {
				if ($this->session->flashdata('success_login')) {
					$data['show_info'] = '';
				} else {
					$data['show_info'] = 'hide';
				}
				$data['male_check']   = '';
				$data['female_check'] = '';

				if ($data['jeniskelamin'] == 'male') {
					$data['male_check'] = 'checked';
				} else if ($data['jeniskelamin'] == 'female') {
					$data['female_check'] = 'checked';
				}
				$select = language('select');
				$data['jeniskelamin'] = ($data['jeniskelamin'] == 'male') ? language('male') : language('female');
				$data['marketing'] = ($data['marketing']) ? '1' : '0';
				$data['newsletter'] = ($data['newsletter']) ? '1' : '0';
				$home_phone = explode('-', $data['home_phone']);
				$data['home_phone'] = $home_phone[1];
				$data['home_phone_code'] = $home_phone[0];
				$office_phone = explode('-', $data['office_phone']);
				$data['office_phone'] = $office_phone[1];
				$data['office_phone_code'] = $office_phone[0];
				$data['tgllahir_data'] = iso_date_custom_format($data['tgllahir'], 'd/m/Y');
				$data['tgllahir_pasangan'] = iso_date_custom_format($data['tgllahir_pasangan'], 'd/m/Y');
				$data['process_time'] = iso_date_custom_format($data['process_time'], 'd/m/Y H:i:s');
				$data['top_content']     = top_content();
				$data['tmptlahir_data'] = $this->db->select('ibukota')->get_where('t_master_tempat_lahir', "id_tmpt_lhr='$data[tmptlahir]'")->row()->ibukota;
				$data['provinsi_data'] = $this->db->select('propinsi')->get_where('propinsi', "id='$data[provinsi]'")->row()->propinsi;
				if ($data['pekerjaan'] == 'G') {
					$data['pekerjaan_data_show'] = '';
				} else {
					$data['pekerjaan_data_show'] = 'hide';
				}
				$data['is_married_have_child_show'] = 'hide';
				$data['is_married_show'] = 'hide';
				if ($data['status_nikah'] == 'Q') {
					$data['is_married_have_child_show'] = '';
					$data['is_married_show'] = '';
				}
				if ($data['status_nikah'] == 'S') {
					$data['is_married_show'] = '';
				}
				$data['pekerjaan_data'] = $this->db->select('keterangan')->get_where('t_master_pekerjaan', "id_pekerjaan='$data[pekerjaan]'")->row()->keterangan;
				$data['pekerjaan_data'] = $this->db->select('keterangan')->get_where('t_master_pekerjaan', "id_pekerjaan='$data[pekerjaan]'")->row()->keterangan;
				$data['pekerjaan_lain_data'] = $this->db->select('keterangan')->get_where('t_master_pekerjaan', "id_pekerjaan='$data[pekerjaan_lain]'")->row()->keterangan;
				$data['list_total_anak'] 	= selectlist2(array('table' => 't_master_kid_total', 'order' => 'id_kt', 'title' => $select . ' ' . language('total_kid'), 'name' => 'keterangan', 'id' => 'id_kt', 'selected' => $data['jumlah_anak']));
				$data['pendapatan_bulanan_data'] = $this->db->select('keterangan')->get_where('t_master_pendapatan_bulanan', "id_pb='$data[pendapatan_bulanan]'")->row()->keterangan;
				$data['status_nikah_data'] = $this->db->select('keterangan')->get_where('t_master_status_nikah', "id_sn='$data[status_nikah]'")->row()->keterangan;
				$data['pendidikan_data'] = $this->db->select('keterangan')->get_where('t_master_pendidikan', "id_pt='$data[pendidikan]'")->row()->keterangan;
				$data['page_name'] = language('account_setting');
				$data['message'] = $this->session->flashdata('success_login');
				$data['list_provinsi'] 	= selectlist2(array('table' => 'propinsi', 'order' => 'propinsi', 'title' => $select . ' ' . language('province'), 'name' => 'propinsi', 'selected' => $data['provinsi']));
				$data['list_tmp_lahir'] 	= selectlist2(array('table' => 't_master_tempat_lahir', 'order' => 'ibukota', 'title' => $select . ' ' . language('birth_place'), 'name' => 'ibukota', 'id' => 'id_tmpt_lhr', 'selected' => $data['tmptlahir']));
				$data['list_pekerjaan'] 	= selectlist2(array('table' => 't_master_pekerjaan', 'where' => 'is_active=1', 'order' => 'sort', 'title' => $select . ' ' . language('occupation'), 'name' => 'keterangan', 'id' => 'id_pekerjaan', 'selected' => $data['pekerjaan']));
				$data['list_pekerjaan_lain'] 	= selectlist2(array('table' => 't_master_pekerjaan', 'where' => 'is_active=1', 'order' => 'sort', 'title' => $select . ' ' . language('occupation'), 'name' => 'keterangan', 'id' => 'id_pekerjaan', 'selected' => $data['pekerjaan_lain']));
				$data['list_status_nikah'] 	= selectlist2(array('table' => 't_master_status_nikah', 'order' => 'sort', 'title' => $select . ' ' . language('marital_status'), 'name' => 'keterangan', 'id' => 'id_sn', 'selected' => $data['status_nikah']));
				$data['list_pendidikan'] 	= selectlist2(array('table' => 't_master_pendidikan', 'order' => 'sort', 'title' => $select . ' ' . language('education'), 'name' => 'keterangan', 'id' => 'id_pt', 'where' => 'is_publish=1', 'selected' => $data['pendidikan']));
				$data['list_pendapatan_bulanan'] 	= selectlist2(array('table' => 't_master_pendapatan_bulanan', 'order' => 'sort', 'order_by' => 'desc', 'title' => $select . ' Pendapatan Bulanan', 'name' => 'keterangan', 'id' => 'id_pb', 'selected' => $data['pendapatan_bulanan']));
				$data['list_umur_anak'] 	= selectlist2(array('table' => 't_master_kid_age', 'order' => 'id_ka', 'title' => $select . ' ' . language('age'), 'name' => 'keterangan', 'id' => 'id_ka', 'where' => 'is_publish=1'));
				$data['list_umur_pasangan'] 	= selectlist2(array('table' => 't_master_spouse_age', 'order' => 'id_sa', 'title' => $select . ' ' . language('age'), 'name' => 'keterangan', 'id' => 'id_sa', 'where' => 'is_publish=1', 'selected' => $data['umur_pasangan']));
				$data['newsletter_checked'] 	= ($data['newsletter'] == 1) ? 'checked' : '';
				$data['marketing_checked'] 	= ($data['marketing'] == 1) ? 'checked' : '';
				$this->load->model('LogActivityModel');
				$data_child_member = $this->DashboardModel->get_child($user_sess_data['id']);
				$i = 0;
				foreach ($data_child_member as $n =>  $data_child) {
					++$i;
					$data_child_member[$n]['nomor'] 	= ++$n;
					$data_child_member[$n]['dob_child'] 	= iso_date_custom_format($data_child['dob_child'], 'd/m/Y');
					$data_child_member[$n]['jeniskelamin_child'] 	= $data_child['jeniskelamin'];
					$data_child_member[$n]['umur_anak_data'] = $this->db->select('keterangan')->get_where('t_master_kid_age', "id_ka='$data_child[umur_anak]'")->row()->keterangan;
				}
				if ($i > 0) {
					$data['data_child_member_true'] = 'hide';
					$data['data_child_member_false'] = '';
				} else {
					$data['data_child_member_true'] = '';
					$data['data_child_member_false'] = 'hide';
				}
				$data['list_data_child'] = $data_child_member;
				$data['list_data_child_member'] = $data_child_member;
				$list_data = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'], $data);
				foreach ($list_data as $key => $value) {
					$list_data[$key]['class_abu'] = $key % 2 == 0 ? '' : 'timeline-inverted';
					$list_data[$key]['invert'] = $key % 2 == 0 ? '' : 'invert';

					$list_data[$key]['is_news'] = 'hide';
					$list_data[$key]['is_not_news'] = '';
					$list_data[$key]['create_date'] = iso_date_custom_format($value['create_date'], 'd/m/Y H:i:s');
					$list_data[$key]['create_date_ago'] = time_elapsed_string($value['last_date_read']);
					if ($value['id_article']) {
						$where['a.id'] = $value['id'];
						$this->db->select('a.*,b.news_title,b.uri_path,b.img,b.teaser,c.name as category,c.uri_path as uri_path_category');
						$this->db->join('news b', 'b.id = a.id_article');
						$this->db->join('news_category c', 'c.id = b.id_news_category');
						$data_news = $this->db->get_where('user_activity_log a', $where)->row_array();
						$list_data[$key]['img'] = image($data_news['img'], 'large');
						$list_data[$key]['uri_path_category'] = $data_news['uri_path_category'];
						$list_data[$key]['uri_path'] = $data_news['uri_path'];
						$list_data[$key]['category'] = $data_news['category'];
						$list_data[$key]['news_title'] = $data_news['news_title'];
						$list_data[$key]['teaser'] = $data_news['teaser'];
						$list_data[$key]['last_date_read'] = iso_date_custom_format($data_news['last_date_read'], 'd/m/Y H:i:s');
						$list_data[$key]['last_date_read_ago'] = time_elapsed_string($data_news['last_date_read']);
						$list_data[$key]['log_count'] = $data_news['log_count'];

						$list_data[$key]['is_news'] = '';
						$list_data[$key]['is_not_news'] = 'hide';
					}
				}
				$data['list_data'] = $list_data;
				$ttl_record = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'], 'all');
				$data['load_more'] = $ttl_record > PAGING_PERPAGE_LOG ? "<div class='parent-load-more'><li class='clearfix' style='float: none;'></li><a class='btn btn-default load-more' data-page='" . PAGING_PERPAGE_LOG . "'>" . language('load_more') . "</a></div>" : '';
				if (getimagesize($data['image'])) {
					$data['avatar'] = $data['image'];
				} else {
					$data['avatar']	= base_url() . (($data['image']) ? "images/member/profile_pictures/$data[image]" : 'images/member/profile_pictures/no_image.jpg');
				}

				render('layout/ddi/member/edit_profile', $data);
			} else if ($data['is_complete_data'] == 0 and $data['is_active'] == 0) {
				if ($this->session->flashdata('success_login')) {
					$data['show_info'] = '';
				} else {
					$data['show_info'] = 'hide';
				}
				$data['message'] = $this->session->flashdata('success_login');
				$data['fb_like_widget'] = fb_like_widget();
				render('layout/ddi/member/complete_data', $data);
			} else if ($data['is_complete_data'] == 1 and $data['is_active'] == 0) {
				$data['fb_like_widget'] = fb_like_widget();
				$data['title'] = language('account_not_active');
				$data['message'] = language('account_not_active_error');
				render('layout/ddi/member/page', $data);
			}
		} else {
			redirect('/member/login');
		}
	}
	function success_registration()
	{
		$user_sess_data = $this->session->flashdata('session_register_success');
		if ($user_sess_data) {
			$data['news_title'] = language('register_success');
			render('layout/ddi/member/success_registration', $data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function success_deactive_account()
	{
		$user_sess_data = $this->session->flashdata('session_deactive_success');
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		if ($user_sess_data) {
			$this->kill_session();
			$data['title'] = language('success_deactive_account');
			$data['message'] = language('success_deactive_account_message');
			render('layout/ddi/member/page', $data);
		} else {
			$data['title'] = language('success_deactive_account');
			$data['message'] = language('success_deactive_account_message');
			render('layout/ddi/member/page', $data);
		}
	}
	function logs_more($page)
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');
		$this->load->model('LogActivityModel');
		$list_data = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'], $page);
		foreach ($list_data as $key => $value) {
			$list_data[$key]['class_abu'] = $key % 2 == 0 ? '' : 'timeline-inverted';
			$list_data[$key]['invert'] = $key % 2 == 0 ? '' : 'invert';

			$list_data[$key]['is_news'] = 'hide';
			$list_data[$key]['is_not_news'] = '';
			$list_data[$key]['is_qa'] = 'hide';
			$list_data[$key]['is_not_qa'] = '';
			$list_data[$key]['create_date'] = iso_date_custom_format($value['create_date'], 'd/m/Y H:i:s');
			$list_data[$key]['create_date_ago'] = time_elapsed_string($value['create_date']);

			if ($value['id_article']) {
				$where['a.id'] = $value['id'];
				$this->db->select('a.*,b.news_title,b.uri_path,b.img,b.teaser,c.name as category,c.uri_path as uri_path_category');
				$this->db->join('news b', 'b.id = a.id_article');
				$this->db->join('news_category c', 'c.id = b.id_news_category');
				$data_news = $this->db->get_where('user_activity_log a', $where)->row_array();
				$list_data[$key]['img'] = image($data_news['img'], 'large');
				$list_data[$key]['uri_path_category'] = $data_news['uri_path_category'];
				$list_data[$key]['uri_path'] = $data_news['uri_path'];
				$list_data[$key]['category'] = $data_news['category'];
				$list_data[$key]['news_title'] = $data_news['news_title'];
				$list_data[$key]['teaser'] = $data_news['teaser'];
				$list_data[$key]['last_date_read'] = iso_date_custom_format($data_news['last_date_read'], 'd/m/Y H:i:s');
				$list_data[$key]['last_date_read_ago'] = time_elapsed_string($data_news['last_date_read']);
				$list_data[$key]['log_count'] = $data_news['log_count'];

				$list_data[$key]['is_news'] = '';
				$list_data[$key]['is_not_news'] = 'hide';
			} else {
				if ($value['id_ask_expert']) {
					$where['a.id'] = $value['id_ask_expert'];
					$this->db->select('a.*,b.title as title_category, c.title as title_subcategory');
					$this->db->join('category b', 'b.id = a.id_category');
					$this->db->join('sub_category c', 'c.code = a.sub_category');

					$data_qa = $this->db->get_where('member_ask_expert a', $where)->row_array();
					$list_data[$key]['is_qa'] = '';
					$list_data[$key]['is_not_qa'] = 'hide';
					$list_data[$key]['question_qa'] = $data_qa['question_descr'];
					$list_data[$key]['answer_descr_qa'] = $data_qa['answer_descr'];
					$list_data[$key]['title_category_qa'] = $data_qa['title_category'];
					$list_data[$key]['title_subcategory_qa'] = $data_qa['title_subcategory'];
				}
			}
		}
		$data['list_data'] = $list_data;
		$ttl_record = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'], 'all');
		$data['load_more']      = ($ttl_record > ($page + PAGING_PERPAGE_LOG)) ? "<div class='parent-load-more'><li class='clearfix' style='float: none;'></li><a class='btn btn-default load-more' data-page='" . ($page + PAGING_PERPAGE_LOG) . "'>" . language('load_more') . "</a></div>" : '';

		render('layout/ddi/member/logs_more', $data, 'blank');
	}
	function kill_session()
	{
		// delete_cookie('user');
		delete_cookie('password');
		// $this->input->cookie('user',TRUE);
		$this->input->cookie('password', TRUE);

		delete_cookie('username');
		// delete_cookie('password');
		$this->input->cookie('username', TRUE);
		// $this->input->cookie('password',TRUE);
		$this->session->sess_destroy();
	}
	function logout()
	{
		// $data_now = date('Y-m-d H:i:s');
		// $user_sess_data = $this->session->userdata('MEM_SESS');
		// if($user_sess_data){
		// 	$this->load->model('RegisterModel');
		// 	$data = $this->DashboardModel->fetchRow(array('id'=>$user_sess_data['id']));
		// 	if($data['last_login']){
		// 		$log_user_activity = array(
		// 			'id_user'          =>  $data['id'],
		// 			'process_date' =>  $data_now,
		// 			'id_log_category'   =>  27,
		// 		);
		// 		$this->RegisterModel->log_user_activity($log_user_activity);

		// 		$where['id'] = $data['id'];
		// 		$data_update['last_logout'] 	= $data_now;
		// 		$this->db->update('t_aegon_profile_member',$data_update,$where);
		// 	}	
		$this->kill_session();
		// }
		redirect('/');
	}
	function complete_data_process()
	{
		$post = purify($this->input->post());
		if ($post) {
			$this->load->model('RegisterModel');
			$this->form_validation->set_rules('namadepan', '"Nama Depan"', 'required');
			$this->form_validation->set_rules('pwd', '"Password"', 'required');
			$this->form_validation->set_rules('email', '"Email"', 'required|valid_email');
			if ($this->form_validation->run() == FALSE) {
				$message = validation_errors();
				$status = 'error';
			} else {
				$email = $post['email'];
				$user_sess_data = $this->session->userdata('MEM_SESS');
				$proses = $this->RegisterModel->complete_data($post, $user_sess_data['id']);
				if ($proses['status'] == 1) {
					$status = 'success';
					$this->session->set_flashdata('success_login', $proses['message']);
				} else {
					$status = language('something_error');
					$message = $proses['message'];
				}

				$data['message'] 	=  "<div class='$status-label'> $message</div>";
				$data['status'] 	=  $status;
				echo json_encode($data);
			}
		}
	}
	function update_process()
	{
		$post = purify($this->input->post());
		if ($post) {
			$this->load->model('RegisterModel');
			$email = $post['email'];
			if ($post['home_phone']) {
				$post['home_phone'] = $post['home_phone_code'] . '-' . $post['home_phone'];
			}
			if ($post['office_phone']) {
				$post['office_phone'] = $post['office_phone_code'] . '-' . $post['office_phone'];
			}
			unset($post['home_phone_code'], $post['office_phone_code']);
			$user_sess_data = $this->session->userdata('MEM_SESS');
			$proses = $this->RegisterModel->update($post, $user_sess_data['id']);
			if ($proses['status'] == 1) {
				$status = 'success';
				$message = $proses['message'];
			} else {
				$status = language('something_error');
				$message = $proses['message'];
			}

			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
	function update_process_with_email()
	{
		$post = purify($this->input->post());
		if ($post) {
			$this->load->model('RegisterModel');
			$this->load->model('RegisterModel');
			$this->form_validation->set_rules('namadepan', '"Nama Depan"', 'required');
			$this->form_validation->set_rules('namabelakang', '"Nama Belakang"', 'required');
			if ($this->form_validation->run() == FALSE) {
				$message = validation_errors();
				$status = 'error';
			} else {
				$email = $post['email'];
				$re_email = $post['email_new_retype'];
				unset($post['email'], $post['email_new_retype']);
				$user_sess_data = $this->session->userdata('MEM_SESS');
				$proses = $this->RegisterModel->update($post, $user_sess_data['id']);
				if ($proses['status'] == 1) {
					$status = 'success';
					$message = $proses['message'];
				} else {
					$status = language('something_error');
					$message = $proses['message'];
				}
				if ($email != null or $email != '' and $user_sess_data) {
					$where_email['email'] = $email;
					$check_email = $this->db->select('*')->get_where('t_aegon_profile_member', $where_email)->row();
					$status = language('something_error');
					if ($email and $re_email) {
						if (valid_email($email) and valid_email($re_email)) {
							if ($email != $re_email) {
								$message = language('email_confirmation_doesnt_match');
							} else if ($check_email) {
								$message = language('email_already_registered');
							} else {
								$this->RegisterModel->change_new_email($email);
								$status = 'success';
								$message = language('change_email_success_message');
							}
						} else {
							$message = language('error_invalid_email_format');
						}
					} else {
						$message = language('retype_email_error');
					}
				}
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
	function check_avalible_email()
	{
		$where_email['email'] = $this->input->get('email');
		$check_email = $this->db->select('*')->get_where('t_aegon_profile_member', $where_email)->row();
		if ($check_email) {
			$message = language('email_already_registered');
			echo 'false';
		} else {
			echo 'true';
		}
	}
	function confirmation_change_email($activation_code)
	{
		$process = $this->RegisterModel->change_new_email_confirmed($activation_code);
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		if ($process['status'] == 1) {
			$data['title'] = language('activation_code_capcha_change_email');
			$data['message'] = language('activation_code_capcha_message_change_email');
			$data['google_captcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
			$data['activation_code'] = $activation_code;
			render('layout/ddi/member/activation_member_change_email', $data);
		} else {
			$data['title'] = language('activation_code_not_valid');
			$data['message'] = $process['message'];
			render('layout/ddi/member/page', $data);
		}
	}
	function process_activation_member_change_email()
	{
		$this->load->library('curl');
		$post = purify($this->input->post());
		if ($post) {
			$userIp = $this->input->ip_address();
			$data_captcha = array();
			$secret = GOOGLE_CAPTCHA_SECRET_KEY;
			$responsecaptcha = trim($post['g-recaptcha-response']);
			$url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $responsecaptcha . "&remoteip=" . $userIp;

			$ch = curl_init();
			$user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			$data_captcha = curl_exec($ch);
			curl_close($ch);

			$status = json_decode($data_captcha, true);
			if (!$status['success']) {
				$status_data = language('not_check_captcha');
				$message = language('not_check_captcha_message');
			} else if ($status['success']) {
				$process_active = $this->RegisterModel->change_new_email_confirmed_process($post['activation_code']);
				if ($process_active['status'] == 1) {
					$this->session->set_flashdata('change_email_success_flash', 'true');
					$status_data = 'success';
					//$this->kill_session();
				} else {
					$status_data = 'failed';
					$message = $process_active['message'];
				}
			}
			$this->session->set_flashdata('change_email_success_flash', 'true');
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status_data;
			echo json_encode($data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function change_password()
	{
		$post = purify($this->input->post());
		$this->load->model('RegisterModel');
		if ($post) {
			$post['id_contact_us_topic'] =  $post['topic'];
			$this->form_validation->set_rules('current_pwd', '"Password Lama"', 'required');
			$this->form_validation->set_rules('pwd', '"Password"', 'required');
			if ($this->form_validation->run() == FALSE) {
				$message = validation_errors();
				$status = 'error';
			} else {
				$proses = $this->RegisterModel->change_password($post);
				if ($proses['status'] == 1) {
					$status = 'success';
					$message = $proses['message'];
					$this->kill_session();
				} else {
					$status = language('something_error');
					$message = $proses['message'];
				}
			}

			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
	function change_profile_picture()
	{
		$data_now = date('Y-m-d H:i:s');
		$user_sess_data = $this->session->userdata('MEM_SESS');
		$file 	= $_FILES['avatar'];
		$fname 	= $file['name'];
		if ($fname and $user_sess_data) {
			$size = getimagesize($file['tmp_name']);
			$resize_height = ($size[1] * 200) / $size[0];
			$ext				= explode('.', $fname);
			$ext				= $ext[count($ext) - 1];
			$data_reset['image'] = md5($user_sess_data['id']) . '.' . $ext;
			$where_reset['id'] = $user_sess_data['id'];
			$this->db->update('t_aegon_profile_member', $data_reset, $where_reset);
			$config['image_library'] 	= 'gd2';
			$config['source_image'] 	= $file['tmp_name'];
			$config['new_image']		= 'images/member/profile_pictures/' . $data_reset['image'];
			$config['create_thumb'] 	= FALSE;
			$config['maintain_ratio'] 	= TRUE;
			$config['width'] 			= 200;
			$config['height'] 			= $resize_height;

			$this->load->library('image_lib');
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$status = 'success';
			$message = language('profile_image_success_changed');
		} else {
			$status = language('something_error');
			$message = language('something_error_call');
		}
		$data['message'] 	=  $message;
		$data['status'] 	=  $status;
		echo json_encode($data);
	}
	function change_subscriber()
	{
		$post = purify($this->input->post());
		$this->load->model('RegisterModel');
		if ($post) {
			$proses = $this->RegisterModel->change_subscriber($post);
			if ($proses['status'] == 1) {
				$status = 'success';
				$message = $proses['message'];
			} else {
				$status = language('something_error');
				$message = $proses['message'];
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
	function login()
	{
		$post = $this->input->post();

		$username           = $post['username'];
		$password           = strtoupper($post['password']);
		$where['email']     = $username;
		$where['password']  = md5($password);
		$where['is_delete'] = 0;

		$datadataan = $this->db->get_where('auth_member', $where)->row_array();

		$check = $this->RegisterModel->fetchRow($where);
		$secret   = GOOGLE_CAPTCHA_SECRET_KEY;
        $response = $_POST['g-recaptcha-response'];
        $ip       = $_SERVER['REMOTE_ADDR'];
        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$response."&remoteip=".$ip);
        $Return   = json_decode($Response);

        if($Return->success){
			if ($check) { // true
				if ($check['is_block'] == 1) {
					// check is account banned / block
					$ret['error']    = 1;
					$ret['msg']      = 'Sorry Your Accounts is Banned, Please Contact The Administator';
				} else if ($check['status_payment_id'] == 6) {
					$ret['error']    = 1;
					$ret['msg']      = 'Sorry Your Accounts is Banned, Please Contact The Administator ';
				} else {
					$remember = $post['remember_me'] ? 1 : 0;
					$this->RegisterModel->login_member($check, $remember);
					$ret['error']    = 0;
					$ret['redirect'] = base_url() . 'member/profile';
				}
			} else {

				$ret['msg']      = 'User or Password is Incorrect';
				$ret['error']    = 1;
				$ret['redirect'] = base_url() . 'member';
			}
		} else {
			$ret['msg']      = 'Sorry, Please Try Again';
			$ret['error']    = 1;
			$ret['redirect'] = base_url() . 'member';
		}
		echo json_encode($ret);
	}
	private function create_Invoice($invoice_code, $invoice_email, $invoice_price, $invoice_desc, $invoice_option = [], $create_Invoice)
	{
		include APPPATH . 'third_party/xendit/XenditPHPClient.php';
		$options['secret_api_key'] = XENDIT_SECRET_KEY;
		$xendit = new XenditClient\XenditPHPClient($options);
		$url = $invoice_option['url'];
		unset($invoice_option['url']);
		$invoice_option = [
			'success_redirect_url' => $url . '?status=success',
			'failure_redirect_url' => $url . '?status=error',
			'should_send_email' => true,
		];

		switch ($create_Invoice) {
			case 'virtual_account':
				$invoice_price =  $invoice_price + 5000;
				$invoice_option['payment_methods']  = [
					"BRI", "MANDIRI", "BNI",
					"PERMATA"
				];

				break;

			case 'card_kredit':
				if ($invoice_price==22000000) {
					$invoice_price = $invoice_price + 649990;
					$invoice_option['payment_methods']  = ["CREDIT_CARD"];
				} else if ($invoice_price==7500000) {
					$invoice_price = $invoice_price + 223080;
					$invoice_option['payment_methods']  = ["CREDIT_CARD"];
				}
				 else {
					$invoice_price = ($invoice_price + 2000) + ($invoice_price * 0.026) + 52;
					$invoice_option['payment_methods']  = ["CREDIT_CARD"];
				}
				break;
		}

		$createInvoice = $xendit->createInvoice($invoice_code, $invoice_price, $invoice_email, $invoice_desc, $invoice_option);

		return $createInvoice;
	}
	function register($category='')
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if ($user_sess_data) {
			redirect('/');
		}

		$data['page_heading']        = 'Membership';
		$data['banner_top']          = banner_top(); // pake banner top
		$data['widget_sidebar']      = widget_sidebar(); //pake sidebar
		$data['seo_title']           = "AMCHAM INDONESIA";
		$data['hide_breadcrumb']     = 'hide';
		$this->data['recaptcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;

		render('member/register', $data);
	}
	function check_email($email)
	{
		$post            = $this->input->post();
		$where['email']  = $post['email'];
		$check_user  	= $this->member_model->findBy($where, 1);

		if ($check_user) {
			$data['valid'] = false;
			echo json_encode($data);
		} else {
			$data['valid'] = true;
			echo json_encode($data);
		}
	}
	function register_proses()
	{
		$post                       = $this->input->post();
		$full_url = $post['full_url'];
		unset($post['full_url']);

		$action = $_POST['action'];
		$response = $_POST['g-recaptcha-response'];
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => GOOGLE_CAPTCHA_SECRET_KEY, 'response' => $response)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$Response = curl_exec($ch);
		curl_close($ch);
		$arrResponse = json_decode($Response, true);

		if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
			// validasi email double di matikan 
			/*$where['email'] = $post['member']['email'];
			$check_user  	= $this->member_model->findBy($where,1);

			if ($check_user) {
				$ret['error'] = 1;
				$ret['msg'] = 'Email has been registered, please use other email.';
				echo json_encode($ret);exit;
			}*/
			$ret['error'] = 0;

			// fitur account sementara dimatikan  
			// $password                             = generatePassword();


			$membership_category =  db_get_one('auth_member_category', 'name', array('id' => $post['member']['member_category_id']));
			$member_fullname     = full_name($post['member']);		
			$member_category_data = $this->db->get_where('auth_member_category', array('id' => $post['member']['member_category_id']))->row_array();
			$invoice_code = ($post['payment_method'] == 'payment_online') ? 'M-' : 'MB-';	
			$invoice_code .= random_string('numeric',12);	
			$invoice_desc   = 'Membership ';
			$invoice_desc   .= $member_category_data['alias'] == '' ? $member_category_data['name'] : $member_category_data['alias'];
			$invoice_price = $member_category_data['price'];

			$post['member']['email']              = $post['member']['email'];
			// $post['member']['password']           = md5($password);
			$post['member']['m_t_number']         = $post['company']['t_number'];
			$post['member']['status_payment_id']  = 2;
			$post['member']['uri_path']           = generate_url_member(full_name($post['member']));
			$post['member']['is_invis']           = $post['is_invis'];
			$id_member                            = $this->member_model->insert($post['member']);

			if (!$id_member) {
				$ret['error']    = 1;
				$ret['msg']      = 'Something wrong';
				$ret['redirect'] = '';
			}

			detail_log();
			insert_frontend_log('New member Register');
			// insert company for member
			$post['company']['member_id_create']  = $id_member;
			$post['company']['uri_path_name_out'] = generate_url_company($post['company']['name_in']);
			$post['company']['is_invis_company']  = $post['is_invis'];

			$id_company                           = $this->company_model->insert($post['company']);

			// update member  company
			$update_member['company_id']          = $id_company;
			$this->member_model->update_frontend($update_member, $id_member);

			//insert membership member 
			$data_member                              = $this->member_model->findViewById($id_member);
			$membership_save['member_id']             = $id_member;
			$membership_save['company_id']            = $id_company;
			$membership_save['first_registered_date'] = date("Y-m-d");
			$membership_id                            = $this->membership_model->insert($membership_save);

			// update member id membership information
			$membership_update['membership_information_id'] = $membership_id;
			$this->member_model->update_frontend($membership_update, $id_member);

			// Invoice
			
			$data_invoice['id_ref_payment_category'] = 2;
				$data_invoice['invoice_number']          = $invoice_code;
				$data_invoice['member_id']               = $id_member;
				$data_invoice['is_paid']                 = 0;
				$data_invoice['id_ref_payment_type'] = ($post['payment_method'] != 'payment_online') ? 2 : 1;
				$this->paymentconfirmation_model->insert_frontend($data_invoice);		
				
			// email section 
			
			// sent email user untuk confirmation pembayaran 
			if ($post['payment_method'] != 'payment_online') {
				// bank transfer
				$this->load->model('bank_account_model');
				// $bank_data   					 = $this->bank_account_model->findById($post['bank_name']);
				$bank_data                       = $this->bank_account_model->bank_list();
				$bank_page_content               = bank_page_email($bank_data);
				
				$email_user['name']              = $member_fullname;
				$email_user['category']          = $membership_category;
				$email_user['invoice_number']    = $invoice_code;
				$email_user['name_price']        = $invoice_desc;
				$email_user['price']             = number_format($invoice_price);
				$email_user['grand_total']       = number_format($invoice_price);
				$email_user['bank_page_content'] = $bank_page_content;
				$email_user['link']              = base_url_lang() . '/payment_confirmation/' . $invoice_code;
				
				sent_email_by_category(17, $email_user, $post['member']['email']);
			}
			
			// send email admin
				$email_admin['category']        = $membership_category;
				$email_admin['is_company']      = $post['member']['member_category_id'] == 1  ? 'hide' : '';
				
				$email_admin['name']            = $member_fullname;
				$email_admin['job']             = $post['member']['job'];
				
				$email_admin['email']           = $post['member']['email'];
				
				$email_admin['name_in']         = $post['company']['name_in'];
				$email_admin['company_address'] = $post['company']['address'];
				$email_admin['city']            = $post['company']['city'];
				$email_admin['postal']          = $post['company']['postal_code'];
				$email_admin['headquarters']    = $post['company']['headquarters'];
				$email_admin['website']         = $post['company']['website'];
				$email_admin['company_email']   = $post['company']['email'];
				$email_admin['t_number']        = $post['company']['t_number'];
				$email_admin['link']            = base_url() . 'apps';
				
				sent_email_by_category(6, $email_admin, EMAIL_ADMIN_TO_SEND);
			// 
			
			
			//SENT EMAIL TEMPORARY TO MEMBER 
			
			/*$email_user['name']     = $member_fullname;
			$email_user['category'] = $membership_category;
			$email_user['username'] = $post['member']['email'];
			$email_user['password'] = $password;
			$email_user['link']     = base_url().'member';
		
			sent_email_by_category(2,$email_user,$post['member']['email']);*/
			
			// NOTIF MEMBER REGISTRASI to admin 
			// $get_member                     = $this->member_model->findBy(array('id'=> $id_member),1);
			// $get_company                    = $this->company_model->findBy(array('id'=>$id_company),1);
			

			
			// $ret['modalname'] = 'myModalThanks';
			
			
			if ($post['payment_method'] == 'payment_online') {
				$invoice = $this->create_Invoice($invoice_code,$post['member']['email'],$invoice_price, $invoice_desc,['url'=>$full_url], $post['online_payment']);
				$ret['redirect'] = $invoice['invoice_url'];
				echo json_encode($ret);
				exit;
			}else{
				echo json_encode($ret);
				exit;
			}
		} else {
			$ret['error']    = 1;
			$ret['msg']      = 'Something wrong';
			echo json_encode($ret);
		    // spam submission
		    // show error message
		}
		
	}
	/*function get_town(){
		$select = language('select');
    
	    $id = $this->input->post('id');
	    if($id){
		$list 			 = $this->db->query('select kota,id_propinsi,type_kota from master_kd_pos where id_propinsi='.$id.' group by kota,id_propinsi,type_kota order by kota asc')->result_array();
		$opt 			 = $conf['no_title'] ? '' : "<option value=''>". language('select')." ". language('city')."</option>";
		foreach($list as $l){
			if($l['kota'] == $this->input->post('kota')){
			    $opt 	.= "<option value='$l[kota]' selected> $l[type_kota] $l[kota]</option>";
		    } else {
			    $opt 	.= "<option value='$l[kota]'> $l[type_kota] $l[kota]</option>";
		    }
		}
		echo $opt;
	    } else {
		echo '';
	    }
	}*/
	/*function get_kecamatan(){
	    $id = $this->input->post('id');
	    if($id){
		$list 			 = $this->db->query("select kecamatan,kota from master_kd_pos where kota='".$id."' group by kecamatan,kota order by kecamatan asc")->result_array();
		$opt 			 = $conf['no_title'] ? '' : "<option value=''>".language('select')." ".language('district')."</option>";
		foreach($list as $l){
		    if($l['kecamatan'] == $this->input->post('kecamatan')){
			    $opt 	.= "<option value='$l[kecamatan]' selected> $l[kecamatan]</option>";
		    } else {
			    $opt 	.= "<option value='$l[kecamatan]'> $l[kecamatan]</option>";
		    }
		}
		echo $opt;
	    } else {
		echo '';
	    }
	}*/
	/*function get_kelurahan(){
	    $id = $this->input->post('id');
	    if($id){
		$list 			 = $this->db->query("select kelurahan,kecamatan from master_kd_pos where kecamatan='".$id."' group by kecamatan,kelurahan order by kelurahan asc")->result_array();
		$opt 			 = $conf['no_title'] ? '' : "<option value=''>".language('select')." ".language('sub_district')."</option>";
		foreach($list as $l){
		    if($l['kelurahan'] == $this->input->post('kelurahan')){
			    $opt 	.= "<option value='$l[kelurahan]' selected> $l[kelurahan]</option>";
		    } else {
			    $opt 	.= "<option value='$l[kelurahan]'> $l[kelurahan]</option>";
		    }
		}
		echo $opt;
	    } else {
		echo '';
	    }
	}*/
	public function social_signup($provider)
	{
		if (LANGUAGE == 'english') {
			log_message('debug', "controllers.HAuth.login($provider) called");

			try {
				log_message('debug', 'controllers.HAuth.login: loading HybridAuthLib');
				$this->load->library('HybridAuthLib');

				if ($this->hybridauthlib->providerEnabled($provider)) {
					log_message('debug', "controllers.HAuth.login: service $provider enabled, trying to authenticate.");
					$service = $this->hybridauthlib->authenticate($provider);

					if ($service->isUserConnected()) {
						log_message('debug', 'controller.HAuth.login: user authenticated.');

						$user_profile = $service->getUserProfile();

						log_message('info', 'controllers.HAuth.login: user profile:' . PHP_EOL . print_r($user_profile, TRUE));

						$data['user_profile'] = $user_profile;
						$process = $this->RegisterModel->inserting_data($data, $provider);
						if ($process == 1) {
							redirect('/member');
						} else {
							redirect('/member');
						}
						//$this->load->view('hauth/done',$data);
					} else // Cannot authenticate user
					{
						show_error('Cannot authenticate user');
					}
				} else // This service is not enabled.
				{
					log_message('error', 'controllers.HAuth.login: This provider is not enabled (' . $provider . ')');
					show_404($_SERVER['REQUEST_URI']);
				}
			} catch (Exception $e) {
				$error = 'Unexpected error';
				switch ($e->getCode()) {
					case 0:
						$error = 'Unspecified error.';
						break;
					case 1:
						$error = 'Hybriauth configuration error.';
						break;
					case 2:
						$error = 'Provider not properly configured.';
						break;
					case 3:
						$error = 'Unknown or disabled provider.';
						break;
					case 4:
						$error = 'Missing provider application credentials.';
						break;
					case 5:
						log_message('debug', 'controllers.HAuth.login: Authentification failed. The user has canceled the authentication or the provider refused the connection.');
						//redirect();
						if (isset($service)) {
							log_message('debug', 'controllers.HAuth.login: logging out from service.');
							$service->logout();
						}
						show_error('User has cancelled the authentication or the provider refused the connection.');
						break;
					case 6:
						$error = 'User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.';
						break;
					case 7:
						$error = 'User not connected to the provider.';
						break;
				}

				if (isset($service)) {
					$service->logout();
				}

				log_message('error', 'controllers.HAuth.login: ' . $error);
				show_error('Error authenticating user.');
			}
		}
	}

	public function endpoint()
	{

		log_message('debug', 'controllers.HAuth.endpoint called.');
		log_message('info', 'controllers.HAuth.endpoint: $_REQUEST: ' . print_r($_REQUEST, TRUE));

		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			log_message('debug', 'controllers.HAuth.endpoint: the request method is GET, copying REQUEST array into GET array.');
			$_GET = $_REQUEST;
		}

		log_message('debug', 'controllers.HAuth.endpoint: loading the original HybridAuth endpoint script.');
		require_once APPPATH . '/third_party/hybridauth/index.php';
	}
	function active_member($activation_code)
	{
		$process = $this->RegisterModel->active_member($activation_code);
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		if ($process['status'] == 1) {
			$data['title'] = language('activation_code_capcha');
			$data['message'] = language('activation_code_capcha_message');
			$data['google_captcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
			$data['activation_code'] = $activation_code;
			render('layout/ddi/member/activation_member', $data);
		} else {
			$data['title'] = language('activation_failed');
			$data['message'] = $process['message'];
			render('layout/ddi/member/page', $data);
		}
	}
	function process_activation_member()
	{
		$this->load->library('curl');
		$post = purify($this->input->post());
		if ($post) {
			$userIp = $this->input->ip_address();
			$data_captcha = array();
			$secret = GOOGLE_CAPTCHA_SECRET_KEY;
			$responsecaptcha = trim($post['g-recaptcha-response']);
			$url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $responsecaptcha . "&remoteip=" . $userIp;

			$ch = curl_init();
			$user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			$data_captcha = curl_exec($ch);
			curl_close($ch);

			$status = json_decode($data_captcha, true);

			if (!$status['success']) {
				$status_data = language('not_check_captcha');
				$message = language('not_check_captcha_message');
			} else if ($status['success']) {
				$process_active = $this->RegisterModel->process_activation($post['activation_code']);
				if ($process_active['status'] == 1) {
					$this->session->set_flashdata('email_activation_success', 'true');
					$status_data = 'success';
					$message = language('activation_success_message');
				}
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status_data;
			echo json_encode($data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function active_member_success_change_email()
	{
		$user_sess_data = $this->session->flashdata('change_email_success_flash');
		if ($user_sess_data) {
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article'] = popular_article(0);
			$data['new_article']    = new_article(1, 2);
			$data['new_expert']    = new_expert(1, 1);
			$this->kill_session();
			$data['title'] = language('change_email_success_title');
			$data['message'] = language('change_email_success');
			render('layout/ddi/member/page', $data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function active_member_success()
	{
		$user_sess_data = $this->session->flashdata('email_activation_success');
		if ($user_sess_data) {
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article'] = popular_article(0);
			$data['new_article']    = new_article(1, 2);
			$data['new_expert']    = new_expert(1, 1);
			$data['title'] = language('activation_success');
			$data['message'] = language('activation_success_message');
			render('layout/ddi/member/page', $data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function active_email_member($activation_code)
	{
		$process = $this->RegisterModel->active_member($activation_code, 1);
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		if ($process['status'] == 1) {
			$data['title'] = language('activation_code_capcha_email');
			$data['message'] = language('activation_code_capcha_message_email');
			$data['google_captcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
			$data['activation_code'] = $activation_code;
			render('layout/ddi/member/activation_member_email', $data);
		} else {
			$data['title'] = language('activation_failed_new');
			$data['message'] = $process['message'];
			render('layout/ddi/member/page', $data);
		}
	}
	function process_active_email_member()
	{
		$this->load->library('curl');
		$post = purify($this->input->post());
		if ($post) {
			$userIp = $this->input->ip_address();
			$data_captcha = array();
			$secret = GOOGLE_CAPTCHA_SECRET_KEY;
			$responsecaptcha = trim($post['g-recaptcha-response']);
			$url = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $responsecaptcha . "&remoteip=" . $userIp;

			$ch = curl_init();
			$user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			$data_captcha = curl_exec($ch);
			curl_close($ch);

			$status = json_decode($data_captcha, true);

			if (!$status['success']) {
				$status_data = language('not_check_captcha');
				$message = language('not_check_captcha_message');
			} else if ($status['success']) {
				$process_active = $this->RegisterModel->process_activation($post['activation_code'], 1);
				if ($process_active['status'] == 1) {
					$this->session->set_flashdata('email_activation_member_success', 'true');
					$status_data = 'success';
					$message = language('activation_success_new');
				}
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status_data;
			echo json_encode($data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function active_email_member_success()
	{
		$user_sess_data = $this->session->flashdata('email_activation_member_success');
		if ($user_sess_data) {
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article'] = popular_article(0);
			$data['new_article']    = new_article(1, 2);
			$data['new_expert']    = new_expert(1, 1);
			$data['title'] = language('activation_success_new');
			$data['message'] = language('activation_success_message_new');
			render('layout/ddi/member/page', $data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function resend_email_activation()
	{
		$post = purify($this->input->post());
		if ($post) {
			$this->form_validation->set_rules('email', '"Email"', 'required|valid_email');
			if ($this->form_validation->run() == FALSE) {
				$message = validation_errors();
				$status = 'error';
			} else {
				$email = $post['email'];
				$proses = $this->LoginModel->resend_email_activation($post);
				if ($proses['status'] == 1) {
					$status = 'success';
					$message = $proses['message'];
					$this->session->set_flashdata('resend_email_activation_success', 'true');
				} else {
					$status = language('something_error');
					$message = $proses['message'];
				}
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		} else {
			$data['fb_like_widget'] = fb_like_widget();
			$data['ads_widget']     = ads_widget();
			render('layout/ddi/member/resend_email_activation', $data);
		}
	}
	function forgot_password()
	{
		$post = purify($this->input->post());
		if ($post) {
			$this->form_validation->set_rules('email', '"Email"', 'required|valid_email');
			if ($this->form_validation->run() == FALSE) {
				$message = validation_errors();
				$status = 'error';
			} else {
				$email = $post['email'];
				$proses = $this->LoginModel->reset_password($post);
				if ($proses['status'] == 1) {
					$this->session->set_flashdata('session_reset_password_send_email_success', 'true');
					$status = 'success';
					$message = $proses['message'];
				} else {
					$status = language('something_error');
					$message = $proses['message'];
				}
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		} else {
			$data['fb_like_widget'] = fb_like_widget();
			$data['ads_widget']     = ads_widget();
			render('layout/ddi/member/reset_password', $data);
		}
	}
	function reset_password_send_email_success()
	{
		$user_sess_data = $this->session->flashdata('session_reset_password_send_email_success');
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		if ($user_sess_data) {
			$data['title'] = language('reset_password_success');
			$data['message'] = language('reset_password_success_message');
			render('layout/ddi/member/page', $data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function resend_email_activation_success()
	{
		$user_sess_data = $this->session->flashdata('resend_email_activation_success');
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		if ($user_sess_data) {
			$data['title'] = language('send_activation_code_success');
			$data['message'] = language('send_activation_code_success_message');
			render('layout/ddi/member/page', $data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function reset_password_success()
	{
		$user_sess_data = $this->session->flashdata('session_reset_password_success');
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		if ($user_sess_data) {
			$data['title'] = language('reset_password_success_code');
			$data['message'] = language('reset_password_success_message_code');
			header("refresh:10; url=" . base_url() . "member/login");
			render('layout/ddi/member/page', $data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function reset_password_code($activation_code)
	{
		$post = purify($this->input->post());
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		$process = $this->LoginModel->check_reset_due($activation_code);
		if ($process['status'] == 1) {
			if ($post) {
				$this->form_validation->set_rules('pwd', '"Password"', 'required');
				if ($this->form_validation->run() == FALSE) {
					$message = validation_errors();
					$status = 'error';
				} else {
					$reset_process = $this->LoginModel->reset_password_code($post);
					if ($reset_process['status'] == 1) {
						$this->session->set_flashdata('session_reset_password_success', 'true');
						$status = 'success';
						$message = $reset_process['message'];
					} else {
						$status = language('something_error');
						$message = $reset_process['message'];
					}
				}
				$data['message'] 	=  "<div class='$status-label'> $message</div>";
				$data['status'] 	=  $status;
				echo json_encode($data);
			} else {
				$data['reset_code'] = $activation_code;
				$data['ads_widget']     = ads_widget();
				render('layout/ddi/member/reset_password_code', $data);
			}
		} else {
			$data['title'] = language('reset_password_code_not_valid');
			$data['message'] = language('reset_password_code_not_valid_message');
			render('layout/ddi/member/page', $data);
		}
	}
	function login_process()
	{
		$post = purify($this->input->post());
		$data['message_data'] = 0;
		if ($post) {
			$url_redirect = $post['url_redirect'];
			unset($post['url_redirect']);
			$this->form_validation->set_rules('modal_login_form_username', '"Username"', 'required');
			$this->form_validation->set_rules('modal_login_form_password', '"Password"', 'required');
			if ($this->form_validation->run() == FALSE) {
				$message = validation_errors();
				$status = 'error';
			} else {
				$proses = $this->LoginModel->login($post);
				if ($proses['status'] == 1) {
					$status = 'success';
					if ($url_redirect == '') {
						$data['redicret'] = base_url() . 'member/';
					} else {
						$data['redicret'] = $url_redirect;
					}
				} else {
					if ($proses['status'] == 2) {
						$data['message_data'] = 1;
					}
					$status = language('something_error');
					$message = $proses['message'];
					$data['redicret'] = '';
				}
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
	function deactivate_account()
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if ($user_sess_data) {
			$proses = $this->RegisterModel->deactivate_account($user_sess_data['id']);
			if ($proses['status'] == 1) {

				$status = 'success';
				$message = $proses['message'];
				$this->session->set_flashdata('session_deactive_success', 'true');
			} else {
				$status = language('something_error');
				$message = $proses['message'];
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
	function reactivate_account($idrenderpage)
	{
		if ($idrenderpage) {
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article'] = popular_article(0);
			$data['new_article']    = new_article(1, 2);
			$data['new_expert']    = new_expert(1, 1);
			$proses = $this->RegisterModel->reactivate_account_check($idrenderpage);
			if ($proses['status'] == 1) {
				$this->session->set_flashdata('success_login', $proses['message']);

				$status = language('something_error');
				$message = $proses['message'];
				$data['title'] = $status;
				$data['idrenderpage'] = $idrenderpage;
				$data['message'] = $proses['message'];
				render('layout/ddi/member/reactive_member', $data);
			} else {
				$status = language('something_error');
				$message = $proses['message'];
				$data['title'] = $status;
				$data['message'] = $proses['message'];
				render('layout/ddi/member/page', $data);
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			//echo json_encode($data);
		} else {
			redirect('/');
		}
	}
	function reactivate_account_process($idrenderpage)
	{
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article'] = popular_article(0);
		$data['new_article']    = new_article(1, 2);
		$data['new_expert']    = new_expert(1, 1);
		if ($idrenderpage) {
			$proses = $this->RegisterModel->reactivate_account($idrenderpage);
			if ($proses['status'] == 1) {
				$this->session->set_flashdata('success_login', $proses['message']);
				redirect('/member/');

				$status = 'success';
				$message = $proses['message'];
			} else {
				$status = language('something_error');
				$message = $proses['message'];
				$data['title'] = $status;
				$data['message'] = $proses['message'];
				render('layout/ddi/member/page', $data);
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			//echo json_encode($data);
		} else {
			redirect('/');
		}
	}
	function edit_data_child()
	{
		$post = purify($this->input->post());
		$data = '';
		if ($post) {
			$this->RegisterModel->delete_all_child();
			foreach ($post['nama'] as $idx => $val) {
				if ($post['nama'][$idx]) {
					$this->RegisterModel->insert_child($post['nama'][$idx], $post['dob_child'][$idx], $post['jeniskelamin'][$idx], $post['umur_anak'][$idx]);
				}
			}
		}
		$data['status'] 	=  'success';
		$data['message'] 	=  language('success_change_profile');
		$this->session->set_flashdata('success_login', language('success_change_profile'));
		echo json_encode($data);
	}
	function baru()
	{ /*buat mas manto */
		render('layout/ddi/member/baru', []);
	}

	function edit_profile_member($full_name, $id)
	{
		$post = purify($this->input->post());

		$this->db->select("b.*");
		$this->db->join('auth_member b', "b.id = a.member_id", 'left');
		if ($id) {
			$data_member = $this->member_model->findViewBy(array('member_id' => $id), 1);
		} else {
			$data_member = $this->member_model->findViewBy(array('uri_path' => $full_name), 1);
		}


		// debugvar($data_member);
		// exit();
		//member
		$data['firstname']   = $data_member['firstname'];
		$data['lastname']    = $data_member['lastname'];
		$data['prefix_name'] = $data_member['prefix_name'];

		$data['job']         = $data_member['job'];
		// $data['linkedin_id'] = $data_member['linkedin_id'];
		$data['email']       = $data_member['email'];
		$data['img']         = $data_member['img'];
		if ($data_member['member_category_id'] == 3 || $data_member['member_category_id'] == 1) {
			$img_member          = ($data_member['img'] != '') ? imageProfile($data_member['img'], 'representative') : '';
		} else {
			$img_member          = ($data_member['img'] != '') ? imageProfile($data_member['img'], 'individu') : '';
		}
		$data['img_src']  = $img_member;
		$data['is_invis'] = $data_member['is_invis'];

		//company
		$data_company = $this->company_model->findById($data_member['company_id']);
		$data['m_t_number'] = $data_member['m_t_number'];
		// $data['m_m_number'] = $data_member['m_m_number'];
		$data['website']    = $data_company['website'];
		$data['address']    = $data_company['address'];



		$data['member_id']  = $data_member['id'];
		$data['company_id'] = $data_member['company_id'];
		// print_r($data);exit();

		echo json_encode($data);
	}

	function update_profile_member($full_name)
	{
		$post = purify($this->input->post());
		// print_r($post);
		// print_r($_FILES['img']['name']);

		// exit();
		if (!$post['img']) {
			unset($post['img']);
		}
		if ($_FILES['img']['name']) {
			$ext                   = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
			$fileRename            = 'individu' . preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['img'], 0, 50)))) . "-" . date("dMYHis") . "." . $ext;;
			fileToProfileImage($_FILES['img'], 0, $fileRename, 'individu');
			$post['img'] 			= $fileRename;
		}

		$idedit                       = $post['id'];
		$update_member                = $post;
		unset($update_member['id']);
		unset($update_member['id_com']);

		// $update_member['linkedin_id'] = $post['linkedin_id'];
		// $update_member['job']         = $post['job'];
		// $update_member['m_t_number'] 	= $post['t_number'];
		// $update_member['m_m_number'] 	= $post['m_number'];
		// $update_member['email'] 		= $post['email'];
		$this->member_model->update($update_member, $idedit);
		detail_log();
		$user_sess_data = $this->session->userdata('MEM_SESS');
		$information_member = member_category_id($user_sess_data['member_category_id']);
		insert_frontend_log('Update Profile Member ' . $information_member);


		// $idedit = $post['id_com'];
		// $update_company['m_t_number'] 	= $post['t_number'];
		// $update_company['m_m_number'] 	= $post['m_number'];
		// $update_company['website'] 		= $post['website'];
		// $update_company['address'] 		= $post['address'];
		// $this->company_model->update($update_company,$idedit);


		redirect('member/profile', 'refresh');
	}

	function edit_profile_member_company($full_name)
	{
		$post = purify($this->input->post());
		$data_company = $this->company_model->findBy(array('id' => $full_name), 1);
		$data['name_in']          = $data_company['name_in'];
		// $data['name_out']         = $data_company['name_out'];
		$data['city']             = $data_company['city'];
		$data['postal_code']      = $data_company['postal_code'];
		$data['is_invis_company'] = $data_company['is_invis_company'];
		$data['website']          = $data_company['website'];
		$data['t_number']         = $data_company['t_number'];
		// $data['m_number']         = $data_company['m_number'];
		$data['email']            = $data_company['email'];
		$data['headquarters']     = $data_company['headquarters'];
		$data['address']          = $data_company['address'];
		$data['description']      = $data_company['description'];
		$data['img']              = $data_company['img'];
		$img_member               = imageProfile($data_company['img'], 'company');
		// ($data_company['img'] != '') ? base_url().'images/member/company/'.$data_company['img'] : '';
		$data['img_src']          = $img_member;
		$data['id_company']       = $data_company['id'];

		echo json_encode($data);
	}

	function update_profile_member_company($full_name)
	{
		$post = purify($this->input->post());
		if (!$post['img']) {
			unset($post['img']);
		}
		if ($_FILES['img']['name']) {
			$ext                   = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
			$fileRename            = 'company' . preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['img'], 0, 50)))) . "-" . date("dMYHis") . "." . $ext;;
			fileToProfileImage($_FILES['img'], 0, $fileRename, 'company');
			$update_company['img'] 			= $fileRename;
		}
		$idedit                             = $post['id_com'];
		$update_company['website']          = $post['website'];
		$update_company['address']          = $post['address'];
		$update_company['description']      = $post['description'];
		$update_company['is_invis_company'] = ($post['is_invis_company']) ? 1 : 0;

		$update_company['name_in']          = $post['name_in'];
		$update_company['city']             = $post['city'];
		$update_company['postal_code']      = $post['postal_code'];
		$update_company['t_number']         = $post['t_number'];
		$update_company['email']            = $post['email'];
		$update_company['headquarters']     = $post['headquarters'];
		// $update_company['name_out']         = $post['name_out'];
		// $update_company['m_number']         = $post['m_number'];

		$this->model_log_updated->before_insert_log_company($idedit);

		$this->company_model->update($update_company, $idedit);

		detail_log();

		insert_frontend_log('Update Profile Member ' . $post['name_in']);

		// copy jadi log dan kirim email
		$this->model_log_updated->log_company($idedit);

		$user_sess_data = $this->session->userdata('MEM_SESS');
		$information_member = member_category_id($user_sess_data['member_category_id']);
		// print_r($this->db->last_query()	);exit;
		redirect('member/profile', 'refresh');
	}

	function save_member_sector_committee()
	{

		$user_sess_data = $this->session->userdata('MEM_SESS');

		$post = $this->input->post();
		// print_r($post);exit;

		$committee 		= $post['id_committee'];

		if ($post['id_committee']) {

			foreach ($committee as $key => $value) {
				if ($value) {
					$cek = $this->committee_model->fetchRow(array('id' => $value)); //liat tags name di tabel ref
					if (!$cek) { //kalo belom ada
					} else {
						$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
					}
					$cekTagsNews = $this->auth_member_committee_model->fetchRow(array('committee_id' => $id_tags, 'member_id' => $user_sess_data['id'])); //liat di tabel news tags, (utk edit)


					if (!$cekTagsNews) { //kalo blm ada ya di insert
						$tag['committee_id'] = $id_tags;
						$tag['member_id'] = $user_sess_data['id'];
						$id_news_tags = $this->auth_member_committee_model->insert($tag);

						detail_log();
						$user_sess_data = $this->session->userdata('MEM_SESS');
						$information_member = member_category_id($user_sess_data['member_category_id']);
						insert_frontend_log('Insert Committee ' . $information_member);
					} else { //kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
						$id_news_tags = $cekTagsNews['id'];
					}
					$del_tags_news[] = $id_news_tags;
				}
			}

			$this->db->where_not_in('a.id', $del_tags_news);
			$delete = $this->auth_member_committee_model->findBy(array('a.member_id' => $user_sess_data['id'])); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)

			foreach ($delete as $key => $value) {
				$this->auth_member_committee_model->delete($value['id']);

				detail_log();
				$user_sess_data = $this->session->userdata('MEM_SESS');
				$information_member = member_category_id($user_sess_data['member_category_id']);
				insert_frontend_log('Update Committee ' . $information_member);
			}
			#end_committee
		}

		$sector 		= $post['id_sector'];
		#sector
		if ($post['id_sector']) {

			$id_parent_sector = $this->sector_model->get_idotherparent();
			if (in_array($id_parent_sector, $post['id_sector'])) {
				if ($post['other-sector-name']) { // name other sector
					$insert_sector['name']              = $post['other-sector-name'];
					$insert_sector['is_other']          = 1;
					$insert_sector['uri_path']          = generate_url($post['other-sector-name']);
					$insert_sector['id_status_publish'] = 2;
					$checksector                        = $this->sector_model->findBy(array('uri_path' => generate_url($post['other-sector-name'])), 1);

					if ($checksector) {
						$insert_sector_id          = $this->sector_model->update_frontend($insert_sector, $checksector['id']);

						detail_log();
						$user_sess_data = $this->session->userdata('MEM_SESS');
						$information_member = member_category_id($user_sess_data['member_category_id']);
						insert_frontend_log('Update Sector ' . $information_member);
					} else {
						$insert_sector_id          = $this->sector_model->insert_frontend($insert_sector);
						detail_log();

						$user_sess_data = $this->session->userdata('MEM_SESS');
						$information_member = member_category_id($user_sess_data['member_category_id']);
						insert_frontend_log('Insert Sector ' . $information_member);
					}

					$key_update                = array_search($id_parent_sector, $post['id_sector']);

					$sector[$key_update] = $insert_sector_id; // set sector id with new sector
				}
			}
			// print_r($sector);exit;

			foreach ($sector as $key => $value) {
				if ($value) {
					// $cek = $this->sector_model->fetchRow(array('id'=>$value));//liat tags name di tabel ref
					// if(!$cek){//kalo belom ada
					// }
					// else{
					$id_tags = $value; //kalo udah ada, tinggal ambil idnya
					// $id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
					// }
					$cekTagsNews = $this->auth_member_sector_model->findByAll(array('sector_id' => $id_tags, 'company_id' => $user_sess_data['company_id']), 1); //liat di tabel news tags, (utk edit)


					if (!$cekTagsNews) { //kalo blm ada ya di insert
						$tags['sector_id']  = $id_tags;
						$tags['company_id'] = $user_sess_data['company_id'];
						$id_news_tags = $this->auth_member_sector_model->insert($tags);

						detail_log();
						$user_sess_data = $this->session->userdata('MEM_SESS');
						$information_member = member_category_id($user_sess_data['member_category_id']);
						insert_frontend_log('Insert Sector ' . $information_member);
					} else { //kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
						$id_news_tags = $cekTagsNews['id'];
					}
					$del_tags_news[] = $id_news_tags;
				}
			}
			// $this->db->where_in('a.id',$del_tags_news); s
			// $update = $this->auth_member_sector_model->findBy(array('a.member_id'=>$user_sess_data['id'])); //dapetin id news tags yg diapus  (
			foreach ($del_tags_news as $key => $value) {
				$up['is_delete'] = 0;
				$this->auth_member_sector_model->update_frontend($up, $value);

				detail_log();
				$user_sess_data = $this->session->userdata('MEM_SESS');
				$information_member = member_category_id($user_sess_data['member_category_id']);
				insert_frontend_log('Update Sector ' . $information_member);
				// print_r(	$this->db->last_query());
			}

			// print_r($del_tags_news);exit;
			$this->db->where_not_in('a.id', $del_tags_news);
			$delete = $this->auth_member_sector_model->findBy(array('a.company_id' => $user_sess_data['company_id'])); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)

			foreach ($delete as $key => $value) {
				$this->auth_member_sector_model->delete($value['id']);
			}
			#end_sector
		}
		/*else{
			$delete = $this->auth_member_sector_model->findBy(array('a.company_id'=>$user_sess_data['company_id'])); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)

			foreach ($delete as $key => $value) {
				$this->auth_member_sector_model->delete($value['id']);
			}
		}*/
		$ret['error'] = 1;
		$ret['msg'] = "Success";
		echo json_encode($ret);
	}

	function add_member_representative($id_member)
	{
		$user_sess_data = $this->session->userdata('MEM_SESS');

		$post = $this->input->post();

		$data['company_id']                = $user_sess_data['company_id'];
		$data['firstname']                 = $post['firstname'];
		$data['lastname']                  = $post['lastname'];
		$data['prefix_name']               = $post['prefix_name'];
		$data['job']                       = $post['job'];
		$data['email']                     = $post['email'];
		$data['membership_information_id'] = $user_sess_data['company_id'];

		$data['m_t_number'] 				= $post['t_number'];

		$data['is_invis']   				= ($post['is_invis']) ? 1 : 0;

		if (!$post['img']) {
			unset($post['img']);
		}
		if ($_FILES['img']['name']) {
			$ext                   = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
			$fileRename            = 'representative' . preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['img'], 0, 50)))) . "-" . date("dMYHis") . "." . $ext;;
			fileToProfileImage($_FILES['img'], 0, $fileRename, 'representative');
			$data['img'] 			= $fileRename;
		}

		unset($post['id_member']);

		$datas                     = $this->member_model->findById($id_member);
		$data['status_payment_id'] = $datas['status_payment_id'];
		// cek apa pernah edit sebelum nya
		$this->model_log_updated->before_insert_log_profile($id_member);

		$this->member_model->update($data, $id_member);

		insert_frontend_log('Update Profile Member Representative');
		// copy jadi log dan kirim email
		$this->model_log_updated->log_profile($id_member);

		redirect('member/profile', 'refresh');
	}


	// function representative($uri_path){
	// 	$user_sess_data = $this->session->userdata('MEM_SESS');
	// 	if($user_sess_data){
	// 		$data_view = $this->member_model->findViewById($user_sess_data['id']);
	// 		//member info 
	// 		$data_member = $this->member_model->findById($data_view['member_id']);

	// 		$temp['member_name'] 		= full_name($data_member);
	// 		$temp['member_job']         = $data_member['job'];
	// 		$temp['member_email']       = $data_member['email'];
	// 		$temp['member_linkedin_id'] = $data_member['linkedin_id'];
	// 		$temp['member_uri_path'] 	= $data_member['uri_path'];
	// 		$img_member = ($data_view['member_img'] != '') ? base_url().'images/member/individu/'.$data_view['member_img'] : '';
	// 		$temp['member_img'] 		= $img_member;

	// 			//company
	// 		$data_company = $this->company_model->findById($data_view['company_id']);
	// 		$temp['member_t_number']    = $data_company['t_number'];
	// 		$temp['member_m_number']    = $data_company['m_number'];
	// 		$temp['member_address']     = $data_company['address'];
	// 		$temp['member_website']     = $data_company['website'];

	// 		$data['data_member'][] = $temp;
	// 		// end member info
	// 		unset($temp);


	// 		//company
	// 		$temp['company_name']        = $data_company['name_out'] ;
	// 		$temp['company_name_out']    = $data_company['uri_path_name_out'] ;
	// 		$temp['company_address']     = $data_company['address'] ;
	// 		$temp['company_website']     = $data_company['website'] ;
	// 		$img_company = ($data_view['company_img'] != '') ? base_url().'images/member/company/'.$data_view['company_img'] : '';
	// 		$temp['company_img']         = $img_company;
	// 		$temp['company_description'] = $data_company['description'] ;

	// 		$data['data_company'][] = $temp;
	// 		unset($temp);

	// 		//membership
	// 		$data_membership = $this->membership_model->findById($data_view['membership_id']);

	// 		$temp['membership_code']       = $data_membership['membership_code'];
	// 		$temp['membership_registered'] = iso_date_custom_format($data_membership['registered_date'],'Y-m-d');
	// 		$temp['membership_expired']    = iso_date_custom_format($data_membership['expired_date'],'Y-m-d');
	// 		$temp['membership_lastvisit']  = iso_date_custom_format($data_membership['last_visited_date'],'Y-m-d');
	// 		$data['data_membership'][]  = $temp;
	// 		unset($temp);
	// 		if ($data_membership['registered_date']) {
	// 			$data['is_membership']  = '';
	// 			$data['not_membership'] = 'hide';
	// 		}else{
	// 			$data['is_membership']  = 'hide';
	// 			$data['not_membership'] = '';
	// 		}
	// 		// represen
	// 		$CI =& get_instance();
	// 		$data  = array_merge($data,$CI->data);
	// 		if (check_is_company($data_view['category_id'])) {//company
	// 			unset($data['data_member']);
	// 			$data_representative = $this->member_model->findBy(array('uri_path' => $uri_path,'member_category_id' => 3));

	// 			if (!empty($data_representative)) {

	// 				foreach ($data_representative as $key => $value) {
	// 					$data_member_r 			= $this->member_model->findById($value['id']);
	// 					$temp['re_id']          = $data_member_r['id'] ;
	// 					$temp['re_name']        = $data_member_r['firstname'] ? full_name($data_member_r) : '';
	// 					$temp['re_url']       	= $data_member_r['uri_path'];
	// 					$temp['re_job']         = $data_member_r['job'];
	// 					$temp['re_email']       = $data_member_r['email'];
	// 					$temp['re_linkedin_id'] = $data_member_r['linkedin_id'];
	// 					$img_member          = ($data_member['img'] != '') ? imageProfile($data_member['img'],'representative'):'';
	// 					$temp['re_img']    		= $img_member ;

	// 					$data_company_r			= $this->company_model->findById($value['company_id']);
	// 					$temp['re_t_number']    = $data_company_r['t_number'];
	// 					$temp['re_m_number']    = $data_company_r['m_number'];
	// 					$data['data_re'][]      = $temp;
	// 					unset($temp);
	// 				}
	// 			}else{
	// 				$data['no_reprepesentative'] = 'hide';
	// 				$data['data_re'][] = '';
	// 			}

	// 			$data['content_top'] = $this->parser->parse('member/company_profile.html',$data,1);
	// 			// $data['content_top'] = render('member/company_profile',$CI->data,'blank',1);
	// 		}else if ($data_view['category_id'] = 2) {//individu
	// 			$data['content_top'] = $this->parser->parse('member/individu_profile.html',$data,1);
	// 			// $data['content_top'] = render('member/individu_profile',$CI->data,'blank',1);
	// 		}else{//reprentative
	// 			$data['content_top'] = $this->parser->parse('member/representative_profile.html',$data,1);
	// 			// $data['content_top'] = render('member/representative_profile',$CI->data,'blank',1);
	// 		}
	// 		//upcoming
	// 		$today    = date('Y-m-d');
	// 		$where_up['a.end_date >=']  = $today;
	// 		$where_up['a.is_close ']    = 0;
	// 		$data_events_up          = $this->eventModel->findBy($where_up);

	// 		$id_cat_amcham_event = id_child_news(26,1);
	// 		    // print_r($data_events);exit;
	// 		foreach ($data_events_up as $key => $value) {
	// 		    unset($temp);
	// 			$temp['upcoming_cat']  = ($value['subcategory'] != '') ? $value['subcategory'].' event' : '';
	// 			$temp['upcoming_link'] = site_url('event/detail/'.$value['uri_path']);
	// 			$temp['upcoming_name'] = $value['name'];
	// 		    if ($uri_path == 'annual-golf-turnament') {
	// 				$temp['upcoming_date'] = event_date($value['publish_date'],'','','-',1);
	// 		    }else{
	// 				$temp['upcoming_date'] = event_date($value['start_date'],$value['start_time'],$value['end_time']);
	// 		    }
	// 			$temp['upcoming_address'] = $value['location_name'];
	// 			$temp['upcoming_teaser']  = character_limiter($value['teaser'],140,'...');
	// 			$temp['upcoming_color']   = in_array($value['id_event_category'],$id_cat_amcham_event)?'widget-blue-left':' widget-red-left';
	// 			$temp['upcoming_img']     = getImg($value['img'],'small');
	// 		    $data['upcoming_event'][] = $temp;
	// 		    // $temp['']
	// 		}
	// 		$data['dsp_event_up'] = !$data_events_up ? 'hide' : '' ;

	// 		//pass
	// 		$where_past['a.end_date <'] = $today;
	// 		$where_past['a.is_close_1'] = '1';
	// 		$data_events_past           = $this->eventModel->findBy($where_past);

	// 		$id_cat_amcham_eventpast = id_child_news(26,1);
	// 		foreach ($data_events_past as $key => $value) {
	// 		    unset($temp);
	// 			$temp['past_cat']  = ($value['subcategory'] != '') ? $value['subcategory'].' event' : '';
	// 			$temp['past_link'] = site_url('event/detail/'.$value['uri_path']);
	// 			$temp['past_name'] = $value['name'];
	// 		    if ($uri_path == 'annual-golf-turnament') {
	// 				$temp['past_date'] = event_date($value['publish_date'],'','','-',1);
	// 		    }else{
	// 				$temp['past_date'] = event_date($value['start_date'],$value['start_time'],$value['end_time']);
	// 		    }
	// 			$temp['past_address'] = $value['location_name'];
	// 			$temp['past_teaser']  = character_limiter($value['teaser'],140,'...');
	// 			$temp['past_color']   = in_array($value['id_event_category'],$id_cat_amcham_eventpast)?'widget-blue-left':' widget-red-left';
	// 			$temp['past_img']     = getImg($value['img'],'small');
	// 			//past_material
	// 			$this->load->model('eventFilesModel');
	// 			$materials = $this->eventFilesModel->listFiles(array('id_event' => $value['id']));
	// 			foreach ($materials as $key => $value) {
	// 				$materialsfile['material_file'] = $value['filename'];
	// 				$materialsfile['mat_idx']       = md5plus($value['idFile']);
	// 				$temp['past_material'][]        = $materialsfile;
	// 			}
	// 				$temp['past_dsp_material']      = empty($materials) ? 'hidden' : ''; 
	// 				// print_r($temp['past_dsp_material']);
	// 				// exit;


	// 		    $data['past_event'][] = $temp;


	// 		    // $temp['']
	// 		}

	// 		$data['is_paid']        = ($data_view['status_id'] == 1)? '':'hide';
	// 		$data['is_not_paid']    = ($data_view['status_id'] == 1)? 'hide':'';
	// 		$data['is_company']     = (check_is_company($data_view['category_id']))? '':'hide';
	// 		$data['is_not_company'] = (check_is_company($data_view['category_id']))? 'hide':'';

	// 		$data['page_heading']    = 'Membership';
	// 		$data['banner_top']      = banner_top(); // pake banner top
	// 		$data['widget_sidebar']  = widget_sidebar(); //pake sidebar
	// 		$data['seo_title']       = "AMCHAM INDONESIA";
	// 		$data['hide_breadcrumb'] = 'hide';

	// 		$data['hidden_list']        = ($data_view['status_id'] == 1)? '':'hidden';

	// 		$this->db->order_by('a.name','asc');

	// 		$datas = $this->committee_model->findBy();


	// 		$cekTagsNews = $this->auth_member_committee_model->findBy(array('member_id'=>$user_sess_data['id']));
	// 		foreach ($cekTagsNews as $key => $value) {
	// 			$a[] .= $value['committee_id'];
	// 		}

	// 		foreach ($datas as $key => $value) {
	// 			if (in_array($value['id'], $a, true)) {
	//    				$checked = 'checked';
	// 			} else {
	// 				$checked = '';
	// 			}
	// 			$ret_committee .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
	// 	           <label  class="checkbox-committee"><input type="checkbox" '.$checked.' value="'.$value['id'].'" id="'.$value['id'].'" class="committee" name="id_committee[]" ><span><i class="glyphicon glyphicon-ok"></i></span>'.$value['name'].$data['is_paid'].'</label>
	// 	        </div>';
	// 		}
	// 		$data['list_committee'] .= $ret_committee;



	// 		$this->db->order_by('a.name','asc');
	// 		$datas = $this->sector_model->findBy();

	// 		$count_datas	 = count($datas); 

	// 		$cekSector = $this->auth_member_sector_model->findBy(array('member_id'=>$user_sess_data['id']));


	// 		foreach ($cekSector as $key => $value) {
	// 			$b[] .= $value['sector_id'];
	// 		}

	// 		foreach ($datas as $key => $value) {

	// 			if (in_array($value['id'], $b, true)) {
	//    				$checked = 'checked';
	// 			} else {
	// 				$checked = '';
	// 			}

	// 			$ret_sector .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
	// 	          <label class="checkbox-sector"><input type="checkbox" '.$checked.' value="'.$value['id'].'" id="'.$value['id'].'" class="sector" name="id_sector[]" ><span><i class="glyphicon glyphicon-ok"></i></span>'.$value['name'].'</label>
	// 	        </div>';
	// 		}
	// 		$data['list_sector'] 	.= $ret_sector;


	// 		render("member/profile",$data);			
	// 	}else{
	// 		redirect('member','refresh');
	// 	}

	// }

	function getPastEvent()
	{
		$post = purify($this->input->post(NULL, TRUE));

		// if ($post['year'] != 0 && $post['month'] != 0 ){
		// 	$where_past['a.start_date like '] = '"%'.$post['year'].'-'.$post['month'].'"%';
		// }else if ($post['year'] != 0 ){
		//     		$where_past['YEAR(a.start_date)'] = $post['year'];
		//     	}else if ($post['month'] != 0 ){
		//     		$where_past['MONTH(a.start_date)'] = $post['month'];
		//     	}
		$y = $post['year'];
		$m = $post['month'];
		// print_r(base_url_lang().'/event/more/past-events/0/0/'.$y.'/'.$m);exit;
		$ret = get_web_page(base_url_lang() . '/event/more/past-events/0/0/' . $y . '/' . $m);
		echo json_encode($ret);

		// $today    = date('Y-m-d');
		// $where_past['a.end_date <'] = $today;
		// $where_past['a.is_close_1'] = '1';
		// $this->db->join('event_participant d',"d.event_id = x.id",'left');
		// $data_events_past           = $this->eventModel->findBy($where_past);
		// $count_data_event_past 		= count($data_events_past);

		// if ($count_data_event_past == 0){
		// 	 $data['past_event'] = array();
		// }

		// foreach ($data_events_past as $key => $value) {
		//     unset($temp);
		// 	$temp['past_cat']  = ($value['subcategory'] != '') ? $value['subcategory'].' event' : '';
		// 	$temp['past_link'] = site_url('event/detail/'.$value['uri_path']);
		// 	$temp['past_name'] = $value['name'];
		//     if ($uri_path == 'annual-golf-turnament') {
		// 		$temp['past_date'] = event_date($value['publish_date'],'','','-',1);
		//     }else{
		// 		$temp['past_date'] = event_date($value['start_date'],$value['start_time'],$value['end_time']);
		//     }
		// 	$temp['past_address'] = $value['location_name'];
		// 	$temp['past_teaser']  = character_limiter($value['teaser'],140,'...');
		// 	$temp['past_color']   = in_array($value['id_event_category'],$id_cat_amcham_eventpast)?'widget-blue-left':' widget-red-left';
		// 	$temp['past_img']     = getImg($value['img'],'small');
		// 	//past_material
		// 	$this->load->model('eventFilesModel');
		// 	$materials = $this->eventFilesModel->listFiles(array('id_event' => $value['id']));
		// 	foreach ($materials as $key => $value) {
		// 		$materialsfile['material_file'] = $value['filename'];
		// 		$materialsfile['mat_idx']       = md5plus($value['idFile']);
		// 		$temp['past_material'][]        = $materialsfile;
		// 	}
		// 		$temp['past_dsp_material']      = empty($materials) ? 'hidden' : ''; 
		//     $data['past_event'][] = $temp;
		// }
		// render('member/search_past_event',$data,'blank');
	}

	function renew_membership()
	{
		$user_sess_data                 = $this->session->userdata('MEM_SESS');

		$post                           = purify($this->input->post(NULL, TRUE));

		$cek                            = $this->member_model->cekStatusKirim($post['id'], $this->today);

		$data_member                    = $this->member_model->findViewById($user_sess_data['id']);

		/*update set colomn is_self_renew untuk prefix dia ingin renew membership*/
		$update_membership['is_renew'] = 1;
		$this->membership_model->update($update_membership, $data_member['membership_id']);

		detail_log();
		$user_sess_data = $this->session->userdata('MEM_SESS');
		$information_member = member_category_id($user_sess_data['member_category_id']);
		insert_frontend_log('Renew Membership ' . $information_member);

		//send email to admin 
		$get_member                     = $this->member_model->findBy(array('id' => $data_member['member_id']), 1);
		$get_company                    = $this->company_model->findBy(array('id' => $data_member['company_id']), 1);
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
		$email_admin['link']            = base_url() . 'apps';

		if (!$cek) {
			// sent_email_by_category(10, $email_admin, $data_member['member_email']);
			$insert['sent']                      = date('Y-m-d');
			$insert['membership_information_id'] = $data_member['member_id'];
			$insert['is_manual']                 = 0;
			$this->member_model->simpanDataLaporanTerkirim($insert);
			$ret['error'] = 0;
			$ret['modalname'] = "myModalRenewMembership";

			detail_log();
			$user_sess_data = $this->session->userdata('MEM_SESS');
			$information_member = member_category_id($user_sess_data['member_category_id']);
			insert_frontend_log('Sent Email Renew Membership ' . $information_member);
		} else {
			$ret['error'] = 1;
			$ret['msg'] = "Sorry, User Expired Reminder has been sent";
		}
		echo json_encode($ret);
	}
	function partners_click()
	{
		$post           = $this->input->post();
		$where['id']    = $post['idcom'];

		$hits           = $this->db->get_where('about_partners', $where)->row_array()['hits'];

		$update['hits'] = ++$hits;
		$where['id']    = $post['idcom'];
		$this->db->update('about_partners', $update, $where);
	}
}
