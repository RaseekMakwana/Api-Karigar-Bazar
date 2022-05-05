<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	

	public function get_list_vendor_type_with_categories() {
		$this->load->view('welcome');
	}

}
