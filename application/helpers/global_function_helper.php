<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * using for general need
 */

function alamat(){
	 $CI=& get_instance();
	 return $CI->load->view('front/alamat.html');
}


/**
 * list option utk combo box / select list pada grid
 * @author Linda Hermawati 
 * @param $tbl nama tabel
 * @param $id primary key tabel
 * @param $name nama field tabel yg digunakan utk list
 * @param @where (optional) where query tabel
 * @param $terpilih (optional) list terpilih (selected)
 * @param $title (optional) title, default -----------------
 * @return string list option combo box val1:Name 1;val2:Name 2;
 *
 */
function select_grid($tbl,$id='id',$name='name',$where='',$terpilih='',$title='-------'){
	 $CI=& get_instance();
	 $CI->load->database();
	 $list = $CI->db->select("$id , $name")->get_where($tbl,"$id is not null $where order by $name asc")->result_array();
	 $opt = ":select";
	 foreach($list as $l){
				$selected = ($terpilih == $l[$id]) ? 'selected' : '';
				$opt .= ";".$l[$id].":".$l[$name];
	 }
	 return $opt;
}

/**
 * fungsi untuk membuat generate passwrd
 * @author Linda Hermawati 
 * @param $password password
 * @param $panjang untuk menentukan berapa panjang karakter dari password
 * @param $character karakter yang di random
 * @param @where (optional) where query tabel
 * @param $terpilih (optional) list terpilih (selected)
 * @return string untuk password siswa dan orang tua
 *
 */
function generatePassword() {  
    $character = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
    $password = "";  
	 $panjang = 6;  
	 for($i=0;$i<$panjang;$i++) {
		  $password .= $character[rand(0, 36)];  
	 }  
    return $password;  
}

function null_empty($array){
	 if(is_array($array)){
		  foreach($array as $id => $val){
				$ret[$id] = ($val || $val == '0') ? $val : null;
		  }
		  return $ret;
	 }
}
function id_user($data='id_auth_user'){
	 $CI 			= get_instance();
	 $user_sess = $CI->session->userdata('ADM_SESS'); 
	 $field = 'admin_'.$data;
	 return $user_sess[$field];
}
function id_mitra(){
	 $CI 			= get_instance();
	 $user_sess = $CI->session->userdata('ADM_SESS'); 
	 return $user_sess['profil_mitra_id'];
}
function group_id(){
	 $CI 			= get_instance();
	 $user_sess 	= $CI->session->userdata('ADM_SESS'); 
	 return $user_sess['admin_id_auth_user_group'];
}
function company_id(){
	 $CI 			= get_instance();
	 return db_get_one('auth_user','company_id',"id_auth_user = ".id_user());
}
/**
 *render untuk merge template dengan content
 *@param $view file name
 *@param $data array data sent to view
 */
