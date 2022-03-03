<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_Activity extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('memberActivityModel');
	}
	function index(){
		$data['list_activity'] = selectlist2(array('table'=>'t_aegon_log_category','title'=>'All Activity','name'=>'description'));

		render('apps/member_activity/index',$data,'apps');
	}
	function records(){
		$data = $this->memberActivityModel->records();
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] = iso_date_custom_format($value['create_date'], 'd/m/Y');
			$data['data'][$key]['create_time'] = iso_date_custom_format($value['create_date'], 'H:i:s');
			$data['data'][$key]['process_date'] = iso_date_time($value['process_date']);
			$ismobile = substr($value['ismobile'],0,150);
			if(strlen($value['ismobile']) > 150){
				$ismobile .= ' ...';
			}
			$data['data'][$key]['ismobile'] = $ismobile;
		}
		render('apps/member_activity/records',$data,'blank');
	}

	function export_to_excel(){
		$post = $this->input->post();

		$alias['search_activity'] = 'b.description';
		$alias['search_activity_id'] = 'b.id';
		$alias['search_datestart'] = 'a.datestart';
		$alias['search_dateend'] = 'a.dateend';

		$post['search_datestart'] = $post['datestart'];
		$post['search_dateend'] = $post['dateend'];

		where_grid($post, $alias);
		$data['data'] = $this->memberActivityModel->export_to_excel();
		$i=1;
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] = iso_date_custom_format($value['create_date'], 'd/m/Y');
			$data['data'][$key]['create_time'] = iso_date_custom_format($value['create_date'], 'H:i:s');
			$data['data'][$key]['process_date'] = iso_date_time($value['process_date']);
			$ismobile = substr($value['ismobile'],0,150);
			if(strlen($value['ismobile']) > 150){
				$ismobile .= ' ...';
			}
			$data['data'][$key]['ismobile'] = $ismobile;
			$data['data'][$key]['nomor'] = $i++;
		}
		render('apps/member_activity/export_to_excel',$data,'blank');
		export_to('Member Activity.xls');
	}
}

/* End of file contactus.php */
/* Location: ./application/controllers/apps/contactus.php */