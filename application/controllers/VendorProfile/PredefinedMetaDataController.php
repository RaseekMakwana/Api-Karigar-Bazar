<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PredefinedMetaDataController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function stora_predefined_meta_data(){
		$request = $this->input->post();
		$this->common->field_required(array('categories_collection'),$request);

		$check_user_exist = $this->db->query("SELECT count(*) as number_of_records FROM login_master WHERE mobile='".$request['mobile_no']."' AND status='1'")->row();

		if(empty($check_user_exist->number_of_records)){
			$user_id = time().uniqid();
			$insertData = array(
				"user_id" => $user_id,
				"user_name" => $request['contact_person_name'],
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
		}
	}
	
}
