<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index(){
        $this->load->model('slideshowmodel');
        $this->load->model('eventmodel');
        $this->load->model('newsmodel');
        $id_lang            = id_lang();
        $where['a.id_lang'] = $id_lang;
        $data['slideshow']  = $this->slideshowmodel->findBy($where);

        $where['a.start_date >']  = date ('Y-m-d');
        $this->db->order_by('a.start_date','asc');
        $this->db->limit(4);
        $data['list_event'] = $this->eventmodel->findBy($where);


        // news pertama 
        $w['a.id_lang'] = $id_lang;
        $this->db->order_by('publish_date','desc');
        $this->db->limit(1);
        $news_first = $this->newsmodel->findBy($w);    
        foreach ($news_first as $key => $value) {
            // print_r($value);
            $news_list4[$key]['publish_date']   = iso_date($value['publish_date']);
            $news_list4[$key]['news_title']     = $value['news_title'];
            $news_list4[$key]['teaser']         = $value['teaser'];
            // $news_list[$key]['cat']     = site_url("event/detail/$value[uri_path]");
            $news_list4[$key]['img']            = image($value['img'],'small');
            $news_list4[$key]['cat']            =  $value['category'];
        } 
        $data['nu'] = $news_list4;

        // end news pertama 

        // news kedua 
        $w['a.id_lang'] = $id_lang;
        $this->db->order_by('publish_date','desc');
        $this->db->limit(1,1);
        $news2 = $this->newsmodel->findBy($w);   
        foreach ($news2 as $key => $value) {
            $news_list1[$key]['publish_date']   = iso_date($value['publish_date']);
            $news_list1[$key]['news_title']  = $value['news_title'];
            $news_list1[$key]['teaser']= $value['teaser'];
            // $news_list[$key]['cat']     = site_url("event/detail/$value[uri_path]");
            $news_list1[$key]['img']     = image($value['img'],'small');
            $news_list1[$key]['cat']     =  $value['category'];
        }
        $data['news_kedua'] = $news_list1;

        // end news kedua 
        $w['a.id_lang'] = $id_lang;
        $this->db->order_by('publish_date','desc');
        $this->db->limit(2,2);
        $news = $this->newsmodel->findBy($w);   
        foreach ($news as $key => $value) {
            $news_list[$key]['publish_date']   = iso_date($value['publish_date']);
            $news_list[$key]['news_title']  = $value['news_title'];
            $news_list[$key]['teaser']= $value['teaser'];
            // $news_list[$key]['cat']     = site_url("event/detail/$value[uri_path]");
            $news_list[$key]['img']     = image($value['img'],'small');
            $news_list[$key]['cat']     =  $value['category'];
            $news_list[$key]['top3_month']   = $bulan[(int)substr($value['start_date'], 5,2)];
        }
        $data['list_news'] = $news_list;

        //editor choice
        $w['a.id_lang']             = $id_lang;
        $w['a.is_editor_choice']    = 1;
        $this->db->limit(4);
        $ec = $this->newsmodel->findBy($w);   
        foreach ($ec as $key => $value) {
            $ec_list[$key]['publish_date']    = iso_date($value['publish_date']);
            $ec_list[$key]['news_title']      = $value['news_title'];
            $ec_list[$key]['teaser']          = $value['teaser'];
            $ec_list[$key]['img']             = image($value['img'],'small');
            $ec_list[$key]['cat']             = $value['category'];
            $ec_list[$key]['url']             = site_url("news/detail/$value[uri_path]");
        }
        $data['list_ec'] = $ec_list;


        render('home',$data);
    }
    function sitemap(){
        $data['sitemap']  = header_menu();
        $data['sitemap'] .= footer_menu();
        render('sitemap',$data);
    }
}