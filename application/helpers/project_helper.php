<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * helper khusus utk futuready
 */
function slider_widget(){
    $CI=& get_instance();
	$CI->load->model('slideshowModel');
	$lang_id = get_language_id();

	$CI->db->where('a.id_status_publish', '2');
	$CI->db->order_by('a.publish_date', 'dsc')->limit(5);
	$data = $CI->slideshowModel->findBy(array('id_lang' => $lang_id));
	$i = 0;
    if ($data) {
		foreach ($data as $key => $value) {
			++$i;
			$temp            = array();
			$temp['active']  = ($i == 1)?'active':'';
			$dont_add_slide  = 0 ; 

			if ($value['url'] !='' && $value['url'] !='#') {
				$content = '<a href="'.$value['url'].'">
	                			<img src="'.base_url().'images/article/large/'.$value['img'].'">
	              			</a>';
			}else if ($value['img']) {
				$content = '<img src="'.base_url().'images/article/large/'.$value['img'].'">';
			}else{
				$content =  '';
				$dont_add_slide = 1;
			}
			$temp['content'] 	  = $content;
			
			// kalau data memiliki gambar maka di tambah 
			if ($dont_add_slide == 0) {
				$ret['home_slider'][] = array('content'=>$content,
			 								 'slideshow_description'=>$value['description'],
			 								 'dsp_slideshow_description'=>$value['description'] == '' ? 'hide' :''
			 								 );
			}

		}
    }else{
		$ret['home_slider'][] = array('content'=>'',
									  'slideshow_description' =>'',
									  'dsp_slideshow_description' =>'hide'
		);
		// $ret['iframe'] = $content;
    }
	return $CI->parser->parse('layout/ddi/widget_slider.html', $ret,true);
}
function widget_sidebar(){
	$CI=& get_instance();
	$CI->load->model('eventmodel');
	$CI->load->model('newsmodel');
	$id_lang = id_lang();

	//========== event  ===========
		$CI->db->select('c.name as subcategory');

		$where_event['a.is_not_available']  = 0;
		$where_event['a.id_status_publish'] = 2;
		$where_event['a.publish_date <=']   = date('Y-m-d');
		$where_event['a.end_date    >=']    = date('Y-m-d');
		$where_event['a.id_lang']           = $id_lang;

		$CI->db->limit(2);
		$CI->db->order_by('a.start_date','asc');
		$CI->db->join('event_category c', 'c.id = a.id_event_subcategory', 'left');
		$CI->db->where('a.id_status_publish = 2');
		$data_event =$CI->eventmodel->findBy($where_event);
		if ($data_event) {
			foreach ($data_event as $key => $value) {
		        $temp['color']    = ($value['id_event_category'] == 27)?'darkblue':'red';//id amcham event
		        $temp['category'] = $value['subcategory'];
		        $temp['url']      = site_url('event/detail/'.$value['uri_path']);
		        $temp['name']     = $value['name'];
		        $temp['time']     = event_date($value['start_date'],$value['start_time'],$value['end_time']);
	            $temp['place']    = (!$value['location_name']) ? '-' : $value['location_name'];
	            $temp['hidden']   = '';
		        $data['events'][] = $temp;
		    }
		} else {
	            $temp['hidden']   = 'hidden';
		        $data['events'][] = $temp;
		}
    //========== end event ===========

    //==== publication ====
		$where_publication['a.id_status_publish'] = 2;
		$where_publication['a.approval_level']    = 100;
		$where_publication['a.publish_date <=']   = date('Y-m-d');
		$where_publication['a.id_lang']           = $id_lang;
		$id_category_publication                  = id_news_publication(1);
		

	    $CI->db->where_in('a.id_news_category',$id_category_publication);
	    $CI->db->limit(1);
	    $data_news  = $CI->newsmodel->highlight($where_publication);
	    foreach ($data_news as $key => $value) {
	        $temp['img']           = $value['img'];
	        $temp['url']           = site_url('news/detail/'.$value['uri_path']);
	        $temp['title']         = $value['category'];
    		$temp['css_event_list'] = $CI->data['css_event_list'] == 'hide'? '': 'mt30';
	        
	        $data['publication'][] = $temp;
	    }
	    // print_r($CI->db->last_query());exit;
    //==== end publication ====

    //==== newsletter ====
		$where_news['a.id_status_publish'] = 2;
		$where_news['a.approval_level']    = 100;
		$where_news['a.publish_date <=']   = date('Y-m-d');
		$where_news['a.id_lang']           = $id_lang;

	    $CI->db->limit(1);

	    $CI->db->order_by('publish_date','desc');
	    $CI->db->where_in('a.id_news_category','54');
	    $data_newsletter  = $CI->newsmodel->highlight($where_news);
	    foreach ($data_newsletter as $key => $value) {
	        $temp['heading']     = $value['category']." | ".event_date($value['publish_date']);
	        $temp['title']       = $value['news_title'];
	        $temp['url']      	 = site_url('news/detail/'.$value['uri_path']);
	        $temp['teaser']      = character_limiter($value['teaser'],71,'...');
	        $temp['img']     	 = $value['img'];
	        
	        $data['highlight'][] = $temp;
	    }
    //==== end newsletter ====
    
    //==== amcham update ====
		$where_news['a.id_status_publish'] = 2;
		$where_news['a.approval_level']    = 100;
		$where_news['a.publish_date <=']   = date('Y-m-d');
		$where_news['a.id_lang']           = $id_lang;

	    $CI->db->limit(1);

	    $CI->db->order_by('publish_date','desc');
	    $CI->db->where_in('a.id_news_category','87');
	    $data_amcham_update  = $CI->newsmodel->highlight($where_news);
	    foreach ($data_amcham_update as $key => $value) {
	        $temp['heading']     = $value['category']." | ".event_date($value['publish_date']);
	        $temp['title']       = $value['news_title'];
	        $temp['url']      	 = site_url('news/detail/'.$value['uri_path']);
	        $temp['teaser']      = character_limiter($value['teaser'],71,'...');
	        $temp['img']     	 = $value['img'];
	        
	        $data['amcham_update'][] = $temp;
	    }
    //==== end amcham_update ====

	$ret['event']        = $data['events'];
	$ret['report']       = $data['publication'];
	$ret['newsletter']   = $data['highlight'];
	$ret['amcham_update']= $data['amcham_update'];
	$ret['feed_twitter'] = feed_twitter();
	$ret['base_url']     = base_url();

    $ret['dsp_event_list'] = $CI->data['dsp_event_list'];

	//
    return $CI->parser->parse('layout/ddi/widget_sidebar.html', $ret,true);
}
function side_menu(){
	$CI =& get_instance();
	$CI->load->model();
 	$ret['feed_twitter'] = feed_twitter();
	return $CI->parser->parse('layout/ddi/widget_sidebar.html', $ret,true);
}

function new_expert($page=0,$limit=4){
	$CI 			= & get_instance();
	$CI->load->model('newsmodel');
	$ctrl 			= $CI->router->fetch_class();
	$kategori 		= $CI->uri->segment(3);
	$id_category 	= db_get_one('news_category','id',array('uri_path'=>"category/$kategori"));

	$CI->db->where("(id_news_category = 25 or (is_qa = 1 and is_experts = 0))");

	if($id_category){
		$CI->db->where(array("id_news_category"=>$id_category));
	}

	$CI->db->order_by('publish_date','desc');
	$CI->db->limit($limit);
	$CI->db->where_not_in('a.id',$CI->topArticleIds);
	$CI->db->where_not_in('a.id',$CI->new_article);
	// if($id_category){
		// $CI->db->where(array("id_news_category"=>$id_category));
	// }
	$dt = $CI->newsmodel->findBy(array('id_status_publish'=>2,'approval_level'=>100,'publish_date <='=>date('Y-m-d')));
	// last_query();
	foreach ($dt as $key => $value) {
		$CI->newExpertIds[] = $value['id'];
		$dt[$key]['img'] =  image($value['img'],'small');
		$dt[$key]['url_detail'] =  get_url_article_qa($value['is_qa'],$value['is_experts']);
		$dt[$key]['img_news_class'] = '';
		$dt[$key]['img_news_class_button'] = 'hide';
		$dt[$key]['img_news_class_data'] = 'hide';
		$dt[$key]['img_news_class_link'] = '';
		if($value['link_youtube_video']){
			$dt[$key]['link_youtube_video'] = str_replace("watch?v=","embed/",$value['link_youtube_video']);
			$dt[$key]['img_news_class'] = 'img-news-with-video';
			$dt[$key]['img_news_class_button'] = 'img-news-class-button-small-new';
			$dt[$key]['img_news_class_link'] = 'hide';
			$dt[$key]['img_news_class_data'] = '';
		}
	}

	$data['new_article_expert'] = $dt;
	if($page==1){
	    if($limit==1){
		$cols_data = "col-md-12 col-sm-12";
	    } else {
		$cols_data = "col-md-6 col-sm-6";
	    }
	    $data['cols_data_expert'] = $cols_data;
	    $CI->data = array_merge($data,$CI->data);
	    return $CI->parser->parse('layout/ddi/member/new_expert_page.html', $CI->data,true);
	}else{
	    $CI->data = array_merge($data,$CI->data);
	    return $CI->parser->parse('layout/ddi/new_article.html', $CI->data,true);
	}
}
function new_article($page=0,$limit=4,$expert=0,$kategory=''){
	$CI 			= & get_instance();
	$CI->load->model('newsmodel');
	$ctrl 			= $CI->router->fetch_class();
	$kategori 		= ($kategory) ?  $kategory : $CI->uri->segment(3);
	$id_category 	= db_get_one('news_category','id',array('uri_path'=>"category/$kategori"));

	if($ctrl == 'expert' or $expert==1){
		$CI->db->where("(id_news_category = 25 or (is_qa = 1 and is_experts = 0))");
	}
	else if($ctrl == 'qanew'){
		$CI->db->where("(id_news_category = 26 or (is_qa = 1 and is_experts = 1))");
	}
	else{
		// $CI->db->where("is_qa = 1 and is_experts = 1");
		//$CI->db->where('is_qa',0);
		//$CI->db->where('is_experts',0);
		// $CI->db->where_not_in(array("id_news_category"=>array(26,25)));
	}


	if($id_category){
		$CI->db->where(array("id_news_category"=>$id_category));
	}

	$CI->db->order_by('publish_date','desc');
	$CI->db->limit($limit);
	$CI->db->where_not_in('a.id',$CI->newExpertIds);
	$CI->db->where_not_in('a.id',$CI->topArticleIds);
	// if($id_category){
		// $CI->db->where(array("id_news_category"=>$id_category));
	// }
	$dt = $CI->newsmodel->findBy(array('id_status_publish'=>2,'approval_level'=>100,'publish_date <='=>date('Y-m-d')));
	// last_query();
	foreach ($dt as $key => $value) {
		$CI->new_article[] = $value['id'];
		$dt[$key]['img'] =  image($value['img'],'small');
		$dt[$key]['url_detail'] =  get_url_article_qa($value['is_qa'],$value['is_experts']);
		$dt[$key]['img_news_class'] = '';
		$dt[$key]['img_news_class_button'] = 'hide';
		$dt[$key]['img_news_class_data'] = 'hide';
		$dt[$key]['img_news_class_link'] = '';
		if($value['link_youtube_video']){
			$dt[$key]['link_youtube_video'] = str_replace("watch?v=","embed/",$value['link_youtube_video']);
			$dt[$key]['img_news_class'] = 'img-news-with-video';
			$dt[$key]['img_news_class_button'] = 'img-news-class-button-small-new';
			$dt[$key]['img_news_class_link'] = 'hide';
			$dt[$key]['img_news_class_data'] = '';
		}
	}

	$data['new_article'] = $dt;
	if($page==1){
	    if($limit>1){
		$cols_data = "col-md-6 col-sm-6";
	    } else {
		$cols_data = "col-md-12 col-sm-12";
	    }
	    $data['cols_data'] = $cols_data;
	    $CI->data = array_merge($data,$CI->data);
	    return $CI->parser->parse('layout/ddi/member/new_article_page.html', $CI->data,true);
	}else{
	    $CI->data = array_merge($data,$CI->data);
	    return $CI->parser->parse('layout/ddi/new_article.html', $CI->data,true);
	}
}
function top_article($id_category=0,$is_featured=1,$is_qa=0,$is_expert=0){
	$CI=& get_instance();
	$CI->load->model('topArticleModel');
	$CI->load->model('newsmodel');
	// $where['a.sort'] = 1;
	// $where['a.is_featured'] = 1;

	// $top_article = $CI->topArticleModel->fetchRow($where);
	// $news = $CI->newsmodel->findById($top_article['id_news']);

	// $CI->data = array_merge($CI->data,$news);
	// $CI->data['img'] = image($news['img'],'ori');

	// unset($where['a.sort']);
	// $where['a.sort >'] = 1;
	// $news2 = $CI->topArticleModel->findBy($where);
	// foreach ($news2 as $key => $value) {
	// 	$news2[$key]['img'] = image($value['img'],'small');
	// 	$news2[$key]['position'] = $key % 2 == 1 ? '2' : '';
	// }
	// $CI->data['list_data'] = $news2;
	$news2 = $CI->topArticleModel->get_top_news_by_category($id_category,$is_featured);
	foreach ($news2 as $key => $value) {
		$news = $CI->newsmodel->findById($value['id']);
		$CI->topArticleIds[] = $value['id'];
		if($key==0){
			$CI->data = array_merge($CI->data,$news);
			$CI->data['img'] = image($news['img'],'large');
			$CI->data['uri_path'] = get_url_article_qa($news['is_qa'],$news['is_experts']).$news['uri_path'];
			unset($news2[0]);
			$top = 1;
			
			$CI->data['img_news_class'] = '';
			$CI->data['img_news_class_button'] = 'hide';
			$CI->data['img_news_class_data'] = 'hide';
			$CI->data['img_news_class_link'] = '';
			if($news['link_youtube_video']){
				$CI->data['link_youtube_video'] = str_replace("watch?v=","embed/",$news['link_youtube_video']);
				$CI->data['img_news_class'] = 'img-news-with-video';
				$CI->data['img_news_class_button'] = 'img-news-class-button';
				$CI->data['img_news_class_link'] = 'hide';
				$CI->data['img_news_class_data'] = '';
			}
		}
		else{
			$news2[$key]['img_news_class2'] = '';
			$news2[$key]['img_news_class_button2'] = 'hide';
			$news2[$key]['img_news_class_data2'] = 'hide';
			$news2[$key]['img_news_class_link2'] = '';
			if($news['link_youtube_video']){
				$news2[$key]['link_youtube_video2'] = str_replace("watch?v=","embed/",$news['link_youtube_video']);
				$news2[$key]['img_news_class2'] = 'img-news-with-video';
				$news2[$key]['img_news_class_button2'] = 'img-news-class-button-small';
				$news2[$key]['img_news_class_link2'] = 'hide';
				$news2[$key]['img_news_class_data2'] = '';
			}
			
			$news2[$key]['img2'] = image($news['img'],'small');
			$news2[$key]['news_title2'] = $news['news_title'];
			$news2[$key]['id2'] = $news['id'];
			$news2[$key]['category2'] = $news['category'];
			$news2[$key]['teaser2'] = $news['teaser'];
			$news2[$key]['uri_path2'] = get_url_article_qa($news['is_qa'],$news['is_experts']).$news['uri_path'];
			$news2[$key]['uri_path_category2'] = $news['uri_path_category'];
			$news2[$key]['position'] = $key % 2 == 0 ?  2 : '';
			$all = 2;
		}
	}
	$CI->data['dsp_no_artile_top'] = $top ? '' : 'hide';
	$CI->data['dsp_no_artile'] = $all ? '' : 'hide';
	$CI->data['top_article'] = $news2;
	// print_r($news2);exit;
	return $CI->parser->parse('layout/ddi/top_article.html', $CI->data,true);
}

