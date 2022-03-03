<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * common function
 * @author Agung Iskandar <agung.iskandar@gmail.com>
 */

function is_file_exsist($path,$file){
	$fl = str_replace('//', '/', $path.'/'.$file);
	if(is_file($fl)){
		return $file;
	}
}
function last_query(){
	 $CI 		= & get_instance();
	echo '<pre>'.$CI->db->last_query().'</pre>';
}
function is_file_copy($path,$file){
	$fl = str_replace('//', '/', $path.'/'.$file);
	return $fl;	
}
function upload_file($field,$path='',$allowed_type='*',$max_size=0){
	 $CI 		= & get_instance();
	 $name 		= strtolower($_FILES[$field]['name']);
	 $ext		= end(explode('.',$name));
	 $CI->load->helper(array('form', 'url'));
	 $config['upload_path'] 	= UPLOAD_DIR.$path;
	 $config['allowed_types'] 	= $allowed_type;
	 $config['file_name'] 		= url_title(str_replace($ext,'',$name));
	 $config['max_size']		= $max_size;
	 
	 $CI->load->library('upload', $config);
	 if(! $CI->upload->do_upload($field)){
		  return $CI->upload->display_errors(' ', ' ');
	 }
	 else{
		  return $CI->upload->data();
	 }
}
function imagemanager($field='img',$img='',$max_width_cropzoom=277,$max_height_cropzoom=150,$id,$imgname='',$idTitle){
	$CI 		= & get_instance();
	$html['config'] = '
	<div class="modal fade invis" id="popImageManager">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title">Image Manager - Max filesize('.ini_get('upload_max_filesize').') </h4>
					<div class="form-inline">
						<input type="text" id="search-picture" style="width: 50%; height: 34px; padding: 6px 12px; display: inline-block;
						margin-top: 10px;" placeholder="Search...">
						<a class="btn btn-primary" id="search_image_manager" style="margin-top: -1px;"><i class="fa fa-search"></i> Search</a>
					</div>
				</div>
				<div class="modal-body">
					<div class="row-fluid" id="list-image-manager">
						<i class="fa fa-refresh fa-spin"></i> Loading...
					</div>
				</div>
				<span class="col-md-offset-1">
					<div class="pagination"></div>
				</span>
				<div class="modal-footer">
					<div class="col-md-12">
						<div class="col-md-4">
							<input type="file" id="imagemanagersource"  name="img">
						</div>
						<div class="col-md-4">
							<label style="display:inline;"><input type="checkbox" checked value="1" id="is_public"> Public Access</label>
						</div>
						<div class="col-md-4">
							<a class="btn btn-primary" id="upload-img">Upload</a>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
	<div class="modal fade invis" id="popImageManagerDetail">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title">Image Detail </h4>
				</div>
				<div class="modal-body">
					<img id="imageDetail">
				</div>
			</div>
		</div>
	</div>
	<div class="modal modal-message fade invis" id="modal-crop">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title">Create Thumbnail </h4>
				</div>
				<div class="modal-body">
					<div id="crop_container"></div>
	
					<div id="image-thumb">
						<img src="">
					</div>
					<a id="crop" class="btn btn-success">Create Thumbnail</a>
				</div>
				<div class="clearfix">&nbsp;</div>
				<div class="modal-footer">
					<a id="imagemanager-cancel" class="btn btn-warning">Cancel</a>
					<a class="btn btn-primary" id="imagemanager-save"> Simpan </a>
					<input type="hidden" id="imagemanager-name" value="'.$imgname.'">
					<input type="hidden" id="imagemanager-getkey" value="'.$idTitle.'">
				</div>
			</div>
		</div>
	</div>
	<script>
	    var max_width_cropzoom = "'.$max_width_cropzoom.'";
	    var max_height_cropzoom = "'.$max_height_cropzoom.'";
	    var function_pagination = 0;
	</script>
	<link href="'.$CI->baseUrl.'assets/plugins/cropzoom/jquery.cropzoom.css" rel="Stylesheet" type="text/css" /> 
	<script type="text/javascript" src="'.$CI->baseUrl.'assets/plugins/cropzoom/jquery.cropzoom.js"></script>
';

// if ($is_array == 1){
// 	$input = '<input type="hidden" name="'.$field.'[]">';
// } else {
// 	$input = '<input type="hidden" name="'.$field.'">';
// }
$html['browse'] = '<div class="browse-image" id="'.$field.$id.'">
				<img src="'.$img.'" width="100%">
				<!--<i class="fa fa-file-picture-o" style="font-size: 60px; padding-top:30px"></i>-->
				<div>Select Image</div>
			</div>
			<input type="hidden" name="'.$field.'[]">';

// $html['browse'] = '<div class="browse-image" id="'.$field.$id.'">
// 				<img src="'.$img.'" width="100%">
// 				<div>Select Image</div>
// 				<i class="fa fa-file-picture-o" style="font-size: 60px;"></i>
// 			</div>
// 			'.$input;
return $html;
}

