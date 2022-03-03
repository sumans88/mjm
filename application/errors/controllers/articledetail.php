<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Articledetail extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index($uri_path){
        $this->load->model('newsmodel');
        $this->load->model('newstagsmodel');
        $this->load->model('tagsmodel');
        $data = $this->newsmodel->fetchRow(array('a.uri_path'=>$uri_path));
        if(!$data or $data['publish_date'] > date('Y-m-d')){
            redirect('tidakditemukan');
        }
        else{
		$data['img_news_class'] = '';
		$data['img_news_class_button'] = 'hide';
		$data['img_news_class_data'] = 'hide';
		$data['img_news_class_link'] = '';
		if($data['link_youtube_video']){
			$data['link_youtube_video'] = str_replace("watch?v=","embed/",$data['link_youtube_video']);
			$data['img_news_class'] = 'img-news-with-video';
			$data['img_news_class_button'] = 'img-news-class-button-super-large';
			$data['img_news_class_link'] = 'hide';
			$data['img_news_class_data'] = '';
		}
            $data['share_widget']       = share_widget();

            $tags = $this->newstagsmodel->findBy(array('id_news'=>$data['id']));
            foreach ($tags as $key => $value) {
                $tag            .=  ', '."<a href='".$this->baseUrl."article/tags/$value[uri_path]'>".$value['tags'].'</a>';
				$meta_keywords .= ', '.$value['tags'];
                $id_tags[]       = $value['id_tags'];
            }
            $this->tagsmodel->tagsCounter(implode(',', $id_tags));
            $data['tags']        = '<span>TOPIK:</span> '.substr($tag,1);
            $this->newsmodel->newsCounter($data['id']);
            $img = $data['img'];
            $data['img']             = image($data['img'],'large');
	    $data['id_news'] = $data['id'];
            $user_sess_data = $this->session->userdata('MEM_SESS');
		
            if($user_sess_data){
                $this->load->model('LogArticleModel');
                $this->LogArticleModel->log_read_article_user_activity($data['id'],$user_sess_data['id']);
            }
        }
	$data['disabled_form'] = '';
	$data['hidden_form'] = '';
	$user_sess_data = $this->session->userdata('MEM_SESS');
	if(!$user_sess_data){
			$user_sess_data = $this->session->userdata('ADM_SESS');
		}
	if(!$user_sess_data){
		$data['disabled_form'] = 'disabled';
	} else {
		$data['hidden_form'] = 'hidden';	
	}
        $data['top_content']     = top_content();
        $data['ads']             = ads_widget();
        $data['box_widget']      = box_widget();
        $data['create_date']     = iso_date_custom_format($data['publish_date'],'d').' '.get_month(iso_date_custom_format($data['publish_date'],'F')).' '.iso_date_custom_format($data['publish_date'],'Y');
        $data['artikel_terkait']    = artikel_terkait($data['id'],$id_tags,$data['id_news_category']);
        $data['comment_article_not_login']= comment_article_not_login();
		$data['meta_description_general']= ($data['meta_description']) ? $data['meta_description'] : $data['teaser'] ;
		$data['meta_title']= ($data['seo_title']) ? $data['seo_title'] : $data['news_title'] ;
		$data['meta_keywords_general']= $data['meta_keywords'] .$meta_keywords;
		$data['comments_data'] = getcomments($data['id_news']);
		$data['meta'] = 
			'<!-- meta fb og start -->
			<meta property="og:url" content="'.base_url().'articledetail/index/'.$data['uri_path'].'" />
			<meta property="og:type" content="article" /> 
			<meta property="og:title" content="'.remove_kutip($data['meta_title']).' |  Futuready" />
			<meta property="og:image:type" content="image/jpeg"> 
			<meta property="og:image" content="'.base_url().'images/article/large/'.$img.'" />
			<meta property="og:site_name" content="Futuready" />
			<meta property="og:description" content='."'".remove_kutip($data['meta_description_general'])."'".' />
			<!-- meta fb og end -->
			<script> var news_title ="'.$data['news_title'].'"; </script>
			<!-- meta twitter og start -->
			<meta content="'.remove_kutip($data['meta_title']).' | @futureadyID" data-page-subject="true" name="twitter:title" />
			<meta content='."'".remove_kutip($data['meta_description_general'])."'".' data-page-subject="true" name="twitter:description" />
			<meta content="'.base_url().'images/article/large/'.$img.'" data-page-subject="true" name="twitter:image" />
			<meta content="@futureadyID" data-page-subject="true" name="twitter:site" />
			<meta content="'.base_url().'articledetail/index/'.$data['uri_path'].'" data-page-subject="true" name="twitter:url" />
			<meta content="@futureadyID" data-page-subject="true" name="twitter:creator" />
			<meta content="photo" data-page-subject="true" name="twitter:card" />
			<meta content="560" data-page-subject="true" name="twitter:image:width" />
			<meta content="750" data-page-subject="true" name="twitter:image:height" />
			<!-- meta twitter og end -->';
			render('articledetail',$data);
    }
	function get_comments_data($id_news){
		$data['comment_data'] = getcomments($id_news);
		$data['id_news'] = $id_news;
		echo json_encode($data);
	}
	function comment_process(){
	$post = purify($this->input->post());
        if($post){
		$this->load->model('commentModel');
    		$this->form_validation->set_rules('commentar', '"Comment"', 'required'); 
		if ($this->form_validation->run() == FALSE){
			 $message = validation_errors();
			 $status = 'error';
		} else {
			$user_sess_data = $this->session->userdata('ADM_SESS');
			$post['is_admin'] = 1;
			if(!$user_sess_data){
				$user_sess_data = $this->session->userdata('MEM_SESS');
				$post['is_admin'] = 0;
			}
			if($user_sess_data){
				$post['user_id_create'] = $user_sess_data['id'];
				$this->commentModel->insert($post);
				$status = 'success';
				$message = 'Komentar berhasil di tambahkan.';
			} else {
				$status = 'Terjadi Kesalahan';
				$message = comment_article_not_login();
			}
			$data['message'] 	=  "<div class=''> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
    }
    function like_process(){
	$post = purify($this->input->post());
        if($post){
		$this->load->model('commentModel');
		$user_sess_data = $this->session->userdata('ADM_SESS');
		if(!$user_sess_data){
			$user_sess_data = $this->session->userdata('MEM_SESS');
		}
		if($user_sess_data){
			$post['user_id_create'] = $user_sess_data['id'];
			$check_exist_active = $this->commentModel->findBycommentlike($post,1);
			if($check_exist_active==0){
				$this->commentModel->comment_like($post);
			}
			$status = 'success';
			$message = 'Komentar Berhasil di Tambahkan.';
		} else {
			$status = 'Terjadi Kesalahan';
			$message = comment_article_not_login();
		}
		$data['message'] 	=  "<div class=''> $message</div>";
		$data['status'] 	=  $status;
		echo json_encode($data);
	}
    }
    function delete_comment(){
	$post = purify($this->input->post());
        if($post){
		$this->load->model('commentModel');
		$user_sess_data = $this->session->userdata('ADM_SESS');
		$post['is_admin'] = 1;
		if(!$user_sess_data){
			$user_sess_data = $this->session->userdata('MEM_SESS');
			$post['is_admin'] = 0;
		}
		if($user_sess_data){
			$post['user_id_modify'] = $user_sess_data['id'];
			$check_exist_active = $this->commentModel->delete($post['id_comment'], $user_sess_data['id']);
			$status = 'success';
			$message = 'Komentar berhasil di hapus.';
		} else {
			$status = 'Terjadi Kesalahan';
			$message = comment_article_not_login();
		}
		$data['message'] 	=  "<div class=''> $message</div>";
		$data['status'] 	=  $status;
		echo json_encode($data);
	}
    }
    function unlike_process(){
	$post = purify($this->input->post());
        if($post){
		$this->load->model('commentModel');
		$user_sess_data = $this->session->userdata('ADM_SESS');
		if(!$user_sess_data){
			$user_sess_data = $this->session->userdata('MEM_SESS');
		}
		if($user_sess_data){
			$post['user_id_create'] = $user_sess_data['id'];
			$this->commentModel->comment_unlike($post);
			$status = 'success';
			$message = 'Komentar Berhasil di Tambahkan.';
		} else {
			$status = 'Terjadi Kesalahan';
			$message = comment_article_not_login();
		}
		$data['message'] 	=  "<div class=''> $message</div>";
		$data['status'] 	=  $status;
		echo json_encode($data);
	}
    }
    function flag_comment(){
	$post = purify($this->input->post());
        if($post){
		$this->load->model('commentModel');
		$check_exist_active = $this->commentModel->flag($post['id_comment']);
		echo json_encode($data);
	}
    }
}