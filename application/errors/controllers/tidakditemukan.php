<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class TidakDiTemukan extends CI_Controller {

    public function __construct() {
            parent::__construct();              
    }

    //
    // @ start 404 page code
    //
    public function index() {
        $this->load->view("layout/ddi/404_error_page", $data);

    }

}