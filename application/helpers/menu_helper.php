<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * fungsi-fungsi yg meng-handle menu
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 */

/**
 * Generate Top menu
 * @return string html list menu
 */
function menus(){
	 auth_menu();//aksesmenu;
	 $CI = & get_instance();
	 $CI->load->database();
	 $CI->load->model('model_menu_admin','MenuAdmin');
	 $menu			= '';
	 $user_sess 	= $CI->session->userdata('ADM_SESS');
	 $id_group 		= $user_sess['admin_id_auth_user_group'];
	 $id_user 		= $user_sess['admin_id_auth_user'];
	 $query 			= $CI->MenuAdmin->GetMenuAdminByGroup($id_group,0);
	 foreach($query->result_array() as $row){
		  $query2 	= $CI->MenuAdmin->GetMenuAdminByGroup($id_group,$row['id_ref_menu_admin']);
		  $sub_menu	= '';
		  $id_admin_menu=$row['id_ref_menu_admin'];
		  $title_admin_menu=$row['menu'];
		  $file_admin_menu=$row['controller'];
		  //$menu .='<li class="divider-vertical"></li>';
		  if($CI->MenuAdmin->Cek_Has_Child($id_group,$id_admin_menu)){
			   //$menu .='<li class="dropdown">';
			   //$menu .='<a data-toggle="dropdown" class="dropdown-toggle" href="#">'.$title_admin_menu.'<b class="caret"></b></a>';
			   //$menu .='<ul class="dropdown-menu">';
			   $menu .=print_menu_child($id_group,$id_admin_menu,$title_admin_menu,$active,$row['img_icon']);
			   //$menu .='</ul></li>';
		  }else{
			   $ctrl=$CI->uri->segment(2);
			   $parent_data = $CI->db->get_where('ref_menu_admin',"controller = '$ctrl'")->row_array();
			   $link=base_url().'apps/'.$file_admin_menu;
			   if($parent_data['menu'] == $title_admin_menu){
				    $class='active';
			   }else{
				    $class ='';
			   }
			   $menu .='<li class="'.$class.'"><a href="'.$link.'"><i class="'.$row['img_icon'].'"></i><span>'.$title_admin_menu.'</span></a></li>';
		  }
		  
		//  if ($query2->num_rows() > 0){
		//		$sub_menu.="<ul>";
		//		foreach($query2->result_array() as $row2){	
		//			 $new		= '';
		//			 $sub_menu2= '';
		//			 $query3 	= $CI->MenuAdmin->GetMenuAdminByGroup($id_group,$row2['id_ref_menu_admin']);
		//			 if ($query3->num_rows() > 0){
		//				  $sub_menu2.="<ul>";
		//				  foreach($query3->result_array() as $row3){
		//						$sub_menu2.=item_menu($row3['menu'],$row3['file']);
		//				  }
		//				  $sub_menu2.='</ul>';
		//			 }
		//			 $sub_menu .= item_menu($row2['menu'],$row2['file'],$sub_menu2);
		//		}
		//		$sub_menu.='</ul>';
		//  }
		
		  //$menu.= item_menu($row['menu'],$row['file'],$sub_menu);
	 }
	 //$menu .='<li class="divider-vertical"></li>';
	 $CI->data['base_url'] 			= base_url();
	 $CI->data['menu'] 				= $menu;
	 $CI->data['form'] 				= '';
	 $CI->data['today'] 				= date('l, d F Y');
	 $CI->data['nama_admin']		= $user_sess['admin_name'];
	 $CI->data['current_date']		= date('d-m-Y');
	 $CI->data['current_date_sql']= date('Y-m-d');
	 $CI->data['id_auth_user']		= $CI->session->userdata['ADM_SESS']['admin_id_auth_user'];
	 $CI->data['message']				= alert($CI->session->flashdata('message'));
	 $ctrl								= $CI->uri->segment(1);
	 if($ctrl=='apps'){
		 $file							= $CI->uri->segment(2);
	 	 $ctrl							= base_url().'apps/'.$file.'/';
	 }
	 else{
		  $ctrl							= base_url().$ctrl.'/';
		  // $file_2					    = $CI->uri->segment(2);
		  // $ctrl							= base_url().$ctrl.'/'.$file_2.'/';
	 }
	 $CI->data['menu_name']			= db_get_one('ref_menu_admin','menu',"controller = '$file'");
	 $CI->data['this_controller']	= $ctrl;
	 
	 $id_menu							= db_get_one('ref_menu_admin','id_parents_menu_admin',"controller = '$file' and id_parents_menu_admin != 0");
	 $parent								= db_get_one('ref_menu_admin','menu',"id_ref_menu_admin = '$id_menu'");
	 $grup								= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	 if($id_menu > 0) {
		  //$CI->db->select('menu as menuz,file')->get_where('ref_menu_admin',"id_parents_menu_admin = '$id_menu'")->result_array();
		  $sql							= "select menu as menuz, controller
												from ref_menu_admin a, auth_pages b
												where a.id_parents_menu_admin = '$id_menu'
												and a.id_ref_menu_admin = b.id_ref_menu_admin
												and b.id_auth_user_grup = '$grup'
												and b.r = 1
												";
		  $left_menu 					=  $CI->db->query($sql)->result_array();
	 }
	 else{
		  $left_menu 					=  array();
	 }
	 //echo $CI->db->last_query();
	 $now = date('Y-m-d');
	 // $sql_date="SELECT DATE_FORMAT('$now','%e') AS date";
  //           $query_date=$CI->db->query($sql_date);
  //           foreach($query_date->result_array() as $row) {
  //               $CI->data['date']=$row['date'];
  //           }
	 // $sql_bulan="SELECT DATE_FORMAT('$now','%M') AS bulan";
  //           $query_bulan=$CI->db->query($sql_bulan);
  //           foreach($query_bulan->result_array() as $row) {
  //               $CI->data['bulan']=$row['bulan'];
  //           }
	 //    $sql_day="SELECT DATE_FORMAT('$now','%W') AS day";
  //           $query_day=$CI->db->query($sql_day);
  //           foreach($query_day->result_array() as $row) {
  //               $CI->data['day']=$row['day'];
  //           }
	 $CI->data['if_sub_menu']		= (count($left_menu) > 0 ) ? '' : 'invis';
	 $CI->data['left_menu'] 		= $left_menu;
	 $CI->data['parent_menu']		= $parent;
}

