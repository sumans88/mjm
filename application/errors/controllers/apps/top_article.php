<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Top_Article extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('TopArticleModel');
		session_start();
	}
	function index(){
		$data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status','selected'=>$data['id_status_publish']));
		session_destroy();
		render('apps/top_article/index',$data,'apps');
	}
	function get_category(){
		if($_GET['q']==1){
			grid($_GET,'TopArticleModel','get_menu');
			exit;
		}
		render('apps/top_article/top_article',$data,'apps');
	}
	function get_top_news_by_category(){
		$data = $this->TopArticleModel->get_top_news_by_category($_POST['ids'],$_POST['is_featured']);
		echo json_encode($data);
	}
	function get_callback($id){
		if($id==0){
			echo db_get_one('news','news_title');
		}else{
            if($id==26){
                echo db_get_one('news','news_title',array('is_qa'=>1,'is_experts'=>1));
            }else if($id==25 ){
                    echo db_get_one('news','news_title',array('is_qa'=>0,'is_experts'=>1));
            } else {
                echo db_get_one('news','news_title',array('id_news_category'=>$id));
            }
		}	
	}
	function record_select_category($ids,$is_featured){
		$data = $this->TopArticleModel->get_all_news_by_category($_SESSION['ids_top_article'],$_SESSION['is_featured_top_article']);
		render('apps/top_article/record_select_category',$data,'blank');
	}
	function select_category(){
		$_SESSION['ids_top_article'] = $_POST['ids'];
		$_SESSION['is_featured_top_article'] = $_POST['is_featured'];
		$data['where'] = $_GET['where'];
		render('apps/top_article/select_category',$data,'blank');
	}
	function process(){
		$value	= explode(',',$_POST['value']);
		$data_insert['create_date'] 	= date('Y-m-d H:i:s');
		$data_insert['user_id_create'] = id_user();
		$sort=0;
		$data_now = date('Y-m-d H:i:s');
		$this->db->update('top_article_log',
			array(
				'is_delete' => 1,
				'modify_date' => $data_now,
				'user_id_modify' => id_user()
			), 
			array(
				'id_category' => $_POST['id_category']
			)
		);
		$this->db->delete('top_article',array("id_category"=> $_POST['id_category']));
		for ($x = 0; $x < count($value); $x++) {
			$data = explode('_',$value[$x]);
			$data_insert['id_news'] = $data[0];
			$data_insert['sort'] = ++$sort;
			$data_insert['id_category'] = $_POST[id_category];
			$data_insert['is_featured'] = 1;
			$data_insert['create_date'] = $data_now;	
			$data_insert_log = $data_insert;
			$data_insert_log['create_date'] 	= date('Y-m-d H:i:s');
			$data_insert_log['user_id_create'] = id_user();
			$insert = $this->db->insert('top_article',$data_insert);	
			$this->db->insert('top_article_log',$data_insert_log);		
		}
		
		if($insert){
			$data['error_status'] = 'success';
			$data['msg'] = 'Success to save';
		}else{
			$data['msg'] = "there's somethings wrong";
		}
		echo json_encode($data);
	}
}

/* End of file top_acticle.php */
/* Location: ./application/controllers/apps/top_article.php */