<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Old_content extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model('newsmodel');
    }

    public function index($uri_path)
    {
        print_r($this->uri->segment_array());
    }

    public function news($uri_path)
    {
        
        // if (preg_match('#^[0-9]{0,}-#',$uri_path)){
        //     preg_match('%([0-9]{0,}.)?(.*)%',$uri_path, $real_uri_path_temp);
        //     $real_uri_path = $real_uri_path_temp[2];
        // }else{
        //     $real_uri_path = $uri_path;
        // }
        if(preg_match('#^[0-9]{0,}-#',$uri_path,$id_array)){
            preg_match('%.*[0-9]%',$id_array[0],$id);
            $news_uri_path = $this->newsmodel->findBy(array('id_migrasi' => $id[0]),1)['uri_path'];

            if ($news_uri_path != "") {
              redirect('en/news/detail/'.$news_uri_path,'refresh');
            }else{
              redirect('en/home/','refresh');
            }

        }else{
            redirect('en/home/','refresh');
        }
        
        // print_r($news_uri_path);
        // print_r($real_uri_path);
        // exit;
        // redirect('en/news/detail/'.$real_uri_path,'refresh');
    }
}

/* End of file newsletter.php */
/* Location: ./application/controllers/newsletter.php */