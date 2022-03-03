<?php
class GenerateRSSFeedModel extends  CI_Model{
	var $table = 'news';
	var $tableAs = 'news a';
	function __construct(){
		parent::__construct();    
	}
	function getallarticle($id_lang=2){
		$where['a.is_delete'] = 0;
		$this->db->select("a.*,b.name as news_category,c.name as status,d.username, b.uri_path as news_categori_url");
		$this->db->where('a.is_delete',0);
		$this->db->where('a.id_lang',$id_lang);
		$this->db->where('a.id_status_publish',2);
		$this->db->where('a.is_rssfeed',1);
		$this->db->where('a.is_not_available',0);
		$this->db->where('a.user_id_create !=',6);
		$this->db->join('news_category b',"b.id = a.id_news_category",'left');
		$this->db->join('status_publish c',"c.id = a.id_status_publish",'left');
		$this->db->join('auth_user d',"d.id_auth_user = a.user_id_create",'left');
		$this->db->order_by('publish_date','desc');
		return $this->db->get($this->tableAs)->result_array();
	} 
 }
