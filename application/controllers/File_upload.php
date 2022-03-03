<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class file_upload extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('eventmodel');
        $this->load->model('newstagsmodel');
        $this->load->model('eventFilesModel');
        $this->load->model('eventImagesModel');
        $this->load->model('eventCategoryModel');
        $this->load->model('frontendmenumodel');
        $this->load->model('template_tipe_input_form_register_model');
        $this->load->model('pagesmodel');
        $this->load->model('eventPriceModel');
    }

    function index(){
        // print_r($this->uri->segment_array());exit;
        $data['path_url'] = APPPATH.'file_upload/confirm_01-31Jul2018144843.pdf';
        return $this->load->view('layout/ddi/pdfjs/web/viewer.html', $data, FALSE);
        // return $this->parser->parse('layout/ddi/pdfjs/web/viewer.html',$data);
    }
   
}

/*
Past Event Total 290 record dengan id_event_category=26 
- a.is_close dimatikan karena belum membuat query is_close di view_content_event
- sangat berpengaruh kepada id_status_publish 2
- $where['a.is_not_available']  = 0; dimatikan karena tidak tahu fungsinya

End Date Upcoming Events dimatikan

Annnual Golf 
IS Close tidak ada di query
*/