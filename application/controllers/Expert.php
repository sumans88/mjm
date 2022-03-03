<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Expert extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index(){
        $this->load->model('newscategorymodel');
        $category = $this->newscategorymodel->fetchRow(array('uri_path'=>'expert'));
        $data['header_category'] = $category['name'];
        $data['top_article'] = top_article_expert($category['id'],0);
        $data['popular_topic'] = popular_topic();
        $data['ads_widget'] = ads_widget();
        $data['fb_like_widget'] = fb_like_widget();
        $data['new_article'] = new_article();
        render('article/article_category',$data);
    }

}