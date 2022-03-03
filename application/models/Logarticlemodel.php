<?php
class LogArticleModel extends  CI_Model{
	var $table = 'user_article_log';
	var $tableAs = 'user_article_log a';
    function __construct(){
       parent::__construct();
	   
    }
	function log_read_article_user_activity($id_article,$id_user){
        //to table user_article_log
        $data_now = date('Y-m-d H:i:s');
        $where['id_article'] = $id_article;
        $where['id_user'] = $id_user;
	$check_exist_data = $this->db->select('*')->get_where($this->tableAs,$where)->row();
        if(!$check_exist_data){
            $array_log_login = array(
                'id_article' => $id_article,
                'id_user' => $id_user,
                'create_date' => $data_now,
                'last_date_read' => $data_now,
                'log_count' => 1,
            );
	    
            $query = $this->db->insert($this->table, $array_log_login);
        } else {
            $where_data['id'] = $check_exist_data->id;
            $data['last_date_read'] = $data_now;
            $data['log_count'] 	= $check_exist_data->log_count += 1;
            $this->db->update($this->table,$data,$where_data);
        }
	//to table user_activity_log
	$where_log['id_article'] = $id_article;
        $where_log['id_user'] = $id_user;
	$check_exist_data_log = $this->db->select('*')->get_where('user_activity_log a',$where_log)->row();
        if(!$check_exist_data_log){
            $array_log_activity = array(
                'id_article' => $id_article,
                'id_user' => $id_user,
                'create_date' => $data_now,
                'last_date_read' => $data_now,
				'id_log_category' => 11,
                'log_count' => 1,
				'ipaddress' => $_SERVER['REMOTE_ADDR'],
				'ismobile' => $_SERVER['HTTP_USER_AGENT']
            );
            $query = $this->db->insert('user_activity_log', $array_log_activity);
        } else {
            $where_data_log['id'] = $check_exist_data_log->id;
            $data_log['last_date_read'] = $data_now;
            $data_log['log_count'] 	= $check_exist_data->log_count += 1;
            $this->db->update('user_activity_log',$data_log,$where_data_log);
        }
    }
	
 }