function render($view,$data='',$layout="", $ret=false){
	if(!$layout){
		if(LANGUAGE=="english"){
			$data['product_link'] = '';
			$layout='ddi/main';
		} else if(LANGUAGE=="indonesia"){
			$layout='ddi/main';
			$data['product_link'] = 'hide';
		}
	}
	$CI=& get_instance();
	$uri1 		= $CI->uri->segment(1);
	$uri2 		= $CI->uri->segment(2);
	$uri3 		= $CI->uri->segment(3);

	$pages = $CI->db->get_where("pages", array("uri_path"=>$uri3))->row_array();
	if($pages){
		$data['pages_content'] 	= $pages['page_content'];
		$data['pages_img'] 		= image($pages['img'],'large');
	} else{
		$data['pages_content'] 	= '';
		$data['pages_img'] 		= '';
	}

	$data['base_url'] = $CI->baseUrl;
	$data['lang'] = $CI->uri->segment(1);
	if(!$data['js_file']){
		$data['js_file'] = '';
	}
	if(!isset($data['slider_widget'])){
		$data['slider_widget'] = '';
	}

	if(!isset($data['popular_article'])){
		$data['popular_article'] = '';
	}

	if(!isset($data['meta'])){
		$data['meta'] = '';
	}
	if(!$data['qa_widget']){
		$data['qa_widget'] = '';
	}
	if(!$data['qa_widget_mobile']){
		$data['qa_widget_mobile'] = '';
	}
	$data['base_url_lang'] 	 	 = base_url_lang()."/";
	$data['breadcrumb']          = $data['lang'] == 'apps' ? breadcrumb() : breadcrumb2();
	$CI->breadcrumb              = $data['breadcrumb'];
	$data['breadcrumb_frontend'] = breadcrumb_frontend($data['breadcrumb']);
	// print_r($data['breadcrumb_frontend']);exit;
	$data['hide_banner_bottom'] = $data['hide_banner_bottom']? $data['hide_banner_bottom'] : '';
	$data['app_name']           = APP_NAME;
	$data['partner_menu']       = partner_menu();
	$data['header_menu']        = header_menu();
	$data['footer_menu']        = footer_menu();
	$data['banner_bottom']      = banner_bottom();
	$data['logo'][]             = get_logo();
	$data['logo_footer'][]      = get_logo();
	$data['footer_data'][]      = get_footer_data();
	$data['top_menu']           = top_menu();
	$data['language']           = LANGUAGE;
	$data['top_menu_mobile']    = top_menu_mobile();
	$data['this_year']          = date('Y');
	$data['minify']             = minify();
	$data['signin']             = '';
	$data['signout']            = 'hide';
	$data['base_url']           = base_url();
	$data['page_title']         = generate_title();
	$data['ASSETS_VERSIONING'] = ASSETS_VERSIONING;
	
	$user_sess_data             = $CI->session->userdata('MEM_SESS');
	if(!$data['page_name']){
		$data['page_name'] = generate_title();
	}
	if($user_sess_data){
		if($user_sess_data['remember_me']=1){
			$CI->load->model('loginModel');
			$CI->loginModel->remember_me_login();
		}
		$data['member_namadepan']    = $user_sess_data['member_namadepan'];
		$data['member_namabelakang'] = $user_sess_data['member_namabelakang'];
		$data['signin']              = 'hide';
		$data['signout']             = '';
	}

	$CI->load->model('pagesmodel');
	$langSelected 		= $CI->languagemodel->fetchRow(array("code"=>$data['lang']));
	if($langSelected['id'] == 1){
		$idPrivacy = 17;
	} else{
		$idPrivacy = 18;
	}
	$dataPrivacy 		= $CI->pagesmodel->fetchRow(array("id" => $idPrivacy));
	$data['privacy_teaser'] = $dataPrivacy['teaser'];
	$data['privacy_title'] = $dataPrivacy['page_name'];
	$data['privacy_url'] = base_url().$data['lang']."/pages/".$dataPrivacy['uri_path'];

	/*new script (dwiki)*/
	if(!$data['head_title']){
		$data['head_title']= 'AmCham Indonesia, MJM since 1971';
	}
	if(!$data['meta_description']){
		$data['meta_description']= 'AmCham Indonesia, MJM since 1971';
	}
	if(!$data['meta_keywords']){
		$data['meta_keywords']= 'AmCham';
	}

	$data['meta_img'] = $data['share'] ?  $data['meta_img'] :site_url('/asset/images/logo_sosmed.jpg') ;
	if ($data['meta_img']== '') {
		$data['meta_img'] = 'https://amcham.or.id/asset/images/logo-share-linkedin.jpeg';
	} else {
		$data['meta_img'] = $data['meta_img'];
	}
	$data['meta_og'] = meta_og($data);
	/*end new script (dwiki)*/

	$data['head_title'] = head_title($head_title);
	$data['meta_description_general'] = meta_description($data['meta_description_general']);
	$data['meta_keywords_general'] = meta_keywords($data['meta_keywords_general']);
	$CI->lang->load("general",db_get_one('language','name',array('code'=>$data['lang'])));
	$lang 		= $CI->lang->language;
	$find 		= LANG_CONTROLLER_FIND();
	$replace	= LANG_CONTROLLER_REPLACE();
	foreach ($lang as $key => $value) {
		$data[$key] =str_replace($find, $replace, $value);
	}
	$data['english_view']    = LANGUAGE == 'english' ? '' : 'hidden';
	$data['indonesia_view']  = LANGUAGE == 'indonesia' ? '' : 'hidden';
	if(DEVELOPMENT_MEMBER){
		$data['hide_development'] = 'hide';
	} else {
		$data['hide_development'] = '';
	}
    $data['current_controller'] = current_controller();
	$CI->data['list_language'] = list_language();
	if(id_lang()==1){
		$data['active_id'] = 'active';
	} else {
		$data['active_en'] = 'active';
	}
	if(is_array($data)){
		$CI->data = array_merge($CI->data,$data);
   }
   if(!$layout){
		$CI->parser->parse($view.'.html', $CI->data);
   }
   else{
		$CI->data['content'] = $CI->parser->parse($view.'.html', $CI->data,true);
		if($ret==true){
			 return $CI->parser->parse("layout/$layout.html",$CI->data,true);
		}
		else{
			 $CI->parser->parse("layout/$layout.html",$CI->data);
		}
   }
}
function id_lang(){
	$CI = &get_instance();
    $lang = $CI->uri->segment(1);
    return db_get_one('language','id',array('code'=>$lang));

}
function list_language(){
	$CI 		 = &get_instance();
	$selected 	 = $CI->uri->segment(1);
	$lang 		 = $CI->languagemodel->findBy();
	foreach ($lang as $key => $value) {
		$active  = $selected == $value['code'] ? " 'opacity:1;'" :'opacity:0.5;';
		$url 	 = str_replace("/$selected/", "/$value[code]/", current_url());
		// $ret 	.= "<li $active><a href='$url'>$value[name]</a></li>";
		$ret .= '<a href="'.$url.'" style="'.$active.'"><img alt="'.$value['name'].'" src="'.base_url()."asset/images/lang/$value[code].png".'"></a>';
	}
	return $ret;

	
}
function default_lang_id(){
	$CI 		 = &get_instance();
	$CI->load->model('languagemodel');
	$lang 		 = $CI->languagemodel->fetchRow(array('status_lang'=>1,'is_delete' =>0));
	return $lang['id'];

}

function list_month($selected=''){
	 $bulan = array(1=>'January','February','March','April','May','June','July','August','September','October','November','December');
	  $opt = "<option value=''>Month</option>";
	 foreach($bulan as $key => $bln){
		  $terpilih = ($selected == $key) ? 'selected' : '';
		  $opt .= "<option value=\"$key\" $terpilih>$bln</option>";
	 }
	 return $opt;
}
function list_year($selected='',$len=10){
	 $opt 				= "<option value=''>Year</option>";
	 $this_year 		= date('Y');
	 // $selected			= ($selected == '') ? $this_year : $selected;
	 $year_bef 			= (int)$this_year - $len;
	 $year_aft			= (int)$this_year ;//+ $len;
	 $year = range($year_aft,$year_bef);
	 // $year = range($year_bef,$year_aft);
	 foreach($year as $y){
		  $terpilih = ($selected == $y) ? 'selected' : '';
		  $opt .= "<option $terpilih value=\"$y\">$y</option>";
	 }
	
	 return $opt;
}

