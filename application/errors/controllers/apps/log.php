<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Log extends CI_Controller {
    function index(){
        if($_GET['q']==1){
				$alias['log_date'] 			 = "date_format(log_date,'%d/%m/%Y %h:%i:%s')";
				grid($_GET,'Model_log','log',$alias);
				exit;
		  }
		render('apps/log/log',$data,'apps');
    }
    
    function detail($id=0){
        $this->load->model('model_log');
         if(get('id_edit')){
			$id = get('id_edit');
		}
      if (!$id) {
         set_flash_session('success_process','You can\'t do this. Please try again');
         redirect('apps/log/log');
      }
        
        $data=array();
        $data['base_url']=base_url();      
        $data=$this->model_log->getLogById($id);
    
        $this->parser->parse('apps/log/log_detail.html',$data);
        
    }

    function export_to_excel(){
      $this->load->model('model_log');
      $post = $this->input->post();
      
      $alias['id_auth_user'] = 'b.id_auth_user';
      $alias['userid'] = 'b.userid';
      $alias['username'] = 'b.username';
      $alias['log_date'] = 'a.log_date';
      $alias['activity'] = 'a.activity';
      $alias['ip'] = 'a.ip';
      
      where_grid($post,$alias,1);

      $data['data'] = $this->model_log->export_to_excel();
      $i=1;
      foreach($data['data'] as $key => $value) {
        $data['data'][$key]['nomor'] = $i++;
        $data['data'][$key]['log_date'] =  date('d/m/Y h:i:s', strtotime($value['log_date']));
      }
      render('apps/log/export_to_excel',$data,'blank');
      export_to('Log.xls');
    }
}
    