function top_article_expert($id_category=0,$is_featured=1,$is_qa=0,$is_expert=0){
	$CI=& get_instance();
	$CI->load->model('topArticleModel');
	$CI->load->model('newsmodel');
	$news2 = $CI->topArticleModel->get_top_news_by_category($id_category,$is_featured);
    $i=0;
	foreach ($news2 as $key => $value) {
		$news = $CI->newsmodel->findById($value['id']);
		$CI->topArticleIds[] = $value['id'];
        if($i==0){
			$CI->data = array_merge($CI->data,$news);
            $CI->data['uri_path'] = get_url_article_qa($news['is_qa'],$news['is_experts']).$news['uri_path'];
			$CI->data['img'] = image($news['img'],'large');
			unset($news2[0]);
			$top = 1;
			$CI->data['img_news_class'] = '';
			$CI->data['img_news_class_button'] = 'hide';
			$CI->data['img_news_class_data'] = 'hide';
			$CI->data['img_news_class_link'] = '';
			if($news['link_youtube_video']){
				$CI->data['link_youtube_video'] = str_replace("watch?v=","embed/",$news['link_youtube_video']);
				$CI->data['img_news_class'] = 'img-news-with-video';
				$CI->data['img_news_class_button'] = 'img-news-class-button';
				$CI->data['img_news_class_link'] = 'hide';
				$CI->data['img_news_class_data'] = '';
			}
		
		}
		else{
			$news2[$i]['img_news_class2'] = '';
			$news2[$i]['img_news_class_button2'] = 'hide';
			$news2[$i]['img_news_class_data2'] = 'hide';
			$news2[$i]['img_news_class_link2'] = '';
			if($news['link_youtube_video']){
				$news2[$i]['link_youtube_video2'] = str_replace("watch?v=","embed/",$news['link_youtube_video']);
				$news2[$i]['img_news_class2'] = 'img-news-with-video';
				$news2[$i]['img_news_class_button2'] = 'img-news-class-button-small';
				$news2[$i]['img_news_class_link2'] = 'hide';
				$news2[$i]['img_news_class_data2'] = '';
			}
			$news2[$i]['img2'] = image($news['img'],'small');
			$news2[$i]['uri_path2'] = get_url_article_qa($news['is_qa'],$news['is_experts']).$news['uri_path'];
			$news2[$i]['news_title2'] = $news['news_title'];
            $news2[$i]['uri_path_category2'] = $news['uri_path_category'];
			$news2[$i]['category2'] = $news['category'];
			$news2[$i]['teaser2'] = $news['teaser'];
			$news2[$i]['position'] = $i % 2 == 0 ?  2 : '';
			$all = 2;
		}
        $i++;
	}
	$CI->data['dsp_no_artile_top'] = $top ? '' : 'hide';
	$CI->data['dsp_no_artile'] = $all ? '' : 'hide';
	$CI->data['top_article'] = $news2;
	return $CI->parser->parse('layout/ddi/top_article_expert.html', $CI->data,true);
}
function popular_topic(){
	$CI=& get_instance();
	$CI->load->model('tagsmodel');
	$CI->load->model('newsmodel');
	$CI->load->model('newstagsmodel');
	$CI->db->order_by('tags_count','desc');
	$CI->db->limit(10);
	$list_tag = $CI->tagsmodel->findBy(array('tags_count >'=> 0));
	// $data['list_popular_topic'] = $CI->newsmodel->getPopularTopic();
	$CI->db->order_by('tags_count','desc');
	$CI->db->limit(4);
	$list_tag_bottom = $CI->tagsmodel->findBy(array('tags_count >'=> 0));

	$data['list_tags'] = $list_tag;
	$data['list_tag_bottom'] = $list_tag_bottom;
	$ret = "<div class='col-md-12 col-sm-12 topik-cat-list'>";
	$CI->lang->load("general",LANGUAGE);
	$lang = $CI->lang->language;

	foreach ($list_tag_bottom as $key => $value) {
	    $news =  $CI->newstagsmodel->getLatesNewsByTagsId($value['id'],$newsIds);
	    $ret .= "<div class='sub-topic'><h2><a href='".$CI->base_url."article/tags/".$value['uri_path']."' title='".$value['name']."' >".$value['name']."</a> - ".$CI->newstagsmodel->getTotalUpdateNewsByTagsId($value['id'])." Terkini</h2>";
	    foreach ($news as $keynews => $valuenews) {
			    $url_detail =  get_url_article_qa($valuenews['is_qa'],$valuenews['is_experts']);
			    $newsIds[] = $valuenews['id'];
		$ret .= "<h3><a href='".$url_detail.$valuenews['uri_path']."' title='".$valuenews['news_title']."'>".$valuenews['news_title']."</a></h3>";
	    }
	    $ret .= '</div>';
	}
	$ret .= '</div>';
	$data['data_popular_topic'] = $ret;
	$CI->data = array_merge($data,$CI->data);

	return $CI->parser->parse('layout/ddi/popular_topic.html', $CI->data,true);
}
function popular_topic_search(){
	$CI=& get_instance();
	$CI->load->model('tagsmodel');
	$CI->load->model('newsmodel');
	$CI->load->model('newstagsmodel');
	$CI->db->order_by('tags_count','desc');
	$CI->db->limit(10);

	$list_tag = $CI->tagsmodel->findBy(array('tags_count >'=> 0));
	// $data['list_popular_topic'] = $CI->newsmodel->getPopularTopic();

	$data['list_tags'] = $list_tag;
	foreach ($list_tag as $key => $value) {
		$news =  $CI->newstagsmodel->getLatesNewsByTagsId($value['id'],$newsIds);
		$list_tag[$key]['ttl_update'] = $CI->newstagsmodel->getTotalUpdateNewsByTagsId($value['id']);
		$list_tag[$key]['news_title'] = $news['news_title'];
		$list_tag[$key]['uri_path_news'] = $news['uri_path'];
		$newsIds[] = $news['id'];
	}
	$data['list_tags2'] = $list_tag;
	$CI->data = array_merge($data,$CI->data);

	return $CI->parser->parse('layout/ddi/popular_topic_search.html', $CI->data,true);
}
function popular_article($page=1){ /*editor choice*/
	$CI=& get_instance();
	$CI->load->model('newsmodel');
	$CI->db->limit(3);
	$data = $CI->newsmodel->findBy(array('is_editor_choice'=>1));
	foreach ($data as $key => $value) {
	    $data[$key]['img'] =  image($value['img'],'small');
	    $data[$key]['uri_path'] = get_url_article_qa($value['is_qa'],$value['is_experts']).$value['uri_path'];
	}

	$CI->data['list_data'] = $data;
	if($page==1){
	    return $CI->parser->parse('layout/ddi/popular_article.html', $CI->data,true);
	}else{
	    return $CI->parser->parse('layout/ddi/member/popular_article.html', $CI->data,true);
	}
}
function artikel_terkait($id,$status){
	$CI=& get_instance();

	$id_lang         = id_lang();
	$data['id_lang'] = $id_lang;

			$CI->db->where('id',$id);
			$CI->db->where('status',$status);
			$CI->db->where('id_lang',$id_lang);
	$tags = $CI->db->get('view_tags_content')->result_array();
	// echo $CI->db->last_query();exit();
	// print_r($tags);exit();
	if ($tags) {
		$t= array();
		foreach ($tags as $key => $value) {
			$t[] = $value['id_tags'];
		}
		

		$CI->db->where('id !=',$id);
		// if ($status == 'news') {
			/*$CI->db->where('publish_date <=', date('Y-m-d'));
			$CI->db->or_where('publish_date', '');*/
		// }
		$CI->db->where('id_lang',$id_lang);
		$CI->db->where_in('id_tags',$t);
		$CI->db->limit(6);
		$CI->db->order_by('publish_date','desc');
		$CI->db->group_by('title');
		$datax =$CI->db->get_where('view_tags_content',$where)->result_array();
		// echo $CI->db->last_query();exit();
		if($datax){
			foreach($datax as $k=> $dtx){
				$datax[$k]['id']                = $dtx['id'];
				$datax[$k]['news_title']        = character_limiter($dtx['title'], 50);
				$datax[$k]['uri_path']          = $dtx['uri_path'];
				$datax[$k]['teaser']            = character_limiter($dtx['teaser'], 63);
				$datax[$k]['category']          = ucwords(strtolower($dtx['category']));
				$datax[$k]['uri_path_category'] = $dtx['uri_path_category'];
				$datax[$k]['publish_date']      = $dtx['publish_date'];
				$datax[$k]['img']               = ($dtx['img'] != '' ? getImg($dtx['img'],'small') : getImg('no-image-available.png','small'));
				$datax[$k]['isImg']             = $datax[$k]['img'] ? '' : 'hide';
				$datax[$k]['uri2']              = $datax[$k]['status'];
				$datax[$k]['detail_url']        = ($datax[$k]['status']=='news' || $datax[$k]['status']=='event') ? 'detail' : 'detailphoto';
				$datax[$k]['styleHeight']        = strlen($datax[$k]['title']) > 30 ? 'style="height:390px;"' : '';
			}
			$data['list_data_related'] = $datax;
			$CI->data = array_merge($CI->data,$data);
			return $CI->parser->parse('layout/ddi/artikel_terkait.html', $CI->data,true);
		}
	}
}
/*function artikel_terkait($id_news){
	$CI=& get_instance();
	$CI->load->model('newsmodel');
	$CI->load->model('newstagsmodel');
	$tags = $CI->newstagsmodel->findBy(array('id_news'=>$id_news));
	$newsIds[] = 0;
	$t[] = 0;
	foreach ($tags as $key => $value) {
		$t[] = $value['id_tags'];
	}
	$id_lang = id_lang();
	$where['a.is_delete'] 			= 0;
	$where['a.id_status_publish']	= 2;
	if($id_news){
		$where['a.id !=']	= $id_news;
	}
	// $where['a.approval_level']		= 100;
	$where['a.publish_date <='] = date('Y-m-d');
	$CI->db->select('a.news_title,a.img,a.uri_path,a.teaser,b.name as category,b.uri_path as uri_path_category,a.publish_date');
	$CI->db->join('news_category b','b.id = a.id_news_category');
	$CI->db->join('news_tags c','c.id_news = a.id');
	$CI->db->limit(3);
	$CI->db->where('a.id_lang',$id_lang);
	$CI->db->where_in('c.id_tags',$t);
	// $CI->db->where('a.id_news_category',$id_news_category);
	$CI->db->order_by('publish_date','desc');
	$CI->db->group_by('a.news_title');
	$datax =$CI->db->get_where('news a',$where)->result_array();
	if($datax){
		foreach($datax as $k=> $dtx){
			$datax[$k]['id']		= $dtx['id'];
			$datax[$k]['news_title']	= $dtx['news_title'];
			$datax[$k]['uri_path']	= $dtx['uri_path'];
			$datax[$k]['teaser']	= $dtx['teaser'];
			$datax[$k]['category']	= $dtx['category'];
			$datax[$k]['uri_path_category']	= $dtx['uri_path_category'];
			$datax[$k]['publish_date']	= $dtx['publish_date'];
			$datax[$k]['img'] =  getImg($dtx['img'],'small');
            $datax[$k]['isImg']    = $datax[$k]['img'] ? '' : 'hide';
			$newsIds[] = $dtx['id'];
		}
		$data['list_data_related'] = $datax;
		$CI->data = array_merge($CI->data,$data);
		return $CI->parser->parse('layout/ddi/artikel_terkait.html', $CI->data,true);
	}
}*/
function top_content($static=0){
	$CI=& get_instance();
	$CI->load->model('topArticleModel');
	$CI->load->model('newsmodel');
	// $where['a.is_featured'] = 1;
	// $news2 = $CI->topArticleModel->findBy($where);
	// foreach ($news2 as $key => $value) {
	// 	$news2[$key]['img'] = image($value['img'],'small');
	// }
	// $CI->data['list_data'] = $news2;
	$news2 = $CI->topArticleModel->get_top_news_by_category(0,1);
	foreach ($news2 as $key => $value) {
		$news = $CI->newsmodel->findById($value['id']);
		$news2[$key] = $news;
		$news2[$key]['img'] = image($news['img'],'small');
		$news2[$key]['url_detail'] = get_url_article_qa($news['is_qa'],$news['is_experts']);
		
		$news2[$key]['img_news_class'] = '';
		$news2[$key]['img_news_class_button'] = 'hide';
		$news2[$key]['img_news_class_data'] = 'hide';
		$news2[$key]['img_news_class_link'] = '';
		if($news['link_youtube_video']){
			$news2[$key]['link_youtube_video'] = str_replace("watch?v=","embed/",$news['link_youtube_video']);
			$news2[$key]['img_news_class'] = 'img-news-with-video';
			$news2[$key]['img_news_class_button'] = 'img-news-class-button-small';
			$news2[$key]['img_news_class_link'] = 'hide';
			$news2[$key]['img_news_class_data'] = '';
		}
		if($static == 1){
			unset($news2[6],$news2[5],$news2[4]);
		}
	}
	if(!$news2){
		$news2 = array();
	}
	$CI->data['list_data'] = $news2;
	return $CI->parser->parse('layout/ddi/top_content.html', $CI->data,true);
}
function ads_widget(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/ads_widget.html', $CI->data,true);
}
function google_analytics_widget(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/google_analytics_widget.html', $CI->data,true);
}

function share_widget(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/share_widget.html', $CI->data,true);
}
/*new script (dwiki)*/
function meta_og($data){
	$CI=& get_instance();
	$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
	$data['full_url'] = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	if ($data['img']||$data['img']!='NULL') {
		$data['img_og'] = base_url("/images/article/large/".$data['img']);
	} else {
		$data['img_og'] = '';
	}
	return $CI->parser->parse('layout/ddi/meta_og.html', $data,true);
}
/*end new script (dwiki)*/
function box_widget(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/box_widget.html', $CI->data,true);
}
function fb_like_widget(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/fb_like_widget.html', $CI->data,true);
}
function qa_widget(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/qa_widget.html', $CI->data,true);
}
function qa_widget_mobile(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/qa_widget_mobile.html', $CI->data,true);
}
function webtrens_widget(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/webtrens_widget.html', $CI->data,true);
}
function footer_menu(){
	return frontend_menu(5);
}
function header_menu(){
	return frontend_menu(4);
}