function filemanager($key,$value) {
	// echo $key;exit();
	$CI 		=& get_instance();

	$id    = ($key) ? 'fl-'.$key : 'fl-1';
	$out   = ($key) ? '-'.$key : '-1';
	$namex = ($key) ? 'file[]' : 'file';
	$value = ($value) ? $value : '';

	$html['browse'] = '	<div class="fileUpload btn btn-info col-md-2">
						    <span>Select File</span>
						    <input type="file" name="'.$namex.'" class="uploadFile" id="'.$id.'" />
						</div>
						<span class="col-md-4" style="padding-top: 10px !important;">
							<input class="output-file'.$out.' form-control" value="'.$value.'" placeholder="Choose File" disabled="disabled" />
							<label class="msg-inf msg-info'.$out.'"></label>
						</span>			
					';
	$html['config'] = load_css('filemanager.css','assets/plugins/filemanager/',true);
	$html['config'] .= load_jsx('filemanager.js','assets/plugins/filemanager/',true);

	return $html;
}
function fileToUpload($file,$key,$fileRename='') {
	$CI 		=& get_instance();
	/*echo $key;
	print_r($file['name'][$key]);exit();*/
	if($fileRename == ''){
		$fileRename = $file['name'][$key];
	}

	$_FILES['userfile']['name']     = $fileRename;
	$_FILES['userfile']['type']     = $file['type'][$key];
	$_FILES['userfile']['tmp_name'] = $file['tmp_name'][$key];
	$_FILES['userfile']['error']    = $file['error'][$key];
	$_FILES['userfile']['size']     = $file['size'][$key];

	$config['upload_path']          = './file_upload/';
	$config['allowed_types']        = '*';
    $config['max_size']             = 20000000;

	//load library upload;
	$CI->load->library('upload',$config);
	$CI->upload->initialize($config);
	$CI->upload->do_upload();

	/*if ($CI->upload->do_upload('userfile'))
    {
    	$CI->upload->data();
    	echo "berhasil";exit();
    }
    else
    {
        print_r($CI->upload->display_errors());exit();
    }*/
}

function fileToProfileImage($file,$key,$fileRename='',$type) {
	$CI 		=& get_instance();
	/*echo $key;
	print_r($file['name'][$key]);exit();*/
	if($fileRename == ''){
		$fileRename = $file['name'];
	}

	$_FILES['userfile']['name']     = $fileRename;
	$_FILES['userfile']['type']     = $file['type'];
	$_FILES['userfile']['tmp_name'] = $file['tmp_name'];
	$_FILES['userfile']['error']    = $file['error'];
	$_FILES['userfile']['size']     = $file['size'];

	$config['upload_path']          = './images/member/'.$type;
	$config['allowed_types']        = '*';
    $config['max_size']             = 20000000;

	//load library upload;
	$CI->load->library('upload',$config);
	$CI->upload->initialize($config);
	$CI->upload->do_upload();
	
}

function fileToUploadInvoiceImage($file,$key,$fileRename='') {
	$CI 		=& get_instance();
	/*echo $key;
	print_r($file['name'][$key]);exit();*/
	if($fileRename == ''){
		$fileRename = $file['name'][$key];
	}

	$_FILES['userfile']['name']     = $fileRename;
	$_FILES['userfile']['type']     = $file['type'][$key];
	$_FILES['userfile']['tmp_name'] = $file['tmp_name'][$key];
	$_FILES['userfile']['error']    = $file['error'][$key];
	$_FILES['userfile']['size']     = $file['size'][$key];

	$config['upload_path']          = './file_upload/invoice_confirm/';
	$config['allowed_types']        = '*';
    $config['max_size']             = 20000000;

	//load library upload;
	$CI->load->library('upload',$config);
	$CI->upload->initialize($config);
	$CI->upload->do_upload();

	if ($CI->upload->do_upload('userfile'))
    {
    	$CI->upload->data();
    	echo "berhasil";exit();
    }
    else
    {
        print_r($CI->upload->display_errors());exit();
    }
}



function multipleUpload($file, $path, $maxSize) {
	$CI 		=& get_instance();
	// echo $key;
	// print_r($file);
	// print_r($file['name']);
	// print_r(pathinfo( $file['name'] )['filename']." - ".uniqid().'.'.pathinfo( $file['name'] )['extension'] );
	// print_r($file['name']." - ".uniqid().'.'.pathinfo( $file['name'] )['extension']);
	// exit();

	$_FILES['userfile']['name']     = pathinfo( $file['name'] )['filename']."_".uniqid().'.'.pathinfo( $file['name'] )['extension'] ;
	$_FILES['userfile']['type']     = $file['type'];
	$_FILES['userfile']['tmp_name'] = $file['tmp_name'];
	$_FILES['userfile']['error']    = $file['error'];
	$_FILES['userfile']['size']     = $file['size'];

	$config['upload_path']          = $path;
	$config['allowed_types']        = '*';
    $config['max_size']             = $maxSize; 
    

	//load library upload;
	$CI->load->library('upload',$config);
	$CI->upload->initialize($config);
	$CI->upload->do_upload();

	// kalau upload gallery
    if (in_array('gallery',explode('/', $path)) ) {
    	// gallery_watermark($path.$_FILES['userfile']['name']);
        resize_image(
          // str_replace(' ', replace, subject)
          $path.$_FILES['userfile']['name'],
          $path.'thumb/'.$_FILES['userfile']['name'],
          260,
          170,
          90);

    }
	return $CI->upload->data();
	/*
	if ($CI->upload->do_upload('userfile'))
    {
    	$CI->upload->data();
    	echo "berhasil";exit();
    }
    else
    {
        print_r($CI->upload->display_errors());exit();
    }*/
}




