<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdvancedSearchController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function Search(){
		$request = $this->input->post();
		$this->common->field_required(array('filter_city','filter_category'),$request);
		$query_results = $this->db->query("SELECT * FROM `cities_master` WHERE state_id = (select state_id from vendor_master where user_id='".$request['user_id']."')")->result();

		$cities_data = array();
		foreach($query_results as $row){
			$collect = array(
				"city_id" => $row->city_id,
				"city_name" => $row->city_name,
			);
			$cities_data[] = array_map("strval",$collect);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $cities_data;

		$this->common->response($response);
	}

	
}
