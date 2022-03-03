<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pages extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index($uri_path){
        $lang = $this->uri->segment(1);
        $this->load->model('pagesmodel');
        $currentLang = id_lang();
        $data = $this->pagesmodel->fetchRow(array('uri_path'=>$uri_path,'id_lang'=>$currentLang));
        if(!$data){
            $data = $this->pagesmodel->fetchRow(array('uri_path'=>$uri_path));
        }
        // unset($data['page_content'],$data['teaser']);
        // print_r($data);exit;
        if($currentLang != $data['id_lang']){
            $id = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(id_parent_lang = '$id' or id = '$id')");
            $datas = $this->pagesmodel->findBy();
            foreach ($datas as $key => $value) {
                if($value['id_lang'] == $currentLang){
                    redirect(base_url("$lang/pages/$value[uri_path]"));
                }
            }
        }
        $data['img']        = getImg($data['img'],'large');
        if(!$data){
            $data['page_name'] = 'page not found';
            $data['page_content'] = 'error 404. page not found';

        }
        $data['teaser'] = $uri_path=='hubungi-kami' || $uri_path == 'contact-us' ? '<div class="maps-google">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2805233909435!2d106.79906531453794!3d-6.226696762714706!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f14f3954fc15%3A0x6e595feebe62dc00!2sRatu+Plaza%2C+Jl.+Jend.+Sudirman%2C+Gelora%2C+Tanah+Abang%2C+Kota+Jakarta+Pusat%2C+Daerah+Khusus+Ibukota+Jakarta+10270!5e0!3m2!1sen!2sid!4v1473842774114" width="100%" height="350" frameborder="0" style="border:0" allowfullscreen=""></iframe>
            </div>' : $data['teaser'];
        render('pages',$data);
    }
    
}