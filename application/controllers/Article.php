<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Article extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index($uri_path){
        $data['popular_article'] = popular_article();
        $data['new_article'] = new_article();
        $data['top_article'] = top_article();
        $data['popular_topic'] = popular_topic();
        $data['ads_widget'] = ads_widget();
        $data['fb_like_widget'] = fb_like_widget();

        render('article/article',$data);
    }
    function tags_more($uri_path,$page){
        $this->load->model('newsModel');
        $this->load->model('tagsModel');
        $tags = $this->tagsModel->fetchRow(array('uri_path'=>$uri_path));
        $list_data = $this->newsModel->getNewsByTags($tags['id'],$page);
        foreach ($list_data as $key => $value) {
            $list_data[$key]['img'] = image($value['img'],'small');
            $list_data[$key]['class_abu'] = $key % 2 == 0 ? '' : 'topik-list-abu';
		$list_data[$key]['img_news_class'] = '';
		$list_data[$key]['img_news_class_button'] = 'hide';
		$list_data[$key]['img_news_class_data'] = 'hide';
		$list_data[$key]['img_news_class_link'] = '';
		if($value['link_youtube_video']){
			$list_data[$key]['link_youtube_video'] = str_replace("watch?v=","embed/",$value['link_youtube_video']);
			$list_data[$key]['img_news_class'] = 'img-news-with-video';
			$list_data[$key]['img_news_class_button'] = 'img-news-class-button-small-tags';
			$list_data[$key]['img_news_class_link'] = 'hide';
			$list_data[$key]['img_news_class_data'] = '';
		}
	}
        $data['list_data'] = $list_data;
        $data['tags'] = $tags['name'];
        $ttl_record = $this->newsModel->getNewsByTags($tags['id'],'all');
        $data['load_more']      = ($ttl_record > ($page+PAGING_PERPAGE)) ? "<div class='parent-load-more'><a class='btn btn-default load-more' data-page='".($page+PAGING_PERPAGE)."'>".language('load_more')."</a></div>" : '';

        render('article/article_tags_more',$data,'blank');

    }
    function tags($uri_path,$page=0){
        $this->load->model('newsModel');
        $this->load->model('tagsModel');

        $data['ads_widget'] = ads_widget();
        $data['top_content'] = top_content();

        $tags = $this->tagsModel->fetchRow(array('uri_path'=>$uri_path));
		if($tags){
			$this->tagsModel->hitsCounter($uri_path);
		}
        $list_data = $this->newsModel->getNewsByTags($tags['id'],$page);
        foreach ($list_data as $key => $value) {
            $list_data[$key]['img'] = image($value['img'],'small');
            $list_data[$key]['class_abu'] = $key % 2 == 0 ? '' : 'topik-list-abu';
		$list_data[$key]['img_news_class'] = '';
		$list_data[$key]['img_news_class_button'] = 'hide';
		$list_data[$key]['img_news_class_data'] = 'hide';
		$list_data[$key]['img_news_class_link'] = '';
		if($value['link_youtube_video']){
			$list_data[$key]['link_youtube_video'] = str_replace("watch?v=","embed/",$value['link_youtube_video']);
			$list_data[$key]['img_news_class'] = 'img-news-with-video';
			$list_data[$key]['img_news_class_button'] = 'img-news-class-button-small-tags';
			$list_data[$key]['img_news_class_link'] = 'hide';
			$list_data[$key]['img_news_class_data'] = '';
		}
        }
        $data['list_data'] = $list_data;
        $data['tags'] = $tags['name'];
        $data['uri_path_tags'] = $uri_path;


        // $this->load->library('pagination');
        // $config['base_url'] = $this->currentController.'tags/'.$uri_path.'/';
        // $config['total_rows'] = $this->newsModel->getNewsByTags($tags['id'],'all');
        // $config['per_page'] = PAGING_PERPAGE; 
        // $config['uri_segment'] = 4; 
        // $this->pagination->initialize($config); 
        // $data['paging'] =  $this->pagination->create_links();
        $ttl_record = $this->newsModel->getNewsByTags($tags['id'],'all');
        $data['load_more'] = $ttl_record > PAGING_PERPAGE ? "<div class='parent-load-more'><a class='btn btn-default load-more' data-page='".PAGING_PERPAGE."'>".language('load_more')."</a></div>" : '';
		$data['meta'] = '<meta name="robots" content="noindex, follow">';

        render('article/article_tags',$data);
    }
    function category($uri_path){
        // $data['popular_article'] = popular_article();
        $this->load->model('newsCategoryModel');
        $category = $this->newsCategoryModel->fetchRow(array('uri_path'=>'category/'.$uri_path));
        $data['header_category'] = $category['name'];
        $data['top_article'] = top_article($category['id'],0);
        $data['popular_topic'] = popular_topic();
        $data['ads_widget'] = ads_widget();
        $data['fb_like_widget'] = fb_like_widget();
        $data['new_article'] = new_article();
        $data['ask_expert_widget'] = ask_expert_widget();
        render('article/article_category',$data);
    }
    

}