/**
 * Export data to excel/csv/txt
 * @author Agung Iskandar
 * @param $fname nama file
 */
function export_to($fname){
	 header("Content-type: application/x-msdownload");
	 $fname = str_replace(' ','_',$fname);
	 header ("Content-Disposition: attachment; filename=$fname");
	 header("Pragma: no-cache");
	 header("Expires: 0");
}
/**
 * Add nomor urut
 * @author Agung Iskandar
 * @param $array datanya
 * @return array dengan tambahan element id urut
 */
function set_nomor_urut($array,$nomor=0){
	 $datas = array();
	 foreach($array as $n =>  $data){
		  $datas[$n]				= $data;
		  $datas[$n]['nomor'] 	= ++$nomor;
	 }
	 return $datas;
}


/**
 * Generate Format Date Time dari mysql style ke format standart atau sebaliknya
 * @author Ivan Lubis
 * @param $datetime date time format
 * @param $mark (optional) separator date, default -
 * @return string format date time
 */
function iso_date_time($datetime,$mark='-'){
	 if(!$datetime) return;
	 list($date,$time) = explode(' ', $datetime);
	 list($thn,$bln,$tgl) = explode('-',$date);
	 return $tgl.$mark.$bln.$mark.$thn.' '.substr($time, 0,8);
}
/**
 * Generate Format Date dari mysql style ke format standart atau sebaliknya
 * @author Ivan Lubis
 * @param $datetime date format
 * @param $mark (optional) separator date, default -
 * @return string format date
 */
function iso_date($date,$mark='-'){
	 if(!$date) return;
	 list($thn,$bln,$tgl) = explode($mark,$date);
	 $tgl = explode(' ', $tgl);
	 return $tgl[0].$mark.$bln.$mark.$thn;
}



function generate_time($time,$mark='.'){
	 if(!$time) return;
	 list($jam,$menit) = explode(':',$time);
	 return $jam.$mark.$menit;
}
/**
 * list option utk combo box / select list
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $tbl nama tabel
 * @param $id primary key tabel
 * @param $name nama field tabel yg digunakan utk list
 * @param @where (optional) where query tabel
 * @param $terpilih (optional) list terpilih (selected)
 * @param $title (optional) title, default -----------------
 * @return string list option combo box <option value='val1>Name 1</option><option value='val2>Name 2</option>...
 *
 */
function select($tbl,$id='id',$name='name',$where='',$terpilih='',$title=''){
	 $CI=& get_instance();
	 $CI->load->database();
	 $list = $CI->db->select("$id , $name")->get_where($tbl,"$id is not null $where order by $name asc")->result_array();
	 $opt = "<option value=''>select</option>";
	 foreach($list as $l){
				$selected = ($terpilih == $l[$id]) ? 'selected' : '';
				$opt .= "<option $selected value='$l[$id]'> $l[$name]</option>";
	 }
	 return $opt;
}
/**
 *
 * function select versi 2 - list option utk combo box / select list
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $tbl nama tabel
 * @param $id primary key tabel
 * @param $name nama field tabel yg digunakan utk list
 * @param $where (optional) where query tabel
 * @param $selected (optional) item selected
 * @param $title (optional) title, default -----------------
 * @return string list option combo box <option value='val1>Name 1</option><option value='val2>Name 2</option>...
 *
 */
function selectlist($tbl,$id='id',$name='name',$where=null,$selected='',$title='select',$order=''){
	 $CI=& get_instance();
	 $CI->load->database();
	 $or = (empty($order) ? $id : $order);
	 $CI->db->order_by($or,'asc');
	 $list = $CI->db->select("$id , $name")->get_where($tbl,$where)->result_array();
	 $opt = "<option value=''>$title</option>";
	 foreach($list as $l){
				$terpilih = ($selected == $l[$id]) ? 'selected' : '';
				$opt .= "<option $terpilih value='$l[$id]'> $l[$name]</option>";
	 }
	 return $opt;
}
/**
 * fungsi untuk menambah hari dalam format y-m-d. contoh : add_date('2012-01-01', 3) // return 2012-01-04
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $dateSql tanggal dalam format sql (y-m-d)
 * @param $jmlHari jumlah hari yg ditambahkan
 * @return date
 *
 */

function add_date($dateSql,$jmlHari){
	 $sql = "SELECT DATE_ADD('$dateSql', INTERVAL $jmlHari DAY) as tanggal";
	 $CI=& get_instance();
	 return $CI->db->query($sql)->row()->tanggal;
}
/**
 * fungsi mendapatkan data hasil query dalam bentuk string (1 field saja yg return)
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $table nama tabel
 * @param $field nama kolom
 * @param $where (optional) where kondisi
 * @return string
 *
 */
function db_get_one($table,$field,$where=''){
	 $CI=& get_instance();
	 if($where != ''){
	 	 return $CI->db->select($field)->get_where($table,$where)->row()->$field;
	 }
	 else{
	  	 return $CI->db->select($field)->get($table)->row()->$field;
	 }
	 
}
/**
 * Javascript Alert Function
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $alert_message alert message yg ditampilkan dalam dialog box
 * @return string javascript <script>alert(message)</script>
 */
function alert($alert_message){
	 if($alert_message != ''){
	 	 return "<script>$(document).ready(function(){notify('$alert_message','success')})</script>";
	 }
}

/**
 * Untuk mendapatkan data via url seperti $_GET
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 * @param $keyword string contoh http://example.com/id/1/name/example ;get('id') return 1; get('name') return example
 *	@param $return_if_null (optional) return value if keyword is null
 * @return string

 */
