<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PredefinedMetaDataController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function predefined_vendor_mata_data(){
		$request = $this->input->post();

		$this->common->field_required(array('user_id'),$request);

		$query_results = $this->db->query("SELECT * FROM `sub_category_master` WHERE category_id = (SELECT category_id FROM vendor_master WHERE user_id='".$request['user_id']."') AND status='1'")->result();

		$sub_categories_data = array();
		foreach($query_results as $row){
			$collect = array(
				"sub_category_slug" => $row->sub_category_slug,
				"sub_category_id" => $row->sub_category_id,
				"sub_category_name" => $row->sub_category_name
			);
			$sub_categories_data[] = array_map("strval",$collect);
		}

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
		$response['data']['sub_categories'] = $sub_categories_data;
		$response['data']['cities'] = $cities_data;

		$this->common->response($response);
	}

	
}