function minify(){
    //return $_SERVER['REMOTE_ADDR']=='127.0.0.1' || $_SERVER['REMOTE_ADDR']=='192.168.1.111' ? '' : ".min";
    return $_SERVER['REMOTE_ADDR']=='127.0.0.1' || $_SERVER['REMOTE_ADDR']=='192.168.1.111' ? '' : "";
}

function frontend_menu($pos,$id_parent=''){
	$CI=& get_instance();
	$lang 		= $CI->uri->segment(1);
	$class		= $CI->uri->segment(2);
	$class 		= $class ? $class : 'home';
	$extraParam	= $CI->uri->segment(3);
	$uri4 		= $CI->uri->segment(4);
	$uri5 		= $CI->uri->segment(5);
	$CI->load->model('frontendmenumodel');
	$CI->load->model('languagemodel');
	$language 	= $CI->languagemodel->fetchRow(array('code'=>$lang));
	$CI->db->order_by('position');
	$data 		= $CI->frontendmenumodel->findBy(array('id_menu_position'=>$pos,'id_language'=>$language['id'],'id_parent'=>$id_parent)); 

	$pathname   = $_SERVER['REQUEST_URI'];
	/*echo "<pre>";
		print_r($data);
	echo "</pre>";exit();*/
	// echo print_r($CI->breadcrumb);
	// echo '<br>';
	// echo '<pre>';
	foreach ($data as $key => $value){

		$additional 	= ($value['controller'] == 'news' || $value['controller'] == 'event') ? '/index' : '';
		$url 			= $value['controller'] == '' ? '#' : "{$CI->baseUrl}$lang/$value[controller]$additional/$value[extra_param]";

		$addx  = preg_replace('/\s+/', '-', strtolower($value['name']));
		$urlx = $v['controller'] == '' ? '#' : "{$CI->baseUrl}$lang/$value[controller]$additional/$addx";

		// echo "$value[controller] == $class && $value[extra_param] == $extraParam : <br>";
		$CI->db->order_by('a.name', 'asc');
		$sub = $CI->frontendmenumodel->findBy(array('id_menu_position'=>$pos,'id_language'=>$language['id'],'id_parent'=>$value['id']));
		
		$aktif  = '';
		$aktif2 = '';
		$ret2   = '';
		
		if($sub){
			foreach ($sub as $k => $v){
				$additional2 	= $v['controller'] == 'event' ? '/index' : '';
				$additional2	= ($v['controller'] == 'news' || $v['controller'] == 'event') ? '/index' : '';
				
				$url2 			= $v['controller'] == '' ? '#' : "{$CI->baseUrl}$lang/$v[controller]$additional/$v[extra_param]";
		
				$additional2y	= ($v['controller'] == 'news' || $v['controller'] == 'event') ? '/detail' : '';
				$url2y		= $v['controller'] == '' ? '#' : "{$CI->baseUrl}$lang/$v[controller]$additional2/".$_SESSION['page_uri'];

				$add2x  = preg_replace('/\s+/', '-', strtolower($v['name']));
				$url2x = $v['controller'] == '' ? '#' : "{$CI->baseUrl}$lang/$v[controller]$additional2/$add2x";
							
				if($url2 == "#"){
					$url2 = $v[extra_param];
					$addBlank = 'target = "_blank"';
				} else{
					$addBlank = '';
				}
        	
				//echo $url2."---a---".$url2y."---b---";
				// $aktif2 = ($pathname == $url2) ? 'active' : '';
				$aktif2 = '';
				if ($pathname == $url2) {
					$aktif2 = 'active';
				} elseif ($pathname == $url2x) {
					$aktif2 = '';
				} elseif ($url2 == $url2y){
					$aktif2 = '';
				}
				if ($v['controller'] == 'member') {
					$aktif2	= '';
				}
			
				$hidewhenlogin = ($v['controller'] == 'member' && $v['extra_param'] == "") ? "{signin}":"";
				$ret2 .= "    <li role='presentation' class='$aktif2 $hidewhenlogin'>\n";
				$ret2 .= "        <a href='$url2' ".$addBlank.">$v[name]</a>\n";
				$ret2 .= "   </li>\n";

				if ($aktif2!='') {
					// $aktif  =  $aktif2;
					$aktif  =  'active';
				}
			}
		}
		if ($aktif == '') {
			if ($pathname == $url) {
				$aktif = 'active';
			} elseif ($pathname == $urlx) {
				$aktif = 'active';
			} else {
				$aktif = '';
			}
		}
		if ($value['name'] == "Members") {
			$aktif = "members-menu"	;
		}
	
		$ret .= "    <li class='$aktif'>\n";
		$linkk = $ret2 ? ' href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"' : " href='$url'";
		$ret .= "        <a $linkk>$value[name]</a>\n";
		if($ret2){
			$ret .= "        <ul class='dropdown-menu dropdown-menu-amcham'>\n";
			$ret .= "            ".$ret2;
			$ret .= "        </ul>\n";
		}
		$ret .= "    </li>\n";
	}
	
	return $ret;
}

function footer_logo(){
	$CI=& get_instance();
	$lang 		= $CI->uri->segment(1);
	$CI->load->model('footerImagesModel');
	$CI->load->model('languagemodel');
	$language 	= $CI->languagemodel->fetchRow(array('code'=>$lang));
	$data 		= $CI->footerImagesModel->findBy(array('id_lang'=>$language['id']));
	$ret = '';
	foreach ($data as $key => $value) {
		$ret .= '<a href="'.$value['url'].'" target="_blank"><img src="'.base_url().'images/article/large/'.$value['img'].'" title="'.$value['title'].'" width="140" style="height: inherit;"></a>';
	}
	return $ret;
}

function banner_top(){
	$CI       =& get_instance();
	$base_url = base_url();
	$lang_id = get_language_id();
	$CI->load->model('aboutpartnersmodel');

	
	$data_featured_partners = $CI->aboutpartnersmodel->findby(array('id_partners_category'=>8,'id_status_publish' => 2,'id_lang' => $lang_id));
	$data['featured_partners_hide'] = '';
	if (!$data_featured_partners) {
		$data['featured_partners_hide'] = 'hide';
	}else{
		foreach ($data_featured_partners as $key => $value) {
			$temp['id_com']              = $value['id'];
			$temp['img']                 = image($value['img'],'large');
			$temp['url']                 = $value['url'] ? $value['url'] : '#';
			$data['featured_partners'][] = $temp;
		}
	}

	$ret  = '
           <div class="title-sponsors text-center mt20 mb0">Platinum Members</div>
			<div class="feature-partners">
			  <div class="owl-carousel owl-theme" id="slider-partners">';
			  foreach ($data['featured_partners'] as $key => $value) {
			  	 $ret  .='<div class="item">
			        		<div class="thumb-slider-partners">';
			     $ret  .='   <a href="'.$value['url'].'" idcom="'.$value['id_com'].'" class="partners_click">';
			     $ret  .='     <img src="'.$value['img'].'">';	
			     $ret  .='   </a> 
			     		    </div> 
			     		  </div>';
			  }
	$ret  .='</div>
			</div>';

	// $ret = strip_tags($data['iframe'],'<img>');
	return $ret;
}

function banner_bottom(){
	$CI       =& get_instance();
	$base_url = base_url();

	$CI->load->model('banner_model');
	$lang_id = get_language_id();
	$CI->db->where('a.id_status_publish', '2');
	$CI->db->order_by('a.publish_date', 'desc')->limit(1);
	$data = $CI->banner_model->findBy(array('id_lang' => $lang_id,'id_banner_position' => 2),1);

	$ret = $data['iframe'];
	// for production
	$ret = strpos($ret, 'http://') ? str_replace('http', 'https', $ret): $ret;
	
	return $ret;
}
function feed_twitter(){
	$ret = '<a class="twitter-timeline" data-height="260" href="https://twitter.com/AmChamIndonesia?ref_src=twsrc%5Etfw">Tweets by TwitterDev</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';
	return $ret;
}
function get_logo(){
	$CI =& get_instance();
	$CI->load->model('web_config_model');	
	$lang_id = get_language_id();

	// get web config
	$CI->db->select('b.type_name');
	$CI->db->join('web_config_type b','b.id = a.type_id');
	$data = $CI->web_config_model->findBy(array('a.id_lang' => $lang_id, 'a.id_status_publish' =>2));
	foreach ($data as $key => $value) {
		$ret[$value['type_name']] =  $value['type_id'] == 4 ?$value['img']:$value['value'];
	}

	return $ret;
}

function get_footer_data(){
	$CI =& get_instance();
	$CI->load->model('web_config_model');
	$lang_id = get_language_id();

	// get web config
	$CI->db->join('web_config_type b','b.id = a.type_id');
	$data = $CI->web_config_model->findBy(array('a.id_lang' => $lang_id, 'a.id_status_publish' =>2));

	foreach ($data as $key => $value) {

		if($value['type_id'] == 5){ 
			$ret['f_address'] =  $value['value']; 
		}
		if($value['type_id'] == 7){ 
			$ret['f_description'] =  $value['value']; 
		}
	}
	// get web config - footer contact
	$CI->db->join('web_config_type b','b.id = a.type_id');
	$data1 = $CI->web_config_model->findBy(array('a.id_lang' => $lang_id,'a.type_id' => 6, 'a.id_status_publish' =>2));

	foreach ($data1 as $key => $value) {
		$temp = array();		
		$temp['value'] = $value['value'];
		$temp['logo'] = $value['logo'];

		$ret['contact'][] = $temp;
	}
	// get sosmed 
	$CI->load->model('Social_media_model');
	$data2 = $CI->Social_media_model->findBy(array('id_lang' => get_language_id(), 'id_status_publish' =>2));
	foreach ($data2 as $key => $value) {
		$temp = array();		
		$temp['link'] = $value['url'];
		$temp['image'] = $value['img'];

		$ret['sosmed'][] = $temp;
	}
	return $ret;

}

function get_language_id(){
	$CI =& get_instance();
	$lang 		= $CI->uri->segment(1);
	$CI->load->model('languagemodel');
	$language 	= $CI->languagemodel->fetchRow(array('code'=>$lang))['id'];

	return $language;
}

/*function frontend_menu($pos,$id_parent=''){
	$CI=& get_instance();
	$lang 		= $CI->uri->segment(1);
	$class		= $CI->uri->segment(2);
	$class 		= $class ? $class : 'home';
	$uri4 		= $CI->uri->segment(4);
	$extraParam	= $CI->uri->segment(3);
	$CI->load->model('frontendmenumodel');
	$CI->load->model('languagemodel');
	$language 	= $CI->languagemodel->fetchRow(array('code'=>$lang));
	$CI->db->order_by('position');
	$data 		= $CI->frontendmenumodel->findBy(array('id_menu_position'=>$pos,'id_language'=>$language['id'],'id_parent'=>$id_parent)); 
	// echo print_r($CI->breadcrumb);
	// echo '<br>';
	// echo '<pre>';
	foreach ($data as $key => $value){
		// echo $value['controller'] .'--'.$value['extra_param'].'---'.$value['name'].'<br>';
		// echo '<br>';
		$additional 	= $value['controller'] == 'event' ? '/index' : '';
		$url 			= $value['controller'] == '' ? '#' : "{$CI->baseUrl}$lang/$value[controller]$additional/$value[extra_param]";
		$additional 	= $value['controller'] == 'news' ? '/index' : '';
		// echo "$value[controller] == $class && $value[extra_param] == $extraParam : <br>";
		if($class=='news'){
			// $aktif  = $CI->parentMenu == $value['extra_param'] && $CI->parentMenu ? 'active' : '';
			// echo "$CI->parentMenu == $value[extra_param] : $value[name]<br>";
			if($CI->breadcrumb[0]==$value['name'] || $CI->breadcrumb[1]==$value['name'] || $uri4 == $value['extra_param']){
				$aktif = 'active';
			}
			else if(!array_filter($CI->breadcrumb)){
				$where['extra_param'] 	= $uri4;
				$dt 					= $CI->frontendmenumodel->fetchRow($where);
				$parent 				= $CI->frontendmenumodel->fetchRow(array('a.id'=>$dt['id_parent']));
				if($parent){
					if($parent['extra_param'] ==$value['extra_param']){
						$aktif = 'active';
					}
					else{
						$aktif ='';
					}
				}
			}
			else{
				$aktif = '';
			}
		}
		else if($class='pages'){
			

		}
		elseif ($class=='event' && $class==$value['controller']) {
			if($value['extra_param']==$uri4 || $value['extra_param']==''){
				$aktif = 'active';
			}
			else{
				$aktif ='';
			}
		}
		elseif ($class=='gallery' && strtolower($value['name'])=='media') {
			$aktif = 'active';
		}

		else{
			$aktif = $value['controller'] == $class && $value['extra_param'] == $extraParam ? 'active' : '';
		}

		$ret .= "    <li role='presentation' class='$aktif'>\n";
		$ret .= "        <a href='$url'>$value[name]</a>\n";
		$sub = frontend_menu($pos,$value['id']);
		if($sub){
			$ret .= "        <ul class='nav nav-tabs menus'>\n";
			$ret .= "            ".$sub;
			$ret .= "        </ul>\n";
		}
		$ret .= "    </li>\n";
	}
	return $ret;
}
*/
function getParentMenu($parent=''){
	$CI 		= & get_instance();
	$id_lang 	= id_lang();
	$CI->load->model('frontendmenumodel');
	$CI->load->model('newscategorymodel');
	$uri4		= $CI->uri->segment(4);
	$uri3		= $CI->uri->segment(3);
	$uri2		= $CI->uri->segment(2);
	$uri1		= $CI->uri->segment(1);
	$where = array();
	if($uri2 == 'news'){
		$CI->load->model('newsmodel');
		$where2['a.uri_path'] 	= $uri4;
		$where2['a.id_lang'] 	= $id_lang;
		$data		 			= $CI->newsmodel->fetchRow($where2);
		// print_r($data);
		// echo $CI->db->last_query();exit;
		$newsCat	 			= $CI->newscategorymodel->findById($data['id_news_category']);
		// print_r($newsCat);exit;
		$uri3		 			= $newsCat['uri_path'];
	}

	$CI->db->order_by('id_parent','desc');
	$where['id_language'] = $id_lang;
	if($parent){
		// $where['a.id'] = $parent;
	}
	else{
		$where['extra_param'] = $uri3;
	}
	$data = $CI->frontendmenumodel->fetchRow($where);
	if($data['id_parent'] == 0){
		$ret = $data['extra_param'];
	}
	else{
		$ret = getParentMenu($data['id_parent']);
	}
	return $ret;
}

