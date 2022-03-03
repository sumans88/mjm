<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."helpers/json/JSON.php");
/**
 * @file
 */

/**
 * Create Icon View
 * @return string html view image icon
 */
function view_grid(){
	return "<img title='View Detail' class='edit' src='".base_url()."assets/images/view.gif'>";
}
/**
 * Create Icon Delete pada grid yg memanggil fungsi hapus javascript (onclick)
 * @param $id id data yg akan di hapus
 * @return string html delete image icon
 */
function delete_grid($id){
	$cek = auth_access('d');
	return ($cek == 1) ? "<i title='Delete' onclick='if(confirm(\"Delete?\")) hapus($id)' class='fa fa-trash-o tangan'></i>" : '';
}
function delete_row_grid($id,$url,$idGrid='gridData'){
	$cek = auth_access('d');
	return ($cek == 1) ? "<i title='Delete' onclick=\"delete_row($id,'".base_url()."$url','$idGrid')\" class='fa fa-trash-o tangan'></i>" : '';
}
/**
 * Create Icon Edit pada grid yg memanggil fungsi edit javascript (onclick)
 * @param $id id data yg akan diedit
 * @return string html edit image icon
 */
function edit_grid($id,$fungsi='edit'){
	$cek = auth_access('u');
	return ($cek == 1) ? "<i title='Edit' onclick='$fungsi($id)' class='fa fa-pencil-square-o tangan'></i>":'';
	
}
function update_grid($id,$function=''){
	$cek = auth_access('u');
	$func = ($function =='') ? 'edit' : $function;
	return ($cek == 1) ? "<a href='javascript:$func($id)'><img title='Edit' class='edit' src='".base_url()."assets/images/edit.png'></a>" : '';
}
/**
 * Generate data untuk grid
 * @param $req array data $_REQUEST
 * @param $mod string nama class model yg dipanggil
 * @param $func string nama fungsi yg terdapat dalam model $model  yg dipanggil
 * @param $alias (optional) array tambahan untuk query (utk pencarian & sort). contoh : $alias['nama'] = 'a.nama' => jadi hasil querynya and a.nama = '%$key%'
 * @return json data utk jqgrid
 */
function grid($req,$mod,$func,$alias=array()){
	$grid = get('grid');
	if($req['q'] != '' || $grid != ''){
		$CI 						= & get_instance();
		$CI->load->model($mod);
		$cond						= where($req,$alias);
		$count 					= $CI->$mod->$func($cond['where'],1);
		$set						= set($count,$cond['limit'],$cond['page']);
		$row 				 		= $CI->$mod->$func($cond['where'],'',$cond['sidx'],$cond['sord'],$set['start'],$set['end']);
		json($cond['page'],$set['total_pages'],$count,$row);
		exit;
	}
}
/**
 * Fungsi untuk menghandle pencarian
 *
 */
function where($req,$alias = ''){
	unset($req['filters']);
	$sField				= $req['searchField'];
	$sOper				= $req['searchOper'];
	$hal					= $req['page'];
	$baris				= $req['rows'];
	$show					= $req['rows'];
	$shortKey			= $req['sidx'];
	$shortDirection	= $req['sord'];
	$isSearch			= $req['_search'];
	$page					= (int)($hal=='')?1:$hal;
	$limit				= (int)($baris=='')?30:$baris;
	$sidx					= ($shortKey=='')?'id':$shortKey;
	$sord					= $shortDirection;
	$sValue				= ($isSearch=='false')?'':$req['searchString'];
	if ($sField != ''){
		//$sField = "lower($sField)";
		if ($sOper == "eq") $cond = $sField." = '".$sValue."'";
		elseif ($sOper == "ne") $cond = $sField." <> '".$sValue."'";
		elseif ($sOper == "lt") $cond = $sField." < '".$sValue."'";
		elseif ($sOper == "le") $cond = $sField." <= '".$sValue."'";
		elseif ($sOper == "gt") $cond = $sField." > '".$sValue."'";
		elseif ($sOper == "ge") $cond = $sField." >= '".$sValue."'";
		elseif ($sOper == "bw") $cond = $sField." LIKE '".$sValue."%'";
		elseif ($sOper == "ew") $cond = $sField." LIKE '%".$sValue."'";
		elseif ($sOper == "cn") $cond = $sField." LIKE '%".$sValue."%'";
	}
	$data['where'] 		= ($cond != '') ? " and $cond" : '';
	$data['limit']		= $limit;
	$data['page']		= $page;
	$data['sord']		= $sord;
	$data['sidx']		= $sidx;
	unset($req['q']);
	foreach ($req as $key =>$val){
	  $key = ($alias[$key]!='') ? $alias[$key] : $key;
	  if($key != 'PHPSESSID'&&$key!='_search'&&$key!='nd'&&$key!='rows'&&$key!='page'&&$key!='sidx'&&$key!='sord'){
		  $data['where'] .= " and $key like  '%$val%'";
	  }
	}
	return $data;
 }
/**
 *Fungsi untuk convert array data ke format json
 */
function json($page,$total_pages,$record,$data){
  if(count($data) > 0){
	  foreach ($data as $v){
		  $n = 0;
		  foreach ($v as $k => $val){
			  if($k != 'NO'){
				  $arr[$n] = $v[$k];
				  ++$n;
			  }
		  }
		  $elements[] = array('id'=>$v['id'],'cell'=>$arr);
	  }
	 }
	 echo JSON::encode(
							 array(
								 'page'		=>	$page,
								 'total'		=>	$total_pages,
								 'records'	=>	$record,
								 'rows'		=>	$elements
							 )
		 );
}
function set($count,$limit,$page){
  $total_pages 	 		= ( $count > 0 ) ? ceil($count/$limit) : 1;
  $start					= $limit * $page  - $limit + 1;
  $end					= $start + $limit - 1;
  $data['end'] 			= $end;
  $data['start']			= $start;
  $data['total_pages']	= $total_pages;
  return $data;
}