function print_menu_child($id_group,$id_admin_menu,$title_admin_menu,$active='',$img_icon=''){
	 $CI = & get_instance();
	 $CI->load->database();
	 $CI->load->model('model_menu_admin','MenuAdmin');
	 $ctrl=$CI->uri->segment(2);
	 $data = $CI->db->get_where('ref_menu_admin',"controller = '$ctrl'")->row_array();
	 if($data['id_parents_menu_admin'] != ''){
		  $id_parent_menu=$data['id_parents_menu_admin'];
		  $parent 					 = $CI->db->get_where('ref_menu_admin',"id_ref_menu_admin =$id_parent_menu")->row_array();
		  if($parent['id_parents_menu_admin'] != ''){
			   $parent_lagi 		 = $CI->db->get_where('ref_menu_admin',"id_ref_menu_admin = '$parent[id_parents_menu_admin]'")->row_array();
			   $parent_name_child = $parent_lagi['menu'];
		  }
			
		  $parent_name=$parent['menu'];
	 }
	 
	 if($parent_name == $title_admin_menu or $parent_name_child == $title_admin_menu){
		  $class='active';
	 }else{
		  $class ='';
	 }
	 $sub_menu	= '';
	 $sub_menu .='<li class="has-sub '.$class.' '.$active.'">';
	 $sub_menu .='<a href="#"><span>'.$title_admin_menu.'</span><b class="caret pull-right"></b><i class="'.$img_icon.'"></i></a>';
	 $sub_menu .='<ul class="sub-menu">';
	 $query 			= $CI->MenuAdmin->GetMenuAdminByGroup($id_group,$id_admin_menu);
	 foreach ($query->result_array() as $row){
	 
		  $id_admin_menu=$row['id_ref_menu_admin'];
		  $title_admin_menu=$row['menu'];
		  $file_admin_menu=$row['controller'];
		  if($ctrl == $file_admin_menu){
			   $class_child='active';
		  }else{
			   $class_child ='';
		  }
		 if($CI->MenuAdmin->Cek_Has_Child($id_group,$id_admin_menu)){
	
			   $sub_menu .=print_menu_child2($id_group,$id_admin_menu,$title_admin_menu);
			  
		  }else{
			   $link=base_url().'apps/'.$file_admin_menu;
			   $sub_menu .='<li class="'.$class_child.'"><a href="'.$link.'"><span>'.$title_admin_menu.'</span></a></li>';
		  }
	 }
	 $sub_menu .='</ul></li>';
	 
	 return $sub_menu;
}