function top_menu($parent = null){
	$CI=& get_instance();
	$CI->load->model('frontendmenumodel');
	$CI->load->model('newsmodel');
	$data = $CI->frontendmenumodel->findBy(array('id_menu_position'=>1,'id_parent'=>$parent)); 
	if($parent){
		return $data;
	}

	$ret = '';
	foreach ($data as $key => $value) {
        $add = 0;
		$subs = top_menu($value['id']);
		$prett = count($subs) > 0 ? "class='prett'" : '';
		$ret .= '<li class="left-menu" id="top_menu_hover">';
		$link = count($subs) > 0 ? '#' : ($CI->baseUrl."$value[controller]/$value[extra_param]");
		$ret .= "	<a  $prett href='".$link."'>$value[name]</a>";
		if($prett){
			$ret .= '<ul class="menus">';
			$ret .= '<div class="bck-twmn"></div>';
            foreach ($subs as $s) {
                ++$n;
                ++$add;
                if($add==1){
                    $class_add = "widget-container-show";
                } else {
                    $class_add = '';
                }
				$news = $CI->newsmodel->getNewsByCategory($s['extra_param'],$limit,$s['controller']);
                $ret .= '<span class="thumb-menu">';	
	      		$ret .= "<ul class='widget-container $class_add' id='nav-3-".($n)."'>";
	      		foreach ($news as $article) {
                	$img = image($article['img'],'small');
            		$ret .=	"<li><a title='".$article['news_title']."' href='".get_url_article_qa($article['is_qa'],$article['is_experts']).$article['uri_path']."'><img width='100%' src='$img' alt='$article[news_title]'>".$article['news_title']."</a></li>";
	      		}
      			$ret .="</ul>";
                $ret .="</span>";
                $ret .= "<li><a href='".$CI->baseUrl."$s[controller]/$s[extra_param]' class='child-hover' id='nav-3-".($n)."'>$s[name]</a></li>";
			}
		    $ret .='</ul>';
		}
		$ret .= '</li>';
	}
	return $ret;
}
function get_url_article_qa($is_qa=0,$is_experts=0){
    $CI=& get_instance();
    if($is_qa==1 and $is_experts==1){
        return $CI->baseUrl."qadetail/index/";    
    } else {
        return $CI->baseUrl."articledetail/index/";   
    }
}
function limit_text($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos = array_keys($words);
        $text = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
}
function top_menu_mobile($parent = null){
	$CI=& get_instance();
	$CI->load->model('frontendmenumodel');
	$CI->load->model('newsmodel');
	//$CI->output->cache(60);
	$data = $CI->frontendmenumodel->findBy(array('id_menu_position'=>1,'id_parent'=>$parent)); 
	if($parent){
		return $data;
	}

	$ret = '';
	foreach ($data as $key => $value) {
		$subs = top_menu($value['id']);
		$prett = count($subs) > 0 ? "class='prett'" : '';
		if($prett){
            foreach ($subs as $s) {
				$ret .= "<li><a href='".$CI->baseUrl."$s[controller]/$s[extra_param]'>$s[name]</a></li>";
			}
		}
	}
	return $ret;
}
function image($img,$path,$ret=0){
	$path = "$path/";
	$path = str_replace('//', '', $path);
	$CI=& get_instance();
	$no_img = $ret == '404' ? $CI->baseUrl.'asset/images/404.png' : ($CI->baseUrl.'images/article/'.$path.'no_image.png');
	$cek = is_file_exsist(UPLOAD_DIR.$path,$img);
	if($ret==1){
		return $cek;
	}
	else{
		return  $cek ? ($CI->baseUrl.'images/article/'.$path.$img) : $no_img;
	}

}
function imageProfile($img,$path,$ret=0){
	$path = "$path/";
	$path = str_replace('//', '', $path);
	$CI=& get_instance();
	switch ($path) {
		case 'individu/'      : 
		case 'representative/': $path_no_img = 'frame-photo-not-availabale.jpg'       ;break;
		case 'company/'       : $path_no_img = 'frame-company-logo-not-available.jpg' ;break;		
	}
	$no_img = base_url().'images/article/large/'.$path_no_img;
		$cek = is_file_exsist(UPLOAD_DIR_PROFILE.$path,$img);
	
	if($ret==1){
		// return "1";
		return $cek;
	}else if($ret==2){

		return  $cek ? (base_url().'images/member/'.$path.$img) : $no_img;
	}
	else{
		return  $cek ? ($CI->baseUrl.'images/member/'.$path.$img) : $no_img;
	}

}

function calendar_days_number($class=''){
    $CI=& get_instance();
    $CI->lang->load("general",LANGUAGE);
	$lang = $CI->lang->language;

    $days = "<select class='$class search-param' id='calendar_days_number' name='tgl'>";
    $days .= "<option value='' selected>$lang[lang_day]</option>";
    for ($x = 1; $x <= 31; $x++) {
        if($x<10){
            $val = '0'.$x;
        } else {
            $val = $x;
        }
        $days .= "<option value='$val'>$x</option>";
    }
    $days .= "</select>";
    
    return $days;
}
function calendar_months_word($class=''){
    $CI=& get_instance();
    $CI->lang->load("general",LANGUAGE);
	$lang = $CI->lang->language;

    $months = "<select class='$class search-param' id='calendar_months_word' name='bln'>";
    $months .= "<option value='' selected>$lang[lang_month]</option>";
    $months .= "<option value='01'>$lang[lang_january]</option>";
    $months .= "<option value='02'>$lang[lang_february]</option>";
    $months .= "<option value='03'>$lang[lang_march]</option>";
    $months .= "<option value='04'>$lang[lang_april]</option>";
    $months .= "<option value='05'>$lang[lang_may]</option>";
    $months .= "<option value='06'>$lang[lang_june]</option>";
    $months .= "<option value='07'>$lang[lang_july]</option>";    
    $months .= "<option value='08'>$lang[lang_august]</option>";
    $months .= "<option value='09'>$lang[lang_september]</option>";
    $months .= "<option value='10'>$lang[lang_october]</option>";
    $months .= "<option value='11'>$lang[lang_november]</option>";
    $months .= "<option value='12'>$lang[lang_december]</option>";
    $months .= "</select>";
    
    return $months;
}

function calendar_years_number($class=''){
    $years_now = date('Y');

    $CI=& get_instance();
    $CI->lang->load("general",LANGUAGE);
	$lang = $CI->lang->language;
    $years = "<select class='$class search-param' id='calendar_years_number' name='thn'>";
    $years .= "<option value='' selected>$lang[lang_year]</option>";
    for ($x = 2013; $x <= $years_now; $x++) {
       $years .= "<option value='$x'>$x</option>";
    }
    $years .= "</select>";
    
    return $years;
}

function iso_date_custom_format($date,$format){
    if($date == '1900-01-01' or $date == ''){
	return '';
    } else {
       return date("$format",strtotime($date));
    }
}

function get_month($name=''){
	$day_in_eng = $name;
    if ($day_in_eng == 'January') return "Januari";
    else if ($day_in_eng == 'February') return "Februari";
    else if ($day_in_eng == 'March') return "Maret";
    else if ($day_in_eng == 'April') return "April";
    else if ($day_in_eng == 'May') return "Mei";
    else if ($day_in_eng == 'June') return "Juni";
    else if ($day_in_eng == 'July') return "July";
    else if ($day_in_eng == 'August') return "Agustus";
    else if ($day_in_eng == 'September') return "September";
    else if ($day_in_eng == 'October') return "Oktober";
    else if ($day_in_eng == 'November') return "November";
    else if ($day_in_eng == 'December') return "Desember";
}

