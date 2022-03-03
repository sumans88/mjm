<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsletter extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model('newsModel');
        $this->load->model('newsCategoryModel');
        $this->load->model('newsTagsModel');
        $this->load->model('frontendmenumodel');
        $this->load->model('newsletterModel');
        // $this->parentMenu = getParentMenu();
        // echo $this->parentMenu;exit;
    }

    public function registNewsletter()
    {
        $post           =  purify($this->input->post());

        $ret['error']   = 1;

        $where['email'] = $post['email'];
        $checkemail     = $this->newsletterModel->findBy($where);
        if ($checkemail) {
            $ret['msg']   = "sorry, email has registered befored";
        }else{
            $this->newsletterModel->insert($post);

            // $check_email['activation_code'] = base_url_lang().'newsletter/activemember/'.$check_email['activation_code'];
            // $post['name']                   = base_url();
            sent_email_by_category(1,$check_email, $post['email']);

            $ret['error'] = 0;
            $ret['modalname'] = 'myModalThanks';
        }
        $ret['data'] = $post;
        echo json_encode($ret);
    }    

}

/* End of file newsletter.php */
/* Location: ./application/controllers/newsletter.php */