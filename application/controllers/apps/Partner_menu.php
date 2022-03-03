<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Partner_menu extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->model('partnermodel');
		
	}
    function index($uri_path){ 
        render('apps/partner/index',$data,'apps');
    }

    public function add($id=''){
        if($id){
            $where['id'] = $id;
            $datas = $this->partnermodel->fetchRow($where);
            // $datas  = $this->partnermodel->selectData($id); 

            if(!$datas){
                die('404');
            }
            $data                           = quote_form($datas);
            $data['judul']                  = 'Edit';
            $data['proses']                 = 'Update';
            $data['url']                    = $datas['url'];
            $data['name']                   = $datas['name'];
        }else{
            $id=0;
            $data['judul']                  = 'Add';
            $data['proses']                 = 'Simpan';
            $data['url']                    = '';
            $data['name']                   ='';
            $data['id']                     = '';
        }

        render('apps/partner/add',$data,'apps');
    }  
    function records(){
        $data = $this->partnermodel->records();
        foreach ($data['data'] as $key => $value) { 
            $data['data'][$key]['name']     = $value['name'];
            $data['data'][$key]['url']      = $value['url'];
            $data['data'][$key]['id']      = $value['id'];
        } 
        
        render('apps/partner/records',$data,'blank');
    } 
    function proses($idedit=''){
        $id_user                = id_user();
        $this->layout           = 'none';
        $post                   = purify($this->input->post());
        $ret['error']           = 1;
        $id_parent_lang         = NULL;
        $this->db->trans_start();
        
    if(!$idedit){ 
        $this->form_validation->set_rules('name', '"Partner Name"', 'required');  
        if ($this->form_validation->run() == FALSE){
            $ret['message']  = validation_errors(' ',' ');
        } 
    }
              
    if($idedit){  
            auth_update();
            $ret['message'] = 'Update Success';
            $act            = "Update News"; 
            $data_save['name']      = $post['name'];
            $data_save['url']       = $post['url'];
            $iddata         = $this->partnermodel->update($data_save,$idedit);
        
    }else{
        $data_save['name']      = $post['name'];
        $data_save['url']       = $post['url'];
        auth_insert();
        $ret['message']     = 'Insert Success';
        $act                = "Insert News";
        $iddata             = $this->partnermodel->insert($data_save);
    }
     

    detail_log();
    insert_log($act);
    $this->db->trans_complete();
    set_flash_session('message', $ret['message']);
    $ret['error'] = 0;   
        echo json_encode($ret);
    }



    function del(){
        auth_delete();
        $this->db->trans_start();   
        $id = $this->input->post('iddel');
        $this->partnermodel->delete($id);
        detail_log();
        insert_log("Delete News");
        $this->db->trans_complete();
    } 
    
}