<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_Member extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('datamemberModel');
		$this->load->model('dashboardmodel');
		$this->load->model('registermodel');
		$this->load->model('loginmodel');
	}
	function index(){
		$data['list_is_active'] = selectlist2(array('table'=>'is_active','title'=>'All Status','selected'=>$data['is_active']));
		render('apps/data_member/index',$data,'apps');
	}
	function records(){
		$data = $this->datamemberModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['process_time'] 	= iso_date($value['process_time']);
			$data['data'][$key]['marketing'] = ($value['marketing']==1) ? 'Yes' : 'No';
			$data['data'][$key]['newsletter'] = ($value['newsletter']==1) ? 'Yes' : 'No';
		}
		render('apps/data_member/records',$data,'blank');
	}
	
	public function view($id=''){
		if($id){
			$user_sess_data['id'] = $id;
			$data = $this->datamemberModel->findById($id);
			if(!$data){
				die('404');
			}
			if(get_flash_session('success_login')){
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
				$data['id_member_data'] = $id;
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
				$data['message'] = get_flash_session('success_login');
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
				$check_data_exist = check_data_exist_counter(array("$data[tmptlahir]","$data[tgllahir]","$data[status_nikah]","$data[provinsi]",
									       "$data[pendidikan]","$data[pekerjaan]","$data[nohp]","$data[namadepan]","$data[namabelakang]",
									       "$data[kota]","$data[kelurahan]","$data[kecamatan]",
									       "$data[jeniskelamin]","$data[alamat]",$data_anak_true),
									array("tmptlahir","tgllahir","status_nikah","provinsi",
									       "pendidikan","pekerjaan",
									       "nohp","namadepan","namabelakang",
									       "kota","kelurahan","kecamatan",
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
				render('apps/data_member/view',$data,'main_edit_member');
		}

	}
	function del(){
		$this->db->trans_start();   
		$id_member = $this->input->post('iddel');
		$this->db->delete('t_aegon_keep_login', array('userid' => $id_member));
		$this->db->delete('t_aegon_log_login', array('userid' => $id_member));
		$this->db->delete('user_activity_log', array('id_user' => $id_member));
		$this->db->delete('user_article_log', array('id_user' => $id_member));
		$this->db->delete('t_aegon_member_child', array('id_member' => $id_member));
		$this->db->delete('t_aegon_profile_social_media', array('id_social_media' => $data['id_social_media']));
		$this->db->delete('t_aegon_profile_member', array('id' => $id_member));
		$this->db->delete('t_aegon_member_deactive', array('id_member' => $id_member));
		$this->db->trans_complete();
	}
	function logs_more($page, $id){
		$user_sess_data['id'] = $id;
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
    
	    render('layout/futuready/member/logs_more',$data,'blank');
    
	}

	function export_to_excel($status=0,$unduhfile){
		$this->load->model('model_user', 'userModel');
		$exportData = $this->datamemberModel->exportDataMember($status,1);
	
		$userid = id_user();
		$userdata = $this->userModel->fetchRow(array('id_auth_user'=>$userid));
		if($status == 0) {
			$date = date('Y_m_d_h_i_s');
			$process_number_id = "".$userdata['userid']."_export_$date";
			$post = $this->input->post();
			$alias['search_namadepan'] = 'a.namadepan';
			$alias['search_is_active'] = 'b.id';

			where_grid($post, $alias);
			$this->datamemberModel->insertExportDM(array('userid'=>$userid, 'search_filter'=>http_build_query($post), 'filename'=>$process_number_id));
		}
			
		foreach($exportData as $key => $export) {
			$foldername = EXPORT_EXCEL_DATA_MEMBER_DIR.$export['filename'];
			$compress = EXPORT_EXCEL_DATA_MEMBER_DIR.$export['filename'].'.tar';
			if($status == 1) {
				if($export) {
					$up = 5000;
					$total_data = $this->datamemberModel->export_to_excel($export['search_filter']);
					$filename = 1;
					$limit = ($total_data < $up) ? $total_data : $up;
					for($x=$limit; $x<=$total_data; $x+=$limit){
						$offset = ($total_data < $up) ? NULL : $x;
						$this->db->limit($limit,$offset);
						$data['data'] = $this->datamemberModel->export_to_excel($export['search_filter'],1);
						$i=1;
						foreach ($data['data'] as $key => $value) {
							$data_excel[$key][0] 	= $i++;
							$data_excel[$key][1]	= $value['namadepan'];
							$data_excel[$key][2] 	= $value['namabelakang'];
							$data_excel[$key][3]	= $value['email'];
							$data_excel[$key][4]	= $value['nohp'];
							$data_excel[$key][5]	= iso_date($value['process_time']);
							$data_excel[$key][6] 	= $value['is_active_status'];
						}
						$header = array('No.','Nama Depan','Nama Belakang','Email','No HP','Join Date','Status');

						export_to_csv($header, $data_excel, EXPORT_EXCEL_DATA_MEMBER_DIR.$export['filename'].'/', 'data_member_'.$filename++.'.csv');
						$this->db->update('export_data_member', array('status'=>2), array('userid'=>$export['userid'],'status'=>1));
					}
				} else {
					exit('No data to be processed.');
				}
			} 
			elseif($status == 2) {
				try
				{
					$archive = new PharData($compress);
					$archive->buildFromDirectory($foldername.'/');
					$archive->compress(Phar::GZ);
					unlink($compress);
				}
				catch (Exception $e) 
				{
				    echo "Exception : " . $e;
				}
				$user = $this->userModel->fetchRow(array('id_auth_user'=>$export['userid']));
				$this->db->update('export_data_member', array('status'=>3), array('userid'=>$export['userid'],'status'=>2));

				$data['link_download'] = base_url().'apps/data_member/export_to_excel/3/'.$export['filename'];
				$data['name'] = $user['username'];
				sent_email_by_category(37,$data,$user['email']);			
			}
			else {
				if($unduhfile){
				    $gzip = EXPORT_EXCEL_DATA_MEMBER_DIR.$unduhfile.'.tar.gz';
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Length: ' . filesize($gzip));
					header('Content-Disposition: attachment; filename=' . basename($gzip));
					readfile($gzip);
					unlink($gzip);

					$this->db->update('export_data_member', array('status'=>4), array('filename'=>$unduhfile,'status'=>3));
					exit;
				}
			}
		}
	}
}

/* End of file slideshow.php */
/* Location: ./application/controllers/apps/slideshow.php */