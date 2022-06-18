<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VendorMasterController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function get_vendor_master_data() {
		$request = $this->input->post();

		$this->common->field_required(array('filter_type', 'filter_sagment_one', 'filter_sagment_two'),$request);

		$query_string = "";
		if($request['filter_type'] == "category"){
			$get_category_row = $this->db->query("SELECT category_id FROM category_master WHERE category_slug = '".$request['filter_sagment_two']."'")->row();
			$query_string .= " AND vm.category_id = '".$get_category_row->category_id."'";
		} else  if ($request['filter_type'] == "tag"){
			$query_string .= " AND FIND_IN_SET('".$request['filter_sagment_two']."',target_categories)";
		}

		$query_results = $this->db->query("SELECT vm.*, cm.city_name FROM vendor_master AS vm
		LEFT JOIN cities_master AS cm ON cm.city_id=vm.city_id AND cm.status='1'
		 WHERE 1=1 $query_string  AND vm.status='1'")->result();

		
		$tag_data = array();
		$tag_query_results = "";
			if($request['filter_type'] == "category"){
				$tag_query_results = $this->db->query("SELECT service_id, service_slug, service_name FROM service_master WHERE category_id=(SELECT category_id FROM category_master where category_slug='".$request['filter_sagment_two']."') AND status='1'")->result();
			} else if($request['filter_type'] == "tag"){
				$tag_query_results = $this->db->query("SELECT service_id, service_slug, service_name FROM service_master WHERE category_id=(SELECT category_id FROM category_master where category_slug='".$request['filter_sagment_one']."') AND status='1'")->result();
			}
			if(!empty($tag_query_results)){
				foreach($tag_query_results as $row){
					$collect = array(
						"service_id" => $row->service_id,
						"service_slug" => $row->service_slug,
						"service_name" => $row->service_name,
					);
					$tag_data[] = array_map("strval",$collect);
				}
				$response['data']['tag_data'] = $tag_data;
			} else {
				$response['data']['tag_data'] = array();
			}
			

		$vendor_data = array();
		if(!empty($query_results)){
			foreach($query_results as $row){
				$collect = array(
					"user_id" => $row->user_id,
					"vendor_name" => $row->vendor_name,
					"business_name" => $row->business_name,
					"mobile" => $row->mobile,
					"email" => $row->email,
					"city_name" => $row->city_name,
					"profile_picture" => STORAGE_CONTENT_URL.$row->profile_picture
				);
				$vendor_data[] = array_map("strval",$collect);
			}
			$response['data']['vendor_data'] = $vendor_data;
			
		} else {
			$response['data']['vendor_data'] = array();
		}
		
		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		
		$this->common->response($response);
	}

	public function get_vendor_detail_by_user_id() {
		$request = $this->input->post();

		$this->common->field_required(array('user_id'),$request);

		$user_details = $this->db->query("SELECT vm.*, cm.`city_name` FROM vendor_master AS vm LEFT JOIN cities_master AS cm ON cm.`city_id`=vm.`city_id` WHERE user_id='".$request['user_id']."'")->row();
		$response_data = array_map("strval",array(
			"user_id" => $user_details->user_id,
			"vendor_name" => $user_details->vendor_name,
			"business_name" => $user_details->business_name,
			"mobile" => $user_details->mobile,
			"email" => $user_details->email,
			"city_id" => $user_details->city_id,
			"city_name" => $user_details->city_name,
			"target_categories" => $user_details->target_categories,
			"profile_picture" => STORAGE_CONTENT_URL.$user_details->profile_picture
		));
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;
		$this->common->response($response);
	}

	public function get_tag_by_user_id(){
		$request = $this->input->post();
		$this->common->field_required(array('user_id'),$request);
		$query_results = $this->db->query("SELECT * FROM `service_master` WHERE category_id = (SELECT category_id FROM vendor_master WHERE user_id='".$request['user_id']."') AND status='1'")->result();

		$sub_categories_data = array();
		foreach($query_results as $row){
			$collect = array(
				"service_slug" => $row->service_slug,
				"service_id" => $row->service_id,
				"service_name" => $row->service_name,
				"picture_thumb" => $row->picture_thumb
			);
			$sub_categories_data[] = array_map("strval",$collect);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $sub_categories_data;

		$this->common->response($response);
	}

	public function get_cities_by_user_id(){
		$request = $this->input->post();
		$this->common->field_required(array('user_id'),$request);
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
		$response['data'] = $cities_data;

		$this->common->response($response);
	}
	
	public function account_get_personal_information_section(){
		$request = $this->input->post();
		$this->common->field_required(array('user_id'),$request);
		$verndor_detail = $this->db->query("SELECT vm.*, cm.`city_name` FROM vendor_master AS vm LEFT JOIN cities_master AS cm ON cm.`city_id`=vm.`city_id` WHERE vm.user_id='".$request['user_id']."'")->row();
		$category_result = $this->db->query("SELECT * FROM service_master WHERE `service_slug` IN ('".str_replace(',','\',\'',$verndor_detail->target_categories)."')")->result();

		$vendor_data = array(
			"vendor_name" => $verndor_detail->vendor_name,
			"mobile" => $verndor_detail->mobile,
			"city_id" => $verndor_detail->city_id,
			"city_name" => $verndor_detail->city_name,
		);

		$tag_data = array();
		foreach($category_result as $row){
			$tag_data[] = array(
				"service_slug" => $row->service_slug,
				"service_name" => $row->service_name
			);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data']['vendor_data'] = $vendor_data;
		$response['data']['tag_data'] = $tag_data;

		$this->common->response($response);
	}

	public function account_set_personal_information_section(){
		$request = $this->input->post();
		$this->common->field_required(array('user_id','mobile_number','city_id','categories_collection'),$request);

		$category_collection_array = json_decode($request['categories_collection']);
		$collect_ids = array();
		foreach($category_collection_array as $row){
			$collect_ids[] = $row->id;
		}

		$target_categories = implode(",",$collect_ids);
		$updateData = array(
			"mobile"=> $request['mobile_number'],
			"city_id"=> $request['city_id'],
			"target_categories"=> $target_categories,
		);
		$this->db->where(array("user_id"=>$request['user_id']));
		$this->db->update('vendor_master',$updateData);

		$updateData = array(
			"mobile"=> $request['mobile_number'],
		);
		$this->db->where(array("user_id"=>$request['user_id']));
		$this->db->update('login_master',$updateData);

		$response['status'] = 1;
		$response['message'] = DATA_SAVED_SUCCESSFULLY;
		
		$this->common->response($response);
	}

	public function upload_profile_picture(){
		$request = $this->input->post();
		$this->common->field_required(array('user_id','profile_picture_path'),$request);

		$vendor_result = $this->db->query("SELECT `profile_picture` FROM vendor_master WHERE `user_id`='".$request['user_id']."'")->row();

		if(!empty($vendor_result->profile_picture)){
			unlink(STORAGE_CONTENT_PATH.$vendor_result->profile_picture);
		}

		$updateData = array(
			"profile_picture" => $request['profile_picture_path'],
		);
		$this->db->where(array("user_id"=>$request['user_id']));
		$this->db->update('vendor_master',$updateData);

		$response['status'] = 1;
		$response['message'] = DATA_SAVED_SUCCESSFULLY;
		$this->common->response($response);
	}

	
}
