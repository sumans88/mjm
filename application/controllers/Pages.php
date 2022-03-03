<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Pages extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('AboutPartnersModel');
        $this->load->model('pagesmodel');
    }
    function index($uri_path)
    {
        $lang = $this->uri->segment(1);
        $currentLang = id_lang();

        $data = $this->pagesmodel->fetchRow(array('uri_path' => $uri_path, 'id_lang' => $currentLang)); // check bahasa ini
        if (!$data) {
            if ($data['no_show_frontend'] == 1) {
                return  not_found_page();
            }
            $data = $this->pagesmodel->fetchRow(array('uri_path' => $uri_path));
            if (!$data) {
                if ($data['no_show_frontend'] == 1) {
                    return  not_found_page();
                }



                $data = $this->pagesmodel->pages_content($uri_path);
                if ($data['no_show_frontend'] == 1) {
                    return  not_found_page();
                }
                if (!$data) {
                    return  not_found_page();
                }
            }
        }

        if ($currentLang != $data['id_lang']) { // kalo data tidak dapet di bahasa awal
            $id = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(id_parent_lang = '$id' or id = '$id')");
            $datas = $this->pagesmodel->findBy();
            foreach ($datas as $key => $value) {
                if ($value['id_lang'] == $currentLang) {
                    redirect(base_url("$lang/pages/$value[uri_path]"));
                }
            }
        }

        
        $data['amcham_committe_list']    = '';
        $data['amcham_accordion_list']   = '';
        switch ($uri_path) {
            case 'committee':
                $data['amcham_committe_list']    = $this->pagesmodel->get_amcham_committe_list(); //committe amcham indonesia
                break;
            case 'amcham-indonesia':
                $data['amcham_accordion_list']    = $this->pagesmodel->get_amcham_accordion_list();
                break;
        }
        $data['banner_top']              = banner_top(); // pake banner top
        $data['widget_sidebar']          = widget_sidebar(); //pake sidebar

        if (!$data['seo_title']) {
            $data['seo_title']               = "AMCHAM INDONESIA";
        }

        $data['img']        = getImg($data['img'], 'large');

        if (!$data) {
            $data['page_name']    = 'page not found';
            $data['page_content'] = 'error 404. page not found';
        }
        $data['hide_breadcrumb']  = '';

        if ($data['uri_path'] == "amcham-indonesia") {
            $data['page_content'] .= $this->parser->parse('pages/custom_profile_amcham.html', "", true);
        }

        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        // $data['share'] = share_widget(); // widget_share

        render('pages', $data);
    }
}
