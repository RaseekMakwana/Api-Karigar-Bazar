<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VendorMasterController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function get_vendor_by_sub_category_id() {
		$request = $this->input->post();

		$this->common->field_required(array('sub_category_id'),$request);

		$query_results = $this->db->query("SELECT * FROM vendor_master WHERE sub_category_id='".$request['sub_category_id']."' AND STATUS='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"vendor_id" => $row->vendor_id,
				"vendor_name" => $row->vendor_name,
				"business_name" => $row->business_name,
				"mobile" => $row->mobile,
				"email" => $row->email,
				"profile_picture" => $row->profile_picture
			);
			$response_data[] = array_map("strval",$collect);
		}
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}
}