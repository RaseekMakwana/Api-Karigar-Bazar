<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CityMasterController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function get_cities_by_state_id(){
		$request = $this->input->post();
		$this->common->field_required(array('state_id'),$request);
		$query_results = $this->db->query("SELECT city_id,city_slug,city_name FROM `cities_master` WHERE state_id='".$request['state_id']."' AND STATUS='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"city_id" => $row->city_id,
				"city_slug" => $row->city_slug,
				"city_name" => $row->city_name,
			);
			$response_data[] = array_map("strval",$collect);
		}
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

}
