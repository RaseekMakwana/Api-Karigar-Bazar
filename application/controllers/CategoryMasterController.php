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
											LEFT JOIN `vendor_type_master` AS vtm ON vtm.`vendor_type_id`=cm.`vendor_type_id` WHERE cm.status='1' AND vtm.status='1' ORDER BY order_by ASC")->result();


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
					"picture_thumb" => STORAGE_CONTENT_URL.$row1->picture_thumb
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

	public function get_list_vendor_type_with_category_with_tag() {
		$request = $this->input->post();

		$query_results = $this->db->query("SELECT vtm.vendor_type_slug,vtm.vendor_type_id,vtm.`vendor_type_name`,vtm.`picture_thumb`,cm.`category_slug`,cm.`category_id`,cm.`category_name`,scm.`service_slug`,scm.`service_id`,scm.`service_name`
		FROM vendor_type_master AS vtm 
		LEFT JOIN category_master AS cm ON cm.`vendor_type_id`=vtm.`vendor_type_id` AND cm.`status`='1'
		LEFT JOIN `service_master` AS scm ON scm.`category_id`=cm.`category_id` AND scm.`status`='1'
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
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['tag_data'][$row->service_id]['service_slug'] = $row->service_slug;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['tag_data'][$row->service_id]['service_id'] = $row->service_id;
			$arrange_data[$row->vendor_type_id]['categories_data'][$row->category_id]['tag_data'][$row->service_id]['service_name'] = $row->service_name;

		}
		
		// p($arrange_data);
		$response_data = array();
		foreach($arrange_data as $row){
			
			$level_one = array();
			foreach($row['categories_data'] as $row1){
				$level_two = array();
				foreach($row1['tag_data'] as $row2){
					if(!empty($row2['service_id'])){
						$level_two[] = array(
							"service_slug"=> $row2['service_slug'],
							"service_id"=> $row2['service_id'],
							"service_name"=> $row2['service_name'],
						);
					}
				}
				
				$level_one[] = array(
					"category_slug"=> $row1['category_slug'],
					"category_id"=> $row1['category_id'],
					"category_name"=> $row1['category_name'],
					"tag_data"=> $level_two,
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

	public function get_list_categories_and_tag_by_vendor_type_id() {
		$request = $this->input->post();

		$this->common->field_required(array('vendor_type_id'),$request);

		$query_results = $this->db->query("SELECT cm.`category_slug`,cm.`category_id`,cm.`category_name`,scm.`service_slug`,scm.`service_id`,scm.`service_name`
		FROM category_master AS cm 
		LEFT JOIN `service_master` AS scm ON scm.`category_id`=cm.`category_id` AND scm.`status`='1'
		WHERE cm.vendor_type_id='".$request['vendor_type_id']."' AND cm.`status`='1'")->result();

		$arrange_data = array();
		foreach($query_results as $row){
			$arrange_data[$row->category_id]['category_slug'] = $row->category_slug;
			$arrange_data[$row->category_id]['category_id'] = $row->category_id;
			$arrange_data[$row->category_id]['category_name'] = $row->category_name;
			$arrange_data[$row->category_id]['tag_data'][$row->service_id]['service_slug'] = $row->service_slug;
			$arrange_data[$row->category_id]['tag_data'][$row->service_id]['service_id'] = $row->service_id;
			$arrange_data[$row->category_id]['tag_data'][$row->service_id]['service_name'] = $row->service_name;

		}
		
		// p($arrange_data);
		$response_data = array();
		foreach($arrange_data as $row){
			$level_two = array();
			foreach($row['tag_data'] as $row1){
				if(!empty($row1['service_id'])){
					$level_two[] = array(
						"service_slug"=> $row1['service_slug'],
						"service_id"=> $row1['service_id'],
						"service_name"=> $row1['service_name'],
					);
				}
			}

			$response_data[] = array(
				"category_slug"=> $row['category_slug'],
				"category_id"=> $row['category_id'],
				"category_name"=> $row['category_name'],
				"tag_data"=> $level_two,
			);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	//  Search category_id, category_slug
	public function get_tag_with_vendor_by_category() {
		$request = $this->input->post();

		$this->common->field_required(array('action','keyword'),$request);

		$query_string = "";
		if($request['action'] == "slug"){
			$query_string .= " category_slug='".$request['keyword']."'";
		} else {
			$query_string .= " category_id='".$request['keyword']."'";
		}
		$query_results = $this->db->query("SELECT * FROM service_master WHERE category_id in (SELECT category_id FROM category_master where $query_string AND status='1') AND status='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"service_slug" => $row->service_slug,
				"service_id" => $row->service_id,
				"service_name" => $row->service_name,
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
	public function get_category_by_vendor_type_id(){
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

	// vendor_type_slug
	public function get_category_by_vendor_type_slug(){
		$request = $this->input->post();

		$this->common->field_required(array('vendor_type_slug'),$request);

		$query_results = $this->db->query("SELECT category_id,category_slug,category_name,picture_thumb 
		FROM category_master
		WHERE vendor_type_id = (SELECT `vendor_type_id` FROM `vendor_type_master` WHERE `vendor_type_slug`='".$request['vendor_type_slug']."')  AND `status`='1'")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"category_id" => $row->category_id,
				"category_slug" => $row->category_slug,
				"category_name" => $row->category_name,
				"picture_thumb" => STORAGE_CONTENT_URL.$row->picture_thumb
			);
			$response_data[] = array_map("strval",$collect);
		}
		

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	public function get_search_keywords_filter(){
		$request = $this->input->post();

		$this->common->field_required(array('keyword'),$request);

		$query_results = $this->db->query("(SELECT category_id AS search_id, category_slug AS search_slug, category_name AS search_name, 'category' AS `search_type` FROM category_master WHERE category_name LIKE '%".$request['keyword']."%' AND status='1' )
											UNION 
											(SELECT service_id AS search_id, service_slug AS search_slug, service_name AS search_name, 'tag' AS `search_type` FROM service_master WHERE service_name LIKE '%".$request['keyword']."%' AND status='1' )")->result();

		$response_data = array();
		foreach($query_results as $row){
			$collect = array(
				"search_id" => $row->search_id,
				"search_slug" => $row->search_slug,
				"search_name" => $row->search_name,
				"search_type" => $row->search_type
			);
			$response_data[] = array_map("strval",$collect);
		}

		$response['status'] = 1;
		$response['message'] = DATA_GET_SUCCESSFULLY;
		$response['data'] = $response_data;

		$this->common->response($response);
	}

	

}
