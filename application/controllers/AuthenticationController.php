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

		$response = array();
		$login_data = $this->db->query("SELECT * FROM login_master WHERE mobile='".$mobile_number."' AND password='".md5($password)."' AND status='1'")->row();
		if(!empty($login_data)){
			$user_details = $this->db->query("SELECT vm.*, cm.`city_name` FROM vendor_master AS vm LEFT JOIN cities_master AS cm ON cm.`city_id`=vm.`city_id` WHERE mobile='".$mobile_number."' ")->row();
			if(!empty($user_details)){
				$data = array(
					"user_id" => $user_details->user_id,
					"vendor_name" => $user_details->vendor_name,
					"business_name" => $user_details->business_name,
					"mobile" => $user_details->mobile,
					"email" => $user_details->email,
					"vendor_type_id" => $user_details->vendor_type_id,
					"category_id" => $user_details->category_id,
					"state_id" => $user_details->state_id,
					"city_id" => $user_details->city_id,
					"city_name" => $user_details->city_name,
					"user_type"=>$login_data->user_type,
					"profile_picture"=> STORAGE_CONTENT_URL.$user_details->profile_picture,
					"status"=>$user_details->status
				);
				$response['status'] = 1;
				$response['message'] = DATA_GET_SUCCESSFULLY;
				$response['data'] = array_map("strval",$data);
			} else {
				$response['status'] = 0;
				$response['message'] = ERROR_TAG_FOUND;
				$response['data'] = "user_is_not_exist";
			}
			
			
		} else {
			$response['status'] = 0;
			$response['message'] = ERROR_TAG_FOUND;
			$response['data'] = "user_is_not_exist";
		}
		$this->common->response($response);
	}

	public function vendor_registration(){
		$request = $this->input->post();
		$this->common->field_required(array('contact_person_name','mobile_no','password','vendor_type_id','category_id','state_id','city_id'),$request);

		$check_user_exist = $this->db->query("SELECT count(*) as number_of_records FROM login_master WHERE mobile='".$request['mobile_no']."' AND status='1'")->row();

		if(empty($check_user_exist->number_of_records)){
			$user_id = time().uniqid();
			$insertData = array(
				"user_id" => $user_id,
				"vendor_name" => $request['contact_person_name'],
				"business_name" => $request['business_name'],
				"mobile" => $request['mobile_no'],
				// "email" => $request['email_address'],
				"vendor_type_id" => $request['vendor_type_id'],
				"category_id" => $request['category_id'],
				"state_id" => $request['state_id'],
				"city_id" => $request['city_id']
			);
			$this->db->insert('vendor_master',$insertData);

			$insertData = array(
				"user_id" => $user_id,
				"mobile" => $request['mobile_no'],
				"password" => md5($request['password']),
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

	public function direct_vendor_registration(){
		$request = $this->input->post();
		$this->common->field_required(array('business_name','contact_person_name','mobile_no','password','occupation','state','city'),$request);

		$check_user_exist = $this->db->query("SELECT count(*) as number_of_records FROM login_master WHERE mobile='".$request['mobile_no']."' AND status='1'")->row();

		if(empty($check_user_exist->number_of_records)){
			$user_id = time().uniqid();
			$insertData = array(
				"user_id" => $user_id,
				"vendor_name" => $request['contact_person_name'],
				"business_name" => $request['business_name'],
				"mobile" => $request['mobile_no'],
				// "email" => $request['email_address'],
				"occupation" => $request['occupation'],
				"state" => $request['state'],
				"city" => $request['city'],
				"password" => $request['password']
			);
			$this->db->insert('data_vendor_master',$insertData);

			// $insertData = array(
			// 	"user_id" => $user_id,
			// 	"mobile" => $request['mobile_no'],
			// 	"password" => $request['password'],
			// );
			// $this->db->insert('login_master',$insertData);

			$response['status'] = 1;
			$response['message'] = DATA_SAVED_SUCCESSFULLY;
		} else {
			$response['status'] = 0;
			$response['message'] = ERROR_TAG_FOUND;
			$response['data'] = "mobile_no_already_exist";
		}
		
		$this->common->response($response);
	}

}
