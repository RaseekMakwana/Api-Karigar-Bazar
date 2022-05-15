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
				"full_name" => $request['contact_person_name'],
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

	public function get_vendor_details_by_vendor_id() {
		$request = $this->input->post();

		$this->common->field_required(array('user_id'),$request);

		$query_results = $this->db->query("SELECT * FROM vendor_master WHERE user_id ='".$request['user_id']."' AND STATUS='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"user_id" => $row->user_id,
				"vendor_name" => $row->vendor_name,
				"business_name" => $row->business_name,
				"mobile" => $row->mobile,
				"email" => $row->email,
				"profile_picture" => $row->profile_picture
			);
			$response_data = array_map("strval",$collect);
		}
		

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

	
}
