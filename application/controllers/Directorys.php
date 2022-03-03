<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Directorys extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('committee_model');
        $this->load->model('member_model');
        $this->load->model('company_model');
        $this->load->model('auth_member_committee_model');
        $this->load->model('auth_member_sector_model');
        $this->load->model('aboutpartnersmodel');
        $this->load->model('sector_model');
    }

    function index(){
        $post                      = purify($this->input->post());
        $user_sess_data            = $this->session->userdata('MEM_SESS');
        $data["forSelectTwo"]      = ($user_sess_data) ? "directorys" : "directoryAs";
        $data["disablesignout"]    = ($user_sess_data) ? "" : "disabled ='disabled'";
        $data["signout_directory"] = ($user_sess_data) ? "hidden ='hidden'" : "enable";
        $data["selected_directory"] = ($user_sess_data) ? "" : "selected";

        $range_list               = $this->check_sector_range('1',1);
        // $temp_list['list_data']   = $this->get_list_data($range_list[0],2);
        $temp_list["toggle_rule"] = ($user_sess_data) ? 'data-toggle="collapse"' : 'data-toggle="collapse"';
        // $temp_list["toggle_rule"] = ($user_sess_data) ? 'data-toggle="collapse"' : '';
        $temp_list['list_data']   = $this->get_list_data($range_list[0],1);
        $data['first_content']    = $this->parser->parse("directory/search.html",$temp_list,true); 

        $data['range_list1']    = $range_list[0] ;
        $data['range_list2']    = $range_list[1] ;

        $data['seo_title']        = ($data['seo_title'] == "") ? "MJM" : $data['seo_title'];
        $data['meta_description'] = ($data['meta_description'] == '') ? "MJM" : $data['meta_description'];
        $data['meta_keywords']    = ($data['meta_keywords'] == '') ? "MJM" : $data['meta_keywords'];
        
        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar(); //pake sidebar
        $this->data['recaptcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
         $this->directory_file($data);

        render('directory/index',$data);
    }

    function directory_file(&$data){
        $this->load->model('member_directory_model');
        $file = $this->member_directory_model->findBy(['id_status_publish'=> 2],1);
        $data['directory_file'] = '';
        if ($file) {
            $link = 'file_upload/'.$file['name'];
            // $year_range = date('Y', strtotime('- 1 years')).'/'.date('Y');
            // $link_name = "Directory ". $year_range;

            // $mutable['link'] = anchor($link, '<i class="fa fa-download"></i> '.$link_name,'class="btn-red open-modal tangan" target="_BLANK"');
            $mutable['link'] =  $link;
            $mutable['img'] = image($file['banner'],'large');
            $data['directory_file'] = $this->parser->parse("directory/directory_file.html",$mutable,true); 
        }
    }


    function check_sector_range($type,$return){

        $letters = range('A', 'Z'); 

        switch ($type) {
            case '2': // corporate
                // $where["is_invis"]      = 0;
                $where["status_id"]     = 1;
                $where["is_delete"]     = 0;
                $where["company_invis"] = 0;
                $this->db->where_in('category_id', array('1','3','4'));
                $this->db->group_by('company_id');
                $this->db->order_by('company_single_name','asc');
                $list_data    = $this->member_model->findViewBy();
                $pertengahan  = ceil(count($list_data) /2);
                $company_name = $list_data[$pertengahan]['company_single_name'] ;
                $last_word_1  = strtoupper(substr($company_name, 0,1));

                foreach ($letters as $key=>$letter)   
                {  
                    if($letter == $last_word_1){
                        $last_word_2  = $letters[$key+1];  
                    }
                }

                $filter_alpabet  = array('A-'.$last_word_1,$last_word_2.'-Z');
                break;
            case '4':
            case '3': // individual
                $where["is_invis"]      = 0;
                $where["status_id"]     = 1;
                $where["is_delete"]     = 0;
                // $where["company_invis"] = 0;
                $this->db->where_in('category_id', array('2'));
                // $this->db->group_by('company_id');
                $this->db->order_by('member_name_without_prefix','asc');
                $list_data    = $this->member_model->findViewBy();
                $pertengahan  = ceil(count($list_data) /2);
                $company_name = $list_data[$pertengahan]['member_name_without_prefix'] ;
                $last_word_1  = strtoupper(substr($company_name, 0,1));

                foreach ($letters as $key=>$letter)   
                {  
                    if($letter == $last_word_1){
                        $last_word_2  = $letters[$key+1];  
                    }
                }

                $filter_alpabet  = array('A-'.$last_word_1,$last_word_2.'-Z');
                break;

            default:
                $filter_alpabet = array('A-I','J-Z');
                break;
        }
        if ($return) {
            return $filter_alpabet;exit;
        }else{
            echo json_encode($filter_alpabet);exit;
        }

    }
    function get_list_data($range,$id,$ret){
        $user_sess_data            = $this->session->userdata('MEM_SESS');
        $post           = purify($this->input->post());
        $id             = ($id != "") ? $id :$post['category_directory'];
        $array_alphabet = $range;
        $alphabet       = explode('-',$array_alphabet);
        $lower          = '"'.strtolower($alphabet[0]) .'"'. ' and ' . '"'.strtolower($alphabet[1]).'"';
        $upper          = '"'.strtoupper($alphabet[0]) .'"'. ' and ' . '"'.strtoupper($alphabet[1]).'"';
        $this->data['recaptcha_site_key'] = GOOGLE_CAPTCHA_SITE_KEY;
        $secret   = GOOGLE_CAPTCHA_SECRET_KEY;
        $response = $_POST['recaptcha_response'];
        $ip       = $_SERVER['REMOTE_ADDR'];
        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$response."&remoteip=".$ip);
        $Return   = json_decode($Response);
       
        if($Return->success){
            unset($post['recaptcha_response']);
        // get data per category
            switch ($id) {
                case '1': // sector

                    if ($post['type'] != 2){
                        if ($post['title']){
                            $this->db->where("(
                                CONCAT(name) LIKE '%".$post['title']."%' 
                                or company_name_in LIKE '%".trim($post['title'])."%' 
                                or a.representative LIKE '%".trim($post['title'])."%'
                            )");
                        }
                    }

                    if ($post['type'] != 1){
                        $this->db->where("(substring(name FROM 1 FOR 1) between $lower or substring(name FROM 1 FOR 1) between $upper or is_other= 1)");
                    }

                    $this->db->order_by('name','asc');
                    $this->db->group_by('name');
                    if ($ret && $post['title'] !="") {
                        $this->db->where('is_delete', 0);
                        $this->db->where('is_delete_tag', 0);
                        $this->db->where('is_delete_member', 0);
                        $this->db->where('is_delete_company', 0);
                        $this->db->where('company_invis', 0);
                    }
                    $where['id_status_publish'] = 2;

                    // $this->db->where('((a.is_other = 0 and a.is_parent_other = 0) or (a.is_other = 1 and a.is_parent_other = 1))');

                    $data['list_data']          = $this->sector_model->findviewBy($where);
                    
                    // $ada_other = false;
                    // foreach ($data['list_data'] as $key => $value) {
                        // if ($value['is_other']) {
                            // $ada_other = true;
                            // unset($data['list_data'][$key]);
                        // }
                    // }
                    // if ($ada_other) {
                        // $id_parent_sector  = $this->sector_model->get_idotherparent();
                        // $where['id']       = $id_parent_sector;
                        // $data_sector_other = $this->sector_model->findviewBy($where,1);
                        // if (strpos($range,"-Z")) {
                            // array_push($data['list_data'], $data_sector_other);
                        // }
                    // }

                break;

                case '2': // company
                    $numeric_filter    = ($alphabet[0] == "A") ? "or a.company_single_name regexp '^[0-9]'":"";

                    if ($post['type'] != 2){
                        if ($post['title']){
                            // check name dan nama company
                            $post['title'] = query_kutip(trim($post['title']));
                            $this->db->where('(member_full_name like "%'.trim($post['title']).'%" or company_out like "%'.trim($post['title']).'%" or company_in like "%'.trim($post['title']).'%")' );
                        }
                    }

                    if ($post['type'] != 1){
                        $this->db->where("(substring(a.company_single_name FROM 1 FOR 1) between $lower or substring(a.company_single_name FROM 1 FOR 1) between $upper $numeric_filter)");
                    }
         
                    $this->db->where_in('a.category_id',array('1','3','4'));
                    $this->db->where('a.status_id',1);
                    $this->db->where('a.is_delete',0);              
                    $this->db->select('company_single_name as name , 
                                       category_id as member_category_id,
                                       company_id,
                                       firstname,
                                       lastname,
                                       prefix_name,
                                       company_out as name_out,
                                       company_in as name_in,
                                       company_invis as is_invis_company,
                                       status_id as status_payment_id,
                                       company_single_name,
                                       uri_path,
                                       uri_path_name_out
                                        ',false);

                    $this->db->order_by('a.company_single_name','asc');

                    $data['list_data']              = $this->member_model->findViewBy($where);

                break;

                case '4':
                case '3': // individu

                    if ($post['type'] != 2){
                        if ($post['title']){
                            // check name dan nama company                 
                            $post['title'] = query_kutip(trim($post['title']));
                            $this->db->where('(member_full_name like "%'.trim($post['title']).'%" or company_out like "%'.trim($post['title']).'%" or company_in like "%'.trim($post['title']).'%")' );

                        }
                    }

                    if ($post['type'] != 1){
                        $this->db->where("(substring(a.lastname FROM 1 FOR 1) between $lower or substring(a.lastname FROM 1 FOR 1) between $upper)");
                    }
         
                    $this->db->where_in('a.category_id',array('1','2','3'));                
                    $this->db->where('a.status_id',1);
                    $this->db->where('a.is_delete',0);
                    $this->db->where('a.is_block',0);

                    $this->db->select('CONCAT(lastname,",",firstname) as name , 
                                       category_id as member_category_id,
                                       company_id,
                                       firstname,
                                       lastname,
                                       prefix_name,
                                       company_out as name_out,
                                       company_in as name_in,
                                       company_invis as is_invis_company,
                                       status_id as status_payment_id,
                                       company_single_name,
                                       uri_path,
                                       uri_path_name_out
                                        ',false);

                    $this->db->order_by('a.lastname','asc');

                    $data['list_data'] = $this->member_model->findViewBy($where);

                break;
                
                default:
                    return false;
                break;
            }
            
            //end 
            if ($id == 3) {
                $i = 0;
                foreach ($data['list_data']  as $key => $value) {

                    $value['name_out'] = trim($value['name_in']);

                    $data['list_data'][$key]['sort_data'] = ++$i;
                    $data['list_data'][$key]['name_data'] = $value['name'];
                    $data['list_data'][$key]['data_id']   = $value['id'];
                    
                    switch ($value['member_category_id']) {
                        case '1':
                        case '4':
                        case '3':
                            $uri_path                            = $value['uri_path']; 
                            $data['list_data'][$key]['url_data'] = ($user_sess_data) ? base_url_lang().'/directorys/member/individu/'.$uri_path : '' ;
                            $data['list_data'][$key]['alert']    = ($user_sess_data) ? '' : 'forbidden-non-member' ;
                            break;
                        case '2':
                            $uri_path                            = $value['uri_path'];
                            $data['list_data'][$key]['url_data'] = base_url_lang().'/directorys/member/individu/'.$uri_path;
                            $data['list_data'][$key]['alert']    = ($user_sess_data) ? '' : 'forbidden-non-member' ;         
                            break;
                    }
                }

            }else{
                $i = 0;
                foreach ($data['list_data']  as $key => $value) {

                    $value['name_out'] = trim($value['name_in']);

                    $data['list_data'][$key]['sort_data'] = ++$i;
                    $data['list_data'][$key]['name_data'] = $value['name'];
                    $data['list_data'][$key]['data_id']   = $value['id'];
                    
                    switch ($value['member_category_id']) {
                        case '1':
                        case '4':
                        case '3':
                            $uri_path                            = $value['uri_path_name_out']; 
                            $data['list_data'][$key]['url_data'] = ($user_sess_data) ? base_url_lang().'/directorys/member/company/'.$uri_path : '' ;
                            $data['list_data'][$key]['alert']    = ($user_sess_data) ? '' : 'forbidden-non-member' ;  
                            break;

                        case '2':
                            $uri_path                            = $value['uri_path'];
                            $data['list_data'][$key]['url_data'] = base_url_lang().'/directorys/member/individu/'.$uri_path;
                            $data['list_data'][$key]['alert']    = '';                      
                            break;
                    }
                    if ($id == 1) {
                        $data['list_data'][$key]['url_data'] = "";
                        $data['list_data'][$key]['alert']    = "";  
                    }
                }
            }

            // kalo nyari company di remove nama company yang sama
            if ($id == '2'){
                $data['list_data'] = unique_multidim_array($data['list_data'],'name');
            }

            if ($ret) {
                $data["toggle_rule"] = ($user_sess_data) ? 'data-toggle="collapse"' : 'data-toggle="collapse"';
                // $data["toggle_rule"] = ($user_sess_data) ? 'data-toggle="collapse"' : '';

                $temp = $this->parser->parse("directory/search.html",$data,true); 
                echo json_encode($temp);
            }else{
                return $data['list_data'];
            }
        } else {
                return $data['list_data'];
       }
    }

    function uri_path_search_directory(){
        $user_sess_data       = $this->session->userdata('MEM_SESS');
        $post                 = purify($this->input->post());
        $type_member_selected = $post['type_member_selected'];

        $id_parent_sector = $this->sector_model->get_idotherparent();
       
        if ($post['id'] == $id_parent_sector) {
            $this->db->select('group_concat(a.id) as id_sector_all');
            $id_other_child = $this->sector_model->fetchRow(array('is_other'=>1))['id_sector_all'];
            $id_other_child = explode(',', $id_other_child);
            $this->db->where_in('a.id', $id_other_child);

        }else{
            $id           = $post['id'];        
            $sector_model = $this->sector_model->fetchRow(array('id'=>$id));
            $this->db->where('a.id', $sector_model['id']);
        }

        // if ($post['title']){
        //     $this->db->where("CONCAT(name) LIKE '%".$post['title']."%'", NULL, FALSE);
        //     $this->db->or_where("CONCAT(company_name) LIKE '%".$post['title']."%'", NULL, FALSE);
        //     $this->db->or_where("CONCAT(member_name) LIKE '%".$post['title']."%'", NULL, FALSE);
        //     // $this->db->where("CONCAT(b.firstname, ' ', b.lastname) LIKE '%".$post['title']."%'", NULL, FALSE);
        // }

        // $this->db->where('member_is_delete', 0);
        $this->db->where('is_delete', 0);
        $this->db->where('is_delete_member', 0);
        $this->db->where('member_is_block', 0);
        $this->db->where('is_delete_company', 0);
        $this->db->where('is_delete_tag', 0);
        $this->db->where('company_invis', 0);

        $this->db->_protect_identifiers = FALSE;
        $this->db->order_by('company_name_in asc, FIELD(member_category_id, 1,4,2,3)');
        $this->db->_protect_identifiers = TRUE;
        // $this->db->where('a.member_invis', 0);
        // $this->db->group_by('member_category_id');
        // $this->db->order_by('uri_path','asc');
        // $this->db->join('auth_member b',"b.id = a.company_id",'left');
        // $this->db->join('company c',"c.id = b.company_id",'left');
        // $this->db->select("b.*,c.is_invis_company");
        // $this->db->where("(company_name_in like '%".$post['title']."%'
             
        //      or member_name like '%".$post['title']."%'             
        //      or representative like '%".$post['title']."%')");
        $member_commiteee = $this->sector_model->findviewBy();
        // print_r($member_commiteee);
        // exit;    
        
    
        $counter_member_committee = count($member_commiteee);

        // if ($counter_member_committee == 0 && $type_member_selected != 3 && $type_member_selected != 2){
        if ($counter_member_committee == 0){
            $ret = '<div class="col-sm-4 col-md-4 col-xs-12">Not Found</div>';
        } else {
            foreach ($member_commiteee as $key => $value) {
                $member_commiteee[$key]['id']               = $value['id'];

                if ($value['member_category_id'] == 1 or $value['member_category_id'] == 4 or $value['member_category_id'] == null) { // company
                    // $img_company  = 


                    $type_member = 'company';

                    $uri_path    = $value['company_uri_path'];

                    if (imageProfile($value['company_img'],'company',1)) {
                        $name_show   = "";
                        $img_company  = imageProfile($value['company_img'],'company');
                    }else{
                        $name_show   = $value['company_name_in'];
                        $img_company  = "";
                    }

                }else{
                    $img_company  = '';
                    $type_member = 'individu';
                    // $uri_path    = generate_url(full_name($value));
                    $uri_path    = $value['member_uri_path'];
                    $name_show   = $value['company_name_in'];
                }

                //jikaUserBelumLoginMakaAlertInformationNotAvailableForNon-members&bukanTypeCompany
                if($user_sess_data || $type_member == 'company'){
                    $link  = base_url_lang().'/directorys/member/'.$type_member.'/'.$uri_path;
                    $alert = '';
                } else {
                    $link  = '#';
                    $alert = ' forbidden-non-member';
                }


                $ret .= '<div class="col-sm-4 col-md-4 col-xs-12"><a href="'.$link.'" ><div class="thumbnail-amcham thumb-gallery thumb-logo'.$alert.'">'.$name_show.'<img src="'.$img_company.'""></div></a></div>';                    
            }
        }

        echo json_encode($ret);
    }

    function member($type,$uri_path){
        $id_lang            = id_lang();
        $user_sess_data = $this->session->userdata('MEM_SESS');
        if ($uri_path == "") {
            redirect('directorys','refresh');
        }

        if ($type == 'company') {
            $page         = 'detail_company';
            $data_company = $this->company_model->findBy(array('uri_path_name_out'=>$uri_path),1);

            if (empty($data_company)) {
                redirect('directorys','refresh');       
            }
            // $this->db->where('(member_category_id = 3 or member_category_id = 1)');
            $this->db->where('company_id', $data_company['id']);
            $this->db->where("is_invis = 0 and 
                              is_delete = 0 and 
                              is_block = 0 " );
            $this->db->order_by('member_category_id','asc');
            $this->db->order_by('isnull(sort)','asc');
            $this->db->order_by('sort','asc');
            $this->db->order_by('firstname','asc');
            $data_re      = $this->member_model->findBy();
            if ($data_re) {
                foreach ($data_re as $key => $value) {
                    // imageProfile($img,'representative')
                    // $temp_re['re_img']         = $value['img'] ? base_url().'images/member/representative/'.$value['img']:'';
                    $temp_re['re_img']         = imageProfile($value['img'],'representative');
                    $temp_re['re_name']        = full_name($value,1); 
                    $temp_re['re_job']         = $value['job']; 
                    $temp_re['re_m_t_number']  = $value['m_t_number']; 
                    $temp_re['re_email']       = $value['email']; 
                    $temp_re['re_address']     = empty($value['address_member']) ? '': $value['address_member']; 
                    // $temp_re['re_linkedin_id'] = $value['linkedin_id']; 
                    $data['representative'][]  = $temp_re;
                }
            }else{
                $data['no_re'] = 'hide';
            }
            $data['bread_fullname'] = $data_company['name_in'];

        }else if($type == 'individu'){
            $data = $this->member_model->findBy(array('uri_path'=>$uri_path),1);
            if (empty($data)) {
                die('Data not found');
                // redirect('directorys','refresh');
            }
            // $data               = $this->member_model->fetchRow(array('a.uri_path'=>$uri_path));
            $data_view          = $this->member_model->findViewBy(array('member_id'=>$data['id']),1);
            $data_company       = $this->company_model->findBy(array('id'=>$data['company_id']),1);
     
            $data['bread_fullname'] = full_name($data,1);
            $page = 'detail_individu';
        }else{
            redirect('directorys','refresh');
        }
        $temp_com['company_t_number']     = $data_company['t_number'] ;

        $this->db->select('group_concat(sector_id) as sector_id_concat')->group_by('company_id');
        $id_sector =  $this->auth_member_sector_model->findBy(array('company_id'=>$data_company['id']),1)['sector_id_concat']; 

        if(!empty($id_sector)){
            $this->db->select("group_concat(a.name) as sector_group");
            $this->db->where_in('a.id',explode(',',$id_sector));
            $temp_com['company_sector'] = $this->sector_model->findBy()[0]['sector_group'];

        }else{
            $temp_com['company_sector'] = "";
        }
        
        $temp_com['company_fullname']    = $data_company['name_in'];
        $temp_com['company_address']     = $data_company['address'];
        $array_company_website = explode(',', $data_company['website']);
        if ($array_company_website) {
            foreach ($array_company_website as $key => $value) {
                $temp_com['company_website']   .= "<a href=https://".trim($value)." target='__blank'>".$value."</a>,";
            }
            $temp_com['company_website'] = substr($temp_com['company_website'], 0, -1);
        }
        // $temp_com['company_website']     = $data_company['website'];
        // $temp_com['company_url']         = 'http://'.$data_company['website'];

        if (imageProfile($data_company['img'],"company",1)) {
            $temp_com['company_img']  = imageProfile($data_company['img'],'company');
            $temp_com['company_fullname_img']   = "";
        }else{
            $temp_com['company_fullname_img']   = $data_company['name_in'];
            $temp_com['company_img']  = "";
        }

        // $temp_com['company_img']         = imageProfile($data_company['img'],"company");
        $temp_com['company_description'] = $data_company['description'];
        $temp_com['dsp_company_description'] = empty($data_company['description']) ? 'style="border:none;"':'';
        $data['data_company'][]          = $temp_com;
        
        $temp_mem['member_name']         = full_name($data,1);
        $temp_mem['member_img']          = imageProfile($data['img'],"individu");
        $temp_mem['member_job']          = $data['job'];
        $temp_mem['member_email']        = $data['email'];
        $temp_mem['member_t_number']     = $data_company['t_number'];
        // $temp_mem['member_m_number']     = $data_company['m_number'];
        // $temp_mem['member_linkedin_id']  = $data['linkedin_id'];
        $temp_mem['member_address']      = $data['address_member'];
        $temp_mem['member_website']      = $data_company['website'];
        $data['data_member'][]           = $temp_mem;



      
        // $data['member_full_name'] = 1;

        //partners
        // $this->db->group_by('id_partners_category');
        // $data['partners'] = $this->aboutpartnersmodel->findby(array('id_status_publish' => 2));
        //    foreach ($data['partners'] as $key => $value1) {
        //     $data['partners'][$key]['img'] = image($value1['img'],'small');
        //     $data['partners'][$key]['url'] = $value1['url'] ? $value1['url'] : '#';
        // }

        $data['dsp_error'] = in_array($user_sess_data['status'], array(1,5)) ? 'hidden' : ''; 
        $data['dsp_error_expired'] = $user_sess_data['status'] == 5 ? '' : 'hidden'; 
        $data['dsp_user_detail'] = $user_sess_data['status'] == 1 ?'':'hidden';
        $data['seo_title']        = ($data['seo_title'] == "") ? "MJM" : $data['seo_title'];
        $data['meta_description'] = ($data['meta_description'] == '') ? "MJM" : $data['meta_description'];
        $data['meta_keywords']    = ($data['meta_keywords'] == '') ? "MJM" : $data['meta_keywords'];
        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar(); //pake sidebar
        render('directory/'.$page,$data);
        
    }
    function session_back(){
        $post = $this->input->post();
        $this->load->helper('cookie');
        $date_of_expiry = time() * 1 * 24 * 60 * 60 ;
        setcookie( "id_directory_active_dropdown", $post['id'], $date_of_expiry , '/');
        echo json_encode(array('data' => $_COOKIE['id_directory_active_dropdown'] ));
    }
    function session_check(){
        $this->load->helper('cookie');

        $user_sess_data = $this->session->userdata('MEM_SESS');
        if ($_COOKIE['id_directory_active_dropdown'] && $user_sess_data) {
            $ret['id']      = $_COOKIE['id_directory_active_dropdown'] ? $_COOKIE['id_directory_active_dropdown'] :'2';
        }else{
            $ret['id']      = $_COOKIE['id_directory_active_dropdown'] ? $_COOKIE['id_directory_active_dropdown'] :'1';
        }
        echo json_encode($ret);
    }

}