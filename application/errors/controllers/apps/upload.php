<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Upload extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('uploadModel');
	}

	function index(){
		$post = purify($this->input->post());
		$file = $_FILES;

		if($file){
			$file = $_FILES['dat'];
			$fname = $file['name'];
			$maxFileSize = MAX_UPLOAD_SIZE_CHEETAH / 1000000;
			if(!is_writable(BACKUP_NEWSLETTER_DIR)){
				$ret['error'] = 1;
				$ret['message'] = "Directory is readonly";
			} else if($file['size'] >= MAX_UPLOAD_SIZE_CHEETAH){
				$ret['error'] = 1;
				$ret['message'] = "Max File size is ".$maxFileSize." MB";
			}else if($fname) {
				// if(!file_exists($folder)){
				// 	mkdir($folder);
				// }
				$upload_dir = BACKUP_NEWSLETTER_DIR;
				$allowed_types = "zip|gz|rar";
				$upload = upload_file('dat','file_compress',$allowed_types,'',$upload_dir);
				$file_name = $upload_dir.'file_compress/'.$upload['file_name'];
				$insert = array('file_name'=>$upload['file_name'],'file_size'=>$upload['file_size'],'file_type'=>$upload['file_type']);


				if($upload['file_ext'] == '.gz') {
					// Raising this value may increase performance

					$buffer_size = 4096; // read 4kb at a timex
					$fname_uncompress = str_replace('dat.gz', '.dat', $file_name);
					$folder_uncompress = str_replace('file_compress/', 'file_uncompress/', $fname_uncompress);

					// Open our files (in binary mode)
					$files = gzopen($file_name, 'rb');
					$out_file = fopen($folder_uncompress, 'wb');

					// Keep repeating until the end of the input file
					while(!gzeof($files)) {
					    // Read buffer-size bytes
					    // Both fwrite and gzread and binary-safe
					    fwrite($out_file, gzread($files, $buffer_size));

					}

					// Files are done, close files
					fclose($out_file);
					gzclose($files);
					$this->insertToDB($insert);
				} else if ($upload['file_ext'] == '.zip') {
					$zip = new ZipArchive;
					$res = $zip->open($file_name);
					if ($res === TRUE) {
						// extract it to the path we determined above
						$zip->extractTo($upload_dir.'file_uncompress/');
						$zip->close();
						$ret['extract'] = 'success';
					} else {
						$ret['extract'] = 'failed';
					}
					$this->insertToDB($insert);
				} else {
					$ret['msg'] = 'File type is not allowed';
				}
				$ret['message'] = 'success';
			}
			echo json_encode($ret);
			exit;
		}
		render('apps/upload/index',$data,'apps');
	}

	function records(){
		$data = $this->uploadModel->records();
		foreach($data['data'] as $key => $value) {
			$data['data'][$key]['create_date'] = iso_date($value['create_date']);
			if($value['process_date'] == NULL) {
				$data['data'][$key]['hidden'] = 'show';
			} else {
				$data['data'][$key]['hidden'] = 'hide';
			}
		}
		render('apps/upload/records',$data,'blank');
	}

	function del(){
		auth_delete();
		$id = $this->input->post('iddel');
		$this->uploadModel->delete($id);
		detail_log();
		insert_log("Delete Upload");
	}

	function insertToDB($data){
		return $this->uploadModel->insert($data);
	}
}