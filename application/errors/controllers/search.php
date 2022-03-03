<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Search extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('searchModel');
	}
    function index(){
        $lang           = $this->uri->segment(1);
        $page           = $this->uri->segment(4);
        $data['keyword']= htmlspecialchars(strip_tags($_GET['q']));
        $total          = $this->searchModel->findBy(array('lang'=>$lang,'keyword'=>$data['keyword']),'all' );
        $this->db->limit(PAGING_PERPAGE,$page);
        $news          = $this->searchModel->findBy(array('lang'=>$lang,'keyword'=>$data['keyword']));
        // print_r($news);
        foreach ($news as $key => $value) {
            $news[$key]['start_date']           = iso_date($value['start_date']);
            $news[$key]['publish_date']         = iso_date($value['publish_date']);
            $news[$key]['cat']                  = $value['news_category'];
            $news[$key]['uri_path_detail']      = $value['uri_path'];
            $news[$key]['show_img']              = getImg($value['img'],'small');
        }
        $data['url']            = site_url("news");
        $data['list_news']      = $news;
        $config['base_url']     = site_url("search/index/");
        $config['total_rows']   = $total;
        $config['per_page']     = PAGING_PERPAGE;
        $config['uri_segment']  = 4;
        $config['suffix']       = '?q='.$data['keyword'];
        $config['first_url']    = $config['base_url'] . $config['suffix'];
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['paging'] = $this->pagination->create_links();
        render('search',$data);
    }
    
    
}