<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MegaMenuController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}
	
	public function get_mega_menu_tag_and_category(){
		$request = $this->input->post();

		$this->common->field_required(array('vendor_type_slug'),$request);

		$query_results = $this->db->query("SELECT cm.`category_slug`,cm.`category_id`,cm.`category_name`,scm.`service_slug`,scm.`service_id`,scm.`service_name`
			FROM category_master AS cm 
			LEFT JOIN `service_master` AS scm ON scm.`category_id`=cm.`category_id` AND scm.`status`='1'
			WHERE cm.vendor_type_id = (SELECT vendor_type_id FROM vendor_type_master WHERE vendor_type_slug='".$request['vendor_type_slug']."' AND `status`='1') AND cm.`status`='1'")->result();

		$arrange_data = array();
		foreach($query_results as $row){
			$arrange_data[$row->category_id]['category_slug'] = $row->category_slug;
			$arrange_data[$row->category_id]['category_id'] = $row->category_id;
			$arrange_data[$row->category_id]['category_name'] = $row->category_name;
			$arrange_data[$row->category_id]['service_data'][$row->service_id]['service_slug'] = $row->service_slug;
			$arrange_data[$row->category_id]['service_data'][$row->service_id]['service_id'] = $row->service_id;
			$arrange_data[$row->category_id]['service_data'][$row->service_id]['service_name'] = $row->service_name;

		}
		
		// p($arrange_data);
		$response_data = array();
		foreach($arrange_data as $row){
			$level_two = array();
			foreach($row['service_data'] as $row1){
				if(!empty($row1['service_id'])){
					$level_two[] = array(
						"service_slug"=> $row1['service_slug'],
						"service_id"=> $row1['service_id'],
						"service_name"=> $row1['service_name'],
					);
				}
			}

			$response_data[] = array(
				"category_slug"=> $row['category_slug'],
				"category_id"=> $row['category_id'],
				"category_name"=> $row['category_name'],
				"service_data"=> $level_two,
			);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	

}
