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

		$query_results = $this->db->query("SELECT vm.user_id, vm.vendor_name, vm.business_name, vm.mobile, cm.city_name, vm.profile_picture  FROM vendor_master AS vm
		LEFT JOIN cities_master AS cm ON cm.city_id=vm.city_id AND cm.status='1'
		WHERE FIND_IN_SET('".$request['filter_category']."',vm.target_categories) AND cm.city_slug='".$request['filter_city']."' AND vm.status='1'")->result();

		$cities_data = array();
		foreach($query_results as $row){
			$collect = array(
				"user_id" => $row->user_id,
				"vendor_name" => $row->vendor_name,
				"business_name" => $row->business_name,
				"mobile" => $row->mobile,
				"profile_picture" => $row->profile_picture,
			);
			$cities_data[] = array_map("strval",$collect);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $cities_data;

		$this->common->response($response);
	}

	
}
