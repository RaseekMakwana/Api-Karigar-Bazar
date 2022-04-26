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
											LEFT JOIN `vendor_type_master` AS vtm ON vtm.`vendor_type_id`=cm.`vendor_type_id`
											WHERE parent_category_id='0'")->result();


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
}