function print_menu_child2($id_group,$id_admin_menu,$title_admin_menu){
	 $CI = & get_instance();
	 $CI->load->database();
	 $CI->load->model('model_menu_admin','MenuAdmin');
	 $ctrl=$CI->uri->segment(2);
	 $data = $CI->db->get_where('ref_menu_admin',"controller = '$ctrl'")->row_array();
	 if($data['id_parents_menu_admin'] != ''){
		  $id_parent_menu=$data['id_parents_menu_admin'];
		  $parent 					 = $CI->db->get_where('ref_menu_admin',"id_ref_menu_admin =$id_parent_menu")->row_array();
		  if($parent['id_parents_menu_admin'] != ''){
			   $parent_lagi 		 = $CI->db->get_where('ref_menu_admin',"id_ref_menu_admin = '$parent[id_parents_menu_admin]'")->row_array();
			   
		  }
			
		  $parent_name=$parent['menu'];
			   
	 }
	 
	 if($parent_name == $title_admin_menu){
		  $class='active';
	 }else{
		  $class ='';
	 }
	 $sub_menu	= '';
	 $sub_menu .='<li class="has-sub '.$class.' '.$active.'">';
	 $sub_menu .='<a href="#"><span>'.$title_admin_menu.'</span><b class="caret pull-right"></b></a>';
	 $sub_menu .='<ul class="sub-menu">';
	 $query 			= $CI->MenuAdmin->GetMenuAdminByGroup($id_group,$id_admin_menu);
	 foreach ($query->result_array() as $row){
	 
		  $id_admin_menu=$row['id_ref_menu_admin'];
		  $title_admin_menu=$row['menu'];
		  $file_admin_menu=$row['controller'];
		  if($ctrl == $file_admin_menu){
			   $class_child='active';
		  }else{
			   $class_child ='';
		  }
		  if($CI->MenuAdmin->Cek_Has_Child($id_group,$id_admin_menu)){
	
			   $sub_menu .=print_menu_child2($id_group,$id_admin_menu,$title_admin_menu);
			  
		  }else{
			   $link=base_url().'apps/'.$file_admin_menu;
			   $sub_menu .='<li class='.$class_child.'><a href="'.$link.'"><span>'.$title_admin_menu.'</span></a></li>';
		  }
	 }
	 $sub_menu .='</ul></li>';
	 
	 return $sub_menu;
}
/**
 * create HTML Item Menu
 * @param $menu string nama menu
 * @param $file string link menu
 * @param $sub_menu (optional) string html submenu
 * @return item menu dengan sub menu(jika ada)
 */
function item_menu($menu,$file,$sub_menu=''){
	 //echo $sub_menu;
	 $link 	= ($file != '#') ? base_url().'apps/'.$file:$file;
	 return '<li>
					 <a class="submenu" title="'.$menu.'" href="'.$link.'">
					 <span class="text">'.$menu.'</span></a>'.$sub_menu.'	
				</li>';
}

