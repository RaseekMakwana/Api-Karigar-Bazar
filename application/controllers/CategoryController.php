<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}
	public function get_list_all_vendor_type_with_all_categories()
	{
		$request = $this->input->post();

		$query_results = $this->db->query("SELECT cm.category_id, cm.category_name, vtm.vendor_type_name, cm.picture_thumb 
											FROM category_master AS cm
											LEFT JOIN `vendor_type_master` AS vtm ON vtm.`vendor_type_id`=cm.`vendor_type_id`")->result();


		$arrangeData = array();
		foreach($query_results as $row){
			$arrangeData[$row->vendor_type_name][] = $row;
		}

		$response_data = array();
		$i=0;
		foreach($arrangeData as $key => $row){
			$response_data[$i]["vendor_type_name"] = $key;
			$response_data[$i]['categories'] = $row;
			$i++;
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	public function get_list_all_vendor_type_with_all_categories_all_sub_category()
	{
		$request = $this->input->post();

		$query_results = $this->db->query("SELECT vtm.vendor_type_id,vtm.`vendor_type_name`,cm.`category_id`,cm.`category_name`,scm.`sub_category_id`,scm.`sub_category_name`
		FROM vendor_type_master AS vtm 
		LEFT JOIN category_master AS cm ON cm.`vendor_type_id`=vtm.`vendor_type_id` AND cm.`status`='1'
		LEFT JOIN `sub_category_master` AS scm ON scm.`category_id`=cm.`category_id` AND scm.`status`='1'
		WHERE vtm.`status`='1'")->result();
	// p($query_results);

		$response_data = array();
		foreach($query_results as $row){
			$response_data[$row->vendor_type_id]['vendor_type_id'] = $row->vendor_type_id;
			$response_data[$row->vendor_type_id]['vendor_type_name'] = $row->vendor_type_name;
			$response_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_id'] = $row->category_id;
			$response_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_name'] = $row->category_name;
			$response_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_id'] = $row->sub_category_id;
			$response_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_name'] = $row->sub_category_name;
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}
}
