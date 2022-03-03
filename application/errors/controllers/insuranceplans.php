<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class InsurancePlans extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index(){
        $this->load->model('productheaderModel');
	$this->load->model('productModel');
        $data = $this->productheaderModel->fetchRow(array('id_status_publish'=>'2','publish_date <='=>date('Y-m-d')));
        
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

	
	$img = $data['img'];
	$data['img']             = image($data['img'],'large');

        $data_product = $this->productModel->findBy(array('id_status_publish'=>'2','publish_date <='=>date('Y-m-d')));
	foreach ($data_product as $key => $value) {		
		$data['data'][$key]['news_title'] 		= quote_form($value['news_title']);
		$data['data'][$key]['url'] 		= base_url().'insuranceplans/product/'.$value['uri_path'];
		$data['data'][$key]['teaser'] 		= $value['teaser'];
		$data['data'][$key]['publish_date'] 	= iso_date($value['publish_date']);
		$data['data'][$key]['edit'] 		 	= is_edit_news($value['id'],$value['user_id_create'],$approval_level_news,'delete');
		$data['data'][$key]['delete'] 		 	= is_delete_news($value['id'],$value['user_id_create'],$approval_level_news);
	}
        $data['top_content']     = top_content();
        $data['ads']             = ads_widget();
        $data['box_widget']      = box_widget();
        $data['create_date']     = iso_date_custom_format($data['publish_date'],'d').' '.get_month(iso_date_custom_format($data['publish_date'],'F')).' '.iso_date_custom_format($data['publish_date'],'Y');
        
        render('layout/ddi/product/product',$data);
    }
	function product($uri_path){
		$this->load->model('productModel');
		if($uri_path){
			$data = $this->productModel->fetchRow(array('uri_path'=>$uri_path));
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
				if(!$data){
					die('404');
				}
				// $data['news_title']  = quote_form($data['news_title']);
				$data['teaser']		 = quote_form($data['teaser']);
				$data['create_date'] = iso_date_time($data['create_date']);
				$img = $data['img'];
				$data['img']             = image($data['img'],'large');
				$data_desc = $this->productModel->get_desc($data['id']);
				foreach($data_desc as $n =>  $data_child){
					++$i;
					$data_desc[$n]['in'] = '';
					if($i==1){
						$data_desc[$n]['in'] = 'in';
					}
					$data_desc[$n]['nomor'] 	= ++$nomor;
				}
				$data['data_desc'] = $data_desc;
				$data['top_content']     = top_content(1);
				$data['ads']             = ads_widget();
				$data['box_widget']      = box_widget();
				$data['share_widget']       = share_widget();
				$data['meta'] = '<!-- meta fb og start -->
						<meta property="og:url" content="'.base_url().'articledetail/index/'.$data['uri_path'].'" />
						<meta property="og:type" content="article" /> 
						<meta property="og:title" content="'.remove_kutip($data['seo_title']).' |  Futuready" />
							<meta property="og:image:type" content="image/jpeg"> 
						<meta property="og:image" content="'.base_url().'images/article/large/'.$img.'" />
						<meta property="og:site_name" content="Futuready" />
						<meta property="og:description" content='."'".remove_kutip($data['meta_description'])."'".' />
						<!-- meta fb og end -->
							<script> var news_title ="'.$data['news_title'].'"; </script>
						<!-- meta twitter og start -->
							<meta content="'.remove_kutip($data['seo_title']).' | @futureadyID" data-page-subject="true" name="twitter:title" />
							<meta content='."'".remove_kutip($data['meta_description'])."'".' data-page-subject="true" name="twitter:description" />
							<meta content="'.base_url().'images/article/large/'.$img.'" data-page-subject="true" name="twitter:image" />
							<meta content="@futureadyID" data-page-subject="true" name="twitter:site" />
							<meta content="'.base_url().'articledetail/index/'.$data['uri_path'].'" data-page-subject="true" name="twitter:url" />
							<meta content="@futureadyID" data-page-subject="true" name="twitter:creator" />
							<meta content="photo" data-page-subject="true" name="twitter:card" />
							<meta content="560" data-page-subject="true" name="twitter:image:width" />
							<meta content="750" data-page-subject="true" name="twitter:image:height" />
						<!-- meta twitter og end -->'."
		
						<!-- Start Visual Website Optimizer Asynchronous Code -->
						<script type='text/javascript'>

						var _vwo_code=(function(){

						var account_id=29492,

						settings_tolerance=2000,

						library_tolerance=2500,

						use_existing_jquery=false,

						// DO NOT EDIT BELOW THIS LINE

						f=false,d=document;return{use_existing_jquery:function(){return use_existing_jquery;},library_tolerance:function(){return library_tolerance;},finish:function(){if(!f){f=true;var a=d.getElementById('_vis_opt_path_hides');if(a)a.parentNode.removeChild(a);}},finished:function(){return f;},load:function(a){var b=d.createElement('script');b.src=a;b.type='text/javascript';b.innerText;b.onerror=function(){_vwo_code.finish();};d.getElementsByTagName('head')[0].appendChild(b);},init:function(){settings_timer=setTimeout('_vwo_code.finish()',settings_tolerance);this.load('//dev.visualwebsiteoptimizer.com/j.php?a='+account_id+'&u='+encodeURIComponent(d.URL)+'&r='+Math.random());var a=d.createElement('style'),b='body{opacity:0 !important;filter:alpha(opacity=0) !important;background:none !important;}',h=d.getElementsByTagName('head')[0];a.setAttribute('id','_vis_opt_path_hides');a.setAttribute('type','text/css');if(a.styleSheet)a.styleSheet.cssText=b;else a.appendChild(d.createTextNode(b));h.appendChild(a);return settings_timer;}};}());_vwo_settings_timer=_vwo_code.init();

						</script>

						<!-- End Visual Website Optimizer Asynchronous Code -->
						";
                $data['list_city'] = selectlist2(array('table'=>'city','title'=>'Select City','selected'=>$data['city'],'order'=>'id'));

				render('layout/ddi/product/product_detail',$data);
			}
		}
		
	}
    
    function buy_product(){
		$this->layout 	= 'none';
        $this->load->model('productbuyModel');
		$post 					= purify($this->input->post());
		if($post){
			$this->form_validation->set_rules('nama', '"Name"', 'required');
            $this->form_validation->set_rules('mobile', '"Mobile"', 'required');
            $this->form_validation->set_rules('city', '"City"', 'required');
	    $this->form_validation->set_rules('date_of_birth', '"Date of Birth"', 'required');
            $this->form_validation->set_rules('email', '"Email"', 'required|valid_email'); 
                if ($this->form_validation->run() == FALSE){
					 $message = validation_errors();
					 $status = 'error';
				}
				else{
				$now = date('Y');
				$data['date_of_birth_data'] = $now - iso_date_custom_format($post['date_of_birth'], 'Y');
				$data['mobile_data'] = $post['mobile'];
				$data['product_data'] = $post['product'];
				$data['city_data'] = $this->db->select('name')->get_where('city',"id='$post[city]'")->row()->name;
				$data['email_data'] = $post['email'];
				$data['nama_data'] = $post['nama'];
				$post['date_of_birth'] = iso_date($post['date_of_birth']);

                    $proses =  $this->productbuyModel->insert($post);
                    $status = 'success';
					$message = 'Success to buy Product';	
				
				}
				
			$data['message'] 	=  "<div class='$status'> $message</div>";
			$data['status'] 	=  $status;
			echo json_encode($data);
		}
	}
}