function get($keyword,$return_if_null=''){
	 $arr 	= array('http://','https://','https://www.','http://www.');
	 $host	= str_replace($arr,'',base_url());
	 $host 	= array($host,'apps/');
	 $uri 	= explode('/',str_replace($host,'',$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']));
	 foreach ($uri as $key => $val){
		if($key > 1){
			if($key % 2 == 0){
				if($val != ''){
					$data[$val] = $uri[$key+1];
				}
			}
		}
	 }
	 return ($data[$keyword]=='') ? $return_if_null : $data[$keyword];
}
/**
 *generate angka 0 didepan variabel contoh : 0000001, 0000123
 *@param $var number variable angka dibelakang
 *@param $len jumlah digit yg diinginkan
 *@example zero_first(1,3) return 001; zero_first(12,5) return 00012;
 */
function zero_first($var,$len){
	return sprintf("%0{$len}s",$var);
}
/**
 *Show array data
 */
function debugvar ($datadebug){
	 echo "<pre>";
	 print_r ($datadebug);
	 echo "</pre>";
}

function cek_file_size($file_size, $max_size=2097152){
	 if ($file_size > $max_size || $file_size =='') {
		  die('Error, Max File Size Is :' .($max_size/1024).' Kb');
	 };
}
function cek_req($field,$title){
	 $img		= "<img src='".base_url()."assets/images/error.gif'>";
	 $CI=& get_instance();
	 if($field==''){
			$err = "$img $title !<br>";
	 }
	 return $err;
	 
}
function meeting_title($id_meeting){
	 return db_get_one('meeting','meeting_title',"id_meeting = '$id_meeting'");
}

function button_name($idedit){
	 $CI=& get_instance();
	 if($idedit){
		  $proses 						= 'Update';
		  $btn							= 'Update';
	 }
	 else{
		  $proses 						= 'Add';
		  $btn							= 'Simpan';
	 }
	 $CI->data['button'] 			= $btn;
	 $CI->data['proses'] 			= $proses;
	 $CI->data['idedit'] 			= $idedit;
}
function clear_html($html){
	 $html =  str_replace("\n","",$html);
	 $html =  str_replace("\r"," ",$html);
	 return str_replace ("	",'',(trim(strip_tags($html))));
}

function download_button($dir,$file,$id,$link=true,$alias=''){
	 $alias = ($alias=='') ? $file : $alias;
	 if($file){
		  $CI=& get_instance();
		  $files = base64_encode($id.'_'.$file);
		  $dir = base64_encode($dir);
		  $form_name = rand(0,999999999);
		  $form  = "<form method='post' action='".base_url()."apps/home/download' name='f$form_name' id='f$form_name'>";
		  $form .= "<input type='hidden' name='dir' value='$dir'>";
		  $form .= "<input type='hidden' name='file' value='$files'>";
		  //$form .= "<input type='submit' value='$file'>";
		  $form .= ($link==true) ? "<a href='javascript:document.f$form_name.submit()'>$alias</a>" : '';
		  $form .= '</form>';
		  //$f['form_link'] = "<a href='javascript:document.f$form_name.submit()'>$file</a>";
		  //$f['form']		= $form;
		  if($link==true){
				return $form;
		  }
		  else{
				$CI->data['form'] .= $form;
				return "<a href='javascript:document.f$form_name.submit()'>$alias</a>";
		  }
		  return $form;
	 }
}

function upload($tmp,$path,$desc=''){
	 $CI=& get_instance();
	 $ext								= strtolower(end(explode('.',$path)));
	 $fname							= end(explode('/',$path));
	 //cek_file_size(filesize($tmp));
	 move_uploaded_file($tmp,$path);
	 if($ext == 'pdf' || $ext == 'doc'){
		  $end 						= (strlen($path)-3);
		  $txt						= substr($path,0,$end).'txt';
		  if($ext=='pdf'){
				exec(" \"C:\xpdf\bin32\pdftotext.exe\" \"$path\" ");
		  }
		  else if($ext=='doc'){
				exec(" \"C:\antiword\antiword.exe\" \"$path\"  > \"$txt\" ");
		  }
		  $data['content'] 		= file_get_contents($txt);
		  $data['description'] 	= $desc;
		  $data['path'] 			= path($path);
		  $data['file_name'] 	= $fname;
		  $data['file_type'] 	= $ext;
		  $data['adv_search'] 	= $CI->uri->segment(2);		  
		  $CI->db->insert('content_file',$data);
		  unlink($txt);
	 }
	 
	 //echo $tmp;
	 //echo $exp;
}
// relative path buat simpen ke tabel content_file
function path($path){
	 return str_replace(UPLOAD_DIR,'',$path);
}

function delete_content_file($path){
	 $CI=& get_instance();
	 unlink($path);	 
	 $CI->db->delete('content_file',"path = '".path($path)."'");
}
function export_to_pdf($fname){
	 if(get('token')==''){
		 $url 			=  current_url().'/token/'. md5(date('dmy') . '1qazxsw2');
		 $token =  '/token/'.md5(date('dmy') . '1qazxsw2');
		 $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		 $url = str_replace('?',$token.'?',$url);
		 //echo $url;
		 //exit;
		 $temp_file 	= UPLOAD_DIR.'tmp/'.rand(1,99999).'.pdf';
		 exec(" \"C:/Program Files (x86)/wkhtmltopdf/wkhtmltopdf.exe\" \"$url\"  \"$temp_file\" ");
		 export_to($fname);
		 echo file_get_contents($temp_file);
		 unlink($temp_file);
		 exit;
	 }
}
/**
 * utk form jika di variable stringnya ada kutip
 * @author Agung Iskandar
 * @param $string string yg ingin ditampilkan dalam form
 */
function quote_form($string){
	 if(is_array($string)){
		  foreach($string as $key=>$val){
				$new_str[$key] = htmlspecialchars($val, ENT_QUOTES);
		  }
		  return $new_str;
	 }
	 else{
		  return htmlspecialchars($string, ENT_QUOTES);
	 }
}
function help($modul){
	 $CI		= & get_instance();
	 $helps	= $CI->db->get_where('tooltips',"module = '$modul'")->result_array();
		foreach($helps as $help){
			$key 	= $help['field_key'];
			$tips = $help['tips'];
			$CI->data[$key] = ($tips && $help['publish']=='Yes')?"<span class='tooltips'><a href='#$key' class='tips'></a><div id='$key' style='display:none'>$tips</div></span>":'';
		}
}
function date_range2($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange=array();

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}

function date_range($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange=array();

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return count($aryRange);
}

// include header
function panggil_banner($img){
	 $CI		= & get_instance();
	 $CI->data['img'] = $img;
	 return $CI->parser->parse('home/header.html',$CI->data,true);
}

function tgl_indo($str_date){
	 $string = array('January','February','March','May','June','July','August','October','December');
	 $string_replace = array('Januari','Februari','Maret','Mei','Juni','Juli','Agustus','Oktober','Desember');
	 return str_replace($string,$string_replace,$str_date);
	 
}
function insert_log($aktifitas){
	 $CI					= & get_instance();
	 $data['activity'] 		= $aktifitas;
	 $data['detail'] 		= $CI->detail_log;
	 $data['ip'] 			= $_SERVER['REMOTE_ADDR'];
	 $data['id_auth_user'] 	= id_user();
	 $data['log_date'] =  date('Y-m-d H:i:s');
	 $CI->db->insert('access_log',$data);
}
function insert_frontend_log($aktifitas){
	 $CI					= & get_instance();
	 $data['activity'] 		= $aktifitas;
	 $data['detail'] 		= $CI->detail_log;
	 $data['ip'] 			= $_SERVER['REMOTE_ADDR'];
	 $data['log_date']      =  date('Y-m-d H:i:s');
	 $CI->db->insert('access_log',$data);
}
function insert_log_member($aktifitas){
	 $CI					= & get_instance();
	 $data['activity'] 		= $aktifitas;
	 $data['detail'] 		= $CI->detail_log;
	 $data['ip'] 			= $_SERVER['REMOTE_ADDR'];
	 $data['id_auth_user'] 	= id_member();
	 $data['log_date'] =  date('Y-m-d H:i:s');
	 $CI->db->insert('access_log',$data);
}
function detail_log(){
	 $CI = & get_instance();
	 $CI->detail_log .= $CI->db->last_query() .";\n";

}
function arr_to_str($data){
	 foreach ($data as $key => $val){
		  $ret .="$key : $val <br>";
	 }
	 return $ret;
}

function selectlist2($conf){
	$tbl 			 = $conf['table'];
	$id				 = ($conf['id']) ? $conf['id'] : 'id';
	$name			 = ($conf['name']) ? $conf['name'] : 'name';
	$where			 = $conf['where'];
	$selected		 = $conf['selected'];
	$title			 = ($conf['title']) ? $conf['title'] : 'select'; //$conf['title'];
	$order			 = $conf['order'];
	$CI				 = &get_instance();
	$or 			 = (empty($order) ? $name : $order);
	$list 			 = $CI->db->order_by($or,'asc')->select("$id , $name")->get_where($tbl,$where)->result_array();
	$opt 			 = $conf['no_title'] ? '' : "<option value=''>$title</option>";
	$opt			.= ($conf['add_new']) ? ("<option value='addNew'>+ Add $conf[add_new]</option>"): '';
	foreach($list as $l){
		$terpilih = ($selected == $l[$id]) ? 'selected' : '';
		$opt      .= "<option $terpilih $disable value='$l[$id]'> $l[$name]</option>";
	}
	return $opt;
}
function list_account($type){
	 return 		selectlist2(array('table'=>'account','name'=>'account_name','no_title'=>1,'where'=>array('type'=>$type,'company_id'=>company_id())));

}

function selectNewsCat($conf){
	$tbl 			 = $conf['table'];
	$id				 = ($conf['id']) ? $conf['id'] : 'id';
	$name			 = ($conf['name']) ? $conf['name'] : 'name';
	$where			 = $conf['where'];
	$selected		 = $conf['selected'];
	$title			 = ($conf['title']) ? $conf['title'] : 'select'; //$conf['title'];
	$order			 = $conf['order'];
	$CI				 = &get_instance();
	$or 			 = (empty($order) ? $name : $order);

	$where['id_parent_category'] = 0;
	$listParent		 = $CI->db->order_by($or,'asc')->select("$id , $name, id_parent_category")->get_where($tbl,$where)->result_array();
	$opt 			 = $conf['no_title'] ? '' : "<option value=''>$title</option>";
	$opt			.= ($conf['add_new']) ? ("<option value='addNew'>+ Add $conf[add_new]</option>"): '';
	foreach($listParent as $p){
		$terpilih 	 = ($selected == $p[$id]) ? 'selected' : '';
		$opt 		.= "<option $terpilih value='$p[$id]'>$p[$name]</option>";

		$whereList	 = $conf['where'];
		$whereList['id_parent_category']	= $p['id'];
		$list 		 = $CI->db->order_by($or,'asc')->select("$id , $name")->get_where($tbl,$whereList)->result_array();

		foreach($list as $l){
			$terpilihChild	 = ($selected == $l[$id]) ? 'selected' : '';
			$opt 		.= "<option $terpilihChild value='$l[$id]'>&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;$l[$name]</option>";

			$whereSubList	 = $conf['where'];
			$whereSubList['id_parent_category']	= $l['id'];
			$sublist 		 = $CI->db->order_by($or,'asc')->select("$id , $name")->get_where($tbl,$whereSubList)->result_array();

			foreach ($sublist as $s) {
				$terpilihSubChild	 = ($selected == $s[$id]) ? 'selected' : '';
				$opt 		.= "<option $terpilihSubChild value='$s[$id]'>&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;&nbsp;&nbsp;$s[$name]</option>";				
			}
		}
	}
	return $opt;
}

function paging($total_row,$url,$perpage=10,$uri_segment=4){
	 $CI = &get_instance();
	 $CI->load->library('pagination');
	 $config['uri_segment'] 	= $uri_segment;
	 $config['base_url'] 		= $url;
	 $config['total_rows'] 		= $total_row;
	 $config['per_page'] 		= $perpage;
	 $config['anchor_class'] 	= 'class="paging" ';
	 $CI->pagination->initialize($config);
	 return	 $CI->pagination->create_links();
}

function current_controller($param=''){
	//$param						= '/'.$param;
	$CI 						= & get_instance();
	$dir						= $CI->router->directory;
	$class						= $CI->router->fetch_class();
	$func						= ($param=='function') ? ('/'.$CI->router->fetch_method()) : "/$param";
	$base_url					= str_replace('http://'.$_SERVER['HTTP_HOST'],'',base_url());
	$data['base_url']			= str_replace('https://'.$_SERVER['HTTP_HOST'],'',$base_url);//jika https
	return $data['base_url'].$dir.$class.$func;
}
//mygrid
function query_grid($alias,$isTotal=0){
	 $CI 					= & get_instance();
	 // $CI->layout 			= 'blank';
	 $param 				= $_GET;
	 $where					= where_grid($param,$alias);
	 $sort_field	= ($param['sort_field']) ? $param['sort_field'] : 'id';
	 $sort_type		= ($param['sort_type']) ? $param['sort_type'] : 'desc';
	 // $order  				= "order by $param[sort_field] $param[sort_type]";
	 // if($param['perpage']){
		//   $paging 	= "limit $param[perpage] offset $param[page]";
	 // }
	 // $sql			= "$query $where";
	 // $data['data']	= $CI->db->query($sql." $order $paging")->result_array();
	 // $n = 0;
	 // $page = $_GET['page'] ;
	 // foreach($data['data'] as $t){
		//   $data['data'][$n]['no'] = ++$page;
		//   ++$n;
	 // }
	 $CI->db->order_by(str_replace('-','.',$sort_field),$sort_type);
	 if($isTotal!=1){
		$CI->db->limit($param['perpage'],$param['page']);
	 }
	 // $data['data'] = set_nomor_urut($query->result_array());
	 // echo $CI->db->last_query();
	 // $data['paging']	= paging_grid($query->num_rows());
	 // return $data;

}
function ddi_grid($data,$ttl_row,$uri_segment=4,$custom_url=''){
    $data['data'] = set_nomor_urut($data,$_GET['page']);
    $data['paging'] = paging_grid($ttl_row,$uri_segment,$custom_url);
    return $data;
}
function paging_grid($total_row,$uri_segment=4,$custom_url=''){
	$CI    = & get_instance();
	$param = $_GET;
    $CI->load->library('pagination');
    $base_url = ($custom_url) ? $custom_url : current_controller('function');
    $config['base_url']         = $base_url;
	$config['total_rows'] 		= $total_row;
	$config['uri_segment'] 		= $uri_segment;
	$config['anchor_class'] 	= 'class="tangan"';
	$config['per_page'] 		= $param['perpage'];
	$config['first_tag_open'] 	= '<li>';
	$config['first_tag_close'] 	= '</li>';
	$config['first_link'] 		= '<<';
	$config['last_link'] 		= '>>';
	$config['num_tag_open'] 	= '<li>';
	$config['num_tag_close'] 	= '</li>';
	$config['last_tag_close'] 	= '</li>';
	$config['last_tag_open'] 	= '<li>';
	$config['first_tag_close'] 	= '</li>';
	$config['first_tag_open'] 	= '<li>';
	$config['next_link'] 		= '>';
	$config['prev_link'] 		= '<';
	$config['prev_tag_open'] 	= '<li>';
	$config['prev_tag_close'] 	= '</li>';
	$config['next_tag_open'] 	= '<li>';
	$config['next_tag_close']	= '</li>';
	$config['next_tag_open'] 	= '<li>';
	$config['next_tag_close']	= '</li>';
	$config['cur_tag_open'] 	= '<li class="active"><a>';
	$config['cur_tag_close'] 	= '</a></li>';
	$config['first_url'] 		= $config['base_url'] .'/0';
	$CI->pagination->initialize($config);
	
	$n 		 = $param['page'];
	$n2 	 = $n+1;
	$sd 	 = $n + $param['perpage'];
	$sd 	 = ($total_row < $sd) ? $total_row : $sd;
	$remark	 = ($sd > 0) ? ("$n2 - $sd Total $total_row") : '';
	$paging  = '<div class="col-sm-6 col-md-6 col lg-6"><span class="show_page">'.$remark.'</span><span class="paging-select"></span></div>
			   <div class="paginationcol-sm-6 col-md-6 col lg-6"><ul class="pagination m-t-0 m-b-10  pull-right ">';
	$paging .= $CI->pagination->create_links();
	$paging .= '</ul></div>';
	return $paging;
}
function where_grid($param,$alias){
	$CI = & get_instance();
	
	foreach($param as $key=>$val){
		if(substr($key,0,6)=='search'){
			$field  = ($alias[$key]!='') ? $alias[$key] : substr($key,7);
			if($val){
				// $where .= "and $field like '%$val%' ";
                $field_explode = explode('_',$field,-1);
			// if ($key = 'search_exactor_news_category') {
			// print_r($field);exit;
			// 	# code...
			// }
                if($field == 'a.datestart'){
					$CI->db->where("a.create_date >=",iso_date_custom_format($val,'Y-m-d'));
				}else if($field == 'a.dateend'){
					$CI->db->where("a.create_date <=",iso_date_custom_format($val,'Y-m-d'));
				} else if ($field == 'a.full_name') {
					$CI->db->where("(concat (a.firstname ,' ', a.lastname) like '%$val%')");
				} else if ($field_explode[0] == 'exact') {
					$CI->db->where(str_replace('exact_','',$field), "$val");
				}else if (in_array('or', $field_explode)) {
					// print_r('awd');exit;
					$s_or  = explode('_or_',str_replace('exactor_','',$field));
					// if ($s_or) {
					// 	print_r($s_or);exit;
					// }
					       $CI->db->where("( ".$s_or[0]." = '$val' or ".$s_or[1]." = '$val'"." )");
					

				} else if($field_explode[0]!='or'){
					$CI->db->like($field, "$val");
                } else {
                    $CI->db->or_like(str_replace('or_','',$field), "$val");
                }
			}
		}
		else if(substr($key,0,7)=='between'){
			$start = (strpos($key,'to') ? 11 : 13);
			$field  = ($alias[$key]!='') ? $alias[$key] : substr($key,$start);
			if($val){
				$explode = explode('.', $field);
				if($field == 'a.daterange'){
					$from = iso_date_custom_format($param['between_from'],'Y-m-d');
					$to = iso_date_custom_format($param['between_to'],'Y-m-d','+1 day');
					if($param['between_from'] != '' && $param['between_to'] != '') {
						$CI->db->where("a.date between '$from' and '$to'");
					} else if($param['between_from'] != '' && $param['between_to'] == '') {
						$to = iso_date_custom_format(date('Y-m-d'),'Y-m-d','+1 day');
						$CI->db->where("a.date between '$from' and '$from'");
					}
				} else { //$field == appropriate field name on database/not using alias name
					$field_explode = (count($explode) > 1) ? ''.$explode[1] : '_'.$explode[0];
					$field = substr($field_explode,1);
					$from = iso_date_custom_format($param['between_from'.$field_explode],'Y-m-d');
					$to = iso_date_custom_format($param['between_to'.$field_explode].'+1 day','Y-m-d');
					// between_from_crea/te_date
					if($param['between_from'.$field_explode] != '' && $param['between_to'.$field_explode] != '') {
						$CI->db->where('a.'.$field." between '$from' and '$to'");
						unset($param[$key]);
					} else if($param['between_from'.$field_explode] != '' && $param['between_to'.$field_explode] == '') {
						$to = iso_date_custom_format(date('Y-m-d'),'Y-m-d');
						$CI->db->where('a.'.$field." between '$from' and '$to'");
					}
				}
			}
		}
	}
	return $where;
}



function filename($fname){
	 //$fname = "~!@#$%^&asdfj.abc.def.ghi.asdflkj.jpg";
	 $ext 					= explode('.',$fname);
	 $length 				= count($ext)-1;
	 $extension				= $ext[$length];
	 unset($ext[$length]);
	 $fname = implode('-',$ext);
	 return date('ymdHis').'-'.url_title($fname).'.'.$extension;
		
}

function get_day($tgl=''){
	 $day_in_eng = date('l',$tgl);
	 if ($day_in_eng == 'Sunday') return "Minggu";
	 else if ($day_in_eng == 'Monday') return "Senin";
	 else if ($day_in_eng == 'Tuesday') return "Selasa";
	 else if ($day_in_eng == 'Wednesday') return "Rabu";
	 else if ($day_in_eng == 'Thursday') return "Kamis";
	 else if ($day_in_eng == 'Friday') return "Jumat";
	 else if ($day_in_eng == 'Saturday') return "Sabtu"; 
}

function number_formating($data,$field,$ttl_comma=0){
	 foreach ($data as $index => $value){
		  foreach($value as $idx => $val){
			   if($idx == $field){
					$data[$index][$idx] = number_format($val,$ttl_comma);
			   }
		  }
	 }
	 return $data;
}

function getImg($img,$size='small',$isNoPhoto=0){
	// $noPhoto = $isNoPhoto==1 ? ("<img src='".base_url("/images/no-img-$size.png")."'>") : '';
	// $img =  is_file(dirname(__FILE__)."/../../images/article/$size/$img") ? ("<img src='".base_url("/images/article/$size/$img")."'>") : $noPhoto;

	$noPhoto = "<img src='".base_url("/images/no-img-$size.png")."'>";
	$image =  is_file(dirname(__FILE__)."/../../images/article/$size/$img") ? ("<img src='".base_url("/images/article/$size/$img")."'>") : $noPhoto;
	return $image;
}
function getImgLink($img,$size='small',$isNoPhoto=0){
	$noPhoto = $isNoPhoto==1 ? (base_url("/images/no-img-$size.png")) : '';
	$img =  is_file(dirname(__FILE__)."/../../images/article/$size/$img") ? (base_url("/images/article/$size/$img") ) : $noPhoto;
	return $img;
}
function getVideo($video,$get_id){
	$ret = '';
	if($video){
		$v = explode('v=', $video);
		
		if (count($v)<2) {
			$v = explode('embed/', $video);
		}

		if(count($v)<2){
			$v = explode('.be/', $video);
		}

		// $ret = '<iframe width="854" height="480" src="https://www.youtube.com/embed/'.$v[1].'" frameborder="0" allowfullscreen></iframe>';
	}
	// return $ret;
	$vid = explode('&', $v[1]);
	$vid = $vid[0];
	if ($get_id ==1) {
		return $vid;	
	}else{
		return "https://www.youtube.com/embed/$vid";
	}
}
function get_video_iframe_yt($url_youtube){
	$url  = getVideo($url_youtube);
	$ret = '<iframe width="854" height="480" src="'.$url.'" frameborder="0" allowfullscreen></iframe>';
	return $ret;
}
function get_youtube_thumbnail($url_youtube){
	$id_video = getVideo($url_youtube,1);
	return '<img src="http://img.youtube.com/vi/'.$id_video.'/0.jpg" width="100%">';
}

function partner_menu(){
	// $CI=& get_instance();
	// $lang = $CI->uri->segment(1);

	// $CI->load->model('partnerModel');  
	// $data 		= $CI->partnerModel->findBy(); 
	// foreach ($data as $key => $value){
	// 	$url = ($value['url'] == '' ? '#' : $value['url']);
	// 	$ret .= "<a href='$url'>".$value['name']."</a>"; 
	// }
	return false;
	// return $ret;
}

function get_event_files($path, $data, $stPage){
	$CI=& get_instance();

	if($stPage == 1){
		$CI->data['dataFiles']     = $data['list_files'];
		$CI->data['jmlFiles']      = $data['jmlFiles'];
		$CI->data['nomorFile']     = $data['nomorFile'];
		$CI->data['showUploadAll'] = $data['showUploadAll'];
	} else{
		$CI->data['dataFiles'] 	   = $data;
	}
	
	return $CI->parser->parse('apps/'.$path, $CI->data,true);
}
function get_files($path, $data, $stPage)
{
	$CI = &get_instance();

	if ($stPage == 1) {
		$CI->data['dataFiles']     = $data['list_files'];
		$CI->data['jmlFiles']      = $data['jmlFiles'];
		$CI->data['nomorFile']     = $data['nomorFile'];
		$CI->data['showUploadAll'] = $data['showUploadAll'];
	} else {
		$CI->data['dataFiles'] 	   = $data;
	}

	return $CI->parser->parse('apps/' . $path, $CI->data, true);
}

function id_child($conf){
	$CI =& get_instance();
	if (is_array($conf['id'])) {
		$arr_id_child = '';
		foreach ($conf['id'] as $key => $value) {
			$CI->db->select('group_concat('.$conf['colomn_select'].') as id_child');
			$CI->db->where($conf['colomn'],$value);
			$CI->db->where($conf['table'].'.is_delete = 0');
			$CI->db->from($conf['table']);
			if ($conf['with_parent'] == 1) {
				$arr_id_child .= $CI->db->get()->row_array()['id_child'].','.$value;
			}else{
				$arr_id_child .= $CI->db->get()->row_array()['id_child'];
			}
		}
		
		if ($conf['array'] == 1) {
			$arr_id_child = explode(',', $arr_id_child);
		}

		return $arr_id_child;
	}else{
		$CI->db->select('group_concat('.$conf['colomn_select'].') as id_child');
		$CI->db->where($conf['colomn'],$conf['id']);
		$CI->db->where($conf['table'].'.is_delete = 0');
		$CI->db->from($conf['table']);
		if ($conf['with_parent'] == 1) {
			$arr_id_child = $CI->db->get()->row_array()['id_child'].','.$conf['id'];
		}else{
			$arr_id_child = $CI->db->get()->row_array()['id_child'];
		}

		if ($conf['array'] == 1) {
			$arr_id_child = explode(',', $arr_id_child);
		}
		return $arr_id_child;

	}
}
function base_url_lang(){
	$CI =& get_instance();
	return base_url($CI->lang->lang());
}

function url_encode($str, $replace=array(), $delimiter='-') {
   if( !empty($replace) ) {
       $str = str_replace((array)$replace, ' ', $str);
   }

   $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
   $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
   $clean = strtolower(trim($clean, '-'));
   $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

   return $clean;
}
function get_web_page( $url )
{
	// $curl = curl_init();
	// // Set some options - we are passing in a useragent too here
	// curl_setopt_array($curl, array(
	//     CURLOPT_RETURNTRANSFER => 1,
	//     CURLOPT_URL => $url,
	//     CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	// ));
	// // Send the request & save response to $resp
	// $content = curl_exec($curl);
	// // Close request to clear up some resources
	// if(!curl_exec($curl)){
	//     die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
	// }
	// curl_close($curl);
	// print_r($content);

	$options = array(
              CURLOPT_CUSTOMREQUEST  => "GET",    // Atur type request, get atau post
              CURLOPT_POST           => false,    // Atur menjadi GET
              CURLOPT_FOLLOWLOCATION => true,    // Follow redirect aktif
              CURLOPT_CONNECTTIMEOUT => 120,     // Atur koneksi timeout
              CURLOPT_TIMEOUT        => 120,     // Atur response timeout
              CURLOPT_RETURNTRANSFER => true,
          );

	$ch      = curl_init( $url );          // Inisialisasi Curl
	curl_setopt_array( $ch, $options );    // Set Opsi
	$content = curl_exec( $ch );           // Eksekusi Curl
	if(!curl_exec($ch)){
	    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
	}
	curl_close( $ch );                     // Stop atau tutup script

    return $content;
}