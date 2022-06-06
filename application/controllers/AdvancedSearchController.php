<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdvancedSearchController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function Search(){
		$request = $this->input->post();
		$this->common->field_required(array('filter_type', 'filter_city', 'filter_category'),$request);

		$city_lat_long = $this->db->query("SELECT latitude,longitude FROM cities_master WHERE city_slug = '".$request['filter_city']."'")->row();
		
		$calculate_range_cities = "(SELECT city_id FROM (SELECT city_id, latitude, longitude, (7000*ACOS(COS(RADIANS($city_lat_long->latitude)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS($city_lat_long->longitude)) + SIN(RADIANS($city_lat_long->latitude))* SIN(RADIANS(latitude)))) AS distance 
		FROM cities_master HAVING distance < 50) a)";

		$query_string = "";
		if($request['filter_type'] == "c"){
			$get_category_row = $this->db->query("SELECT category_id FROM category_master WHERE category_slug = '".$request['filter_category']."'")->row();
			$query_string .= " AND vm.category_id = '".$get_category_row->category_id."'";
		} else if($request['filter_type'] == "t"){
			$query_string .= " AND FIND_IN_SET('".$request['filter_category']."',vm.target_categories)";
		}

		$query_results = $this->db->query("SELECT vm.user_id, vm.vendor_name, vm.business_name, vm.mobile, cm.city_name, vm.profile_picture  FROM vendor_master AS vm
		LEFT JOIN cities_master AS cm ON cm.city_id=vm.city_id AND cm.status='1'
		WHERE 1=1 $query_string  AND vm.city_id IN $calculate_range_cities AND vm.status='1'")->result();

		$vendor_data = array();
		if(!empty($query_results)){
			
			$tag_data = array();
			$tag_result = "";
			if($request['filter_type'] == "c"){
				$tag_result = $this->db->query("SELECT tag_id, tag_slug, tag_name FROM tags_master WHERE category_id='".$get_category_row->category_id."' AND status='1'")->result();
			} else if($request['filter_type'] == "t"){
				$tag_result = $this->db->query("SELECT tag_id, tag_slug, tag_name FROM tags_master WHERE category_id IN (SELECT category_id FROM tags_master WHERE tag_slug='".$request['filter_category']."') AND STATUS='1'")->result();
			}

			foreach($tag_result as $row){
				$collect = array(
					"tag_id" => $row->tag_id,
					"tag_slug" => $row->tag_slug,
					"tag_name" => $row->tag_name
				);
				$tag_data[] = array_map("strval",$collect);
			}

			foreach($query_results as $row){
				$collect = array(
					"user_id" => $row->user_id,
					"vendor_name" => $row->vendor_name,
					"business_name" => $row->business_name,
					"mobile" => $row->mobile,
					"city_name" => $row->city_name,
					"profile_picture" => STORAGE_CONTENT_URL.$row->profile_picture,
				);
				$vendor_data[] = array_map("strval",$collect);
			}
	
			$response['status'] = 1;
			$response['message'] = DATA_GET_SUCCESSFULLY;
			$response['data']['vendor_data'] = $vendor_data;
			$response['data']['tag_data'] = $tag_data;
		} else {
			$response['status'] = 0;
			$response['message'] = DATA_NOT_FOUND;
		}
		

		$this->common->response($response);
	}

	
}
