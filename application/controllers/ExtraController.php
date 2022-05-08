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

}
