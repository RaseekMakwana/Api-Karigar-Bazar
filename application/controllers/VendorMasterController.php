<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VendorMasterController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function become_a_vendor(){
		$request = $this->input->post();
		$this->common->field_required(array('business_name','contact_person_name','mobile_no','email_address','password','vendor_type_id','category_id','state_id','city_id'),$request);

		$check_user_exist = $this->db->query("SELECT count(*) as number_of_records FROM login_master WHERE mobile='".$request['mobile_no']."' AND status='1'")->row();

		if(empty($check_user_exist->number_of_records)){
			$user_id = time().uniqid();
			$insertData = array(
				"user_id" => $user_id,
				"vendor_name" => $request['contact_person_name'],
				"business_name" => $request['business_name'],
				"mobile" => $request['mobile_no'],
				"email" => $request['email_address'],
				"vendor_type_id" => $request['vendor_type_id'],
				"category_id" => $request['category_id'],
				"state_id" => $request['state_id'],
				"city_id" => $request['city_id']
			);
			$this->db->insert('vendor_master',$insertData);

			$insertData = array(
				"user_id" => $user_id,
				"mobile" => $request['mobile_no'],
				"password" => $request['password'],
			);
			$this->db->insert('login_master',$insertData);

			$response['status'] = 1;
			$response['message'] = DATA_SAVED_SUCCESSFULLY;
		} else {
			$response['status'] = 0;
			$response['message'] = ERROR_TAG_FOUND;
			$response['data'] = "mobile_no_already_exist";
		}
		
		$this->common->response($response);
	}

	public function get_vendor_details_by_sub_category_slug() {
		$request = $this->input->post();

		$this->common->field_required(array('sub_category_slug'),$request);

		$query_results = $this->db->query("SELECT * FROM vendor_master WHERE sub_category_id in (SELECT sub_category_id FROM sub_category_master WHERE sub_category_slug='".$request['sub_category_slug']."') AND STATUS='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"vendor_id" => $row->vendor_id,
				"vendor_slug" => $row->vendor_slug,
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
			"profile_picture" => $user_details->profile_picture
		));
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;
		$this->common->response($response);
	}

	public function get_sub_category_by_user_id(){
		$request = $this->input->post();
		$this->common->field_required(array('user_id'),$request);
		$query_results = $this->db->query("SELECT * FROM `sub_category_master` WHERE category_id = (SELECT category_id FROM vendor_master WHERE user_id='".$request['user_id']."') AND status='1'")->result();

		$sub_categories_data = array();
		foreach($query_results as $row){
			$collect = array(
				"sub_category_slug" => $row->sub_category_slug,
				"sub_category_id" => $row->sub_category_id,
				"sub_category_name" => $row->sub_category_name,
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
		$category_result = $this->db->query("SELECT * FROM sub_category_master WHERE `sub_category_slug` IN ('".str_replace(',','\',\'',$verndor_detail->target_categories)."')")->result();

		$vendor_data = array(
			"vendor_name" => $verndor_detail->vendor_name,
			"mobile" => $verndor_detail->mobile,
			"city_id" => $verndor_detail->city_id,
			"city_name" => $verndor_detail->city_name,
		);

		$sub_category_data = array();
		foreach($category_result as $row){
			$sub_category_data[] = array(
				"sub_category_slug" => $row->sub_category_slug,
				"sub_category_name" => $row->sub_category_name
			);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data']['vendor_data'] = $vendor_data;
		$response['data']['sub_category_data'] = $sub_category_data;

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

	
}
