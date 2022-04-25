<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->common->header_authentication();
	}
	public function get_categories_by_vendor_type()
	{
		$request = $this->input->post();
		$this->common->field_required(array('vendor_type_id'),$request);

		$query_results = $this->db->query("select * from category_master where vendor_type_id='".$request['vendor_type_id']."'")->result();
		$data = array();
		foreach($query_results as $row){
			$array_category = array(
				"category_id"=>$row->category_id,
				"category_name"=>$row->category_name,
				"picture_thumb"=>$row->picture_thumb
			);
			$data[] = array_map("strval",$array_category);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $data;

		$this->common->response($response);
	}
}
