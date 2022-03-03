<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Xml extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index($url){
        $this->load->helper('file');
        $this->load->model('newsmodel');
        $this->load->model('tagsmodel');
        $this->load->model('newstagsmodel');
        $http = 'http://www.bigdecisions.com/blog/';
        echo '<pre>';
        // $url = 'http://www.bigdecisions.com/blog/lifestyle-creep-and-how-it-can-impair-your-retirement/feed/?withoutcomments=1';
        $data = file_get_contents($url);
        $data = str_replace("content:encoded>","content>",$data);
        $xml = simplexml_load_string($data);
        $item = $xml->channel->item;
        // echo $item->category[3];
        // echo '<hr>';

        // "id_news_category"          : ? #bikin kategori baru namanya expert speaks
        // "news_title"                : title
        // "teaser"                    : description
        // "page_content"              : content # imagenya gmn..? biarin aja ngambil ke bigdecision
        // "uri_path"                  : link #perlu diolah
        // "publish_date"              : pubDate # atau current date..?
        // "img"                       : image # perlu didownload ..?
        // "user_id_create"            : bikin user baru namanya big decission
        // "seo_title"                 : title
        // "meta_description"          : teaser
        $news_title = (string)$item->title;
        $cek        = $this->newsmodel->fetchRow(array('news_title'=>$news_title));
        if(!$cek){
            $user_id_create = 6;#perlu disesuaikan
            $url_replace = array($http,'/');
            $url_replace_with = array('','');
            $img = $item->image;
            $gambar = end(explode('/',$img));
            $image_bin = file_get_contents($img);

            if (write_file(UPLOAD_DIR.'small/'.$gambar, $image_bin)){
                copy(UPLOAD_DIR.'small/'.$gambar,UPLOAD_DIR.'large/'.$gambar);
                $dt['img']                = $gambar;
            }
            $dt['id_news_category']       = 30;#perlu disesuaikan
            $dt['news_title']             = $news_title;
            $dt['teaser']                 = (string)$item->description;
            $dt['page_content']           = (string)$item->content;
            $dt['uri_path']               = str_replace($url_replace,$url_replace_with,$item->link);
            $dt['publish_date']           = date('Y-m-d H:i:s', strtotime($item->pubDate));
            $dt['user_id_create']         = $user_id_create;
            $dt['seo_title']              = $news_title;
            $dt['meta_description']       = (string)$item->teaser;

            $this->db->trans_start();   
                $id_news = $this->newsmodel->insert($dt);

                foreach ($item->category as $key => $value) {
                    $tags = strtolower((string)$value);
                    $where = array('name'=>$tags);
                    $id_tags = db_get_one('tags','id',$where);
                    if(!$id_tags){
                        $where['user_id_create'] = $user_id_create;
                        $id_tags = $this->tagsmodel->insert($where);
                    }
                    $this->newstagsmodel->insert(array('id_news'=>$id_news,'id_tags'=>$id_tags,'user_id_create'=>$user_id_create));
                    echo $value.'<br>';
                }
            $this->db->trans_complete();
        }
        // echo '<hr>';
        // print_r($item);
    }
    
    function feed(){
        $this->load->helper('file');
        $this->load->model('newsmodel');
        $this->load->model('tagsmodel');
        $this->load->model('newstagsmodel');
        $http = 'http://www.bigdecisions.com/blog/';
        echo '<pre>';
        $url = 'http://www.bigdecisions.com/blog/feed/?withoutcomments=1';
        $data = file_get_contents($url);
        $data = str_replace("content:encoded>","content>",$data);
        $xml = simplexml_load_string($data);
        $items = $xml->channel->item;
        foreach ($items as $key => $item) {
            // echo $item->link.'<br>';
            // $this->index($item->link.'/feed/?withoutcomments=1');
             $news_title = (string)$item->title;
            $cek        = $this->newsmodel->fetchRow(array('news_title'=>$news_title));
            if(!$cek){
                $user_id_create = 6;#perlu disesuaikan
                $url_replace = array($http,'/');
                $url_replace_with = array('','');
                $img = $item->image;
                $gambar = end(explode('/',$img));
                $image_bin = file_get_contents($img);

                if (write_file(UPLOAD_DIR.'small/'.$gambar, $image_bin)){
                    copy(UPLOAD_DIR.'small/'.$gambar,UPLOAD_DIR.'large/'.$gambar);
                    $dt['img']                = $gambar;
                }
                $dt['id_news_category']       = 30;#perlu disesuaikan
                $dt['news_title']             = $news_title;
                $dt['teaser']                 = (string)$item->description;
                $dt['page_content']           = (string)$item->content;
                $dt['uri_path']               = str_replace($url_replace,$url_replace_with,$item->link);
                $dt['publish_date']           = date('Y-m-d H:i:s', strtotime($item->pubDate));
                $dt['user_id_create']         = $user_id_create;
                $dt['seo_title']              = $news_title;
                $dt['meta_description']       = (string)$item->teaser;
                $dt['approval_level']         = 1;

                $this->db->trans_start();   
                    $id_news = $this->newsmodel->insert($dt);

                    foreach ($item->category as $key => $value) {
                        $tags = strtolower((string)$value);
                        $where = array('name'=>$tags);
                        $id_tags = db_get_one('tags','id',$where);
                        if(!$id_tags){
                            $where['user_id_create'] = $user_id_create;
                            $id_tags = $this->tagsmodel->insert($where);
                        }
                        $this->newstagsmodel->insert(array('id_news'=>$id_news,'id_tags'=>$id_tags,'user_id_create'=>$user_id_create));
                        echo $value.'<br>';
                    }
                $this->db->trans_complete();
            }

        }

        
        // echo '<hr>';
        // print_r($item);
    }
    function excel(){
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=abc.xls");  //File name extension was wrong
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        $this->load->model('newsmodel');
        $this->load->model('newstagsmodel');
        $data['list_data'] = set_nomor_urut($this->newsmodel->findBy(array('a.user_id_create'=>6)));
        foreach ($data['list_data'] as $key => $value) {
            $topics = $this->newstagsmodel->findBy(array('id_news'=>$value['id']));
            $topic = '';        
            foreach ($topics as $t) {
                $topic .= ', '. $t['tags'];
            }
            $data['list_data'][$key]['topic'] = substr($topic,1);
        }
        render('xml/excel',$data,'blank');
    }
}