function head_title($head_title=''){
    if($head_title==''){
		return 'FUTUREADY - Program Persiapan Masa Depan';
    }else{
		return $head_title;
    }
}
function meta_description($meta=''){
	if($meta==''){
		return 'Futuready memandu Anda dengan cara yang praktis, interaktif sekaligus inspiratif untuk siapkan masa depan Anda.';
    }else{
		return remove_kutip(strip_tags($meta));
    }
}
function meta_keywords($meta=''){
	if($meta==''){
		return 'Futuready memandu Anda dengan cara yang praktis, interaktif sekaligus inspiratif untuk siapkan masa depan Anda.';
    }else{
		return remove_kutip(strip_tags($meta));
    }
}
function sent_email_by_category($id_ref_email_category,$data,$to){
    $CI=& get_instance();
    $CI->load->helper('mail');
    $CI->load->model('EmailDefaultModel');
    $CI->load->model('EmailTmpModel');
    $CI->load->model('contactusreceiveModel');
	$data_email_category = $CI->EmailDefaultModel->findById($id_ref_email_category);

    if($data_email_category['id_email_tmp']){


	    $CI->db->select('group_concat(email) as email_group');
		$add_email_to        = $CI->contactusreceiveModel->findBy(array('id_email_category' => $data_email_category['id'],'id_status_publish'=>2),1)['email_group'];
		$add_email_to        = !empty($add_email_to) ? "," . $add_email_to : $add_email_to;
		
		$data_email_template = $CI->EmailTmpModel->findById($data_email_category['id_email_tmp']);
		
        if($data_email_template){
            $email['to'] = $to.$add_email_to;
            $config = array (
                'mailtype' => 'html',
                'charset'  => 'utf-8',
                'priority' => '1'
            );
            $CI->email->initialize($config);
            $email['subject'] = $data_email_template['subject'];
            $data['data_email_content'] = $data_email_template['page_content'];
            $path	= get_path_email_template();
            //for file
             $email['filename']  = $data['filename'];
             $email['path_file'] = $data['path_file'];
            //

		    $message_content['this_year'] = date('Y');
		    $message_content['content'] = $CI->parser->parse('layout/ddi/email_template/'.preg_replace("/&#?[a-z0-9]+;/i","",$data_email_template['template_name']).'.html', $data,true);
		    
			$message                    = $CI->parser->parse('layout/ddi/email_template/ddi_default_template.html', $message_content,true);
			$email['content']           = $message;
			$ret                        = sent_mail($email);
			$data_email['to_email']     = $to;
			$data_email['category']     = $data_email_category['name'];
			$data_email['process_date'] = date('Y-m-d H:i:s');
			$data_email['from_email']   = $CI->db->query('select smtp_user from email_config')->row()->smtp_user;
            // $log_email = $CI->EmailDefaultModel->insert_email_log($data_email);
            return $ret;
        }else{
        	// print_r($data_email_category);
        	echo "template Not found / user create not found";exit;
        }
    }else{
    	echo "template category gak punya id template";exit;
    }
}
function get_path_email_template(){
    return EMAIL_TEMPLATE_DIR;
}
function generate_email_template_file($file_name,$data){
    $CI=& get_instance();
    $CI->load->helper('file');
    $path	= get_path_email_template();
    if(!file_exists($path)){
        mkdir($path);
    }
    if(!is_writable($path)){//kalo ga bisa nulis
        die('ga bisa nulis!');
    }
    if(!write_file($path.preg_replace("/&#?[a-z0-9]+;/i","",$file_name).'.html', $data)){
        echo 'error create file <br>';
    }
}
function get_path_custome_lang_file(){
	return CUSTOME_LANG_DIR;
}
function generate_custome_lang_file($lang_name,$data){
    $CI=& get_instance();
    $CI->load->helper('file');
    $path	= get_path_custome_lang_file().$lang_name.'/';
    if(!file_exists($path)){
        mkdir($path);
    }
    if(!is_writable($path)){//kalo ga bisa nulis
        die('ga bisa nulis!');
    }
    if(!write_file($path.preg_replace("/&#?[a-z0-9]+;/i","",'custom_lang').'.php', $data)){
        echo 'error create file <br>';
    }
}
function get_path_route_url_file(){
	return ROUTE_URL_DIR;
}
function generate_route_url_file($data){
    $CI=& get_instance();
    $CI->load->helper('file');
    $path	= get_path_route_url_file();
    if(!file_exists($path)){
        mkdir($path);
    }
    if(!is_writable($path)){//kalo ga bisa nulis
        die('ga bisa nulis!');
    }
    if(!write_file($path.preg_replace("/&#?[a-z0-9]+;/i","",'custom_routes').'.php', $data)){
        echo 'error create file <br>';
    }
}
function passwordHash($plainTextPassword, $salt = null, $saltLength = 9)
{
    if(is_null($salt))
    {
        // create new salt
        $salt = substr(sha1(uniqid(mt_rand(), true)), 0, $saltLength);
    }
    else
    {
        $salt = substr($salt, 0, $saltLength);
    }
    return $salt . hash('sha256', $salt . $plainTextPassword);
}
function check_date_future($datetime){
	$today      = strtotime(date("Y-m-d"));
	$createdday = strtotime($datetime); 

	$datediff   = $today - $createdday;
	
	if ($datediff < 0 or $today == $createdday ) { //kalo tanggal yang dimasukan > dari hari ini
		return true;
	}else{
		return false;
	}
};
function time_elapsed_string($datetime, $type = '') {

	// $today      = time();  
	$today      = strtotime(date("Y-m-d"));
	$createdday = strtotime($datetime); 
	$datediff   = $today - $createdday;
	$type 		= 'yang lalu';
	if ($datediff < 0) {
		$datediff   = abs($today - $createdday);
		$type = 'kedepan';
	}
	$difftext   = "";  
	$years      = floor($datediff / (365*60*60*24));  
	$months     = floor(($datediff - $years * 365*60*60*24) / (30*60*60*24));  
	$days       = floor(($datediff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  
	$hours      = floor($datediff/3600);  
	$minutes    = floor($datediff/60);  
	$seconds    = floor($datediff);  
    

    switch ($type) {
    	case 'years':
    		if($years>1)  
    		 $difftext= ' '.$years." tahun ".$type;  
    		elseif($years==1)  
    		 $difftext= ' '.$years." tahun ".$type;  
    	break;

    	case 'months':
    		if($months>1)  
    		$difftext= ' '.$months." bulan ".$type;  
    		elseif($months==1)  
    		$difftext= ' '.$months." bulan ".$type;  	
    	break;

    	case 'days':
    		if($days>1)  
    		$difftext= ' '.$days." hari ".$type;  
    		elseif($days==1)  
    		$difftext= ' '.$days." hari ".$type;  
    	break;
    	
    	case 'hours':
		   if($hours>1)  
		   $difftext= ' '.$hours." jam ".$type;  
		   elseif($hours==1)  
		   $difftext= ' '.$hours." jam ".$type;      	    		  
    	break;

    	case 'minutes':
    		if($minutes>1)  
    		$difftext= ' '.$minutes." menit ".$type;  
    		elseif($minutes==1)  
    		$difftext= ' '.$minutes." menit ".$type;  
    	break;

    	case 'seconds':
    		if($seconds>1)  
    		$difftext= ' '.$seconds." detik ".$type;  
    		elseif($seconds==1)  
    		$difftext= ' '.$seconds." detik ".$type;  
    	break;
    	
    	default:
    		if($difftext=="")  
    		{  
    		  if($years>1)  
    		   $difftext= ' '.$years." tahun ".$type;  
    		  elseif($years==1)  
    		   $difftext= ' '.$years." tahun ".$type;  
    		}  
    		//month checker  
    		if($difftext=="")  
    		{  
    		   if($months>1)  
    		   $difftext= ' '.$months." bulan ".$type;  
    		   elseif($months==1)  
    		   $difftext= ' '.$months." bulan ".$type;  
    		}  
    		//day checker  
    		if($difftext=="")  
    		{  
    		   if($days>1)  
    		   $difftext= ' '.$days." hari ".$type;  
    		   elseif($days==1)  
    		   $difftext= ' '.$days." hari ".$type;  
    		}  
    		//hour checker  
    		if($difftext=="")  
    		{  
    		   if($hours>1)  
    		   $difftext= ' '.$hours." jam ".$type;  
    		   elseif($hours==1)  
    		   $difftext= ' '.$hours." jam ".$type;  
    		}  
    		//minutes checker  
    		if($difftext=="")  
    		{  
    		   if($minutes>1)  
    		   $difftext= ' '.$minutes." menit ".$type;  
    		   elseif($minutes==1)  
    		   $difftext= ' '.$minutes." menit ".$type;  
    		}  
    		//seconds checker  
    		if($difftext=="")  
    		{  
    		   if($seconds>1)  
    		   $difftext= ' '.$seconds." detik ".$type;  
    		   elseif($seconds==1)  
    		   $difftext= ' '.$seconds." detik ".$type;  
    		}  
    	break;
    }
    if($difftext=="") {
    	return time_elapsed_string($datetime,'') ;
    }
    
    return $difftext;  
}
function load_js($file,$path='assets/js/'){
	$CI = & get_instance();
	$files = explode(',',$file);
	foreach($files as $fl){
		if($fl)
		    $js .= '	<script type="text/javascript" src="'.$CI->data['base_url'].$path.$fl.'"></script>'."\n";
	}
	$CI->data['js_file'] .= $js;
}

/*alternative load_js()*/
function load_jsx($file, $path='assets/css/',$ret=false){
	$CI=& get_instance();
	$files = explode(',',$file);
	foreach($files as $fl){
		if($fl)
			$js .= '	<script type="text/javascript" src="'.$CI->data['base_url'].$path.$fl.'"></script>'."\n";
	}

	if ($ret==false) {
		$CI->data['jsx_file'] = $js;
	} else {
		return $js;
	}
}

function load_css($file, $path='assets/css/',$ret=false){
	$CI=& get_instance();
	$files = explode(',',$file);
	foreach($files as $fl){
		if($fl)
			$css .= '	<link rel="stylesheet" type="text/css" href="'.$CI->data['base_url'].$path.$fl.'">'."\n";
	}
	
	if ($ret==false) {
		$CI->data['css_file'] = $css;
	} else {
		return $css;
	}
}

function checkremotefile($url)
{
    if (!$fp = curl_init($url)) {
	return false;
    }else{
	return true;
    }
    
}
function delete_cookie($name) {
	unset($_COOKIE["$name"]);
	setcookie("$name", null, -1, '/');

}

function id_member(){
	$CI=& get_instance();
	$sess = $CI->session->userdata('MEM_SESS');;
	return $sess['id'];

}

function set_image_as_title_news($id_news){
    $CI=& get_instance();
    $CI->load->model('newsmodel');
    $CI->load->model('filemanagerModel');
    $news = $CI->newsmodel->findById($id_news);
    $news_ext = explode(".", $news['img']);
    $extension_img = '.'.$news_ext[1];
    $post_file['name'] = $news['uri_path'].$extension_img;
    $check_image_exist = $CI->filemanagerModel->findBy(array('name'=>$post_file['name']));
    if(!$check_image_exist and $news_ext[1] != ''){
		$post['img'] = $news['uri_path'].$extension_img;
		$CI->newsmodel->update($post,$id_news);
		
		$CI->filemanagerModel->insert($post_file);
		
		$fl = str_replace('//', '/', $path.'/'.$file);
		
		$file = is_file_copy(UPLOAD_DIR.'large',$news['img']);
		$newfile = is_file_copy(UPLOAD_DIR.'large',$news['uri_path'].$extension_img);
		if (!copy($file, $newfile)) {
			echo "failed to copy to large image";
		}
		
		$file = is_file_copy(UPLOAD_DIR.'small',$news['img']);
		$newfile = is_file_copy(UPLOAD_DIR.'small',$news['uri_path'].$extension_img);
		
		if (!copy($file, $newfile)) {
			echo "failed to copy to small";
		}
    }
}

function ask_expert_widget(){
	$CI=& get_instance();
	return $CI->parser->parse('layout/ddi/ask_expert_widget.html', $CI->data,true);
}

function language($key){
	$CI=& get_instance();
	$lang = $CI->uri->segment(1);
	$lang = db_get_one('language','name',array('code'=>$lang));
	$CI->lang->load("general",$lang);
	$lang 		= $CI->lang->language;
	$find 		=	LANG_CONTROLLER_FIND();
	$replace	=	LANG_CONTROLLER_REPLACE();
	return str_replace($find, $replace, $lang['lang_'.$key]);

}
function comment_article_not_login(){
    $CI=& get_instance();
    $user_sess_data = $CI->session->userdata('MEM_SESS');
    if(!$user_sess_data){
	return language('comment_not_login');
    }
}
function getcomments_data($row,$not_data=0) {
    $CI=& get_instance();
    $CI->load->model('commentModel');
    $user_sess_data = $CI->session->userdata('MEM_SESS');
    if(!$user_sess_data){
	    $user_sess_data = $CI->session->userdata('ADM_SESS');
    }
    $data = $CI->commentModel->findBy(array('parent_id'=>$row['id']));
    
    $data_tots = $CI->commentModel->findBynumrow(array('parent_id'=>$row['id']));
    $dt = '';
    $dt .= "<div class='comment row'>";
    if(getimagesize($row['image'])){
	    $avatar = $row['image'];
    } else {
	    $avatar	= base_url().(($row['image']) ? "images/member/profile_pictures/$row[image]" : 'images/member/profile_pictures/no_image.jpg');
    }
    $dt .= "<div class='content_avatar col-sm-4 col-md-4  col-xs-4'><img src='$avatar'></div>";
    $dt .= "<div class='content_comment col-sm-8 col-md-8 col-xs-8'>";
    $dt .= "<div class='aut'>";
    if($row['is_admin']==1){
	$data_fullname = '<p class="bg-admin-name" data-toggle="tooltip" data-placement="top" title="'.language('administrator').'">'.$row['namadepan']." ".$row['namabelakang'].'</p>';
    } else {
	$data_fullname = $row['namadepan']." ".$row['namabelakang'];
    }
    if($user_sess_data['id']==$row['user_id_create'] or $user_sess_data['admin_id_auth_user']){
	$dt .= $data_fullname." | <span class='text-right delete_comment' id='$row[id]'>".language('delete_comment')."</span>";
    } else {	
	$dt .= $data_fullname;
    }
	if($user_sess_data){
		$dt .= " | <span class='glyphicon glyphicon-flag flag_comment' id='$row[id]'>&nbsp;</span>";
	}
    $commentar_data = check_text_block($row['commentar']);
    if($user_sess_data['admin_id_auth_user']){
	$dt .= "($row[flag])";
	$commentar_data = check_text_block($row['commentar']);
    }

    $dt .= "</div>";
    
    $dt .= "<div class='comment-body'>".$commentar_data."</div>";
    $dt .= "<div class='timestamp'>".iso_date_time($row['create_date'])."</div>";
    //$dt .= "<a href='#comment_form' class='reply_comment' id='".$row['id']."'>Reply</a> | ";
    $data_tots_like = $CI->commentModel->findBycommentlike(array('id_comment'=>$row['id'],'id_news'=>$row['id_news']),1);
    $data_member_like = $CI->commentModel->findBycommentlike(array('id_comment'=>$row['id'],'id_news'=>$row['id_news'],'user_id_create'=>$user_sess_data['id']),1);
    if($user_sess_data or $user_sess_data['admin_id_auth_user']){
	if($data_member_like>0){
	    $dt .= " <a href='#comment_form' class='like_comment' id='".$row['id']."_2_".$row['id_news']."_".$data_tots_like."'>Unlike ($data_tots_like)</a>";	
	} else {
	    $dt .= " <a href='#comment_form' class='like_comment' id='".$row['id']."_1_".$row['id_news']."_".$data_tots_like."'>Like ($data_tots_like)</a>";	    
	}
    } else {
	$dt .= " <div class='like_comment'> Like ($data_tots_like) </div>";
    }
    $dt .= "</div>";
    if($data_tots>0){
	    $dt .= "<ul>";
	    foreach ($data as $key => $value) {
		    $dt .= getcomments_data($value,1);
	    }
	    $dt .= "</ul>";
    
    }
    $dt .= "</div>";
    return $dt;
}
function getcomments($id_news){
    $CI=& get_instance();
    $CI->load->model('commentModel');
    $dt = '';
    $data = $CI->commentModel->findBy(array('id_news'=>$id_news,'parent_id'=>null));
    foreach ($data as $key => $value) {
	    if($value['is_admin']==1){
		$value = $CI->commentModel->findbyadmin(array('id'=>$value['id']));
	    }
	    $dt .= getcomments_data($value);
	
    }
    return $dt;
}
function valid_email($str){
    return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}
function get_pages_url($url){
    if($url=='syarat-ketentuan'){
	if(LANGUAGE=="english"){
		$data='terms-conditions';
	} else if(LANGUAGE=="indonesia"){
		$data='syarat-ketentuan';
	}
    }else if($url=='kebijakan-privasi'){
	if(LANGUAGE=="english"){
		$data='privacy-policy';
	} else if(LANGUAGE=="indonesia"){
		$data='kebijakan-privasi';
	}
    }
    return base_url().'pages/'.$data;
}
function check_data_exist_counter($data_check, $data_check2){
    $i=0;
    foreach ($data_check as $key => $value) {
	$data_check_data = $data_check2[$key].'_hidden';
	if(!$value){
	    ++$i;
	    $data[$data_check_data] = 'hide';
	} else{
	    $data[$data_check_data] = '';
	}
    }
    $data['total_count'] = $i;
    return $data;
}
function remove_kutip($data){
	$data = str_replace("'",'',$data);
    return str_replace('"','',$data);
}
function remove_whitespace($data){	
    return preg_replace('/\s+/', '', $data);
}
function convert_id_category_ask_expert($category){
    if($category==1){
	return 'kesehatan';
    } else if($category==2){
	return 'keuangan';
    } else if($category==3){
	return 'asuransi-proteksi';
    }
}
function check_block_ip(){
    $CI=& get_instance();
    $CI->load->library('blacklist');
    return $CI->blacklist->check_ip($_SERVER['REMOTE_ADDR'])->is_blocked();
}
function check_text_block($text){
    $CI=& get_instance();
    $CI->load->library('blacklist');
    return $CI->blacklist->check_text($text)->is_blocked();
}
function show_edit_data($show_data_not_complete, $total_data_now_complete,$link=''){
    return '<div class="col-sm-12 well '.$show_data_not_complete.'">
	    '.language('not_complete_data') . '<a href="'.base_url().'member/edit_profile'.$link.'">'. language('complete_data').'</a>.
	</div>';
}
function LANG_CONTROLLER_FIND(){
    return array('{base_url}', '{pages_syarat_ketentuan}', '{pages_kebijakan_privasi}','{member_email}');
}
function LANG_CONTROLLER_REPLACE(){
    $CI=& get_instance();
    $sess = $CI->session->userdata('MEM_SESS');;
    return array(base_url(), get_pages_url('syarat-ketentuan'), get_pages_url('kebijakan-privasi'), $sess['member_email']);
}
function get_flash_session($name){
	$CI=& get_instance();
	$data = $CI->session->userdata($name);
	//$CI->session->unset_userdata($name);
	return $data;
}
/**
* Remove html news tag
* @author Agung Trilaksono Suwarto Putra <agungtrilaksonosp@gmail.com>
* @return string;
* @param string $data  Tags news data to be called;
*/
function remove_html_tag_news($data){
	return preg_replace('/ style=".*?"/i', '$1', strip_tags($data, '<i><a><b><u><div><hr>'));
}
/**
* Set flash session
* @author Agung Trilaksono Suwarto Putra <agungtrilaksonosp@gmail.com>
* @return command;
* @param string $name  Flash session name to be called;
* @param string $value  Flash session value to be called;
*/
function set_flash_session($name,$value){
	$CI=& get_instance();
	return $CI->session->set_userdata($name,$value);
}

function slideshow(){
	$CI=& get_instance();
    $CI->load->model('slideshowmodel');
    $CI->load->model('frontendmenumodel');
    $module = $CI->uri->segment(2);
    $param = $CI->uri->segment(4);
    $id_lang = id_lang();

    if($module=='news'){
    	$w['extra_param'] = $param;
	}
	else if($module =='event'){
    	$w['extra_param'] = $param;
	}
	else{
		$w['b.controller'] 	= $module;
	}

	$w['id_language'] 	= $id_lang;
    $menu = $CI->frontendmenumodel->fetchRow($w);
    // echo $CI->db->last_query();
    // print_r($menu);

    $CI->db->like('menu',",$menu[id],");
    $where['a.id_lang'] = $id_lang;
    $slideshow  = $CI->slideshowmodel->findBy($where);
    if(!$slideshow){
    	return '';
    }
    $ret = '<div class="container-fluid container-fluid-slideshow">
    			<div class="container container-slideshow">
			  <div class="row">
			    <div class="col-sm-12 col-slideshow">
			      <div id="slide-header" class="owl-carousel owl-theme"> ';
				    foreach ($slideshow as $key => $value) {
				    	$captionLeft = ($value['description'] ? 'col-sm-8 col-md-8' : 'col-sm-12 col-md-12');
				    	$ret .= '
				        <div class="item" style="max-height: 480px;"> ';
				        	($value['url'] != NULL ? $ret.= '<a href="'.$value['url'].'" target="_blank"><img src="'.base_url().'images/article/large/'.$value['img'].'" alt="The Last of us"></a>' : $ret.= '<img src="'.base_url().'images/article/large/'.$value['img'].'" alt="The Last of us">');
				        	$ret .= '
				          <div class="caption-slider">
				            <div class="container tb-caption-slider"> ';
				            ($value['url'] != NULL ? $ret.= '<a href="'.$value['url'].'" target="_blank"><div class="'.$captionLeft.' col-xs-12 td-caption-slider caption-left">'.$value['slideshow_title'].'</div></a>' : $ret.= '<div class="'.$captionLeft.' col-xs-12 td-caption-slider caption-left">'.$value['slideshow_title'].'</div>');
				              $ret .= '
				              <div class="'; ($value['description'] ? $ret.= 'col-sm-4 col-md-4' : $ret.= 'col-sm-12 col-md-12'); $ret.= ' col-xs-12 td-caption-slider caption-right">'.$value['description'].'</div>
				            </div>
				          </div>
				        </div>';
				    }
	$ret .= '
			      </div
			    </div>			    </div>			    </div>
			  </div>
			</div>';
			// echo "<script>console.log('".$ret."');</script>";
	return $ret;
}
function generate_rss_feed_file($file_name='rssfeed',$data){
	$CI=& get_instance();
	$CI->load->helper('file');
	if(!write_file(preg_replace("/&#?[a-z0-9]+;/i","",$file_name).'.xml', $data)){
		echo 'error create file <br>';
	}
}

function contact_form(){
	$CI=& get_instance();
	// $string = $CI->parser->parse('layout/ddi/contact_us_form.html', $data, TRUE);
	$string = render('layout/ddi/contact_us_form', $data,'blank', TRUE);
	return $string;
}

function getBetweenString($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}

function deleteBetweenString($beginning, $end, $string) {
  $beginningPos = strpos($string, $beginning);
  $endPos = strpos($string, $end);
  if ($beginningPos === false || $endPos === false) {
    return $string;
  }

  $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

  return str_replace($textToDelete, '', $string);
}
function event_date($startdate,$startime,$endtime,$mark='-',$full=0){
	 if(!$startdate) return;
	 list($thn,$bln,$tgl) = explode($mark,$startdate);
	 $tgl = explode(' ', $tgl);		

	 	// $date = ($full) ? date('F',strtotime($startdate)) : date('M',strtotime($startdate));
	 if (empty($startime) || empty($endtime) || ($startime == '00:00') || ($endtime == '00:00')) { //kalau start time dan end time kosong untuk annual
	 	$date = ($full == 1) ? date('F',strtotime($startdate)) : date('M',strtotime($startdate));
	 	return  $date. ' ' . $tgl[0] . ', ' . $thn . ' ';	
	 }else{		
	 	$date = ($full == 1) ? date('F',strtotime($startdate)) : date('M',strtotime($startdate));
	 	return $date . ' ' . $tgl[0] . ', ' . $thn . ' / '. date('H:i',strtotime(str_replace(' ', '', $startime))). ' - ' . date('H:i',strtotime(str_replace(' ', '', $endtime)));
	 	//Nov 4, 2017 / 18:00 - 20:30
	 }
}

function event_time($startdate,$startime,$endtime,$mark='-'){
	 if(!$startdate) return;
	 if(!$startime) return;
	 if(!$endtime) return;
	 list($thn,$bln,$tgl) = explode($mark,$startdate);
	 $tgl = explode(' ', $tgl);		
	 return date('l',strtotime($startdate)) . ', ' . date('H:i',strtotime(str_replace(' ', '', $startime))) . ' - ' . date('H:i',strtotime(str_replace(' ', '', $endtime)));
	 	//sunday, 18:00 - 20:30
}
function id_child_news($id ,$array =0){
	$conf['id'           ]  = $id ;
	$conf['colomn'       ]  = 'id_parent_category' ;
	$conf['colomn_select']  = 'id' ;
	$conf['table'        ]  = 'news_category' ;
	$conf['with_parent'  ]  =  '1' ;
	$conf['array'        ]  =  $array ; 

	$ret = array_filter(id_child($conf));
	if (!empty(array_filter($ret))   ) {
		foreach ($ret as $key => $value) {
			$conf['id'] = $value;
			$ret = array_merge($ret,array_filter(id_child($conf)));
		}
	}
	$ret = array_unique($ret);
	return $ret;
}
function id_child_event($id ,$array =0,$with_parent = 1){
	$conf['id'           ]  = $id ;
	$conf['colomn'       ]  = 'id_parent_category' ;
	$conf['colomn_select']  = 'id' ;
	$conf['table'        ]  = 'event_category' ;
	$conf['with_parent'  ]  =  $with_parent ;
	$conf['array'        ]  =  $array ; 

	$ret = array_filter(id_child($conf));
	if (!empty(array_filter($ret))   ) {
		foreach ($ret as $key => $value) {
			$conf['id'] = $value;
			$ret = array_merge($ret,array_filter(id_child($conf)));
		}
	}
	$ret = array_unique($ret);
	return $ret;
}

function id_news_publication($array = 0){
	$id  = array('4','56');
	return id_child_news($id,$array);
}

function id_news_newsletters($array = 0){
	$id  = array('53');
	return id_child_news($id,$array);
}

function not_found_page()
{
	$CI=& get_instance();
	$lang = $CI->uri->segment(1);
	redirect(base_url("$lang/not-found"));
}
function register_now($data)
{
	$CI=& get_instance();
	if(is_array($data)){
		$CI->data = array_merge($CI->data,$data);
	}
	return $CI->parser->parse('event/register_now.html', $CI->data,true);
}

function modal_register_event_not_select_member($data)
{

	$CI=& get_instance();
	if(is_array($data)){
		$CI->data = array_merge($CI->data,$data);
	}
	return $CI->parser->parse('event/modal_join_member.html', $CI->data,true);
}

function full_name($data='',$justname='')
{
	if ($justname) {
		unset($data['prefix_name']);
	}
	$first = ($data['prefix_name'] != '') ?$data['prefix_name'].' '. $data['firstname']: $data['firstname'];
	// $last = ($data['middlename'] !='') ? $data['middlename'].' '. $data['lastname'] : $data['lastname'];
	$last = $data['lastname'];
	return $first.' '.$last;
}

function full_name_listing($data)
{
	return  $data['lastname'].', '.$data['firstname'];
}

function generate_tags($tags,$key,$custom_sperator =","){
	$html_tags = '';
	if (is_array($tags)) {
		foreach ($tags as $keyy => $value) {
			if (strpos($value, "'") || strpos($value[$key], "'")) {
				$html_tags .= ($key)?$custom_sperator.'"'.$value[$key].'"':$custom_sperator.'"'.$value.'"';
			}else{
				$html_tags .= ($key)?$custom_sperator."'".$value[$key]."'":$custom_sperator."'".$value."'";
			}
		}
		$data = substr($html_tags,1);
	}else{
		$data = $html_tags;
	}
	return $data;
}
function generate_url($text){
	// replace non letter or digits by -
	$text = preg_replace('~[^\pL\d]+~u', '-', $text);

	// transliterate
	// $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	// remove unwanted characters
	$text = preg_replace('~[^-\w]+~', '', $text);

	// trim
	$text = trim($text, '-');

	// remove duplicate -
	$text = preg_replace('~-+~', '-', $text);

	// lowercase
	$text = strtolower($text);
	if (empty($text)) {
		return 'n-a';
	}

	return $text;
}
function generate_url_member($text){
	$CI =& get_instance();
	
	$uri_path = generate_url($text);

	$uri_path_awal = $uri_path;
	$check = 0 ; 
	// do {
	// 	$check += 1 ; 
	// 	$uri_path = $uri_path_awal."-".$check;
	// } while (
	// 	count($CI->db->get_where('auth_member', array('uri_path'=>$uri_path))->row_array()) > 0
	// );
	while (count($CI->db->get_where('auth_member', array('uri_path'=>$uri_path))->row_array()) > 0) {
		$check += 1 ; 
		$uri_path = $uri_path_awal."-".$check;
	}

	return $uri_path;
}

function generate_url_company($text){
	$CI =& get_instance();
	
	$uri_path = generate_url($text);

	$uri_path_awal = $uri_path;
	$check = 0 ; 
	// do {
	// 	$check += 1 ; 
	// 	$uri_path = $uri_path_awal."-".$check;
	// } while (
	// 	count($CI->db->get_where('company', array('uri_path_name_out'=>$uri_path))->row_array()) > 0
	// );
	while (count($CI->db->get_where('company', array('uri_path_name_out'=>$uri_path))->row_array()) > 0) {
		$check += 1 ; 
		$uri_path = $uri_path_awal."-".$check;
	};

	return $uri_path;
}

function exit_query(){
	$CI =& get_instance();
	print_r(
	$CI->db->last_query()
	);exit;
}
function exit_data($data){
	$CI =& get_instance();
	print_r(
	$data
	);exit;
}
function unique_multidim_array($array, $key) { 
	$temp_array = array(); 
	$i = 0; 
	$key_array = array(); 
	
	foreach($array as $val) { 
		if (!in_array($val[$key], $key_array)) { 
			$key_array[$i] = $val[$key]; 
			$temp_array[$i] = $val; 
		} 
		$i++; 
	} 
	return $temp_array; 
} 
function explodeable($strip,$data,$ret = 0)
{

	$exp = array_filter(explode($strip, $data));

	if (count($exp) > 1) {
		$status = true ;
	}else{
		$status = false;
	}

	if ($ret) {
		return $status;
	}else{
		// if ($status) {
			return array_unique($exp);
		// }else{
			// return '';
		// }
	}
}
function date_today(){
	return date('Y-m-d');
}
function datetime_today(){
	return date('Y-m-d h-i-s');
}
function date_expired(){
	return date('Y-m-d', strtotime('12/31')); // this year 31 desember
}
function query_kutip($data,$kutip = "'"){
	if ($kutip="'") {
		return str_replace("'", "\'", $data);
	}else if($kutip='"'){
		return str_replace('"', '\"', $data);
	}else{
		return $data;
	}
}
function periode($start_date,$end_date,$sp='/'){ #start/end date d-m-y
		$bulan       = array(1=> 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$start_date  = date('M d Y',strtotime($start_date));
		$start       = explode(' ',$start_date);
		$start_tgl   = $start[0];
		$start_month = /*(int)*/$start[1];
		$start_year  = $start[2];
		$end_date    = date('M d Y',strtotime($end_date));
		$end         = explode(' ',$end_date);
		$end_tgl     = $end[0];
		$end_month   = /*(int)*/ $end[1];
		$end_year    = $end[2];
	 
	 // echo "$start_tgl $start_month $start_year ---- $end_tgl $end_month $end_year\n";
	 if($start_year == $end_year){
		  if($start_month == $end_month){
			   if($start_tgl == $end_tgl){
					return $start_tgl.' '.$start_month.', '.$start_year ;
			   }
			   else{
	 	 		  return $start_tgl . ' ' . $sp . ' ' .$end_tgl . ' ' .$start_month.', ' . $end_year;
			   }
		  }
		  else{
			   return $start_tgl . ' ' . $start_month .' '. $sp .' '. $end_tgl. ' '. $end_month.', '.$end_year;
		  }
	 }
	 else{
		  return $start_tgl.' '.$start_month.' '.$start_year .' '. $sp .' '. $end_tgl.' '.$end_month.', '.$end_year;
	 }
}
/*
// function list_galleryImages($id){
// 	$CI =& get_instance();
// 	$CI->load->model('gallerymodel');
// 	$CI->load->model('galleryImagesModel');

// 	$detail = $this->galleryImagesModel->findBy(array('id_gallery'=>$data['id'],'id_lang'=>$id_lang));
    

//     foreach ($detail as $key => $value) {        
// 		$temp['id_gallery']    = $value['id'];
// 		$temp['inv_desc']      = ($value['description']) ? '' : 'hide';
// 		$temp['g_description'] = $value['description'];
// 		$temp['g_name']        = $value['name'];
// 		$temp['g_img']         = ($value['filename'] != '' ? getImg($value['filename'],'large') : getImg('no-image-available.png','large'));

// 		$rend['list_gallery'][] = $temp;
//     }

//     $rend['base_url']       = base_url();
//     $rend['lang']           = $lang_id = get_language_id();
//     $rend['paging']         = 3;
//     $rend['dsp_load_more']  = 


// 	$images  = $CI->gallerymodel->findBy(array('id' => $id),1);
// 	$where['id_gallery'] = $id;
// 	$imageslist          = $CI->galleryImagesModel->findBy($where);
// 	$ret['html']         = '<div class="row mt30" id="gallery_images">';
// 	if ($imageslist) {
// 		$ret['status'] = 1 ;
// 		foreach ($imageslist as $key => $value) {
// 			$img = ($value['img'] != '' ? getImg($value['img'],'small') : getImg('no-image-available.png','small'));

// 			$ret['html'] .= '<div class="col-sm-4 col-md-4 col-xs-12">
// 		                      <a href="" data-toggle="modal" data-target="#myModalVideo">
// 		                        <div class="thumbnail-amcham thumb-gallery">'.$img.'</div>
// 		                      </a>
// 		                    </div>';
// 		}
// 		$ret['html'] = '</div>';
// 	}else{
// 		$ret['status'] = 0;
// 	}

// 	return $ret; 

// }
/*function sitemap($parent=0,$ret){
	$CI			= &get_instance();
	$id_lang 	= id_lang();
	$CI->load->model('frontendmenumodel');
	$data 		= $CI->frontendmenumodel->findBy(array('id_language'=>$id_lang,'id_parent'=>$parent));
	$ret = "<ul class='sitemap'>";
	foreach ($data as $key => $value) {
		$url = '';
		$ret .= "<li><a href='$url'>".ucwords(strtolower($value[name])).'</a>';
		$ret .= sitemap($value['id']);
		$ret .= "</li>";
	}
	$ret .= "</ul>";
	return $ret;
}*/

function closetags($html) {
    preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    for ($i=0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)) {
            $html .= '</'.$openedtags[$i].'>';
        } else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
} 
function unik_uripath_company($name_in,$name_out)
{
	$CI =& get_instance();
	if(!empty($name_in) && !empty($name_out)){
		$uri_path = generate_url($name_in);
	}else if (!empty($name_in)) {
		$uri_path = generate_url($name_in);
	}else if (!empty($name_out)) {
		$uri_path = generate_url($name_out);
	}else{
		die("ERROR");
	}
	$uri_path_awal = $uri_path;
	$check = 0 ; 
	do {
		$check += 1 ; 
		$uri_path = $uri_path_awal."-".$check;
	} while (count($CI->db->get_where('company', array('uri_path_name_out'=>$uri_path))->row_array()) > 0);

	return $uri_path;
}

function member_category_id($id_category){
	$CI =& get_instance();
	$member_category 	= db_get_one('auth_member_category','name',array('id'=>$id_category));
	return $member_category;
}
function view_individual($id)
{
	$CI =& get_instance();
	$CI->load->model('member_model');
	$CI->load->model('company_model');
	$CI->load->model('membership_model');

	$data_member = $CI->member_model->findViewById($id);
	$CI->data['is_sent']   = $data_member['status_id'] == 1 ? 'invis': '';
	$render  ['is_paid']   = $data_member['status_id'] == 1 ? 'invis': '';
	
	$data2 = $CI->company_model->findById($data['company_id']);
	$data  = array_merge($data_member,$data2);
	
	$render['modal_invoice'] = $CI->parser->parse('apps/member/modal_invoice.html',$CI->data,TRUE);
	
	$get_member     = $CI->member_model->findBy(array('id'=> $data_member['member_id']),1);
	$get_company    = $CI->company_model->findBy(array('id'=>$data_member['company_id']),1);
	$get_membership = $CI->membership_model->findBy(array('member_id'=>$data_member['member_id']),1);

	
	$render['name']       = full_name($get_member);
	$render['job']        = $get_member['job'];
	$render['linked_id']  = $get_member['linkedin_id'];
	$render['email']      = $get_member['email'];
	$render['m_t_number'] = $get_member['m_t_number'];
	
	$render['name_in']      = $get_company['name_in'];
	$render['c_address']    = $get_company['address'];
	$render['city']         = $get_company['city'];
	$render['postal']       = $get_company['postal_code'];
	$render['headquarters'] = $get_company['headquarters'];
	$render['website']      = $get_company['website'];
	$render['c_email']      = $get_company['email'];
	$render['t_number']     = $get_company['t_number'];
	// $render['citizenship']          = $get_member['citizenship'];
	// $render['name_out']             = $get_company['name_out'];
	// $render['m_number']             = $get_company['m_number'];	
	// $render["membership_exp"]       = iso_date($get_membership['expired_date']); 
	// $render["membership_last"]      = iso_date($get_membership['last_visited_date']); 
	
	$render['id']                   = $id;			
	$render['is_company']           = ($get_member['member_category_id'] == 1 ) ? 'hide' : '';
	$render['is_membership']        = ($get_membership ) ? '' : 'hide';
	$render['is_membership_active'] = ($get_membership['membership_code'] !="") ? '' : 'hide';
	
	
	$render['status']               = db_get_one('ref_status_payment', 'name',array('id' => $get_member['status_payment_id']));
	$render['membership']           = db_get_one('auth_member_category', 'name',array('id' => $get_member['member_category_id']));
	
	$render["membership_code"]      = $get_membership['membership_code']; 
	$render["membership_regist"]    = iso_date($get_membership['registered_date']); 
	$render["membership_first"]     = iso_date($get_membership['first_registered_date']); 
	$render["member_view_detail"]   = $CI->parser->parse('apps/member/view_detail_individual.html',$render,TRUE);

	$render['page_name'] = "Detail Member" ;
	$render['dsp_btn_invoice'] = "" ;
	return render('apps/member/view',$render,'apps');
}
function view_company($id)
{
 	
}
function proses_member_backend($idedit = "") {
 	$CI =& get_instance();
 	$CI->load->model('member_model');
 	$CI->load->model('company_model');
 	$CI->load->model('membership_model');
 	$CI->load->model('committee_model');
 	$CI->load->model('auth_member_committee_model');
 	$CI->load->model('sector_model');
 	$CI->load->model('auth_member_sector_model');
 	

	$id_user 	  = id_user();
	$CI->layout   = 'none';
	$post 		  = purify($CI->input->post());
	$member_data  = $CI->member_model->findById($idedit); // cari data member edit 

		$ret['error'] = 1;
	$CI->db->trans_start();

	// #check unique email saat input baru atau mengedit dengan email baru
	// if (!$idedit or $member_data['email'] != $post['email_user']) {
	// while ($CI->member_model->findBy(array('email' => $post['email_user']),1)) {
	// 	$ret['message'] = "Email has Registered Before";
	// 	// set_flash_session('message',$ret['message']);
	// 	echo json_encode($ret);
	// 	exit;
	// }
	// }
	if($post['member_category_id'] == 1 && empty($idedit)){
		$check_company_uniqe = $CI->company_model->findBy(array('name_in'=>$post['name_in']),1);
		if(!empty($check_company_uniqe)){
			$ret['message'] = 'Company has created before';
			echo json_encode($ret);
			exit;
		}
	}


	$committee 		= $post['id_committee'];
	$sector 		= $post['id_sector'];
	unset($post['id_committee'],$post['id_sector']);

	if (!$post['company_id']){
		unset($post['company_id']);
	}

	if($post['other-sector-name']){
		$other_sector_name = $post['other-sector-name'];
	}

	if(!$_FILES['img']['name']){
		unset($post['img']);
		if ($post['img_del'] == 1) {
			$data_profile['img'] 			= '';
		}
	} else {
		$ext        = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
		$fileRename = 'individu'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['img'], 0, 50))))."-".date("dMYHis").".".$ext;
		fileToProfileImage($_FILES['img'],0,$fileRename,'individu');
		$data_profile['img']               = $fileRename;
	}

	$data_profile['firstname']             = $post['firstname'];
	$data_profile['lastname']              = $post['lastname'];
	$data_profile['prefix_name']           = $post['prefix_name'];
	$data_profile['job']                   = $post['job'];
	$data_profile['sort']                  = $post['sort'] ? $post['sort'] : NULL;
	$data_profile['is_invis']              = $post['is_invis'];
	$data_profile['m_t_number']            = $post['m_t_number_profile'];
	$data_profile['address_member']            = $post['address_member'];
	$data_profile['email']                 = $post['email_user'];
	$data_profile['uri_path']              = generate_url_member(full_name($data_profile));
	$data_profile['member_category_id']    = $post['member_category_id'];
	$data_profile['status_payment_id']     = $post['status_payment_id'];
	// $data_profile['linkedin_id']    = $post['linkedin_id'];
	// $data_profile['citizenship']    = $post['citizenship'];
	// $data_profile['m_m_number']     = $post['m_m_number_profile'];
	
	//generate password
	$password = generatePassword();
	if ($post['status_payment_id'] == 1){
		$data_profile['password']          = md5($post['member_code']);
	} else if ($idedit) { // bila bila update dan status payment 0
		unset($data_profile['password']);
	}else{
		$data_profile['password']          = md5($password);
	}

	// data member
	if ($idedit) {
		auth_update();
		$iddata = $CI->member_model->update($data_profile,$idedit);	
		$act    = "Update Member";
	}else{
		auth_insert();
		$iddata = $CI->member_model->insert($data_profile);
		$act    = "Insert Member";
	}
	detail_log();	
	insert_log($act);

	//JikaRepresentative
	if ($post['company_id'] && $data_profile['member_category_id'] == 3){
		$data_company = $CI->company_model->findBy(array('id'=>$post['company_id']),1);
		$id_company   = $post['company_id'];

	} else {
		// print_r($_FILES['img_company']) ;exit;
		if(!$_FILES['img_company']['name']){
			unset($post['img_company']);
			if($post['img_company_del'] == 1){
				$data_company['img'] 			= '';
			}
		} else {
			$ext         = pathinfo($_FILES['img_company']['name'], PATHINFO_EXTENSION);
			$fileRename  = 'company'.preg_replace('/[^A-Za-z0-9\-]/', '', (str_replace(" ", "-", substr($post['img_company'], 0, 50))))."-".date("dMYHis").".".$ext;;
			fileToProfileImage($_FILES['img_company'],0,$fileRename,'company');
			$data_company['img'] 			= $fileRename;
		}

		$data_company['name_in']            = $post['name_in']; 
		$data_company['uri_path_name_out']  = generate_url_company($post['name_in']); 
		$data_company['city']               = $post['city'];
		$data_company['address']            = $post['address'];
		$data_company['headquarters']       = $post['headquarters'];
		$data_company['website']            = $post['website'];
		$data_company['t_number']           = $post['m_t_number_company'];
		// $data_company['name_out']    = $post['name_out'];
		// $data_company['m_number']    = $post['m_m_number_company'];
		// $data_company['email']       = $post['email_company'];
		$data_company['description']        = $post['description'];
		$data_company['postal_code']        = $post['postal_code'];
		$data_company['member_id_create']   = $iddata;

		if ( $data_profile['member_category_id'] == 4) {// subdiary
			$data_company['id_parent_company']  = $post['company_id'];
		}else{
			$data_company['id_parent_company']  = 0; // jaga jaga bila user ganti category dari subdiary
		}

		if ($member_data['member_category_id'] == $post['member_category_id'] && $idedit) { // check bila category sebelumnya sama
			$id_company = $CI->company_model->update($data_company,$member_data['company_id']);
			$act            = "Update Company Member";
		}else{
			$id_company = $CI->company_model->insert($data_company);
			$act            = "Insert Company Member";	
		}
			detail_log();	
			insert_log($act);
	}
	//update member company_id
		$update_member['company_id']            = $id_company;
		$CI->member_model->update($update_member,$iddata);

	//membership
		// cari id membership untuk update
		if ($idedit) {
			$id_membership = $CI->membership_model->findBy(array('member_id' => $idedit),1)['id'];
		}

		// status user diedit jadi active
		if ($member_data['status_payment_id'] != $post['status_payment_id'] && $idedit && $post['status_payment_id'] == 1) { 

			$membership_save['membership_code']    = $post['member_code'];
			$membership_save['registered_date']    = date("Y-m-d");
			$membership_save['expired_date']       = date_expired();
			$CI->membership_model->update($membership_save,$id_membership);
			
			// sent email active membership ke user
			$email_user['name']     		= full_name($data_profile);
			$email_admin['company_email']   = $data_company['email'];
			$email_user['category'] 		= db_get_one('auth_member_category','name',array('id'=>$data_profile['member_category_id']));
			$email_user['username'] 		= $post['email_user'];

			if ($post['status_payment_id'] == 1){
				$email_user['password'] = $post['member_code'];
			} else {
				$email_user['password'] = $password;
			}
			$email_user['link']     = base_url().'member';
			sent_email_by_category(2,$email_user,$post['email_user']);


		}else if ($idedit) { 
			// cuma update tanpa ubah status membership
			$membership_save['membership_code']    = $post['member_code'];
			$CI->membership_model->update($membership_save,$id_membership);

		}else{ 
			// insert pertama kali
			$membership_save['member_id']              = $iddata;
			$membership_save['company_id']             = $id_company;
			$membership_save['first_registered_date']  = date("Y-m-d");

			if ($post['status_payment_id'] == 1){
				// kalau status user active 
				$membership_save['membership_code']    = $post['member_code'];
				$membership_save['registered_date']    = date("Y-m-d");
				$membership_save['expired_date']       = date_expired();
			}
			// insert membership_information dan update member untuk simpan idnya
			$membership_id                                  = $CI->membership_model->insert($membership_save);
			$membership_update['membership_information_id'] = $membership_id;
			$CI->member_model->update($membership_update,$iddata);
		
			/*	
			//send email to notif admin 
			$email_admin['category']        = db_get_one('auth_member_category','name',array('id'=>$data_profile['member_category_id']));
			$email_admin['is_company']      = $data_profile['member_category_id'] == 1  ? 'hide' : '';

			$email_admin['name']            = full_name($data_profile);
			$email_admin['job']             = $data_profile['job'];
				// $email_admin['citizenship']     = $data_profile['citizenship'];
				// $email_admin['linkedin_id']     = $data_profile['linkedin_id'];
			$email_admin['email']           = $data_profile['email'];
				// $email_admin['name_out']        = $data_company['name_out'];
			$email_admin['name_in']         = $data_company['name_in'];
			$email_admin['company_address'] = $data_company['address'];
			$email_admin['city']            = $data_company['city'];
			$email_admin['postal']          = $data_company['postal_code'];
			$email_admin['headquarters']    = $data_company['headquarters'];
			$email_admin['website']         = $data_company['website'];
				// $email_admin['company_email']   = $data_company['email'];
			$email_admin['t_number']        = $data_company['t_number'];
				// $email_admin['m_number']        = $data_company['m_number'];
			$email_admin['link']            = base_url().'apps';

			sent_email_by_category(6,$email_admin,EMAIL_ADMIN_TO_SEND);*/
		}

	//
	#committee
		foreach ($committee as $key => $value) {
			if($value){
				$cek = $CI->committee_model->fetchRow(array('id'=>$value));//liat tags name di tabel ref
				if(!$cek){//kalo belom ada
				}
				else{
					$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
				}
				$cekTagsNews = $CI->auth_member_committee_model->fetchRow(array('committee_id'=>$id_tags,'member_id'=>$iddata)); //liat di tabel news tags, (utk edit)


				if(!$cekTagsNews){//kalo blm ada ya di insert
					$tag['committee_id'] = $id_tags;
					$tag['member_id'] = $iddata;
					$id_news_tags = $CI->auth_member_committee_model->insert($tag);
				}
				else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
					$id_news_tags = $cekTagsNews['id'];
				}
				$del_tags_news[] = $id_news_tags;

			}
		}

		$CI->db->where_not_in('a.id',$del_tags_news); 
		$delete = $CI->auth_member_committee_model->findBy(array('a.member_id'=>$iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)
		
		foreach ($delete as $key => $value) {
			$CI->auth_member_committee_model->delete($value['id']);
		}
	#end_committee

	
	#sector
		if($post['member_category_id'] != '3'){

			foreach ($sector as $key => $value) {
				if($value){
				$cek = $CI->sector_model->fetchRow(array('id'=>$value));//liat tags name di tabel ref
				if($cek){
					$id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
				}
				// $cekTagsNews = $CI->auth_member_sector_model->fetchRow(array('sector_id'=>$id_tags,'member_id'=>$iddata)); //liat di tabel news tags, (utk edit)
				$cekTagsNews = $CI->auth_member_sector_model->fetchRow(array('sector_id'=>$id_tags,'company_id'=>$id_company)); //liat di tabel news tags, (utk edit)

				if(!$cekTagsNews){//kalo blm ada ya di insert
					$tags['sector_id']  = $id_tags;
					$tags['company_id'] = $id_company;
					$id_news_tags = $CI->auth_member_sector_model->insert($tags);
				}
				else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
					$id_news_tags = $cekTagsNews['id'];
				}
				$del_tags_news[] = $id_news_tags;
			}
		}
		$CI->db->where_not_in('a.id',$del_tags_news); 
		// $delete = $CI->auth_member_sector_model->findBy(array('a.member_id'=>$iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)

		$delete = $CI->auth_member_sector_model->findBy(array('a.company_id'=>$id_company)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $idedit)
		
		foreach ($delete as $key => $value) {
			$CI->auth_member_sector_model->delete($value['id']);
		}
	}
	#end_sector
		
	$CI->db->trans_complete();
	// set_flash_session('message',$ret['message']);
	$ret['message'] = 'Insert Success';
	$ret['error'] = 0;
	echo json_encode($ret);
}
function add_member_backend($id,$data_view) {
	// $id  = id member
	$CI =& get_instance();
	$CI->load->model('individual_model');
	$CI->load->model('company_model');
	$CI->load->model('membership_model');
	$CI->load->model('company_model');
	$CI->load->model('committee_model');
	$CI->load->model('auth_member_committee_model');
	$CI->load->model('sector_model');

 	if($id){
		$data  = $CI->individual_model->findBy(array('id'=> $id),1);
        if(!$data){
			die('404');
		}else{
			$data['checked_gratis']      = $data['is_invis']?"checked":'';
			$data['email_user']          = $data['email'];
			$data['m_t_number_profile']  = $data['m_t_number'];
			$data['address_member']       = $data['address_member'];
			$data_company                = $CI->company_model->findById($data['company_id']);
			$data['name_in']             = $data_company['name_in'];
			$data['city']                = $data_company['city'];
			$data['address']             = $data_company['address'];
			$data['postal_code']         = $data_company['postal_code'];
			$data['headquarters']        = $data_company['headquarters'];
			$data['website']             = $data_company['website'];
			$data['m_t_number_company']  = $data_company['t_number'];
			$data['description']         = $data_company['description'];
			$data['company_id']          = $data_company['id'];
			$data['member_code']         = $CI->membership_model->findBy(array('member_id'=>$id),1)['membership_code'];
		}
		$data['judul']              = 'Add';
		$data['proses']             = 'Update';
		
	} else{
		$data['judul']              = 'Add';
		$data['proses']             = 'Simpan';
		$data['name']               = '';
		$data['id']                 = '';
		$data['firstname']          = '';
		$data['lastname']           = '';
		$data['prefix_name']        = '';
		$data['citizenship']        = '';		
		$data['address_member']        = '';		
		$data['job']                = '';
		$data['sort']               = '';
		// $data['linkedin_id']        = '';
		$data['m_t_number_profile'] = '';
		// $data['m_m_number_profile'] = '';
		$data['m_t_number_company'] = '';
		// $data['m_m_number_company'] = '';
		$data['email']              = '';
		$data['uri_path']           = '';
		$data['name_in']            = '';
		// $data['name_out']           = '';
		$data['address']            = '';
		$data['website']            = '';
		$data['headquarters']       = '';
		$data['description']        = '';
		$data['postal_code']        = '';
		$data['city']               = '';
		$data['company_id']         = '';
		$data['checked_gratis']     = '';
		$data['email_user']         = '';
		$data['email_company']      = '';		
		$data['member_code']        = '';		
		$data['email_user']         = '';
		$data['m_t_number_profile'] = '';
		$data['name_in']            = '';
		$data['city']               = '';
		$data['address']            = '';
		$data['postal_code']        = '';
		$data['headquarters']       = '';
		$data['website']            = '';
		$data['m_t_number_company'] = '';
		$data['description']        = '';
		$data['company_id']         = '';
		$data['member_code']        = '';
	}
	if ($data['member_category_id'] == 3) {
		$data['list_member_category'] = selectlist2(
		array(
			'table'=>'auth_member_category',
			'selected'=>$data['member_category_id']
			)
		);
	}else{
		$data['list_member_category'] = selectlist2(
			array(
				'table'=>'auth_member_category',
				'selected'=>$data['member_category_id']
				,'where' => 'id != 3 '
				)
			);
		
	}


	$data['list_company'] = selectlist2(
	array(
		'table'=>'company',
		'selected'=>$data['company_id'],
		'where' => array('is_delete' => 0),
		'name' => 'name_in'
		)
	);

	$data['list_status_payment'] = selectlist2(
	array(
		'table'=>'ref_status_payment',
		'selected'=>$data['status_payment_id'],
		'where' => 'id = 1 or id = 2',
		'name' => 'name'
		)
	);
	
	$data['img']					= imageProfile($data['img'],'individu');
	$data['img_company'] 			= imageProfile($data_company['img'],'company');
	$data['list_status_publish'] 	= selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['id_status_publish']));
	$data['list_languages'] 		= selectlist2(array('table'=>'language','title'=>'All Languages','selected'=>$data['id_lang']));

	$CI->db->order_by('a.name','asc');
	$datas = $CI->committee_model->findBy();
	if ($id) {
		$cekTagsNews = $CI->auth_member_committee_model->findBy(array('member_id'=>$id));
		
		foreach ($cekTagsNews as $key => $value) {
		$a[] .= $value['committee_id'];
		}
	}else{
		$a[] = "";
	}

	foreach ($datas as $key => $value) {
		
		if (in_array($value['id'], $a, true)) {
			$checked = 'checked';
		} else {
			$checked = '';
		}
	
		$ret_committee .= '<div class="col-sm-6">
          <label class="checkbox-committee"><input type="checkbox" '.$checked.' value="'.$value['id'].'" id="'.$value['id'].'" class="committee" name="id_committee[]" >'.$value['name'].'</label>
        </div>';
	}

	//sector
	$CI->db->order_by('a.name','asc');
	$CI->db->where('is_other = 0');
	$datas2 = $CI->sector_model->findBy();
	$count_datas	 = count($datas2); 

	list($list_sector_1,$list_sector_2 )= array_chunk($datas2, (ceil(count($datas2) / 2)+3));
	$CI->db->where('is_parent_other != 0');
	$other_opt = $CI->sector_model->findBy();
	$list_sector_2[] = $other_opt[0];

	if ($data_company['id']) {
		$CI->db->where('a.is_delete_tag = 0');
		$CI->db->where('a.company_id',$data_company['id']);
		$cekSector = $CI->sector_model->findviewBy();
		foreach ($cekSector as $key => $value) {
			$b[] .= $value['id'];
			if ($value['is_other'] == 1 && $value['is_parent_other'] == 1) {
				$data['check_other'] = 1;
				$data['check_other_value'] = "";
			}else if ($value['is_other'] == 1) {
				$data['check_other'] = 1;
				$data['check_other_value'] = $value['name'];
			}else{
				$data['check_other'] = 0;
				$data['check_other_value'] = "";
			}
		}
	}
	$first_word1   = strtolower(substr($list_sector_1[0]['name'],0,1));
	$first_word2   = strtolower(substr($list_sector_2[0]['name'],0,1));

	foreach ($list_sector_1 as $key => $value) {
		if (in_array($value['id'], $b, true)) {
			$checked1 = 'checked';
		} else {
			$checked1 = '';
		}
		$word_first1 = strtolower(substr($value['name'],0,1));    

		if ($word_first1 != $first_word1 ) {
			$ret_sector1 .= '<hr class="line-content">';
			$first_word1 = $word_first1;
		}
		$ret_sector1 .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
          <label class="checkbox-sector"><input type="checkbox" '.$checked1.' value="'.$value['id'].'" id="'.$value['id'].'" class="sector" name="id_sector[]" >'.$value['name'].'</label>
        </div>';
	}	
	foreach ($list_sector_2 as $key => $value) {
		if (in_array($value['id'], $b, true)) {
			$checked2 = 'checked';
		} else {
			$checked2 = '';
		}
		$word_first2 = strtolower(substr($value['name'],0,1));    

		if ($word_first2 != $first_word2 ) {
			$ret_sector2 .= '<hr class="line-content">';
			$first_word2 = $word_first2;
		}
		$ret_sector2 .= '<div class="checkbox cb-amcham cb-amcham-grey {hidden_list}">
          <label class="checkbox-sector"><input type="checkbox" '.$checked2.' value="'.$value['id'].'" id="'.$value['id'].'" class="sector" name="id_sector[]" >'.$value['name'].'</label>
        </div>';
	}	
	$data['list_sector_1'] 	.= $ret_sector1;
	$data['list_sector_2'] 	.= $ret_sector2;

	$data['list_committee'] .= $ret_committee;
	$data['url_back'] = $data_view['url_back'] ? $data_view['url_back'] : '' ;

	render('apps/member/add',$data,'apps');
}
function resize_image($source,$new_image,$width=200,$height=200,$quality=90){
    $CI=& get_instance();
    $config['image_library']  = 'gd2';
    $config['source_image']   = $source;
    $config['new_image']      = $new_image;
    $config['create_thumb']   = FALSE;
    $config['maintain_ratio'] = TRUE;
    $config['width']          = $width;
    $config['height']         = $height;
    $config['quality']        = 80;
    $CI->load->library('image_lib');
    $CI->image_lib->initialize($config); 
    $CI->image_lib->resize();
}
function compress_image($source, $destination, $quality) {
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);
    imagejpeg($image, $destination, $quality);
    return $destination;
}

