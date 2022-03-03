<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class GenerateRSSFeed extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('generaterssfeedModel');
	}
	function generate($key){
		if($key==KEY_GENERATE_RSS_FEED){
			$id_lang = 2;
			// $all_data_article = $this->generaterssfeedModel->getallarticle($id_lang);
			$all_data_article = $this->generaterssfeedModel->getallarticle();
	        // print_r($this->db->last_query());exit;
	        foreach ($all_data_article as $key => $value) {
	            // $string = 'Retrieving the last word of a string using PHP.';
	            $last_word_start    = strrpos($value['uri_path'], '-') + 1; // +1 so we don't include the space in our result
	            $last_word          = substr($value['uri_path'], $last_word_start); // $last_word = PHP.

	            $all_data_article[$key]['news_url']             = base_url().'articles/'.$value['news_categori_url'].'/'.$value['uri_path'];
	            $all_data_article[$key]['news_category_link']   = base_url().'articles/'.$value['news_categori_url'];
	            $all_data_article[$key]['img_width']            = '519';
	            $all_data_article[$key]['img_height']           = '292';
	            $all_data_article[$key]['img_link']             = base_url().'images/article/small/'.$value['img'];
	            $all_data_article[$key]['img_link_infographic'] = base_url().'images/article/large/'.$value['img'];
	            $all_data_article[$key]['teaser']               = htmlspecialchars($value['teaser']);
	            $pub_date                                       = strtotime($value['publish_date']);
	            $all_data_article[$key]['publish_date']         = date('D, j M Y H:i:s', $pub_date).' IST';
	            $all_data_article[$key]['news_title']           = htmlspecialchars($value['news_title']);
	            $all_data_article[$key]['news_category']        = htmlspecialchars($value['news_category']);
                $all_data_article[$key]['page_content']     	= htmlspecialchars($value['page_content']);
	            
	            $link_img = base_url().'images/article/large/'.$value['img'];
	            // $infographic = '<img src="$link_img" height="auto" width="100%">';
	            // $infographic = '<![CDATA[<br><img src="'.$link_img.'" height="auto" width="auto">]]>';
	            
	            if($last_word=='infographic' || $last_word=='infographics'){
	                $all_data_article[$key]['img_infographic'] 	= '<![CDATA[<br><img src="'.$link_img.'" height="auto" width="auto">]]>';
	            }
	            else{
	                $all_data_article[$key]['img_infographic'] 	= '';
	            }
	        }
			$date_now = date("D, d M Y H:i:s O");
			$encoding = 'UTF-8';
			$data['xmlversion'] 		= '<?xml version="1.0" encoding="' . $encoding . '"?>' . "\n";
			$data['list_data'] 			= $all_data_article;
			$data['feed_name'] 			= 'MJM Feed RSS Feed';
			$data['feed_url'] 			= base_url().'rssfeed.xml';
			$data['page_description'] 	= 'MJM Feed RSS Feed at '.base_url();
			$data['copyright'] 			= 'Copyright'.' '.date('Y');
			$data['website_url'] 		= base_url();
			$data['date_now'] 			= $date_now;
			generate_rss_feed_file('rssfeed',$this->parser->parse('layout/futuready/generator/generate_rss_feed.html', $data,true));
			echo 'success to updated at '.$date_now;
		}
		else{
			echo 'wrong_key';
			//redirect(base_url().'notfound');
		}
	}

}