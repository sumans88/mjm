<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Index extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('newsModel');
		$this->load->model('newsCategoryModel');
        $this->load->model('SearchModel');
	}
    function index(){
        $data['ads'] = ads_widget();
        $data['top_content'] = top_content();
        $data['calendar_days_number'] = calendar_days_number('form-control');
        $data['calendar_months_word'] = calendar_months_word('form-control');
        $data['calendar_years_number'] = calendar_years_number('form-control');
        $data['search_get_value'] = $_GET['q'];
        render('search',$data);
    }
    function search_results(){
		$data = $this->SearchModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['news_title'] 		= quote_form($value['news_title']);
			$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
            $data['data'][$key]['page_content'] 	= substr(strip_tags($value['page_content']), 0, 500) . '...';
            if($key%2==0){
                $data['data'][$key]['style'] = 'topik-list-abu';
            } else {
                $data['data'][$key]['style'] = '';
            }
		}
        $data['dsp_not_found'] =  $data['data'] ? 'hide' : '';
        $data['keyword'] =  $_GET['search_or_page_content'];
		render('search_results',$data,'blank');
	}
}