<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth_menu extends CI_Controller {
	function index(){
		$this->load->model('Model_auth_menu');
		if($_GET['q']==1){
			$id_parent = $this->uri->segment(5);
			//if($id_parent != ''){
				$this->Model_auth_menu->parent = $id_parent;
				grid($_GET,'Model_auth_menu','get_menu',$alias);
			//}
			exit;
		}
		$this->data['list_group']		= $this->db->get('auth_user_grup')->result_array();
		$parent								= $this->db->order_by('urut')->get_where('ref_menu_admin','id_parents_menu_admin = 0')->result();
		$sp ='&nbsp;&nbsp;&nbsp;&raquo;&nbsp;';
		foreach($parent as $pr){
			$opt .= "<option value='$pr->id_ref_menu_admin'>$pr->menu</option>";
			$sub								= $this->db->order_by('urut')->get_where('ref_menu_admin',"id_parents_menu_admin = $pr->id_ref_menu_admin")->result();
			foreach($sub as $s){
				$opt .= "<option value='$s->id_ref_menu_admin'>$sp $s->menu</option>";
			}
			
		}
		$this->data['list_parent']		= $opt;
		render('apps/auth_menu/auth_menu',$data,'apps');
	}
	function ubah_urutan(){
		//auth_update();
		$post			= $this->input->post();
		$id 			= $post['id_menu'];
		$urutan 		= $post['urutan'];
		$tipe			= $post['tipe'];
		$parent		= $post['parent'];
		if($tipe == 'down'){
			$opr 		=  '>';
			$order	=  'asc';
		}
		else{
			$opr		=  '<';
			$order	= 'desc';
		}
		$data			= $this->db->order_by('urut',$order)->get_where('ref_menu_admin',"urut $opr $urutan and id_parents_menu_admin = $parent")->row();
		$this->db->update('ref_menu_admin',array('urut'=>$data->urut),"id_ref_menu_admin = '$id'");
		$this->db->update('ref_menu_admin',array('urut'=>$urutan),"id_ref_menu_admin = '$data->id_ref_menu_admin'");
	}
	
	
	function proses(){
		$this->load->helper('htmlpurifier');
		$post 		= purify($this->input->post());
		$company 	= $post['company'];
		$idedit 		= $post['idedit'];
		$file			= $_FILES['img_icon'];
		$fname		= $file['name'];
		$c				= $post['insert'];
		$r				= $post['view'];
		$u				= $post['update'];
		$d				= $post['delete'];
		$grup			= $post['grup'];

		#echo '<pre>';
		#echo '<hr>';
		#die(print_r($post));

		unset($post['idedit']);
		unset($post['insert']);
		unset($post['view']);
		unset($post['update']);
		unset($post['delete']);
		unset($post['grup']);
		if($idedit){
			auth_update();
			$this->db->update('ref_menu_admin',$post,"id_ref_menu_admin = '$idedit'");
			$this->db->delete('auth_pages',"id_ref_menu_admin = '$idedit'");
		}
		else{
			auth_insert();
			$post['urut'] = $this->db->select_max('urut')->get_where('ref_menu_admin',"id_parents_menu_admin = $post[id_parents_menu_admin]")->row()->urut + 1;
			$this->db->insert('ref_menu_admin',$post);
			$idedit 		= $this->db->insert_id();
		}
		foreach($grup as $grp){
			$dt['c'] 						= ($c[$grp]=='')?0 : $c[$grp];
			$dt['r']							= ($r[$grp]=='')?0 : $r[$grp];
			$dt['u'] 						= ($u[$grp]=='')?0 : $u[$grp];
			$dt['d'] 						= ($d[$grp]=='')?0 : $d[$grp];
			$dt['id_auth_user_grup'] 	= $grp;
			$dt['id_ref_menu_admin'] 	= $idedit;
			$this->db->insert('auth_pages',$dt);
		}
		if($fname){
			$tmp_file	= $file['tmp_name'];
			$ext			= end(explode('.',$fname));
			$newFile 	= UPLOAD_DIR."$idedit.$ext";
			$this->db->update('ref_menu_admin',array('img_icon'=>"$idedit.$ext"),"id_ref_menu_admin = '$idedit'");
			move_uploaded_file($tmp_file,$newFile);
		}
		$ret['error'] = 0;
		echo json_encode($ret);
		// redirect('apps/auth_menu');
	}
	function delete(){
		auth_delete();
		$post		= $this->input->post();
		$iddel	= $post['iddel'];
		$cek 		= count($this->db->get_where('ref_menu_admin',"id_parents_menu_admin ='$iddel'")->result_array());
		if($cek > 0){
			echo "Masih Ada $cek Sub Menu!";
		}
		else{
			$this->db->delete('auth_pages',"id_ref_menu_admin = '$iddel'");
			$this->db->delete('ref_menu_admin',"id_ref_menu_admin = '$iddel'");
			echo "Delete Success";
		}
	}
	function view_edit($data,$id){
		$idedit = $id;
		$list = "<table class='table table-striped' cellspacing='0'>
							<tr bgcolor='#c0c0c0'>
								<th class='ui-state-default ui-th-column ui-th-ltr bl bt bb' rowspan='2'>User Group</th>
								<th class='ui-state-default ui-th-column ui-th-ltr bt bb'>View</th>
								<th class='ui-state-default ui-th-column ui-th-ltr bt bb'>Insert</th>
								<th class='ui-state-default ui-th-column ui-th-ltr bt bb'>Update</th>
								<th class='ui-state-default ui-th-column ui-th-ltr bt bb br'>Delete</th>
							</tr>
							<tr bgcolor='#c0c0c0' align='center'>
								<td class='ui-state-default ui-th-column ui-th-ltr bb bl'><input type='checkbox' onclick=\"$('.v').attr('checked',this.checked);\"></td>
								<td class='ui-state-default ui-th-column ui-th-ltr bb bl'><input type='checkbox' onclick=\"$('.i').attr('checked',this.checked);\"></td>
								<td class='ui-state-default ui-th-column ui-th-ltr bb bl'><input type='checkbox' onclick=\"$('.u').attr('checked',this.checked);\"></td>
								<td class='ui-state-default ui-th-column ui-th-ltr bb bl br'><input type='checkbox' onclick=\"$('.d').attr('checked',this.checked);\"></td>
							</tr>
							";
		$grup = $this->db->get_where('auth_user_grup')->result();
		foreach($grup as $grp){
			$dtl = $this->db->get_where('auth_pages',"id_auth_user_grup = $grp->id_auth_user_grup and id_ref_menu_admin = $idedit")->row();
							$list .="<tr align='center'>
								<td class='bb bl' align='left'>$grp->grup<input type='hidden' class='no_clear' value='$grp->id_auth_user_grup' name='grup[$grp->id_auth_user_grup]'></td>
								<td class='bb bl'><input type='checkbox' class='v' name='view[$grp->id_auth_user_grup]' value='1' ".(($dtl->r==1) ? 'checked' : '')."></td>
								<td class='bb bl'><input type='checkbox' class='i' name='insert[$grp->id_auth_user_grup]' value='1' ".(($dtl->c==1) ? 'checked' : '')."></td>
								<td class='bb bl'><input type='checkbox' class='u' name='update[$grp->id_auth_user_grup]' value='1' ".(($dtl->u==1) ? 'checked' : '')."></td>
								<td class='bb bl br'><input type='checkbox' class='d' name='delete[$grp->id_auth_user_grup]' value='1' ".(($dtl->d==1) ? 'checked' : '')."></td>
							</tr>";
		}
		$list .="</table>";
		echo $list.'|';
		$this->db->select('id_parents_menu_admin,menu,controller,img_icon,breadcrumb');
		echo implode('|',$this->db->get_where('ref_menu_admin',"id_ref_menu_admin = $idedit")->row_array());
	}

	function remove_img(){
		auth_delete();
		$img = $this->input->post('img');
		unlink(UPLOAD_DIR.$img);
		$this->db->update('ref_menu_admin',array('img_icon'=>null),"img_icon = '$img'");
	}
	function update_auth(){
		//auth_update();
		$post 								= $this->input->post();
		$idedit 								= $post['id_editnya'];
		$c										= $post['insert'];
		$r										= $post['view'];
		$u										= $post['update'];
		$d										= $post['delete'];
		$grup									= $post['grup'];
		$this->db->delete('auth_pages',"id_ref_menu_admin = $idedit");
		foreach($grup as $grp){
			$dt['c'] 						= ($c[$grp]=='') ? 0 : $c[$grp];
			$dt['r']							= ($r[$grp]=='') ? 0 : $r[$grp];
			$dt['u'] 						= ($u[$grp]=='') ? 0 : $u[$grp];
			$dt['d'] 						= ($d[$grp]=='') ? 0 : $d[$grp];
			$dt['id_auth_user_grup'] 	= $grp;
			$dt['id_ref_menu_admin'] 	= $idedit;
			$this->db->insert('auth_pages',$dt);
		}
	}

}
