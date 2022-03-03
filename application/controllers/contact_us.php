<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_us extends CI_Controller {

    function __construct(){
        parent::__construct();
    }

    public function index()
    {

        $data['page_content'] = $this->parser->parse("layout/ddi/contact_us_form.html",$this->data,true); 

        $data['hide_breadcrumb'] = 'hidden';
        $data['page_name'] = 'Contact Us';

        if($data['seo_title'] == ''){
            $data['seo_title'] = "MJM";
        }

        $data['amcham_committe_list'] = '';
        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar();

        render('pages',$data); 


    }

}

/* End of file contact_us.php */
/* Location: ./application/controllers/contact_us.php */