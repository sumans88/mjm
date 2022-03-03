<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Gallery extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function photo(){
        $this->load->model('gallerymodel');
        $id_lang = id_lang();
        $this->db->order_by('id','desc');
        $data['gallery'] = $this->gallerymodel->findBy(array('id_lang'=>$id_lang));
        render('gallery/photo',$data);
    }
    function detailphoto($uri_path){
        $this->load->model('gallerymodel');
        $this->load->model('gallerydetailmodel');
        $id_lang= id_lang();
        $data   = $this->gallerymodel->fetchRow(array('id_lang'=>$id_lang,'uri_path'=>$uri_path));
        $this->db->order_by('id','desc');
        $detail = $this->gallerydetailmodel->findBy(array('id_gallery'=>$data['id'],'id_lang'=>$id_lang));
        foreach ($detail as $key => $value) {
            $detail[$key]['active'] =  $key==0 ? 'active' : '';
            $detail[$key]['desc']   = $value['description'];
            $detail[$key]['col']    = $key==0 || $key%5 == 0 ? 12 : 6;
        }
        $data['list_gallery'] = $detail;
        $data['list_gallery2'] = $detail;
        render('gallery/detailphoto',$data);
    }
}