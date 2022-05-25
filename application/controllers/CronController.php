<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CronController extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}

    public function testing(){
        $response_data = array();
        $query_results = $this->db->query("SELECT * FROM `category_master`")->result();
        foreach($query_results as $row){
			$collect = array(
				"category_slug" => $row->category_slug,
				"category_id" => $row->category_id,
				"category_name" => $row->category_name
			);
			$response_data[] = array_map("strval",$collect);
		}
        $json_data = json_encode($response_data);

        file_put_contents("D:/filter_search.json",$json_data);
    }
}
