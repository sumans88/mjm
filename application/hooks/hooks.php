<?php
/**
 * @file
 */

 /**
  * fungsi utk mengecek autentifikasi 
  */
function auth(){
	$CI		= & get_instance();
	$dir						= $CI->router->directory;
	$class						= $CI->router->fetch_class();
	$base_url					= str_replace('http://'.$_SERVER['HTTP_HOST'],'',base_url());
	$CI->baseUrl				= str_replace('https://'.$_SERVER['HTTP_HOST'],'',$base_url);//jika https
	$CI->currentController 		= $CI->baseUrl.$dir.$class.'/';
	if($_SERVER['REMOTE_ADDR'] != '127.0.0.1' and IS_HTTPS){
		$class = $CI->router->fetch_class();
		$exclude =  array('client');  // add more controller name to exclude ssl.
		if(!in_array($class,$exclude)) {
		  // redirecting to ssl.
		  $CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
		  if ($_SERVER['SERVER_PORT'] != 443) redirect($CI->uri->uri_string());
		} 
		else {
		  // redirecting with no ssl.
		  $CI->config->config['base_url'] = str_replace('https://', 'http://', $CI->config->config['base_url']);
		  if ($_SERVER['SERVER_PORT'] == 443) redirect($CI->uri->uri_string());
		}
	}
	menus();


}
 /**
  * 
  */
function view(){
	$CI						= & get_instance();
	if(isset($CI->layout) && $CI->layout == 'none'){
		return;
	}
	$dir						= $CI->router->directory;
	$class						= $CI->router->fetch_class();
	$method						= $CI->router->fetch_method();
	$method						= ($method=='index') ? $class : $method;
	$data						= (isset($CI->data)) ? $CI->data : array();
	$base_url					= str_replace('http://'.$_SERVER['HTTP_HOST'],'',base_url());
	$data['base_url']			= str_replace('https://'.$_SERVER['HTTP_HOST'],'',$base_url);//jika https
	$data['this_controller'] 	= $data['base_url'].$dir.$class.'/';
	$data['content']			= $CI->load->view($dir.$class.'/'.$method,$data,true);
	$defaultLayout				= ($dir) ? 'admin' : 'front';
	$layout 					= (isset($CI->layout)) ? $CI->layout : $defaultLayout;
	$CI->load->view('/layout/'.$layout,$data);
}
