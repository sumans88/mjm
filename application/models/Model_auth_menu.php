<?php
class Model_auth_menu extends  CI_Model{
	 var $parent;
    function __construct(){
       parent::__construct();
    }
	 function get_menu($where,$total='',$sidx='',$sord='',$mulai='',$end=''){
		$mulai = $mulai -1;
		if ($total==1){
			$sql	= "SELECT count(*) ttl 
						 FROM ref_menu_admin where id_parents_menu_admin = $this->parent 
						$where  order by urut asc";
			$data	= $this->db->query($sql)->row()->ttl;
		}
		else{
			$sql	= "SELECT id_ref_menu_admin as id,menu,controller,urut,id_parents_menu_admin as parent,breadcrumb
						  FROM ref_menu_admin where id_parents_menu_admin = $this->parent order by urut asc";
						  // $where order by $sidx $sord limit $mulai,$end ";
			$dt	= $this->db->query($sql)->result_array();
			$n 	= 0;
			
			foreach($dt as $dtx){
				$id 								= $dtx['id'];
				$urut								= $dtx['urut'];
				$parent							= $dtx['parent'];
				$data[$n]['id'] 				= $id;
				$data[$n]['edit'] 			= edit_grid($id);
				$data[$n]['del'] 				= delete_grid($id);
				$data[$n]['auth'] 			= "<img title='Edit Auth' class='edit' src='".base_url()."assets/images/lock.png' onclick='popup_auth($id)'>";
				$data[$n]['up'] 				= ($n == 0) ? '' : "<img title='Move Down' class='edit' src='".base_url()."assets/images/arrow-up.png' title='Up' onclick='ubah_urutan($id,$urut,$parent,\"up\")'>";
				$data[$n]['down'] 			= (count($dt)-1 == $n) ? '' : "<img title='Move Up' class='edit' src='".base_url()."assets/images/arrow-down.png' onclick='ubah_urutan($id,$urut,$parent,\"down\")'>";
				$data[$n]['menu'] 			= $dtx['menu'];
				$data[$n]['controller'] 	= $dtx['controller'];
				$data[$n]['parent'] 			= $parent;
				$data[$n]['breadcrumb'] 	= $dtx['breadcrumb'];
				++$n;
			}
		}
		return $data;
    }
 }