function template_event_value($field,$id)
{
	switch ($field) {
		// case 'id_ref_handicap':
		// $where['id'] = $id;
		// $ret = db_get_one('ref_handicap',"name",$where);
		// break;

		case 'id_ref_gender':
		$where['id'] = $id;
		$ret = db_get_one('ref_gender',"name",$where);
		break;

		case 'id_ref_meal':
		$where['id'] = $id;
		$ret = db_get_one('ref_meal',"name",$where);
		break;

		case 'id_ref_member':
		$where['id'] = $id;
		$ret = db_get_one('ref_member',"name",$where);
		break;

		case 'id_ref_event_price':
		$where['id'] = $id;
		$ret = db_get_one('price',"name",$where);
		break;

		case 'is_approve':
		$ret = ($id == 1 )? "Paid" :"";
		break;

		default:
		$ret = $id;
		break;
	}
	return $ret;
}

function template_event_dropdown($field)
{
    switch ($field) {
        case 'id_ref_handicap':
            $field  .=  '<select name="'.$value['parameter'].'" id="'.$value['parameter'].'" class="form-control input-amcham" '.$is_required.' >';
            $field  .=  selectlist2(array('table'=>'ref_handicap','title'=>'Select Handicap'));
            $field  .=  '</select>';
        break;

        case 'id_ref_gender':
            $field  .=  '<select name="'.$value['parameter'].'" id="'.$value['parameter'].'" class="form-control input-amcham" '.$is_required.' >';;
            $field  .=  selectlist2(array('table'=>'ref_gender','title'=>'Select Gender'));
            $field  .=  '</select>';
        break;

        case 'id_ref_member':
            $field  .=  '<select name="'.$value['parameter'].'" id="'.$value['parameter'].'" class="form-control input-amcham" '.$is_required.' >';                            
            $field  .=  selectlist2(array('table'=>'ref_member','title'=>'Select Member'));
            $field  .=  '</select>';
            
        break;

        case 'id_ref_meal':
            $field  .=  '<select name="'.$value['parameter'].'" id="'.$value['parameter'].'" class="form-control input-amcham" '.$is_required.' >';
            $field  .=  selectlist2(array('table'=>'ref_meal','title'=>'Select Meal'));
            $field  .=  '</select>';
            
        break;
        
        default:
            $field = "";
            break;
    }
    return $ret;
}
function dirToArray($dir) {
  $contents = array();
  # Foreach node in $dir
  foreach (scandir($dir) as $node) {
      # Skip link to current and parent folder
      if ($node == '.')  continue;
      if ($node == '..') continue;
      # Check if it's a node or a folder
      if (is_dir($dir . DIRECTORY_SEPARATOR . $node)) {
          # Add directory recursively, be sure to pass a valid path
          # to the function, not just the folder's name
          $contents[$node] = dirToArray($dir . DIRECTORY_SEPARATOR . $node);
      } else {
          # Add node, the keys will be updated automatically
          $contents[] = $node;
      }
  }
  # done
  return $contents;
}

