<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Search extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('searchModel');
        $this->load->model('newsmodel');
	}
    function index(){
        $lang           = $this->uri->segment(1);
        $page           = $this->uri->segment(4);

        $get =  purify($this->input->get(NULL,TRUE));    

        $d              = $get['day'] ;
        $m              = $get['month'] ;
        $y              = $get['year'] ;
        $keyword        = $get['search'];

        $data['paging'] = PAGING_PERPAGE;
        $data['search'] = $keyword;

        $list_day = "<option value=''>Date</option>";
        for($i=1;$i<=31;$i++){
            $selected  = $i==$d ? 'selected' : '';
            $list_day .= "<option $selected value='$i'>$i</option>";
        }
        $data['list_year']  = list_year($y,10);
        $data['list_month'] = list_month($m);
        $data['list_day']   = $list_day;


        if ($keyword){
            $where['keyword'] = $keyword;
        }
        // $total = $this->searchModel->findBy($where,'all');

        $this->db->limit(PAGING_PERPAGE,$page);
        $news = $this->searchModel->findBy($where);

        $data['keyword_search'] = count($news) == 0 ? ': "'.$keyword.'" Not Found' : ': "'.$keyword.'" ';
        foreach ($news as $key => $value) {
            $news[$key]['start_date']      = iso_date($value['start_date']);
            $news[$key]['publish_date']    = iso_date($value['publish_date']);
            $news[$key]['cat']             = $value['news_category'];
            $news[$key]['name']            = $value['name'];
            $news[$key]['uri_path_detail'] = $value['uri_path'];
            $news[$key]['module']          = $value['module'];
            $news[$key]['teaser']          = $value['description'];
            $news[$key]['show_img']        = getImg($value['img'],'small');
        }
        $this->db->limit(PAGING_PERPAGE,$page+PAGING_PERPAGE);
        $data['dsp_load_more'] = ($this->searchModel->findBy($where,0,$post) ) ? '' : 'hide';


        $data['url']            = site_url("news");
        $data['list_news']      = $news;

        if($data['seo_title'] == ''){
            $data['seo_title'] = "MJM";
        }

        if($data['meta_description'] == ''){
            $data['meta_description'] = "MJM";
        }

        if($data['meta_keywords'] == ''){
            $data['meta_keywords'] = "MJM";
        }

        $data['page_heading']   = 'Search';
        $data['banner_top']     = banner_top();
        
        $data['widget_sidebar'] = widget_sidebar(); //pake sidebar
        $data['banner_top']     = banner_top(); // pake banner top

        render('search/index',$data);
    }

    function search_data($ret){
        $post           = purify($this->input->post(NULL,TRUE));
        $m              = $post['month'] ;
        $y              = $post['year'] ;
        $data['search'] = $post['search'];
        $page           = $post['page'];
       
        // if ($post['news_title']){
        //     $post['keyword'] = mysql_escape_string($data['keyword']);
        // }
        // if ($post['month']){
        //      $post['month'] = $m;
        // }

        // if ($post['year']){
        //      $post['year'] = $y;
        // }

        $this->db->limit(PAGING_PERPAGE,$page);

        $data['list_news'] = $this->searchModel->findViewBy($where,0,$post);    
        // print_r($this->db->last_query());exit;
        $data['keyword_search'] = count($news) == 0 ? ': "'.$get['search'].'" Not Found' : ': "'.$get['search'].'" ';
        
     
        foreach ($data['list_news'] as $key => $value) {
            $data['list_news'][$key]['start_date']      = iso_date($value['start_date']);
            $data['list_news'][$key]['publish_date']    = iso_date($value['publish_date']);
            $data['list_news'][$key]['cat']             = $value['news_category'];
            $data['list_news'][$key]['uri_path_detail'] = $value['uri_path'];
            $data['list_news'][$key]['module']          = $value['module'];
            $data['list_news'][$key]['show_img']        = getImg($value['img'],'small');
            $data['list_news'][$key]['name']            = $value['name'];
            $data['list_news'][$key]['teaser']          = $value['description'];
            $data['list_news'][$key]['uri_path_detail'] = $value['uri_path'];
        }

        $data['paging']        = $page+PAGING_PERPAGE;
                                 $this->db->limit(PAGING_PERPAGE,$page+PAGING_PERPAGE);
        $data['dsp_load_more'] = ($this->searchModel->findViewBy($where,0,$post) ) ? '' : 'hide';
        if ($ret) {            
            render('search/search_data',$data,'blank');
        }else{
            render('search/search_data',$data,'blank');
        }        
    }
}