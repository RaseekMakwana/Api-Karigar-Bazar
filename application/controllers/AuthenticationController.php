<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthenticationController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function login_attempt(){
		$request = $this->input->post();
		$this->common->field_required(array('mobile_number','password'),$request);

		$mobile_number = base64_decode($request['mobile_number']);
		$password = base64_decode($request['password']);

		$check_user_exist = $this->db->query("SELECT count(*) as number_of_records FROM login_master WHERE mobile='".$mobile_number."' AND password='".$password."' AND status='1'")->row();
		if(!empty($check_user_exist->number_of_records)){
			$user_details = $this->db->query("SELECT * FROM vendor_master WHERE mobile='".$mobile_number."' AND status='1'")->row();
			$data = array(
				"user_id" => $user_details->user_id,
				"user_name" => $user_details->user_name,
				"business_name" => $user_details->business_name,
				"mobile" => $user_details->mobile,
				"email" => $user_details->email,
				"vendor_type_id" => $user_details->vendor_type_id,
				"category_id" => $user_details->category_id,
				"state_id" => $user_details->state_id,
				"city_id" => $user_details->city_id
			);

			$response['status'] = 1;
			$response['message'] = DATA_SAVED_SUCCESSFULLY;
			$response['data'] = array_map("strval",$data);
		} else {
			$response['status'] = 0;
			$response['message'] = ERROR_TAG_FOUND;
			$response['data'] = "user_is_not_exist";
		}
		$this->common->response($response);
	}

}
