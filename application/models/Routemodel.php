<?php 
class RouteModel extends CI_Model{
    var $table = 'route_url';

 
    function __construct(){
        parent::__construct();
    }   
 
    //grab all of the routes from the database, and cache to a file
    public function cache_routes(){
        $this->db->select("*");
        $query = $this->db->get($this->table);
 
        foreach ($query->result() as $row){
            $data[] = '$route["' . $row->slug . '"] = "' . $row->route . '";';
            $output = $this->load->helper('file');
            write_file(APPPATH .  "config/custom_routes.php", $output);
        }
    }
}