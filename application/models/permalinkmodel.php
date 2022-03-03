<?php
class PermalinkModel extends  CI_Model{

    function __construct(){
        parent::__construct();  
    }

    public function getdata_permalink(){
    	$this->db->select('*');
    	$this->db->from('route_url');
    	$data = $this->db->get();
    	return $data->result_array();
    }
}

?>