<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryMasterController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function get_list_vendor_type_with_categories() {
		$request = $this->input->post();

		$query_results = $this->db->query("SELECT cm.category_id,cm.category_slug, cm.category_name, vtm.vendor_type_slug,vtm.vendor_type_name,vtm.picture_thumb,cm.picture_thumb 
											FROM category_master AS cm
											LEFT JOIN `vendor_type_master` AS vtm ON vtm.`vendor_type_id`=cm.`vendor_type_id` WHERE cm.status='1' AND vtm.status='1'")->result();


		$arrangeData = array();
		foreach($query_results as $row){
			$arrangeData[$row->vendor_type_slug]['vendor_type_slug'] = $row->vendor_type_slug;
			$arrangeData[$row->vendor_type_slug]['vendor_type_name'] = $row->vendor_type_name;
			$arrangeData[$row->vendor_type_slug]['picture_thumb'] = $row->picture_thumb;
			$arrangeData[$row->vendor_type_slug]['category_data'][] = $row;
		}
		// p($arrangeData);

		$response_data = array();
		$i=0;
		foreach($arrangeData as $key => $row){
			// p($row);
			$response_data[$i]["vendor_type_slug"] = $row['vendor_type_slug'];
			$response_data[$i]["vendor_type_name"] = $row['vendor_type_name'];
			foreach($row['category_data'] as $key => $row1){
				$response_data[$i]['category_data'][] = array_map("strval",array(
					"category_id" => $row1->category_id,
					"category_slug" => $row1->category_slug,
					"category_name" => $row1->category_name,
					"picture_thumb" => $row1->picture_thumb
				));
			}
			// $response_data[$i]['category_data'] = $row;
			$i++;
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	public function get_list_vendor_type_with_category_with_sub_category() {
		$request = $this->input->post();

		$query_results = $this->db->query("SELECT vtm.vendor_type_slug,vtm.vendor_type_id,vtm.`vendor_type_name`,vtm.`picture_thumb`,cm.`category_slug`,cm.`category_id`,cm.`category_name`,scm.`sub_category_slug`,scm.`sub_category_id`,scm.`sub_category_name`
		FROM vendor_type_master AS vtm 
		LEFT JOIN category_master AS cm ON cm.`vendor_type_id`=vtm.`vendor_type_id` AND cm.`status`='1'
		LEFT JOIN `sub_category_master` AS scm ON scm.`category_id`=cm.`category_id` AND scm.`status`='1'
		WHERE vtm.`status`='1'")->result();

		$arrange_data = array();
		foreach($query_results as $row){
			$arrange_data[$row->vendor_type_id]['vendor_type_slug'] = $row->vendor_type_slug;
			$arrange_data[$row->vendor_type_id]['vendor_type_id'] = $row->vendor_type_id;
			$arrange_data[$row->vendor_type_id]['vendor_type_name'] = $row->vendor_type_name;
			$arrange_data[$row->vendor_type_id]['picture_thumb'] = $row->picture_thumb;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_slug'] = $row->category_slug;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_id'] = $row->category_id;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['category_name'] = $row->category_name;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_slug'] = $row->sub_category_slug;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_id'] = $row->sub_category_id;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_name'] = $row->sub_category_name;

		}
		
		// p($arrange_data);
		$response_data = array();
		foreach($arrange_data as $row){
			
			$level_one = array();
			foreach($row['categories_data'] as $row1){
				$level_two = array();
				foreach($row1['sub_category_data'] as $row2){
					if(!empty($row2['sub_category_id'])){
						$level_two[] = array(
							"sub_category_slug"=> $row2['sub_category_slug'],
							"sub_category_id"=> $row2['sub_category_id'],
							"sub_category_name"=> $row2['sub_category_name'],
						);
					}
				}
				
				$level_one[] = array(
					"category_slug"=> $row1['category_slug'],
					"category_id"=> $row1['category_id'],
					"category_name"=> $row1['category_name'],
					"sub_category_data"=> $level_two,
				);
			}

			$response_data[] = array(
				"vendor_type_slug"=>$row['vendor_type_slug'],
				"vendor_type_id"=>$row['vendor_type_id'],
				"vendor_type_name"=>$row['vendor_type_name'],
				"picture_thumb"=>$row['picture_thumb'],
				"category_data"=>$level_one
			);

		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	public function get_list_categories_and_sub_category_by_vendor_type_id() {
		$request = $this->input->post();

		$this->common->field_required(array('vendor_type_id'),$request);

		$query_results = $this->db->query("SELECT cm.`category_slug`,cm.`category_id`,cm.`category_name`,scm.`sub_category_slug`,scm.`sub_category_id`,scm.`sub_category_name`
		FROM category_master AS cm 
		LEFT JOIN `sub_category_master` AS scm ON scm.`category_id`=cm.`category_id` AND scm.`status`='1'
		WHERE cm.vendor_type_id='".$request['vendor_type_id']."' AND cm.`status`='1'")->result();

		$arrange_data = array();
		foreach($query_results as $row){
			$arrange_data[$row->category_id]['category_slug'] = $row->category_slug;
			$arrange_data[$row->category_id]['category_id'] = $row->category_id;
			$arrange_data[$row->category_id]['category_name'] = $row->category_name;
			$arrange_data[$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_slug'] = $row->sub_category_slug;
			$arrange_data[$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_id'] = $row->sub_category_id;
			$arrange_data[$row->category_id]['sub_category_data'][$row->sub_category_id]['sub_category_name'] = $row->sub_category_name;

		}
		
		// p($arrange_data);
		$response_data = array();
		foreach($arrange_data as $row){
			$level_two = array();
			foreach($row['sub_category_data'] as $row1){
				if(!empty($row1['sub_category_id'])){
					$level_two[] = array(
						"sub_category_slug"=> $row1['sub_category_slug'],
						"sub_category_id"=> $row1['sub_category_id'],
						"sub_category_name"=> $row1['sub_category_name'],
					);
				}
			}

			$response_data[] = array(
				"category_slug"=> $row['category_slug'],
				"category_id"=> $row['category_id'],
				"category_name"=> $row['category_name'],
				"sub_category_data"=> $level_two,
			);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	//  Search category_id, category_slug
	public function get_sub_category_by_category() {
		$request = $this->input->post();

		$this->common->field_required(array('action','value'),$request);

		$query_string = "";
		if($request['action'] == "slug"){
			$query_string .= " category_slug='".$request['value']."'";
		} else {
			$query_string .= " category_id='".$request['value']."'";
		}
		$query_results = $this->db->query("SELECT * FROM sub_category_master WHERE category_id in (SELECT category_id FROM category_master where $query_string AND status='1') AND status='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"sub_category_slug" => $row->sub_category_slug,
				"sub_category_id" => $row->sub_category_id,
				"sub_category_name" => $row->sub_category_name,
				"picture_thumb" => $row->picture_thumb
			);
			$response_data[] = array_map("strval",$collect);
		}
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	// vendor_type_slug
	public function get_category_by_vendor_type(){
		$request = $this->input->post();

		$this->common->field_required(array('vendor_type_id'),$request);

		$query_results = $this->db->query("SELECT category_id,category_slug,category_name,picture_thumb FROM category_master WHERE vendor_type_id='".$request['vendor_type_id']."'  AND STATUS='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"category_id" => $row->category_id,
				"category_slug" => $row->category_slug,
				"category_name" => $row->category_name,
				"picture_thumb" => $row->picture_thumb
			);
			$response_data[] = array_map("strval",$collect);
		}
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	public function get_sub_category_by_like_sub_category_name(){
		$request = $this->input->post();

		$this->common->field_required(array('keyword'),$request);

		$query_results = $this->db->query("SELECT * FROM `sub_category_master` WHERE sub_category_name LIKE '%".$request['keyword']."%' AND status='1' limit 10")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"sub_category_slug" => $row->sub_category_slug,
				"sub_category_id" => $row->sub_category_id,
				"sub_category_name" => $row->sub_category_name
			);
			$response_data[] = array_map("strval",$collect);
		}
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	

}
