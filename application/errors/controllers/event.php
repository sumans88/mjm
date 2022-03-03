<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Event extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('eventModel');
        $this->load->model('eventCategoryModel');
		
	}
    function index(){
        $lang           = $this->uri->segment(1);
        $uri_path       = $this->uri->segment(4);
        $page           = $this->uri->segment(5);
        $id_lang        = id_lang();
        $data = $this->eventCategoryModel->fetchRow(array('uri_path'=>$uri_path,'a.id_lang'=>$id_lang));
        if(!$data){
            $data = $this->eventCategoryModel->fetchRow(array('uri_path'=>$uri_path));
        }
        if($id_lang != $data['id_lang']){
            $id = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(id_parent_lang = '$id' or id = '$id')");
            $datas = $this->eventCategoryModel->findBy();
            foreach ($datas as $key => $value) {
                if($value['id_lang'] == $id_lang){
                    redirect(base_url("$lang/event/index/$value[uri_path]/$page"));
                }
            }
        }
        $where['a.id_lang']     = $id_lang;
        $where['b.uri_path']    = $uri_path;
        // $where['start_date >']    = date('Y-m-d');

        $bulan  = array(1=>'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $this->db->order_by('start_date','desc');
        // $this->db->limit(1,0);
        $top    = $this->eventModel->fetchRow($where);
        // echo $this->db->last_query();
        if($top){
            $data['top_start_date_day']     = substr($top['start_date'], 8);
            $data['top_start_date_month']   = $bulan[(int)substr($top['start_date'], 5,2)];
            $data['top_teaser']             = $top['teaser'];
            $data['top_img']                = image($top['img'],'large');
            $data['banner_img']             = $top['img'];
            $data['top_view']               = '';
            $data['top_url']              = site_url("event/detail/$top[uri_path]");
            $data['dsp_no_event']           = 'hide';
        }
        else{
            $data['top_view']               = 'hide';
            $data['dsp_no_event']           = '';
        }

        $this->db->limit(3,1);
        $this->db->order_by('start_date','desc');
        $top3    = $this->eventModel->findBy($where);
        // echo $this->db->last_query();
        foreach ($top3 as $key => $value) {
            $top3[$key]['top3_title']   = $value['name'];
            $top3[$key]['top3_teaser']  = $value['teaser'];
            $top3[$key]['top3_category']= $value['category'];
            $top3[$key]['top3_url']     = site_url("event/detail/$value[uri_path]");
            $top3[$key]['top3_img']     = image($value['img'],'small');
            $top3[$key]['top3_day']     = substr($value['start_date'], 8);
            $top3[$key]['top3_month']   = $bulan[(int)substr($value['start_date'], 5,2)];
        }
        $data['top3'] = $top3;

        $this->db->limit(3,4);
        $this->db->order_by('start_date','desc');
        $event = $this->eventModel->findBy($where);
        foreach ($event as $key => $value) {
            $event[$key]['event_url']     = site_url("event/detail/$value[uri_path]");
            $event[$key]['event_day']     = substr($value['start_date'], 8);
            $event[$key]['event_name']    = $value['name'];
            $event[$key]['event_month']   = $bulan[(int)substr($value['start_date'], 5,2)];
        }
        $data['event']    = $event;


        /*
        $total          = count($this->eventModel->findBy(array('a.id_lang'=>$id_lang,'b.uri_path'=>$uri_path)));
        $this->db->limit(PAGING_PERPAGE,$page);
        $event          = $this->eventModel->findBy(array('a.id_lang'=>$id_lang,'b.uri_path'=>$uri_path));
        foreach ($event as $key => $value) {
            $event[$key]['start_date']       = iso_date($value['start_date']);
            $event[$key]['uri_path_detail']  = $value['uri_path'];
            $event[$key]['event_name']      = $value['name'];
            $event[$key]['show_img']              = getImg($value['img'],'small');
        }
        $data['url']            = site_url("event");
        $data['list_event']     = $event;
        $config['base_url']     = site_url("event/index/$uri_path/");
        $config['total_rows']   = $total;
        $config['per_page']     = PAGING_PERPAGE;
        $config['uri_segment']  = 5;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['paging'] = $this->pagination->create_links();
        */
        render('event/index',$data);
    }

    /*function index($page=0){
        $this->load->model('eventModel');
        $lang           = $this->uri->segment(1);
        $currentLang    = id_lang(); 
        $total          = count($this->eventModel->findBy(array('id_lang'=>$currentLang)));
        $this->db->order_by('start_date','desc');
        $this->db->limit(PAGING_PERPAGE,$page);
        $event          = $this->eventModel->findBy(array('id_lang'=>$currentLang));
        foreach ($event as $key => $value) {
            $event[$key]['start_date'] = iso_date($value['start_date']);
        }
        $data['url']            = site_url("event");
        $data['list_event']     = $event;

        $config['base_url']     = site_url('event/index/');
        $config['total_rows']   = $total;
        $config['per_page']     = PAGING_PERPAGE;
        $config['uri_segment']  = 4;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['paging'] = $this->pagination->create_links();
        render('event/index',$data);
    }*/

    function detail($uri_path){
        $this->load->model('eventModel');
        $lang           = $this->uri->segment(1);
        $currentLang    = id_lang();
        $data           = $this->eventModel->fetchRow(array('a.uri_path'=>$uri_path,'a.id_lang'=>$currentLang));
        $today          = date('Y-m-d');
        if ($data['is_open'] != 1 || $data['end_date'] <  $today) { //asumsinya selama event masih berlangsung, walaupun hari terakhir masih boleh daftar
            $data['hide'] = 'hidden';
        }
        else {
            $data['hide'] = '';
        }
        if(!$data){
            $data       = $this->eventModel->fetchRow(array('uri_path'=>$uri_path));
        }
        if($currentLang != $data['id_lang']){
            $id          = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(a.id_parent_lang = '$id' or a.id = '$id')");
            $datas       = $this->eventModel->findBy();
            foreach ($datas as $key => $value) {
                if($value['id_lang'] == $currentLang){
                    redirect(base_url("$lang/pages/$value[uri_path]"));
                }
            }
        }
        if(!$data){
            $data['page_name'] = 'page not found';
            $data['page_content'] = 'error 404. page not found';

        }
        $start  = explode('-', $data['start_date']);
        $startY = $start[0];
        $startM = $start[1];
        $startD = $start[2];
        $end    = explode('-', $data['end_date']);
        $endY  = $end[0];
        $endM  = $end[1];
        $endD  = $end[2];
        $month['in'] = array(1=>'Januari','Pebruari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
        $month['en'] = array(1=>'January','February','March','April','May','June','July','August','September','October','November','December');

        // $data['end_date'] = $data['start_date'];
        if($data['start_date'] == $data['end_date']){ //1 hari
            $event_date = "$startD ".$month[$lang][(int)$startM].' '.$startY;
        }
        else if($startY==$endY && $startM == $endM){ // di bulan dan tahun yg sama
            $event_date = "$startD - $endD ".$month[$lang][(int)$startM].' '.$startY;
        }
        else if($startY==$endY && $startM != $endM){ // beda bulan
            $event_date = "$startD ".$month[$lang][(int)$startM]." - $endD ". $month[$lang][(int)$endM] . ' '.$startY;
        }
        else{ // beda semua
            $event_date = "$startD ".$month[$lang][(int)$startM]."$startY - $endD ". $month[$lang][(int)$endM] . ' '.$endY;
        }


        $data['event_date']     = $event_date;
        $data['controller']     = site_url("event");
        $data['uri_path']       = $uri_path;
        $data['suksesRegister'] = $this->session->flashdata('suksesRegister');
        render('event/detail',$data);
    }
    function proses_event($id,$uri_path){
        $post                   = $this->input->post();  
        $post['event_id']       = $id;
        unset($post['submit']);
        
        // $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|email');
        if ($this->form_validation->run() == FALSE){
            echo ' '.validation_errors(' ','<br>');
        }
        else{ 
            $this->db->trans_start();
            $this->db->insert('event_participant',$post);
            // echo $this->db->last_query();exit;
            $this->db->trans_complete(); 
            $msg = '1Insert Success'; 
            echo $msg;
        }
        $this->session->set_flashdata('suksesRegister','terimakasih telah mendaftar. kami akan segera menghubungi anda.');
        redirect(site_url("event/detail/$uri_path"));
    }
    
}