function tags_records($text, $separator = ' // ', $limit = 5){
	if ($text == '') {
		return "";
		exit;
	}
	$array = explode($separator, $text);
	$count = count($array);

	// print_r($array);
	$array = array_slice($array, 0, $limit);
	
	foreach ($array as $key => $value) {
		$data['tags'][]	 =  array('value' => $value);
	}
	// print_r($data);
	// exit;

	$data['moretags'] = $count > $limit ? '...' : '' ;

	$CI =& get_instance();
	return $CI->parser->parse('layout/ddi/records_tags.html', $data,true);

}

 function gallery_watermark($path){
          // Open the image to draw a watermark
          $image = new Imagick();
          $image->readImage(getcwd(). $path);

          // Open the watermark image
          // Important: the image should be obviously transparent with .png format
          $watermark = new Imagick();
          $watermark->readImage(getcwd(). "/asset/images/watermark.png");

          // Retrieve size of the Images to verify how to print the watermark on the image
          $img_Width = $image->getImageWidth();
          $img_Height = $image->getImageHeight();
          $watermark_Width = $watermark->getImageWidth();
          $watermark_Height = $watermark->getImageHeight();

          // // Check if the dimensions of the image are less than the dimensions of the watermark
          // // In case it is, then proceed to 
          // if ($img_Height < $watermark_Height || $img_Width < $watermark_Width) {
          //     // Resize the watermark to be of the same size of the image
          //     $watermark->scaleImage($img_Width, $img_Height);

          //     // Update size of the watermark
          //     $watermark_Width = $watermark->getImageWidth();
          //     $watermark_Height = $watermark->getImageHeight();
          // }

          // Calculate the position
          $x = ($img_Width - $watermark_Width) / 2;
          $y = ($img_Height - $watermark_Height) / 2;

          // Draw the watermark on your image
          $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);


          // From now on depends on you what you want to do with the image
          // for example save it in some directory etc.
          // In this example we'll Send the img data to the browser as response
          // with Plain PHP
          // $image->writeImage("/test_watermark/<kambing class=""></kambing>" . $image->getImageFormat()); 
          // header("Content-Type: image/" . $image->getImageFormat());
          // echo $image;

          // Or if you prefer to save the image on some directory
          // Take care of the extension and the path !
          $image->writeImage(getcwd(). $path); 
    }

