<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function create(){
		$request = $this->input->post();
		$this->common->field_required(array('category_name','vendor_type_id'),$request);

		$insertData = array(
			"category_slug" => $this->common->slug_generator($request['category_name']),
			"category_name" => $request['category_name'],
			"vendor_type_id" => $request['vendor_type_id'],
		);
		$this->db->insert("category_master", $insertData);
	}
}
