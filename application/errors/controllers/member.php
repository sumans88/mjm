<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Member extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('dashboardmodel');
		$this->load->model('RegisterModel');
		$this->load->model('LoginModel');
	
		//please remove when you want to use member (module)
		if(DEVELOPMENT_MEMBER){
			redirect('/');
		}
	}
	function index(){
	    $user_sess_data = $this->session->userdata('MEM_SESS');
	    if($user_sess_data){
		    if($user_sess_data['remember_me']=1){
			    $this->load->model('loginModel');
			    $this->loginModel->remember_me_login();
		    }
		    $data = $this->dashboardmodel->fetchRow(array('id'=>$user_sess_data['id']));
			//if($data['is_complete_data']==1 and $data['is_active']==1){
		    if($data['is_active']==1){
			    if($this->session->flashdata('success_login')){
				$data['show_info'] = '';
			    } else {
				$data['show_info'] = 'hide';
			    }
			    $data['male_check']='';
			    $data['female_check']='';
			    if($data['jeniskelamin']=='male'){
				    $data['male_check'] = 'checked';
				    $jenis_kelamin_user = language('male');
			    } else if($data['jeniskelamin']=='female') {
				    $data['female_check'] = 'checked';
				    $jenis_kelamin_user = language('female');
			    }
			    $select = language('select');
			    $data['jeniskelamin'] =  $jenis_kelamin_user;
			    $data['marketing'] = ($data['marketing']) ? '1' : '0';
			    $data['newsletter'] = ($data['newsletter']) ? '1' : '0';
			    $data['tgllahir_data'] = iso_date_custom_format($data['tgllahir'], 'd/m/Y');
			    $data['tgllahir_pasangan'] = iso_date_custom_format($data['tgllahir_pasangan'], 'd/m/Y');
			    $data['process_time'] = iso_date_custom_format($data['process_time'], 'd/m/Y');
			    $data['top_content']     = top_content();
			    $data['tmptlahir_data'] = $this->db->select('ibukota')->get_where('t_master_tempat_lahir',"id_tmpt_lhr='$data[tmptlahir]'")->row()->ibukota;
			    $data['provinsi_data'] = $this->db->select('propinsi')->get_where('propinsi',"id='$data[provinsi]'")->row()->propinsi;
				$data['data_umur_pasangan'] = $this->db->select('keterangan')->get_where('t_master_spouse_age',"id_sa='$data[umur_pasangan]'")->row()->keterangan;
				$data['is_married_have_child_show'] = 'hide';
				$data['is_married_show'] = 'hide';
				$data_anak_true = 'true';
				$data_anak_true_name = 'jumlah_anak';
				if($data['status_nikah']=='Q'){
					$data['is_married_have_child_show'] = '';
					$data['is_married_show'] = '';
					$data_anak_true = $data['jumlah_anak'];
					$data_anak_true_name = 'jumlah_anak';
				}
				if ($data['status_nikah']=='S'){
					$data['is_married_show'] = '';
				}
				if($data['pekerjaan']=='G'){
					$data['pekerjaan_data'] = $data['pekerjaan_lain'];
				} else {
					$data['pekerjaan_data'] = $this->db->select('keterangan')->get_where('t_master_pekerjaan',"id_pekerjaan='$data[pekerjaan]'")->row()->keterangan;
				}			    $data['pekerjaan_lain_data'] = $this->db->select('keterangan')->get_where('t_master_pekerjaan',"id_pekerjaan='$data[pekerjaan_lain]'")->row()->keterangan;
			    $data['pendapatan_bulanan_data'] = $this->db->select('keterangan')->get_where('t_master_pendapatan_bulanan',"id_pb='$data[pendapatan_bulanan]'")->row()->keterangan;
			    $data['status_nikah_data'] = $this->db->select('keterangan')->get_where('t_master_status_nikah',"id_sn='$data[status_nikah]'")->row()->keterangan;
				$data['total_anak_data'] = $this->db->select('keterangan')->get_where('t_master_kid_total',"id_kt='$data[jumlah_anak]'")->row()->keterangan;
			    $data['pendidikan_data'] = $this->db->select('keterangan')->get_where('t_master_pendidikan',"id_pt='$data[pendidikan]'")->row()->keterangan;
			    $data['page_name']= language('about_me');
			    $data['message'] = $this->session->flashdata('success_login');
			    $data['list_provinsi'] 	= selectlist2(array('table'=>'propinsi','order'=>'propinsi','title'=>$select.' '.language('province'),'name'=>'propinsi','selected'=>$data['provinsi']));
			    $data['list_tmp_lahir'] 	= selectlist2(array('table'=>'t_master_tempat_lahir','order'=>'ibukota','title'=>$select.' '.language('birth_place'),'name'=>'ibukota','id'=>'id_tmpt_lhr','selected'=>$data['tmptlahir']));
			    $data['list_pekerjaan'] 	= selectlist2(array('table'=>'t_master_pekerjaan','order'=>'keterangan','title'=>$select.' '.language('occupation'),'name'=>'keterangan','id'=>'id_pekerjaan','selected'=>$data['pekerjaan']));
				$data['list_total_anak'] 	= selectlist2(array('table'=>'t_master_kid_total','order'=>'keterangan','title'=>$select.' '.language('total_kid'),'name'=>'keterangan','id'=>'id_kt','selected'=>$data['jumlah_anak']));
			    $data['list_pekerjaan_lain'] 	= selectlist2(array('table'=>'t_master_pekerjaan','order'=>'keterangan','title'=>$select.' '.language('occupation'),'name'=>'keterangan','id'=>'id_pekerjaan','selected'=>$data['pekerjaan_lain']));
			    $data['list_status_nikah'] 	= selectlist2(array('table'=>'t_master_status_nikah','order'=>'keterangan','title'=>$select.' '.language('marital_status'),'name'=>'keterangan','id'=>'id_sn','selected'=>$data['status_nikah']));       
			    $data['list_pendidikan'] 	= selectlist2(array('table'=>'t_master_pendidikan','order'=>'id_pt','title'=>$select.' '.language('education'),'name'=>'keterangan','id'=>'id_pt', 'where'=>'is_publish=1','selected'=>$data['pendidikan']));       
			    $data['list_pendapatan_bulanan'] 	= selectlist2(array('table'=>'t_master_pendapatan_bulanan','order'=>'id_pb','title'=>$select. ' Pendapatan Bulanan','name'=>'keterangan','id'=>'id_pb','selected'=>$data['pendapatan_bulanan']));       
			    $data['list_umur_anak'] 	= selectlist2(array('table'=>'t_master_kid_age','order'=>'id_ka','title'=>$select.' '.language('age'),'name'=>'keterangan','id'=>'id_ka', 'where'=>'is_publish=1'));       
			    $data['list_umur_pasangan'] 	= selectlist2(array('table'=>'t_master_spouse_age','order'=>'id_sa','title'=>$select.' '.language('age'),'name'=>'keterangan','id'=>'id_sa', 'where'=>'is_publish=1','selected'=>$data['umur_pasangan']));       
			    $data['newsletter_checked'] 	= ($data['newsletter'] == 1) ? 'checked':'';
			    $data['marketing_checked'] 	= ($data['marketing'] == 1) ? 'checked':'';
			    $this->load->model('LogActivityModel');
			    $data_child_member = $this->dashboardmodel->get_child($user_sess_data['id']);
				if($data['home_phone']){
					$data['home_phone_hidden'] = '';
				}else{
					$data['home_phone_hidden'] = 'hide';
				}
				$kecamatan_true = '';
				$kecamatan_true_name = 'kecamatan';
				if($data['kecamatan']){
					$kecamatan_true = $data['kecamatan'];
					$kecamatan_true_name = 'kecamatan';
				}
				$kodepos_true = '';
				$kodepos_true_name = 'kodepos';
				if($data['kodepos']){
					$kodepos_true = $data['kodepos'];
					$kodepos_true_name = 'kodepos';
				}
				$pendapatan_bulanan_true = '';
				$pendapatan_bulanan_true_name = 'pendapatan_bulanan';
				if($data['pendapatan_bulanan']){
					$pendapatan_bulanan_true = $data['pendapatan_bulanan'];
					$pendapatan_bulanan_true_name = 'pendapatan_bulanan';
				}
				
			   $check_data_exist = check_data_exist_counter(array("$data[tmptlahir]","$data[tgllahir]","$data[status_nikah]","$data[provinsi]",
									       "$data[pendidikan]","$data[pekerjaan]","$data[nohp]","$data[namadepan]","$data[namabelakang]",
									       "$data[kota]","$data[kelurahan]",$kecamatan_true,$kodepos_true,$pendapatan_bulanan_true,
									       "$data[jeniskelamin]","$data[alamat]",$data_anak_true),
									array("tmptlahir","tgllahir","status_nikah","provinsi",
									       "pendidikan","pekerjaan",
									       "nohp","namadepan","namabelakang",
									       "kota","kelurahan",$kecamatan_true_name,$kodepos_true_name,$pendapatan_bulanan_true_name,
									       "jeniskelamin","alamat",$data_anak_true_name)
									);
				$data		= array_merge($check_data_exist,$data);

				if($check_data_exist['total_count']>0){
				    $data['show_data_not_complete'] = '';
				    $data['total_data_now_complete'] = $check_data_exist['total_count'];
				}else{
				    $data['show_data_not_complete'] = 'hide';
				}
				$data['show_not_completed_editprofil'] = show_edit_data($data['show_data_not_complete'],$data['total_data_now_complete'],'#editprofil');
				$data['show_not_completed_editdataanak'] = show_edit_data($data['show_data_not_complete'],$data['total_data_now_complete'],'#spouse');
			    foreach($data_child_member as $n =>  $data_child){
				    ++$i;
				    $data_child_member[$n]['nomor'] 	= ++$nomor;
				    $data_child_member[$n]['dob_child'] 	= iso_date_custom_format($data_child['dob_child'], 'd/m/Y');
				    $data_child_member[$n]['jeniskelamin_child'] 	= $data_child['jeniskelamin'];
				    $data_child_member[$n]['umur_anak_data'] = $this->db->select('keterangan')->get_where('t_master_kid_age',"id_ka='$data_child[umur_anak]'")->row()->keterangan;
					if($data_child['jeniskelamin']=='male'){
						 $data_child_member[$n]['jeniskelamin_child'] = language('male');
					} else if($data['jeniskelamin']=='female') {
						 $data_child_member[$n]['jeniskelamin_child'] = language('female');
					}
			    }
			    if($i>0){
					$data['data_child_member_true'] = 'hide';
					$data['data_child_member_false'] = '';
					$data['data_anak_status'] = '';
			    }else{
					$data['data_child_member_true'] = '';
					$data['data_child_member_false'] = 'hide';
					$data['data_anak_status'] = language('child_data_is_null');
			    }
			    $data['list_data_child'] = $data_child_member;
			    $data['list_data_child_member'] = $data_child_member;
			    $list_data = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'],$page);
			    foreach ($list_data as $key => $value) {
				    $list_data[$key]['class_abu'] = $key % 2 == 0 ? '' : 'timeline-inverted';
				    $list_data[$key]['invert'] = $key % 2 == 0 ? '' : 'invert';
				    
				    $list_data[$key]['is_news']='hide';
				    $list_data[$key]['is_not_news']='';
				    $list_data[$key]['create_date'] = iso_date_custom_format($value['create_date'], 'd/m/Y H:i:s');
				    $list_data[$key]['create_date_ago'] = time_elapsed_string($value['last_date_read']);
					$list_data[$key]['is_qa']='hide';
					$list_data[$key]['is_not_qa']='';
				    if($value['id_article']){
					    $where['a.id'] = $value['id'];
					    $this->db->select('a.*,b.news_title,b.uri_path,b.img,b.teaser,c.name as category,c.uri_path as uri_path_category');
					    $this->db->join('news b','b.id = a.id_article');
					    $this->db->join('news_category c','c.id = b.id_news_category');
					    $data_news = $this->db->get_where('user_activity_log a',$where)->row_array();
					    $list_data[$key]['img'] = image($data_news['img'],'large');
					    $list_data[$key]['uri_path_category'] = $data_news['uri_path_category'];
					    $list_data[$key]['uri_path'] = $data_news['uri_path'];
					    $list_data[$key]['category'] = $data_news['category'];
					    $list_data[$key]['news_title'] = $data_news['news_title'];
					    $list_data[$key]['teaser'] = $data_news['teaser'];
					    $list_data[$key]['last_date_read'] = iso_date_custom_format($data_news['last_date_read'], 'd/m/Y H:i:s');
					    $list_data[$key]['last_date_read_ago'] = time_elapsed_string($data_news['last_date_read']);
					    $list_data[$key]['log_count'] = $data_news['log_count'];
					    
					    $list_data[$key]['is_news']='';
					    $list_data[$key]['is_not_news']='hide';
				    }else{
						if($value['id_ask_expert']){
							$where['a.id'] = $value['id_ask_expert'];
							$this->db->select('a.*,b.title as title_category, c.title as title_subcategory');
							$this->db->join('category b','b.id = a.id_category');
							$this->db->join('sub_category c','c.code = a.sub_category');
							  
							$data_qa = $this->db->get_where('member_ask_expert a',$where)->row_array();
							$list_data[$key]['is_qa']='';
							$list_data[$key]['is_not_qa']='hide';
							$list_data[$key]['question_qa'] = $data_qa['question_descr'];
							$list_data[$key]['answer_descr_qa'] = $data_qa['answer_descr'];
							$list_data[$key]['title_category_qa'] = $data_qa['title_category'];
							$list_data[$key]['title_subcategory_qa'] = $data_qa['title_subcategory'];
						}
					}
			    }
			    $data['list_data'] = $list_data;
			    $ttl_record = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'],'all');
			    $data['load_more'] = $ttl_record > PAGING_PERPAGE_LOG ? "<div class='parent-load-more'><li class='clearfix' style='float: none;'></li><a class='btn btn-default load-more' data-page='".PAGING_PERPAGE_LOG."'>".language('load_more')."</a></div>" : '';
			    if(getimagesize($data['image'])){
				    $data['avatar'] = $data['image'];
			    } else {
				    $data['avatar']	= base_url().(($data['image']) ? "images/member/profile_pictures/$data[image]" : 'images/member/profile_pictures/no_image.jpg');
			    }
			    load_js('ajaxfileupload.js,bootstrap-fileupload.min.js');
			    load_css('bootstrap-fileupload.min.css');
			    render('layout/ddi/member/dashboard',$data);
		    } else if($data['is_complete_data']==0 and $data['is_active']==0) {
			    if($this->session->flashdata('success_login')){
				$data['show_info'] = '';
			    } else {
				$data['show_info'] = 'hide';
			    }
			    $data['message'] = $this->session->flashdata('success_login');
			    $data['fb_like_widget'] = fb_like_widget();
			    render('layout/ddi/member/complete_data',$data);
		    } else if($data['is_complete_data']==1 and $data['is_active']==0){
			    $data['fb_like_widget'] = fb_like_widget();
			    $data['title'] = language('account_not_active');
			    $data['message'] = language('account_not_active_error');
			    render('layout/ddi/member/page',$data);
		    }
	    } else {
		    redirect('/member/login');
	    }
	}
	function edit_profile(){
	    $user_sess_data = $this->session->userdata('MEM_SESS');
	    if($user_sess_data){
		    if($user_sess_data['remember_me']=1){
			    $this->load->model('loginModel');
			    $this->loginModel->remember_me_login();
		    }
		    $data = $this->dashboardmodel->fetchRow(array('id'=>$user_sess_data['id']));
			//if($data['is_complete_data']==1 and $data['is_active']==1){
		    if($data['is_active']==1){
			    if($this->session->flashdata('success_login')){
				$data['show_info'] = '';
			    } else {
				$data['show_info'] = 'hide';
			    }
			    $data['male_check']='';
			    $data['female_check']='';
			    if($data['jeniskelamin']=='male'){
				    $data['male_check'] = 'checked';
			    } else if($data['jeniskelamin']=='female'){
				    $data['female_check'] = 'checked';
			    }
			    $select = language('select');
			    $data['jeniskelamin'] =  ($data['jeniskelamin']=='male') ? language('male') : language('female');
			    $data['marketing'] = ($data['marketing']) ? '1' : '0';
			    $data['newsletter'] = ($data['newsletter']) ? '1' : '0';
				$home_phone = explode('-',$data['home_phone']);
				$data['home_phone'] = $home_phone[1];
				$data['home_phone_code'] = $home_phone[0];
				$office_phone = explode('-',$data['office_phone']);
				$data['office_phone'] = $office_phone[1];
				$data['office_phone_code'] = $office_phone[0];
				$data['tgllahir_data'] = iso_date_custom_format($data['tgllahir'], 'd/m/Y');
			    $data['tgllahir_pasangan'] = iso_date_custom_format($data['tgllahir_pasangan'], 'd/m/Y');
			    $data['process_time'] = iso_date_custom_format($data['process_time'], 'd/m/Y H:i:s');
			    $data['top_content']     = top_content();
			    $data['tmptlahir_data'] = $this->db->select('ibukota')->get_where('t_master_tempat_lahir',"id_tmpt_lhr='$data[tmptlahir]'")->row()->ibukota;
			    $data['provinsi_data'] = $this->db->select('propinsi')->get_where('propinsi',"id='$data[provinsi]'")->row()->propinsi;
				if($data['pekerjaan']=='G'){
					$data['pekerjaan_data_show'] = '';
				} else {
					$data['pekerjaan_data_show'] = 'hide';
				}
				$data['is_married_have_child_show'] = 'hide';
				$data['is_married_show'] = 'hide';
				if($data['status_nikah']=='Q'){
					$data['is_married_have_child_show'] = '';
					$data['is_married_show'] = '';
				}
				if ($data['status_nikah']=='S'){
					$data['is_married_show'] = '';
				}
				$data['pekerjaan_data'] = $this->db->select('keterangan')->get_where('t_master_pekerjaan',"id_pekerjaan='$data[pekerjaan]'")->row()->keterangan;
			    $data['pekerjaan_data'] = $this->db->select('keterangan')->get_where('t_master_pekerjaan',"id_pekerjaan='$data[pekerjaan]'")->row()->keterangan;
			    $data['pekerjaan_lain_data'] = $this->db->select('keterangan')->get_where('t_master_pekerjaan',"id_pekerjaan='$data[pekerjaan_lain]'")->row()->keterangan;
				$data['list_total_anak'] 	= selectlist2(array('table'=>'t_master_kid_total','order'=>'id_kt','title'=>$select.' '.language('total_kid'),'name'=>'keterangan','id'=>'id_kt','selected'=>$data['jumlah_anak']));
			    $data['pendapatan_bulanan_data'] = $this->db->select('keterangan')->get_where('t_master_pendapatan_bulanan',"id_pb='$data[pendapatan_bulanan]'")->row()->keterangan;
			    $data['status_nikah_data'] = $this->db->select('keterangan')->get_where('t_master_status_nikah',"id_sn='$data[status_nikah]'")->row()->keterangan;
			    $data['pendidikan_data'] = $this->db->select('keterangan')->get_where('t_master_pendidikan',"id_pt='$data[pendidikan]'")->row()->keterangan;
			    $data['page_name']= language('account_setting');
			    $data['message'] = $this->session->flashdata('success_login');
			    $data['list_provinsi'] 	= selectlist2(array('table'=>'propinsi','order'=>'propinsi','title'=>$select.' '.language('province'),'name'=>'propinsi','selected'=>$data['provinsi']));
			    $data['list_tmp_lahir'] 	= selectlist2(array('table'=>'t_master_tempat_lahir','order'=>'ibukota','title'=>$select.' '.language('birth_place'),'name'=>'ibukota','id'=>'id_tmpt_lhr','selected'=>$data['tmptlahir']));
			    $data['list_pekerjaan'] 	= selectlist2(array('table'=>'t_master_pekerjaan', 'where'=>'is_active=1','order'=>'sort','title'=>$select.' '.language('occupation'),'name'=>'keterangan','id'=>'id_pekerjaan','selected'=>$data['pekerjaan']));
			    $data['list_pekerjaan_lain'] 	= selectlist2(array('table'=>'t_master_pekerjaan', 'where'=>'is_active=1','order'=>'sort','title'=>$select.' '.language('occupation'),'name'=>'keterangan','id'=>'id_pekerjaan','selected'=>$data['pekerjaan_lain']));
			    $data['list_status_nikah'] 	= selectlist2(array('table'=>'t_master_status_nikah','order'=>'sort','title'=>$select.' '.language('marital_status'),'name'=>'keterangan','id'=>'id_sn','selected'=>$data['status_nikah']));       
			    $data['list_pendidikan'] 	= selectlist2(array('table'=>'t_master_pendidikan','order'=>'sort','title'=>$select.' '.language('education'),'name'=>'keterangan','id'=>'id_pt', 'where'=>'is_publish=1','selected'=>$data['pendidikan']));       
			    $data['list_pendapatan_bulanan'] 	= selectlist2(array('table'=>'t_master_pendapatan_bulanan','order'=>'sort','order_by'=>'desc','title'=>$select. ' Pendapatan Bulanan','name'=>'keterangan','id'=>'id_pb','selected'=>$data['pendapatan_bulanan']));       
			    $data['list_umur_anak'] 	= selectlist2(array('table'=>'t_master_kid_age','order'=>'id_ka','title'=>$select.' '.language('age'),'name'=>'keterangan','id'=>'id_ka', 'where'=>'is_publish=1'));       
			    $data['list_umur_pasangan'] 	= selectlist2(array('table'=>'t_master_spouse_age','order'=>'id_sa','title'=>$select.' '.language('age'),'name'=>'keterangan','id'=>'id_sa', 'where'=>'is_publish=1','selected'=>$data['umur_pasangan']));       
			    $data['newsletter_checked'] 	= ($data['newsletter'] == 1) ? 'checked':'';
			    $data['marketing_checked'] 	= ($data['marketing'] == 1) ? 'checked':'';
			    $this->load->model('LogActivityModel');
			    $data_child_member = $this->dashboardmodel->get_child($user_sess_data['id']);
			    foreach($data_child_member as $n =>  $data_child){
				    ++$i;
				    $data_child_member[$n]['nomor'] 	= ++$nomor;
				    $data_child_member[$n]['dob_child'] 	= iso_date_custom_format($data_child['dob_child'], 'd/m/Y');
				    $data_child_member[$n]['jeniskelamin_child'] 	= $data_child['jeniskelamin'];
				    $data_child_member[$n]['umur_anak_data'] = $this->db->select('keterangan')->get_where('t_master_kid_age',"id_ka='$data_child[umur_anak]'")->row()->keterangan;
			    }
			    if($i>0){
				    $data['data_child_member_true'] = 'hide';
				    $data['data_child_member_false'] = '';
			    }else{
				    $data['data_child_member_true'] = '';
				    $data['data_child_member_false'] = 'hide';
			    }
			    $data['list_data_child'] = $data_child_member;
			    $data['list_data_child_member'] = $data_child_member;
			    $list_data = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'],$page);
			    foreach ($list_data as $key => $value) {
				    $list_data[$key]['class_abu'] = $key % 2 == 0 ? '' : 'timeline-inverted';
				    $list_data[$key]['invert'] = $key % 2 == 0 ? '' : 'invert';
				    
				    $list_data[$key]['is_news']='hide';
				    $list_data[$key]['is_not_news']='';
				    $list_data[$key]['create_date'] = iso_date_custom_format($value['create_date'], 'd/m/Y H:i:s');
				    $list_data[$key]['create_date_ago'] = time_elapsed_string($value['last_date_read']);
				    if($value['id_article']){
					    $where['a.id'] = $value['id'];
					    $this->db->select('a.*,b.news_title,b.uri_path,b.img,b.teaser,c.name as category,c.uri_path as uri_path_category');
					    $this->db->join('news b','b.id = a.id_article');
					    $this->db->join('news_category c','c.id = b.id_news_category');
					    $data_news = $this->db->get_where('user_activity_log a',$where)->row_array();
					    $list_data[$key]['img'] = image($data_news['img'],'large');
					    $list_data[$key]['uri_path_category'] = $data_news['uri_path_category'];
					    $list_data[$key]['uri_path'] = $data_news['uri_path'];
					    $list_data[$key]['category'] = $data_news['category'];
					    $list_data[$key]['news_title'] = $data_news['news_title'];
					    $list_data[$key]['teaser'] = $data_news['teaser'];
					    $list_data[$key]['last_date_read'] = iso_date_custom_format($data_news['last_date_read'], 'd/m/Y H:i:s');
					    $list_data[$key]['last_date_read_ago'] = time_elapsed_string($data_news['last_date_read']);
					    $list_data[$key]['log_count'] = $data_news['log_count'];
					    
					    $list_data[$key]['is_news']='';
					    $list_data[$key]['is_not_news']='hide';
				    }
			    }
			    $data['list_data'] = $list_data;
			    $ttl_record = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'],'all');
			    $data['load_more'] = $ttl_record > PAGING_PERPAGE_LOG ? "<div class='parent-load-more'><li class='clearfix' style='float: none;'></li><a class='btn btn-default load-more' data-page='".PAGING_PERPAGE_LOG."'>".language('load_more')."</a></div>" : '';
			    if(getimagesize($data['image'])){
				    $data['avatar'] = $data['image'];
			    } else {
				    $data['avatar']	= base_url().(($data['image']) ? "images/member/profile_pictures/$data[image]" : 'images/member/profile_pictures/no_image.jpg');
			    }

			    render('layout/ddi/member/edit_profile',$data);
		    } else if($data['is_complete_data']==0 and $data['is_active']==0) {
			    if($this->session->flashdata('success_login')){
				$data['show_info'] = '';
			    } else {
				$data['show_info'] = 'hide';
			    }
			    $data['message'] = $this->session->flashdata('success_login');
			    $data['fb_like_widget'] = fb_like_widget();
			    render('layout/ddi/member/complete_data',$data);
		    } else if($data['is_complete_data']==1 and $data['is_active']==0){
			   $data['fb_like_widget'] = fb_like_widget();
			    $data['title'] = language('account_not_active');
			    $data['message'] = language('account_not_active_error');
			    render('layout/ddi/member/page',$data);
		    }
	    } else {
		    redirect('/member/login');
	    }
	}
	function success_registration(){
		$user_sess_data = $this->session->flashdata('session_register_success');
		if($user_sess_data){
			$data['news_title'] = language('register_success');
			render('layout/ddi/member/success_registration',$data);
		}else{
			redirect('tidakditemukan');
		}
	}
	function success_deactive_account(){
		$user_sess_data = $this->session->flashdata('session_deactive_success');
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		if($user_sess_data){
			$this->kill_session();
			$data['title'] = language('success_deactive_account');
			$data['message'] = language('success_deactive_account_message');
			render('layout/ddi/member/page',$data);
		}else{
			$data['title'] = language('success_deactive_account');
			$data['message'] = language('success_deactive_account_message');
			render('layout/ddi/member/page',$data);
		}
	}
	function logs_more($page){
	    $user_sess_data = $this->session->userdata('MEM_SESS');
	    $this->load->model('LogActivityModel');
	    $list_data = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'],$page);
	    foreach ($list_data as $key => $value) {
		    $list_data[$key]['class_abu'] = $key % 2 == 0 ? '' : 'timeline-inverted';
		    $list_data[$key]['invert'] = $key % 2 == 0 ? '' : 'invert';
		    
		    $list_data[$key]['is_news']='hide';
		    $list_data[$key]['is_not_news']='';
			$list_data[$key]['is_qa']='hide';
		    $list_data[$key]['is_not_qa']='';
		    $list_data[$key]['create_date'] = iso_date_custom_format($value['create_date'], 'd/m/Y H:i:s');
		    $list_data[$key]['create_date_ago'] = time_elapsed_string($value['create_date']);
			
		    if($value['id_article']){
			    $where['a.id'] = $value['id'];
			    $this->db->select('a.*,b.news_title,b.uri_path,b.img,b.teaser,c.name as category,c.uri_path as uri_path_category');
			    $this->db->join('news b','b.id = a.id_article');
			    $this->db->join('news_category c','c.id = b.id_news_category');
			    $data_news = $this->db->get_where('user_activity_log a',$where)->row_array();
			    $list_data[$key]['img'] = image($data_news['img'],'large');
			    $list_data[$key]['uri_path_category'] = $data_news['uri_path_category'];
			    $list_data[$key]['uri_path'] = $data_news['uri_path'];
			    $list_data[$key]['category'] = $data_news['category'];
			    $list_data[$key]['news_title'] = $data_news['news_title'];
			    $list_data[$key]['teaser'] = $data_news['teaser'];
			    $list_data[$key]['last_date_read'] = iso_date_custom_format($data_news['last_date_read'], 'd/m/Y H:i:s');
			    $list_data[$key]['last_date_read_ago'] = time_elapsed_string($data_news['last_date_read']);
			    $list_data[$key]['log_count'] = $data_news['log_count'];
			    
			    $list_data[$key]['is_news']='';
			    $list_data[$key]['is_not_news']='hide';
		    }else{
				if($value['id_ask_expert']){
					$where['a.id'] = $value['id_ask_expert'];
					$this->db->select('a.*,b.title as title_category, c.title as title_subcategory');
					$this->db->join('category b','b.id = a.id_category');
					$this->db->join('sub_category c','c.code = a.sub_category');
					  
					$data_qa = $this->db->get_where('member_ask_expert a',$where)->row_array();
					$list_data[$key]['is_qa']='';
					$list_data[$key]['is_not_qa']='hide';
					$list_data[$key]['question_qa'] = $data_qa['question_descr'];
					$list_data[$key]['answer_descr_qa'] = $data_qa['answer_descr'];
					$list_data[$key]['title_category_qa'] = $data_qa['title_category'];
					$list_data[$key]['title_subcategory_qa'] = $data_qa['title_subcategory'];
				}
			}
	    }
	    $data['list_data'] = $list_data;
	    $ttl_record = $this->LogActivityModel->getactiviylogmember($user_sess_data['id'],'all');
	    $data['load_more']      = ($ttl_record > ($page+PAGING_PERPAGE_LOG)) ? "<div class='parent-load-more'><li class='clearfix' style='float: none;'></li><a class='btn btn-default load-more' data-page='".($page+PAGING_PERPAGE_LOG)."'>".language('load_more')."</a></div>" : '';
    
	    render('layout/ddi/member/logs_more',$data,'blank');
    
	}
	function kill_session(){
		delete_cookie('user');
		delete_cookie('password');
		$this->input->cookie('user',TRUE);
		$this->input->cookie('password',TRUE);
		$this->session->sess_destroy();
	}
	function logout(){
		$data_now = date('Y-m-d H:i:s');
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if($user_sess_data){
			$this->load->model('RegisterModel');
			$data = $this->dashboardmodel->fetchRow(array('id'=>$user_sess_data['id']));
			if($data['last_login']){
				$log_user_activity = array(
					'id_user'          =>  $data['id'],
					'process_date' =>  $data_now,
					'id_log_category'   =>  27,
				);
				$this->RegisterModel->log_user_activity($log_user_activity);

				$where['id'] = $data['id'];
				$data_update['last_logout'] 	= $data_now;
				$this->db->update('t_aegon_profile_member',$data_update,$where);
			}	
			$this->kill_session();
		}
		redirect('/');
	}
	function complete_data_process(){
	    $post = purify($this->input->post());
	    if($post){
		    $this->load->model('RegisterModel');
		    $this->form_validation->set_rules('namadepan', '"Nama Depan"', 'required'); 
		    $this->form_validation->set_rules('pwd', '"Password"', 'required'); 
		    $this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
		    if ($this->form_validation->run() == FALSE){
			     $message = validation_errors();
			     $status = 'error';
		    } else {
			    $email = $post['email'];
			    $user_sess_data = $this->session->userdata('MEM_SESS');
			    $proses = $this->RegisterModel->complete_data($post,$user_sess_data['id']);
			    if($proses['status']==1){
				$status = 'success';
				$this->session->set_flashdata('success_login',$process['message']);
			    }else{
				$status = language('something_error');
				$message = $proses['message'];	
			    }
			    
			    $data['message'] 	=  "<div class='$status-label'> $message</div>";
			    $data['status'] 	=  $status;
			    echo json_encode($data);
		    }
	    }
	}
	function update_process(){
	    $post = purify($this->input->post());
	    if($post){
		$this->load->model('RegisterModel');
			$email = $post['email'];
			if($post['home_phone']){
				$post['home_phone'] = $post['home_phone_code'].'-'.$post['home_phone'];
			}
			if($post['office_phone']){
				$post['office_phone'] = $post['office_phone_code'].'-'.$post['office_phone'];
			}
			unset($post['home_phone_code'],$post['office_phone_code']);
			$user_sess_data = $this->session->userdata('MEM_SESS');
			$proses = $this->RegisterModel->update($post,$user_sess_data['id']);
			if($proses['status']==1){
			    $status = 'success';
			    $message = $proses['message'];
			}
			else{
			    $status = language('something_error');
			    $message = $proses['message'];	
			}
		    
		    $data['message'] 	=  "<div class='$status-label'> $message</div>";
		    $data['status'] 	=  $status;
		    echo json_encode($data);
	    }
	}
	function update_process_with_email(){
	    $post = purify($this->input->post());
	    if($post){
		$this->load->model('RegisterModel');
			$this->load->model('RegisterModel');
			$this->form_validation->set_rules('namadepan', '"Nama Depan"', 'required');
			$this->form_validation->set_rules('namabelakang', '"Nama Belakang"', 'required'); 
			if ($this->form_validation->run() == FALSE){
				 $message = validation_errors();
				 $status = 'error';
			} else {
				$email = $post['email'];
				$re_email = $post['email_new_retype'];
				unset($post['email'],$post['email_new_retype']);
				$user_sess_data = $this->session->userdata('MEM_SESS');
				$proses = $this->RegisterModel->update($post,$user_sess_data['id']);
				if($proses['status']==1){
				    $status = 'success';
				    $message = $proses['message'];
				}
				else{
				    $status = language('something_error');
				    $message = $proses['message'];	
				}
				if($email!= null or $email !='' and $user_sess_data){
					$where_email['email'] = $email;
					$check_email = $this->db->select('*')->get_where('t_aegon_profile_member',$where_email)->row();
					$status = language('something_error');
					if($email and $re_email){
						if(valid_email($email) and valid_email($re_email)){
							if($email != $re_email){
								$message = language('email_confirmation_doesnt_match');
							}else if($check_email){
								$message = language('email_already_registered');
							} else {
								$this->RegisterModel->change_new_email($email);
								$status = 'success';
								$message = language('change_email_success_message');
							}
						}else{
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
	function check_avalible_email(){
		$where_email['email'] = $this->input->get('email');
		$check_email = $this->db->select('*')->get_where('t_aegon_profile_member',$where_email)->row();
		if($check_email){
			$message = language('email_already_registered');
			echo 'false';
		} else {
			echo 'true';
		}
		
	}
	function confirmation_change_email($activation_code){
		$process = $this->RegisterModel->change_new_email_confirmed($activation_code);
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		if($process['status']==1){
			$data['title'] = language('activation_code_capcha_change_email');
			$data['message'] = language('activation_code_capcha_message_change_email');
			$data['google_captcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
			$data['activation_code'] = $activation_code;
			render('layout/ddi/member/activation_member_change_email',$data);
		}else{
			$data['title'] = language('activation_code_not_valid');
			$data['message'] = $process['message'];
			render('layout/ddi/member/page',$data);
		}
	}
	function process_activation_member_change_email(){
		$this->load->library('curl'); 
		$post = purify($this->input->post());
		if($post){
			$userIp= $this->input->ip_address();
			$data_captcha = array();
			$secret= GOOGLE_CAPTCHA_SECRET_KEY;
			$responsecaptcha = trim($post['g-recaptcha-response']);
			$url="https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$responsecaptcha."&remoteip=".$userIp;
			
			$ch = curl_init();
			$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

		    curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			$data_captcha = curl_exec($ch);
		    curl_close($ch);
			
			$status= json_decode($data_captcha,true);
			if(!$status['success']){
				$status_data = language('not_check_captcha');
				$message = language('not_check_captcha_message');
			} else if($status['success']){
				$process_active = $this->RegisterModel->change_new_email_confirmed_process($post['activation_code']);
				if($process_active['status']==1){
					$this->session->set_flashdata('change_email_success_flash','true');
					$status_data = 'success';
					//$this->kill_session();
				} else {
					$status_data = 'failed';
					$message = $process_active['message'];
				}
				
			}
			$this->session->set_flashdata('change_email_success_flash','true');
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status_data;
			echo json_encode($data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function change_password(){
	    $post = purify($this->input->post());
	    $this->load->model('RegisterModel');
	    if($post){
		    $post['id_contact_us_topic'] =  $post['topic'];
		    $this->form_validation->set_rules('current_pwd', '"Password Lama"', 'required'); 
		    $this->form_validation->set_rules('pwd', '"Password"', 'required'); 
		    if ($this->form_validation->run() == FALSE){
			     $message = validation_errors();
			     $status = 'error';
		    }else{
			    $proses = $this->RegisterModel->change_password($post);
			    if($proses['status']==1){
				    $status = 'success';
				    $message = $proses['message'];
					$this->kill_session();
			    }
			    else{
				    $status = language('something_error');
				    $message = $proses['message'];	
			    }
		    }
    
		    $data['message'] 	=  "<div class='$status-label'> $message</div>";
		    $data['status'] 	=  $status;
		    echo json_encode($data);
	    }
	}
	function change_profile_picture(){
	    $data_now = date('Y-m-d H:i:s');
	    $user_sess_data = $this->session->userdata('MEM_SESS');
	    $file 	= $_FILES['avatar'];
	    $fname 	= $file['name'];
	    if($fname and $user_sess_data){
		$size = getimagesize($file['tmp_name']);
		        $resize_height=($size[1]*200)/$size[0];
		    $ext				= explode('.',$fname);
		    $ext				= $ext[count($ext)-1];
		    $data_reset['image'] = md5($user_sess_data['id']).'.'.$ext;
		    $where_reset['id'] = $user_sess_data['id'];
		    $this->db->update('t_aegon_profile_member',$data_reset,$where_reset);
		    $config['image_library'] 	= 'gd2';
		    $config['source_image'] 	= $file['tmp_name'];
		    $config['new_image']		= 'images/member/profile_pictures/'.$data_reset['image'];
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
	function change_subscriber(){
	    $post = purify($this->input->post());
	    $this->load->model('RegisterModel');
	    if($post){
		    $proses = $this->RegisterModel->change_subscriber($post);
		    if($proses['status']==1){
			    $status = 'success';
			    $message = $proses['message'];
		    }
		    else{
			    $status = language('something_error');
			    $message = $proses['message'];	
		    }
		    $data['message'] 	=  "<div class='$status-label'> $message</div>";
		    $data['status'] 	=  $status;
		    echo json_encode($data);
	    }
	}
	function login($page){
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if(!$user_sess_data){
			$user_sess_data = $this->session->userdata('MEM_SESS');
			$data['message'] = $this->session->flashdata('success_login');
			if($this->session->flashdata('success_login')){
				$data['show_info'] = '';
			} else {
				$data['show_info'] = 'hide';
			}
	    
			$data['url'] = htmlspecialchars(strip_tags($this->input->get('url')));
			if($user_sess_data){
				if($user_sess_data['remember_me']=1){
					$this->load->model('loginModel');
					$this->loginModel->remember_me_login();
				}
			}
			$data['fb_like_widget'] = fb_like_widget();
			render('layout/ddi/member/login',$data);
		} else{
			redirect('/');
		}
    
	}
	function register(){
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if(!$user_sess_data){
			$post = purify($this->input->post());
			if($post){
				$post['id_contact_us_topic'] =  $post['topic'];
				$data['check_terms'] = 0;
				if($post['termscondition']==1){
					    $this->form_validation->set_rules('namadepan', '"Nama Depan"', 'required');
					    $this->form_validation->set_rules('namabelakang', '"Nama Belakang"', 'required'); 
					    $this->form_validation->set_rules('pwd', '"Password"', 'required'); 
					    //$this->form_validation->set_rules('hp', '"Handphone"', 'required'); 
					    $this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
					if ($this->form_validation->run() == FALSE){
						$message = validation_errors();
						$status = 'error';
					}else{
						$email = $post['email'];
						unset($post['confirm_email']);
						$proses = $this->RegisterModel->register($post);
						if($proses['status']==1){
							$status = 'success';
							$message = $proses['message'];
							$this->session->set_flashdata('session_register_success','true');
						}else{
							$status = language('something_error');
							$message = $proses['message'];	
						}
					}
				    $data['message'] 	=  "<div class='Kesalahan-label'> $message</div>";
	    
				} else {
				    $status = language('something_error');
				    $message = language('agreement_register_error');
				    $data['check_terms'] = 1;
				}
				    $data['message'] 	=  "<div class='Kesalahan-label'> $message</div>";
				    $data['status'] 	=  $status;
				echo json_encode($data);
				
			}
			else{
				$data['fb_like_widget'] = fb_like_widget();
				$data['ads_widget']     = ads_widget();
				$data['list_provinsi'] 	= selectlist2(array('table'=>'propinsi','order'=>'propinsi','title'=> language('select').' '.language('province'),'name'=>'propinsi'));
				$data['pages_syarat_ketentuan'] = get_pages_url('syarat-ketentuan');
				$data['pages_kebijakan_privasi'] = get_pages_url('kebijakan-privasi');
				render('layout/ddi/member/register',$data);
			}
		}else{
			redirect('/');
		}
	}
	function get_town(){
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
	}
	function get_kecamatan(){
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
	}
	function get_kelurahan(){
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
	}
	public function social_signup($provider)
	{
		if(LANGUAGE=='english'){
			log_message('debug', "controllers.HAuth.login($provider) called");
	
			try
			{
				log_message('debug', 'controllers.HAuth.login: loading HybridAuthLib');
				$this->load->library('HybridAuthLib');
	
				if ($this->hybridauthlib->providerEnabled($provider))
				{
					log_message('debug', "controllers.HAuth.login: service $provider enabled, trying to authenticate.");
					$service = $this->hybridauthlib->authenticate($provider);
	
					if ($service->isUserConnected())
					{
						log_message('debug', 'controller.HAuth.login: user authenticated.');
	
						$user_profile = $service->getUserProfile();
	
						log_message('info', 'controllers.HAuth.login: user profile:'.PHP_EOL.print_r($user_profile, TRUE));
	
						$data['user_profile'] = $user_profile;
						$process = $this->RegisterModel->inserting_data($data, $provider);
			    if($process == 1){
				redirect('/member');
			    } else {
				redirect('/member');
			    }
						//$this->load->view('hauth/done',$data);
					}
					else // Cannot authenticate user
					{
						show_error('Cannot authenticate user');
					}
				}
				else // This service is not enabled.
				{
					log_message('error', 'controllers.HAuth.login: This provider is not enabled ('.$provider.')');
					show_404($_SERVER['REQUEST_URI']);
				}
			}
			catch(Exception $e)
			{
				$error = 'Unexpected error';
				switch($e->getCode())
				{
					case 0 : $error = 'Unspecified error.'; break;
					case 1 : $error = 'Hybriauth configuration error.'; break;
					case 2 : $error = 'Provider not properly configured.'; break;
					case 3 : $error = 'Unknown or disabled provider.'; break;
					case 4 : $error = 'Missing provider application credentials.'; break;
					case 5 : log_message('debug', 'controllers.HAuth.login: Authentification failed. The user has canceled the authentication or the provider refused the connection.');
						 //redirect();
						 if (isset($service))
						 {
							log_message('debug', 'controllers.HAuth.login: logging out from service.');
							$service->logout();
						 }
						 show_error('User has cancelled the authentication or the provider refused the connection.');
						 break;
					case 6 : $error = 'User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.';
						 break;
					case 7 : $error = 'User not connected to the provider.';
						 break;
				}
	
				if (isset($service))
				{
					$service->logout();
				}
	
				log_message('error', 'controllers.HAuth.login: '.$error);
				show_error('Error authenticating user.');
			}
		}
	}
	
	public function endpoint()
	{
		
		log_message('debug', 'controllers.HAuth.endpoint called.');
		log_message('info', 'controllers.HAuth.endpoint: $_REQUEST: '.print_r($_REQUEST, TRUE));

		if ($_SERVER['REQUEST_METHOD'] === 'GET')
		{
			log_message('debug', 'controllers.HAuth.endpoint: the request method is GET, copying REQUEST array into GET array.');
			$_GET = $_REQUEST;
		}

		log_message('debug', 'controllers.HAuth.endpoint: loading the original HybridAuth endpoint script.');
		require_once APPPATH.'/third_party/hybridauth/index.php';

	}
	function active_member($activation_code){
		$process = $this->RegisterModel->active_member($activation_code);
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		if($process['status']==1){
			$data['title'] = language('activation_code_capcha');
			$data['message'] = language('activation_code_capcha_message');
			$data['google_captcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
			$data['activation_code'] = $activation_code;
			render('layout/ddi/member/activation_member',$data);
		}else{
			$data['title'] = language('activation_failed');
			$data['message'] = $process['message'];
			render('layout/ddi/member/page',$data);
		}
	}
	function process_activation_member(){
		$this->load->library('curl'); 
		$post = purify($this->input->post());
		if($post){
			$userIp= $this->input->ip_address();
			$data_captcha = array();
			$secret= GOOGLE_CAPTCHA_SECRET_KEY;
			$responsecaptcha = trim($post['g-recaptcha-response']);
			$url="https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$responsecaptcha."&remoteip=".$userIp;
			
			$ch = curl_init();
			$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

		    curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			$data_captcha = curl_exec($ch);
		    curl_close($ch);
			
			$status= json_decode($data_captcha,true);
			
			if(!$status['success']){
				$status_data = language('not_check_captcha');
				$message = language('not_check_captcha_message');
			} else if($status['success']){
				$process_active = $this->RegisterModel->process_activation($post['activation_code']);
				if($process_active['status']==1){
					$this->session->set_flashdata('email_activation_success','true');
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
	function active_member_success_change_email(){
		$user_sess_data = $this->session->flashdata('change_email_success_flash');
		if($user_sess_data){
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article']= popular_article(0);
			$data['new_article']    = new_article(1,2);
			$data['new_expert']    = new_expert(1,1);
			$this->kill_session();
			$data['title'] = language('change_email_success_title');
			$data['message'] = language('change_email_success');	
			render('layout/ddi/member/page',$data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function active_member_success(){
		$user_sess_data = $this->session->flashdata('email_activation_success');
		if($user_sess_data){
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article']= popular_article(0);
			$data['new_article']    = new_article(1,2);
			$data['new_expert']    = new_expert(1,1);
			$data['title'] = language('activation_success');
			$data['message'] = language('activation_success_message');			
			render('layout/ddi/member/page',$data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function active_email_member($activation_code){
		$process = $this->RegisterModel->active_member($activation_code,1);
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		if($process['status']==1){
			$data['title'] = language('activation_code_capcha_email');
			$data['message'] = language('activation_code_capcha_message_email');
			$data['google_captcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
			$data['activation_code'] = $activation_code;
			render('layout/ddi/member/activation_member_email',$data);
		}else{
			$data['title'] = language('activation_failed_new');
			$data['message'] = $process['message'];
			render('layout/ddi/member/page',$data);
		}
	}
	function process_active_email_member(){
		$this->load->library('curl'); 
		$post = purify($this->input->post());
		if($post){
			$userIp= $this->input->ip_address();
			$data_captcha = array();
			$secret= GOOGLE_CAPTCHA_SECRET_KEY;
			$responsecaptcha = trim($post['g-recaptcha-response']);
			$url="https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$responsecaptcha."&remoteip=".$userIp;
			
			$ch = curl_init();
			$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

		    curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			$data_captcha = curl_exec($ch);
		    curl_close($ch);
			
			$status= json_decode($data_captcha,true);
			
			if(!$status['success']){
				$status_data = language('not_check_captcha');
				$message = language('not_check_captcha_message');
			} else if($status['success']){
				$process_active = $this->RegisterModel->process_activation($post['activation_code'],1);
				if($process_active['status']==1){
					$this->session->set_flashdata('email_activation_member_success','true');
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
	function active_email_member_success(){
		$user_sess_data = $this->session->flashdata('email_activation_member_success');
		if($user_sess_data){
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article']= popular_article(0);
			$data['new_article']    = new_article(1,2);
			$data['new_expert']    = new_expert(1,1);
			$data['title'] = language('activation_success_new');
			$data['message'] = language('activation_success_message_new');
			render('layout/ddi/member/page',$data);
		} else {
			redirect('tidakditemukan');
		}
	}
	function resend_email_activation(){
		$post = purify($this->input->post());
		if($post){
			$this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
				if ($this->form_validation->run() == FALSE){
					 $message = validation_errors();
					 $status = 'error';
				}
				else{
			$email = $post['email'];
				$proses = $this->LoginModel->resend_email_activation($post);
				if($proses['status']==1){
			    $status = 'success';
					$message = $proses['message'];
					$this->session->set_flashdata('resend_email_activation_success','true');
				}
				else{
					$status = language('something_error');
					$message = $proses['message'];	
				}
				}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
	
		}
		else{
			$data['fb_like_widget'] = fb_like_widget();
			$data['ads_widget']     = ads_widget();
			render('layout/ddi/member/resend_email_activation',$data);
		}
	    }
	function forgot_password(){
	    $post = purify($this->input->post());
	    if($post){
		    $this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
			    if ($this->form_validation->run() == FALSE){
				     $message = validation_errors();
				     $status = 'error';
			    }
			    else{
			$email = $post['email'];
			    $proses = $this->LoginModel->reset_password($post);
			    if($proses['status']==1){
					$this->session->set_flashdata('session_reset_password_send_email_success','true');
					$status = 'success';
					$message = $proses['message'];
			    }
			    else{
				    $status = language('something_error');
				    $message = $proses['message'];	
			    }
			    }
		    $data['message'] 	=  "<div class='$status-label'> $message</div>";
		    $data['status'] 	=  $status;
		    echo json_encode($data);
    
	    }
	    else{
		    $data['fb_like_widget'] = fb_like_widget();
		    $data['ads_widget']     = ads_widget();
		    render('layout/ddi/member/reset_password',$data);
	    }
	}
	function reset_password_send_email_success(){
		$user_sess_data = $this->session->flashdata('session_reset_password_send_email_success');
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		if($user_sess_data){
			$data['title'] = language('reset_password_success');
			$data['message'] = language('reset_password_success_message');
			render('layout/ddi/member/page',$data);
		}else{
			redirect('tidakditemukan');
		}
	}
	function resend_email_activation_success(){
		$user_sess_data = $this->session->flashdata('resend_email_activation_success');
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		if($user_sess_data){
			$data['title'] = language('send_activation_code_success');
			$data['message'] = language('send_activation_code_success_message');
			render('layout/ddi/member/page',$data);
		}else{
			redirect('tidakditemukan');
		}
	}
	function reset_password_success(){
		$user_sess_data = $this->session->flashdata('session_reset_password_success');
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		if($user_sess_data){
			$data['title'] = language('reset_password_success_code');
			$data['message'] = language('reset_password_success_message_code');
			header("refresh:10; url=".base_url()."member/login");
			render('layout/ddi/member/page',$data);
		}else{
			redirect('tidakditemukan');
		}
	}
	function reset_password_code($activation_code){
		$post = purify($this->input->post());
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		$process = $this->LoginModel->check_reset_due($activation_code);
		if($process['status']==1){
		    if($post){
			$this->form_validation->set_rules('pwd', '"Password"', 'required'); 
			if ($this->form_validation->run() == FALSE){
			     $message = validation_errors();
			     $status = 'error';
			}
			else{
			    $reset_process = $this->LoginModel->reset_password_code($post);
			    if($reset_process['status']==1){
				$this->session->set_flashdata('session_reset_password_success','true');
				$status = 'success';
				$message = $reset_process['message'];
			    }else{
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
			render('layout/ddi/member/reset_password_code',$data);
		    }
		} else {
			$data['title'] = language('reset_password_code_not_valid');
			$data['message'] = language('reset_password_code_not_valid_message');
			render('layout/ddi/member/page',$data);
		}
		
	}
	function login_process(){
		$post = purify($this->input->post());
		$data['message_data'] = 0;
		if($post){
			$url_redirect = $post['url_redirect'];
			unset($post['url_redirect']);
			$this->form_validation->set_rules('modal_login_form_username', '"Username"', 'required');
			$this->form_validation->set_rules('modal_login_form_password', '"Password"', 'required'); 
			if ($this->form_validation->run() == FALSE){
				 $message = validation_errors();
				 $status = 'error';
			}
			else{
				$proses = $this->LoginModel->login($post);
				if($proses['status']==1){
					$status = 'success';
					if($url_redirect == ''){
						$data['redicret'] = base_url().'member/';
					}else{
						$data['redicret'] = $url_redirect;
					}
				}
				else{
					if($proses['status']==2){
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
	function deactivate_account(){
		$user_sess_data = $this->session->userdata('MEM_SESS');
		if($user_sess_data){
			$proses = $this->RegisterModel->deactivate_account($user_sess_data['id']);
			if($proses['status']==1){
				
				$status = 'success';
				$message = $proses['message'];	
				$this->session->set_flashdata('session_deactive_success','true');
			}
			else{
				$status = language('something_error');
				$message = $proses['message'];	
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
	function reactivate_account($idrenderpage){
		if($idrenderpage){
			$data['fb_like_widget'] = fb_like_widget();
			$data['top_content']     = top_content(1);
			$data['popular_article']= popular_article(0);
			$data['new_article']    = new_article(1,2);
			$data['new_expert']    = new_expert(1,1);
			$proses = $this->RegisterModel->reactivate_account_check($idrenderpage);
			if($proses['status']==1){
				$this->session->set_flashdata('success_login',$proses['message']);
				
				$status = language('something_error');
				$message = $proses['message'];
				$data['title'] = $status;
				$data['idrenderpage'] = $idrenderpage;
				$data['message'] = $proses['message'];
				render('layout/ddi/member/reactive_member',$data);	
			}
			else{
				$status = language('something_error');
				$message = $proses['message'];
				$data['title'] = $status;
				$data['message'] = $proses['message'];
				render('layout/ddi/member/page',$data);
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			//echo json_encode($data);
		} else {
			redirect('/');
		}
	}
	function reactivate_account_process($idrenderpage){
		$data['fb_like_widget'] = fb_like_widget();
		$data['top_content']     = top_content(1);
		$data['popular_article']= popular_article(0);
		$data['new_article']    = new_article(1,2);
		$data['new_expert']    = new_expert(1,1);
		if($idrenderpage){
			$proses = $this->RegisterModel->reactivate_account($idrenderpage);
			if($proses['status']==1){
				$this->session->set_flashdata('success_login',$proses['message']);
				redirect('/member/');
				
				$status = 'success';
				$message = $proses['message'];	
			}
			else{
				$status = language('something_error');
				$message = $proses['message'];
				$data['title'] = $status;
				$data['message'] = $proses['message'];
				render('layout/ddi/member/page',$data);
			}
			$data['message'] 	=  "<div class='$status-label'> $message</div>";
			$data['status'] 	=  $status;
			//echo json_encode($data);
		} else {
			redirect('/');
		}
	}
	function edit_data_child(){
		$post = purify($this->input->post());
		$data = '';
		if($post){
			$this->RegisterModel->delete_all_child();
			foreach($post['nama'] as $idx => $val){
				if($post['nama'][$idx]){
					$this->RegisterModel->insert_child($post['nama'][$idx],$post['dob_child'][$idx],$post['jeniskelamin'][$idx],$post['umur_anak'][$idx]);
				}
			}
		}
		$data['status'] 	=  'success';
		$data['message'] 	=  language('success_change_profile');
		$this->session->set_flashdata('success_login',language('success_change_profile'));
		echo json_encode($data);
	}
	function baru(){ /*buat mas manto */
			render('layout/ddi/member/baru',$data);

	}
}