/**
 * Create HTML Breadcrumb
 * @return string Breadcrumb
 *
 */
function breadcrumb2($parent='',$ret){
	$CI 		= & get_instance();
	$id_lang 	= id_lang();
	$CI->load->model('frontendmenumodel');
	$CI->load->model('newscategorymodel');
	$CI->load->model('eventcategorymodel');
	$CI->load->model('gallerymodel');

	$uri4		= purify($CI->uri->segment(4));
	$uri3		= $CI->uri->segment(3);
	$uri2		= $CI->uri->segment(2);
	$uri1		= $CI->uri->segment(1);

	// pages/uri-path
	// news/index/uri-path
	// news/detail/uri-path

	// event/index/uri-path
	// event/detail/uri-path

	if($uri2=='pages' || $uri2=='news' || $uri2=='event' || $uri2=='gallery'){
		if($uri2 == 'news' && $uri3 == 'detail'){ // kalo news/detail
			$CI->load->model('newsmodel');
			// $where2['a.uri_path'] 	= $uri4;
			$where2['a.id_lang'] 	= $id_lang;
			$CI->db->like('a.uri_path',$uri4);
			$data		 			= $CI->newsmodel->fetchRow($where2);

			$newsCat	 			= $CI->newscategorymodel->findById($data['id_news_category']);
			// $uri3		 			= $newsCat['uri_path'];
			$uri3		 			= $uri2;
			$last_bread		 		= $data['news_title'];
			// print_r($data);exit;
		}else if($uri2 == 'event' && $uri3 == 'detail'){ // kalo event/detail
			$CI->load->model('eventmodel');
			$where2['a.uri_path']  = $uri4;
			$where2['a.id_lang']   = $id_lang;
			$data2                 = $CI->eventmodel->fetchRow($where2);
			$last_bread		 	   = $data2['name'];
			$id_annual             = id_child_event(40,1,1);
			$diff                  = check_date_future($data2['end_date']) ? 1 :0; // check eventstart dengan hari ini

			if($data['id_event_category'] == 40){
				$uri3 = "annual-golf-turnament";

			}else if ($diff) { //event belum dimulai tapi udah di close / belum diclose
				$uri3		 		= 'upcoming-events';
			} else{ // event sudah di mulai sudah di close 
				$uri3		 		= 'past-events';
			}
			
			$eventCat             = $CI->eventcategorymodel->findById($data['id_event_category']);

			
		}else if($uri2 == 'gallery' && $uri3 == 'detailphoto'){
			$where2['a.uri_path'] = $uri4;
			$where2['a.id_lang']  = $id_lang;
			$data2                = $CI->gallerymodel->fetchRow($where2);
			$last_bread		      = $data2['name'];

			$uri3		 	      = 'photo';

		}else if( ($uri2 == 'news' && $uri3 == 'index') || ($uri2 == 'event' && $uri3 == 'index')){
			$uri3		 			= $uri4;
		}else if ($uri2 == 'gallery') {
			$uri3 = $uri3;
		}
		// else if ($uri2=='gallery') {
		// 	$uri3		 		= $uri4;
		// }

		$CI->db->order_by('id_parent','desc');
		$where['id_language'] = $id_lang;
		if($parent){
			$where['a.id'] = $parent;
		}
		else{
			$where['extra_param'] = $uri3;
		}
		$data = $CI->frontendmenumodel->fetchRow($where);
		// print_r($CI->db->last_query());exit;
		if($data['id_parent'] == 0){
			$rett[] = $data['name'];
		}
		else{
			$rett = array_merge(breadcrumb2($data['id_parent'],$ret), array($data['name']), array($last_bread));//. ' > '.$data['name'];
			if (empty($last_bread)) {
				array_pop($rett);
			}

		}

		$ret = $rett;
	}
	
	return $ret;
}
function breadcrumb_frontend(){
	$CI = & get_instance();
	$a = breadcrumb2();
	$a_lenght = count($a)-1;
	foreach ($a as $key => $value) {
		$lang = id_lang();
		if ($key != $a_lenght) {
			$item = $CI->db->select('*')->get_where('frontend_menu',"name = '$value' and is_delete = 0 and id_language = '$lang'")->row_array();
			$link = db_get_one('module','controller',"id = '$item[id_module]'" );
			$uri = $item['extra_param'];
		}else{
			$link = '';
			$uri  = '';

		}

		$data['menu_hide'] = ($a_lenght == 0)?'hide':'';
		$temp['active']    = ($key == $a_lenght)?'active':'';
		$temp['link_hide'] = ($key == $a_lenght)?'hide':'';
		$temp['link']      = ($key== 1)? site_url($link.'/'.$uri):'#';
		$temp['text']      = $value;
		$temp['text2']     = ($key == $a_lenght)?$value:'';
		$data['breadcrumb2'][] = $temp;
	}
	return $CI->parser->parse("layout/ddi/breadcrumb.html",$data,'blank',true);

}
function breadcrumb($name='',$link=''){
	 $CI 								 = & get_instance();
	 $ctrl								 = $CI->uri->segment(2);
	 //$funct							 = $CI->uri->segment(3);
	 
	 $breadcrumb = '';
	 if($name != ''){
		  $breadcrumb 					.= ($link != '') ? " &raquo <a href='$link'>$name</a>" : " &raquo $name";
	 }
	 else{
		  $breadcrumb						.=  "<li><a href='".base_url()."apps/home'>Home</a></li>";
		  $data = $CI->db->get_where('ref_menu_admin',"controller = '$ctrl'")->row_array();
		  $name_ctr=$data['breadcrumb'];
		  if($data['id_parents_menu_admin'] != ''){
			   $id_parent_menu=$data['id_parents_menu_admin'];
			   $link_data = '';
			   $parent 					 = $CI->db->get_where('ref_menu_admin',"id_ref_menu_admin =$id_parent_menu")->row_array();
			   if($parent['id_parents_menu_admin'] != ''){
				    $parent_lagi 		 = $CI->db->get_where('ref_menu_admin',"id_ref_menu_admin = '$parent[id_parents_menu_admin]'")->row_array();
				    $breadcrumb 		.= link_breadcrumb($parent_lagi['controller'],$parent_lagi['breadcrumb']);				    
			   }
			   //$breadcrumb 			.= link_breadcrumb($parent['file'],$parent['breadcrumb']);
			   $parent_name=$parent['breadcrumb'];
		  }
		  if($parent_name){
			   $breadcrumb .="<li><a href='#'>$parent_name</a></li>";
		  }
		  if($name_ctr){
			   $breadcrumb .= "<li class='active'>$name_ctr</li>";
		  }
		  
	 }
	 if($ctrl == 'home') $breadcrumb = '';
	 $CI->data['breadcrumb_text'] =  ($ctrl == 'home') ? APP_NAME : ('<li><a href='.base_url().'>' .APP_NAME . '</a></li>' . strip_tags($breadcrumb));
	 $CI->data['breadcrumb']    .= ($ctrl == 'home') ? APP_NAME : ('<li><a href='.base_url().'>' .APP_NAME . '</a></li>' . $breadcrumb);
	 $CI->data['ctrl'] = $ctrl;
	 return $CI->data['breadcrumb'];
}
function link_breadcrumb($link,$name){
	 if($name != ''){	 
		  if($link =='#'){
				return ' <li> '.$name. '</li>';
		  }
		  else{
				return "  <li> <a href='".base_url()."apps/$link'>$name</a></li>";
		  }
	 }
}

function generate_title(){
	 $CI 									 = & get_instance();
	 $ctrl								 = $CI->uri->segment(2);
	 //$funct							 = $CI->uri->segment(3);
	 	 
	 $data = $CI->db->get_where('ref_menu_admin',"controller = '$ctrl'")->row_array();
	 $parent_name=$data['breadcrumb'];
	 return $parent_name;
}