function is_edit_news($id_news,$user_id_create,$approval_level_news,$type){
 	$CI 			= & get_instance();
	$CI->load->model('newsmodel');

	$grup 			= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	$edit_enable	= "<a href='".$CI->currentController."add/$id_news' title='Edit Data' class='fa fa-pencil-square-o tangan action-form-icon update'></a>";
	$edit_disable	= '<i class="fa fa-pencil-square-o tangan action-form-icon update"></i>';
	// if($id_news == '' || ((id_user() == $user_id_create  && $approval_level_news === 0 ) || $approval_level_news == $CI->newsmodel->approvalLevelGroup) || $grup == 4 ){
	if($id_news == '' || ((id_user() == $user_id_create  && $approval_level_news === 0 ) || $approval_level_news == $CI->newsmodel->approvalLevelGroup) || $grup == 4 || $grup == 1){
		$ret =  $type == 'return' ? 1 : $edit_enable ;
	}
	else{
		$ret =  $type == 'return'  ? 0 : $edit_disable ;
	}
	return $ret;
}
function is_delete_news($id_news,$user_id_create,$approval_level_news,$type){
 	$CI 			= & get_instance();
	$grup 			= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	$delete_enable 	= "<a title='Delete Data' class='fa fa-trash-o action-form-icon tangan hapus delete' id='$id_news' data-url-rm='del'></a>";
	$delete_disable = '<i class="fa fa-trash-o tangan delete action-form-icon"></i>';
	if((($grup == 1 || id_user() == $user_id_create || $approval_level_news !=0) && $approval_level_news === 0) || $grup == 1){
		$ret =  $type == 'return' ? 1 : $delete_enable ;
	}
	else{
		$ret =  $type == 'return'  ? 0 : $delete_disable ;
	}
	return $ret;
}
function is_edit_publish_status(){
    $CI 			= & get_instance();
	$grup 			= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	// if($grup == 4 ){
	if($grup == 4 || $grup == 1){
		$ret =  '';
	}
	else{
		$ret =  'hide';
	}
	return $ret;
}
function enable_edit_editors_choice(){
	$CI 			= & get_instance();
	$grup 			= $CI->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
	if($grup == 2){
		$ret =  'hide';
	}
	else{
		$ret =  '';
	}
	return $ret;
}

function md5plus($string) {
	$CI = & get_instance();
	return '_'.md5('KSI'.$string);
}

function md5field($string,$alias='') {
	$CI    = & get_instance();
	$alias = ($alias) ? "as $alias" : '';
	return "CONCAT('_',md5(CONCAT('KSI',$string))) $alias";
}
// function auth_news($id_user_create,$approval_level_news,$app){
// 	 $id_user 	= id_user();


// }
function indonesia_datetime($timestamp='', $date_format='l, j F Y - H:i:s', $suffix='WIB'){
        if (trim ($timestamp) == '')
        {
                $timestamp = time ();
        }
        elseif (!ctype_digit ($timestamp))
        {
            $timestamp = strtotime ($timestamp);
        }
        # remove S (st,nd,rd,th) there are no such things in indonesia :p
        $date_format = preg_replace ("/S/", "", $date_format);
        $pattern = array (
            '/Mon[^day]/','/Tue[^sday]/','/Wed[^nesday]/','/Thu[^rsday]/',
            '/Fri[^day]/','/Sat[^urday]/','/Sun[^day]/','/Monday/','/Tuesday/',
            '/Wednesday/','/Thursday/','/Friday/','/Saturday/','/Sunday/',
            '/Jan[^uary]/','/Feb[^ruary]/','/Mar[^ch]/','/Apr[^il]/','/May/',
            '/Jun[^e]/','/Jul[^y]/','/Aug[^ust]/','/Sep[^tember]/','/Oct[^ober]/',
            '/Nov[^ember]/','/Dec[^ember]/','/January/','/February/','/March/',
            '/April/','/June/','/July/','/August/','/September/','/October/',
            '/November/','/December/',
        );
        $replace = array ( 'Sen','Sel','Rab','Kam','Jum','Sab','Min',
            'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu',
            'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des',
            'Januari','Februari','Maret','April','Juni','Juli','Agustus','September',
            'Oktober','November','Desember',
        );
        $date = date ($date_format, $timestamp);
        $date = preg_replace ($pattern, $replace, $date);
        $date = "{$date}";
        if($suffix){
            $date .= " {$suffix}";
        }
        return $date;
}