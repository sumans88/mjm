<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reader extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
	}

	function read()
	{

		$_url = $this->uri->segment_array();
		for ($i=0; $i <4 ; $i++) { 
			unset( $_url[$i]);
		}
		$url = site_url().'viewer/web/viewer.html?file=';
		$_url = site_url( $_url);
		$pdf_reader = $url . $_url;
		
		redirect($pdf_reader,'refresh');
	}
}