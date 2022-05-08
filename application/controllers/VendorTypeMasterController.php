<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VendorTypeMasterController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function get_vendor_type(){
		$query_results = $this->db->query("SELECT vendor_type_id,vendor_type_slug,vendor_type_name FROM `vendor_type_master`")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"vendor_type_id" => $row->vendor_type_id,
				"vendor_type_slug" => $row->vendor_type_slug,
				"vendor_type_name" => $row->vendor_type_name
			);
			$response_data[] = array_map("strval",$collect);
		}
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

}
