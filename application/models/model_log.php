<?php
class Model_log extends CI_Model{
    function log($where,$total='',$sidx='',$sord='',$mulai='',$end=''){
	$this->load->helper('text');
	$mulai = $mulai -1;
	if ($total==1){
            $sql= "SELECT count(*) ttl from access_log a, auth_user b
                    where a.id_auth_user = b.id_auth_user
						  $where";
		    $data	= $this->db->query($sql)->row()->ttl;
	}
	else{
            $sql="SELECT a.id ,b.userid,b.username,b.id_auth_user,a.log_date,a.activity,ip
						  FROM access_log a left join auth_user b on a.id_auth_user=b.id_auth_user 
						  where 1=1  $where order by $sidx $sord   LIMIT $mulai, $end ";
            $dt	= $this->db->query($sql)->result_array();
            $n 	= 0;
            foreach($dt as $dtx){
                $data[$n]['id']			= $dtx['id'];
                $data[$n]['view']		= "<span onclick='detail($dtx[id])'>".view_grid($dtx['id']).'</span>';
                $data[$n]['userid'] 	= $dtx['userid'];
                $data[$n]['username']	= $dtx['username'];
                $data[$n]['log_date'] 	= iso_date_time($dtx['log_date'],'/');
                $data[$n]['activity'] 	= word_limiter(strip_tags($dtx['activity']),15);
                $data[$n]['ip'] 			= $dtx['ip'];
		++$n;
	    }
	}
	return $data;
    }

    function getLogById($id){
        //$sql="SELECT a.id as id, a.log_name, a.activity, a.ip, b.id_auth_user, b.userid, b.username, , b.log_date, b.activity,b.email, b.id_auth_user_grup FROM access_log a, auth_user b WHERE a.id_auth_user=b.id_auth_user AND a.id='$id' LIMIT 0,1";
        $sql="SELECT * FROM auth_user_grup c, access_log a left join auth_user b on a.id_auth_user=b.id_auth_user where a.id='$id'";
        $data=$this->db->query($sql)->row_array();
        return $data; 
    }
    
    
}
