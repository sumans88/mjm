<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class News extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('newsmodel');
        $this->load->model('newscategorymodel');
        $this->load->model('newstagsmodel');
    }
    function index(){
        $lang           = $this->uri->segment(1);
        $uri_path       = $this->uri->segment(4);
        $page           = $this->uri->segment(5);
        $id_lang        = id_lang();
        $data = $this->newscategorymodel->fetchRow(array('uri_path'=>$uri_path,'a.id_lang'=>$id_lang));
        if(!$data){
            $data = $this->newscategorymodel->fetchRow(array('uri_path'=>$uri_path));
        }
        if($id_lang != $data['id_lang']){
            $id = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(id_parent_lang = '$id' or id = '$id')");
            $datas = $this->newscategorymodel->findBy();
            foreach ($datas as $key => $value) {
                if($value['id_lang'] == $id_lang){
                    redirect(base_url("$lang/news/index/$value[uri_path]/$page"));
                }
            }
        }

        $total          = count($this->newsmodel->findBy(array('a.id_lang'=>$id_lang,'b.uri_path'=>$uri_path)));
        $this->db->order_by('publish_date','desc');
        $this->db->limit(PAGING_PERPAGE,$page);
        $news          = $this->newsmodel->findBy(array('a.id_lang'=>$id_lang,'b.uri_path'=>$uri_path));
        foreach ($news as $key => $value) {
            $news[$key]['start_date']       = iso_date($value['start_date']);
            $news[$key]['uri_path_detail']  = $value['uri_path'];
            $news[$key]['show_img']              = getImg($value['img'],'small');
        }
        $data['url']            = site_url("news");
        $data['list_news']     = $news;
        $config['base_url']     = site_url("news/index/$uri_path/");
        $config['total_rows']   = $total;
        $config['per_page']     = PAGING_PERPAGE;
        $config['uri_segment']  = 5;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['paging'] = $this->pagination->create_links();
        render('news/index',$data);
    }

    function detail(){
        $lang = $this->uri->segment(1);
        $uri_path = $this->uri->segment(4);
        $this->load->model('newsmodel');
        $this->load->model('newstagsmodel');
        $currentLang = id_lang();
        $data = $this->newsmodel->fetchRow(array('a.uri_path'=>$uri_path,'a.id_lang'=>$currentLang));
        if(!$data){
            $data = $this->newsmodel->fetchRow(array('a.uri_path'=>$uri_path));
        }
        // unset($data['page_content'],$data['teaser']);
        // print_r($data);exit;
        if($currentLang != $data['id_lang']){
            $id = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(a.id_parent_lang = '$id' or a.id = '$id')");
            $datas = $this->newsmodel->findBy();
            foreach ($datas as $key => $value) {
                if($value['id_lang'] == $currentLang){
                    redirect(base_url("$lang/news/detail/$value[uri_path]"));
                }
            }
        }
        if(!$data){
            $data['page_name'] = 'page not found';
            $data['page_content'] = 'error 404. page not found';
        }
        $tags               = $this->newstagsmodel->findBy(array('id_news'=>$data['id']));
        $data['img']        = getImg($data['img'],'large');
        $data['video']      = getVideo($data['link_youtube_video']);
        $data ['list_tags'] = $tags;
        render('news/detail',$data);
    }


    function tags(){
        $lang           = $this->uri->segment(1);
        $uri_path       = $this->uri->segment(4);
        $page           = $this->uri->segment(5);
        $id_lang        = id_lang();
        $data           = $this->newstagsmodel->fetchRow(array('b.uri_path'=>$uri_path));

        if(!$data){
            die('404');
        }

        $total = count($this->newsmodel->getNewsByTags($data['id_tags'],$page,$id_lang));
        $this->db->order_by('publish_date','desc');
        $this->db->limit(PAGING_PERPAGE,$page);
        $news = $this->newsmodel->getNewsByTags($data['id_tags'],$page,$id_lang);
        foreach ($news as $key => $value) {
            $news[$key]['start_date']       = iso_date($value['start_date']);
            $news[$key]['uri_path_detail']  = $value['uri_path'];
            $news[$key]['show_img']         = getImg($value['img'],'small');
        }
        $data['url']            = site_url("news");
        $data['list_news']      = $news;
        $config['base_url']     = site_url("news/tags/$uri_path/");
        $config['total_rows']   = $total;
        $config['per_page']     = PAGING_PERPAGE;
        $config['uri_segment']  = 5;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['paging'] = $this->pagination->create_links();

        render('news/tags',$data);
    }
    
}