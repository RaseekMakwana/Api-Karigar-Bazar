<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PredefinedMetaDataController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function stora_predefined_meta_data(){
		$request = $this->input->post();
		$this->common->field_required(array('user_id','categories_collection'),$request);
		$category_collection_array = json_decode($request['categories_collection']);
		
		$collect_ids = array();
		foreach($category_collection_array as $row){
			$collect_ids[] = $row->id;
		}

		$target_categories = implode(",",$collect_ids);

		$updateData = array(
			"target_categories" => $target_categories,
			"status" => '1',
		);
		$this->db->where(array("user_id"=>$request['user_id']));
		$this->db->update('vendor_master',$updateData);

	}
	
}
