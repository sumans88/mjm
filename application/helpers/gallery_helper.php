<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function listphoto($id = array(),$conf = array()){
	$CI =& get_instance();
	$CI->load->model('galleryImagesModel');
	// print_r($id);exit;

	$id_lang       = id_lang();  
	$lang          = $CI->uri->segment(1);
					 $CI->db->limit(PAGING_PERPAGE_GALLERY);
	$detail        = $CI->galleryImagesModel->listImagesGallery($id,array(),$conf['id_tags']);
	$ret['status'] = 1;

	if (!$detail) {
		return $ret; exit;
	}


	foreach ($detail as $key => $value) {    
		if ($key == 0) {
	    	$temp['first_item_photo'] = "active";
	    }   else{
	    	$temp['first_item_photo'] = "";
	    } 
		$temp['id_images']       = $value['images_id'];
		$temp['inv_desc']        = ($value['images_description']) ? '' : 'hide';
		$temp['g_description']   = $value['images_description'];
		$temp['g_name']          = $value['images_name'];
		$temp['g_img']           = getImgGallery($value['images_filename']);
		$temp['g_img_thumb']     = getImgGallery($value['images_filename'],1) ;

		$render['list_images'][] = $temp;
	}
	if (!empty(array_filter($render['list_images']))) {
		$ret['status'] = 0;
	}		 
	$render['base_url']       = base_url();
	$render['id_gallery']     = is_array($id) ? implode('-', $id): $id;
	$render['lang']           = $lang;
	$render['paging']         = PAGING_PERPAGE_GALLERY;
	$render['backurl']        = $conf['backurl'] ? $conf['backurl'] : 'home';
	$render['hide_button']    = $conf['hide_button'] ? 'hidden' :'';        
	$render['lang_load_more'] = language('load_more');
	$render['dsp_load_more']  = morelistphoto($id,$render['paging'],'',1) ? '' : 'hide';

	$ret['imagelist']         = $CI->parser->parse('gallery/imageslist.html', $render,true);
	// print_r($render);
	$ret['modal_imagelist']   = $CI->parser->parse('gallery/modal_imageslist.html', $render,true);
	// print_r($render);
	// exit;
	return $ret; 
}

function morelistphoto($id = array(),$page,$backurl,$ret=0)
{
	$CI =& get_instance();
	$CI->load->model('galleryImagesModel');
	$id_lang = id_lang();  
	$lang    = $CI->uri->segment(1);
			   $CI->db->limit(PAGING_PERPAGE_GALLERY_MORE,($page));
	$detail  = $CI->galleryImagesModel->listImagesGallery($id);
	// print_r($CI->db->last_query());exit;

	if($ret==1){
		return $detail ? 1 : 0;
	}

	foreach ($detail as $key => $value) {        
		$temp['id_images']       = $value['images_id'];
		$temp['inv_desc']        = ($value['images_description']) ? '' : 'hide';
		$temp['g_description']   = $value['images_description'];
		$temp['g_name']          = $value['images_name'];
		$temp['g_img']           = getImgGallery($value['images_filename']) ;
		$temp['g_img_thumb']     = getImgGallery($value['images_filename'],1) ;
		
		$render['list_images'][] = $temp;
	}
	$render['base_url']       = base_url();
	$render['id_gallery']     = is_array($id) ? implode('-', $id): $id;
	// $render['id_gallery']     = $id;
	$render['lang']           = $lang;
	$render['paging']         = PAGING_PERPAGE_GALLERY_MORE+$page;
	$render['backurl']        = $backurl;
	$render['lang_load_more'] = language('load_more');


	$render['dsp_load_more'] = morelistphoto($id,$render['paging'],'',1) ? '' : 'hide';

	$return['status']        = 'imageslist';
	$return['images']        = $CI->parser->parse('gallery/images.html', $render,true);
	$return['modal_images']  = $CI->parser->parse('gallery/modal_images.html', $render,true);
	echo json_encode($return);
}

function get_gallery_images($path, $data, $stPage){
	$CI=& get_instance();

	if($stPage == 1){
		$CI->data['dataImages']    = $data['list_images'];
		$CI->data['jmlImages']     = $data['jmlImages'];
		$CI->data['nomorGal']      = $data['nomorGal'];
		$CI->data['showUploadAll'] = $data['showUploadAll'];
	} else{
		$CI->data['dataImages']	   = $data;
	}

	return $CI->parser->parse('apps/'.$path, $CI->data,true);
}
function getImgGallery($img,$thumb)
{
	$isthumb = !empty($thumb)?'/thumb':'';
	$noPhoto = $isNoPhoto==1 ? ("<img src='".base_url("/images/no-image-available.png")."'>") : '';
	$check_thumb = is_file(dirname(__FILE__)."/../../images/gallery".$isthumb."/$img");
	if ($thumb && $check_thumb == false) {
		// is_file(dirname(__FILE__)."/../../images/gallery/$img");
		// dirname(__FILE__)."/../../images/gallery".$isthumb."/$img"
		 resize_image(
          // str_replace(' ', replace, subject)
          dirname(__FILE__)."/../../images/gallery/$img",
          dirname(__FILE__)."/../../images/gallery".$isthumb."/$img",
          260,
          170,
          90);
	}
	$img =  is_file(dirname(__FILE__)."/../../images/gallery".$isthumb."/$img") ? ("<img src='".base_url("/images/gallery".$isthumb."/$img")."'>") : $noPhoto;
	return $img;
}
function delete_0_images_gallery()
{
	$CI =& get_instance();
	if ($CI->db->delete('gallery_images', array('id_gallery' => 0))) {
		return true;
	}else{
		return false;
	}

}
function update_0_images_gallery($id)
{
	$CI =& get_instance();
	$CI->db->where("id_gallery = 0");
	if ($CI->db->update('gallery_images', array('id_gallery' => $id))) {
		return true;
	}else{
		return false;
	}
}

function get_event_gallery_id($id='',$type=2)
{
	$CI =& get_instance();

	$CI->load->model('galleryModel');
	if (!$id) {
		return false;
	}
	$where['id_gallery_category'] = $type;
	$where['id_category_item']    = $id;
	
	return $CI->galleryModel->findBy($where,1)['id'];
}

function get_news_gallery_id($id='',$type=3)
{
	$CI =& get_instance();

	$CI->load->model('galleryModel');
	if (!$id) {
		return false;
	}
	$where['id_gallery_category'] = $type;
	$where['id_category_item']    = $id;
	
	return $CI->galleryModel->findBy($where,1)['id'];
}