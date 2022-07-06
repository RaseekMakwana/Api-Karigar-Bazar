<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExtraController extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// $this->common->header_authentication();
	}

	public function import_lat_long(){ 

		$query_results = $this->db->query("SELECT city_id, city_name, state_name FROM cities_master AS cm, states_master AS sm WHERE sm.`state_id`=cm.`state_id` AND cm.latitude='' ORDER BY city_id asc")->result();
		foreach($query_results as $row){

			// From URL to get webpage contents.
			$address = urlencode($row->city_name." ".$row->state_name." India");
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=AIzaSyC-oA4_1KHChEhTTSamnbiuANI0Ezq-OqY";
					
			// Initialize a CURL session.
			$ch = curl_init();

			// Return Page contents.
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			//grab URL and pass it to the variable.
			curl_setopt($ch, CURLOPT_URL, $url);

			$result = curl_exec($ch);
			// p($result);
			$result_data = json_decode($result);
			$location = $result_data->results[0]->geometry->location;

			$updateData = array(
				"latitude"=> $location->lat,
				"longitude"=> $location->lng
			);
			$this->db->where(array("city_id"=>$row->city_id));
			$this->db->update("cities_master",$updateData);
			
		}

		echo "done";
		
	}

}