function event_approve($event_id, $id, $render,$popup_json)
{
	$CI =& get_instance();
	$CI->load->model('eventmodel');
	$CI->load->model('paymentconfirmation_model');
	if (!empty($id)){

			$data_payment    = $CI->paymentconfirmation_model->findBy(array('event_id' => $event_id,'member_id'=>$id),1);
			$data['is_paid'] = 1;
			$CI->paymentconfirmation_model->update_frontend($data,$data_payment['id']);

			$data_event = $CI->eventmodel->findById($event_id);

			$data_save['is_approve'] = 1;
			$update_status = $CI->eventmodel->updateApprovaalParticipant($data_save,$id);
			
			$data_participant = $CI->eventmodel->selectDataParticipant($id,1);
			$email_usernya = $data_participant['email_2'] != '' ? $data_participant['email_2']: $data_participant['email_1'];

			if ($data_participant['fullname'] != '') {
			    $participant_name = $data_participant['fullname'] ; 
			}else if($data_participant['firstname'] != ''){
			    $participant_name = $data_participant['firstname'] ; 
			}else{
			    $participant_name = $data_participant['lastname'] ; 
			}
			   
			$email_user['name']       = $participant_name;
			$email_user['event_name'] = $data_event['name'];

			sent_email_by_category(16,$email_user,$email_usernya);


			if ($update_status){
				//redirect ke halaman participant
				if ($popup_json) {
					if ($popup_json != 'nothing') {
						echo json_encode(array('redirect'=>$render));		
					}
				} else if(!empty($render)){
					redirect($render);
					
				}
			}
		}
}
function bank_page_email($content)
{
	$CI =& get_instance();
	$content['data'] = $content;
	return $CI->parser->parse('layout/ddi/bank_list.html', $content,true);
}
function html_to_excel($html,$filename){
	$CI =& get_instance();
	$CI->load->library('PHPExcel');
	$htmlContent = $html;
			
	$DOM = new DOMDocument();
	$DOM->loadHTML($htmlContent);
	
	$Header = $DOM->getElementsByTagName('tr');
	$Detail = $DOM->getElementsByTagName('td');
	
    //#Get header name of the table
	$data_table = [];
	
	foreach($Header as $NodeHeader) 
	{	
		$temp_table_value = [];
		foreach ($NodeHeader->childNodes as $value) {
			$temp_table_value[] =  trim($value->textContent);  
		}
		$data_table[] = $temp_table_value;
	}
	
	
	$excelDoc = new PHPExcel();
	$excelDoc->setActiveSheetIndex(0);
	$excelDoc->setActiveSheetIndex(0);
	$excelDoc->getActiveSheet()->fromArray($data_table, null, 'A1');

//   ob_clean();
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
     header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
     header('Cache-Control: max-age=0');
     $objWriter = PHPExcel_IOFactory::createWriter($excelDoc, 'Excel2007');
     $objWriter->save('php://output');
    //  exit;
}

function check_is_company($id_user_category)
{
	$id  = ['1','4'];
	return in_array($id_user_category, ['1','4'] );
}