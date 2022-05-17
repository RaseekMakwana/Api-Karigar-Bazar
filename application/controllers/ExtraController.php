<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExtraController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function post_your_requirement(){
		$request = $this->input->post();
		$this->common->field_required(array('name','mobile','email','message'),$request);


		$insertData = array(
			"name" => $request['name'],
			"mobile" => $request['mobile'],
			"email" => $request['email'],
			"message" => $request['message'],
		);
		$this->db->insert('post_your_requirement',$insertData);
		$response['status'] = 1;
		$response['message'] = DATA_SAVED_SUCCESSFULLY;
		$this->common->response($response);
	}

	public function upload_document()
	{
		$filename = time().$_FILES["userfile"]['name'];
		$request = $this->input->post();
		$storage_folder = STORAGE_CONTENT_PATH.$request['location'].'/';
		p($storage_folder);
		$config = array(
			'upload_path' => $storage_folder,
			'file_name' => $filename,
			'allowed_types' => 'jpg|png',
			'max_size' => 100,
			'max_width' => 1024,
			'max_height' => 768,
		);

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('userfile'))
		{
			$error = array('error' => $this->upload->display_errors());
			p($error);
		}
		else
		{
			echo $storage_folder.$filename;
		}
	}


}
