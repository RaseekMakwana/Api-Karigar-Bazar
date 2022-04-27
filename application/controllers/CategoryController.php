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

		$query_results = $this->db->query("SELECT vendor_type_id,vendor_type_name FROM vendor_type_master WHERE status='1'")->result();
	// p($query_results);

		$response_data = array();
		$index1=0;
		foreach($query_results as $row){
			$response_data[$index1]['vendor_type_id'] = $row->vendor_type_id;
			$response_data[$index1]['vendor_type_name'] = $row->vendor_type_name;

			$index2=0;
			$query_results1 = $this->db->query("SELECT category_id,category_name FROM category_master where vendor_type_id='".$row->vendor_type_id."' AND status='1'")->result();
			foreach($query_results1 as $row1){
				$response_data[$index1]['category_data'][$index2]['category_id'] = $row1->category_id;
				$response_data[$index1]['category_data'][$index2]['category_name'] = $row1->category_name;

					$index3=0;
					$query_results2 = $this->db->query("SELECT sub_category_id,sub_category_name FROM sub_category_master where category_id='".$row1->category_id."' AND status='1'")->result();
					foreach($query_results2 as $row2){
						$response_data[$index1]['category_data'][$index2]['sub_category_data'][$index3]['sub_category_id'] = $row2->sub_category_id;
						$response_data[$index1]['category_data'][$index2]['sub_category_data'][$index3]['sub_category_name'] = $row2->sub_category_name;
						$index3++;
					}
					
				$index2++;
			}
			// $response_data[$row->vendor_type_id]['vendor_type_name'] = $row->vendor_type_name;
			// $response_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_id'] = $row->category_id;
			// $response_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_name'] = $row->category_name;
			// $response_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_id'] = $row->sub_category_id;
			// $response_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_name'] = $row->sub_category_name;
			$index1++;
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}
}
