<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tags extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('newsmodel');
        $this->load->model('newscategorymodel');
        $this->load->model('newstagsmodel');
        $this->load->model('frontendmenumodel');
        $uri_path       = $this->uri->segment(4);
        // $this->parentMenu = getParentMenu();
        // echo $this->parentMenu;exit;
    }
    function index(){
        $lang           = $this->uri->segment(1);
        $uri_path       = $this->uri->segment(4);
        $page           = $this->uri->segment(5);
        $id_lang        = id_lang();

                            $this->db->select('a.*,b.name as tags');
                            $this->db->join('tags b','b.id = a.id_tags');

                            $this->db->where('b.uri_path',$uri_path);
                            $this->db->where('a.id_lang',$id_lang);
                            // $this->db->where('a.publish_date <=',date('Y-m-d'));

        $data           =  $this->db->get_where('view_tags_content a')->result_array();
        // echo $this->db->last_query();exit();
        if($data){
            $total = count($data);

            $data['tags'] = db_get_one('tags','name',array('uri_path'=>$uri_path));

                            $this->db->select('a.*,b.name as tags');
                            $this->db->join('tags b','b.id = a.id_tags');

                            $this->db->where('b.uri_path',$uri_path);
                            $this->db->where('a.id_lang',$id_lang);
                            $this->db->where('a.publish_date <=',date('Y-m-d'));
                            
                            $this->db->order_by('publish_date','desc');
                            $this->db->limit(PAGING_PERPAGE,$page);

            $news        =  $this->db->get_where('view_tags_content a')->result_array();

           foreach ($news as $key => $value) {
                $news[$key]['news_title']       = $value['title'];
                $news[$key]['publish_date']     = iso_date($value['publish_date']);
                $news[$key]['uri_path_detail']  = $value['uri_path'];
                $news[$key]['show_img']         = getImg($value['img'],'small');
                $news[$key]['url']              = site_url($value['status']);
                $news[$key]['detail']           = ($value['status']=='gallery')? 'detailphoto' : 'detail';
                $news[$key]['isImg']            = ($news[$key]['show_img'])? '' : 'hide';
            }

            // $data['url']            = site_url("news");
            $data['list_news']      = $news;
            $config['base_url']     = site_url("tags/index/$uri_path/");
            $config['total_rows']   = $total;
            $config['per_page']     = PAGING_PERPAGE;
            $config['uri_segment']  = 5;
            $this->load->library('pagination');
            $this->pagination->initialize($config);
            $data['paging'] = $this->pagination->create_links(); 
            render('tags/tags',$data);
        } else {
            render('tags/tags',$data);
            // die('404');
        }
    }
}