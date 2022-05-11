<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StateMasterController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function get_states(){
		$query_results = $this->db->query("SELECT state_id, state_name FROM states_master WHERE STATUS='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"state_id" => $row->state_id,
				"state_name" => $row->state_name,
			);
			$response_data[] = array_map("strval",$collect);
		}
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

}
