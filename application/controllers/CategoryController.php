<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function get_list_all_vendor_type_with_all_categories() {
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

	public function get_list_all_vendor_type_with_all_categories_all_sub_category() {
		$request = $this->input->post();

		$query_results = $this->db->query("SELECT vtm.vendor_type_id,vtm.`vendor_type_name`,cm.`category_id`,cm.`category_name`,scm.`sub_category_id`,scm.`sub_category_name`
		FROM vendor_type_master AS vtm 
		LEFT JOIN category_master AS cm ON cm.`vendor_type_id`=vtm.`vendor_type_id` AND cm.`status`='1'
		LEFT JOIN `sub_category_master` AS scm ON scm.`category_id`=cm.`category_id` AND scm.`status`='1'
		WHERE vtm.`status`='1'")->result();
	// p($query_results);

		$arrange_data = array();
		foreach($query_results as $row){
			$arrange_data[$row->vendor_type_id]['vendor_type_id'] = $row->vendor_type_id;
			$arrange_data[$row->vendor_type_id]['vendor_type_name'] = $row->vendor_type_name;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_id'] = $row->category_id;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_name'] = $row->category_name;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_id'] = $row->sub_category_id;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_name'] = $row->sub_category_name;

		}
		
		// p($arrange_data);
		$response_data = array();
		foreach($arrange_data as $row){
			
			$level_one = array();
			foreach($row['categories_data'] as $row1){
				$level_two = array();
				foreach($row1['sub_category_data'] as $row2){
					if(!empty($row2['sub_category_id'])){
						$level_two[] = array(
							"sub_category_id"=> $row2['sub_category_id'],
							"sub_category_name"=> $row2['sub_category_name'],
						);
					}
				}
				
				$level_one[] = array(
					"category_id"=> $row1['category_id'],
					"category_name"=> $row1['category_name'],
					"sub_category_data"=> $level_two,
				);
			}

			$response_data[] = array(
				"vendor_type_id"=>$row['vendor_type_id'],
				"vendor_type_name"=>$row['vendor_type_name'],
				"category_data"=>$level_one
			);

		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}
}
