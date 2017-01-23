<?php

require_once(__DIR__ . '/../../model/db.php');
class Home_Controller {

	public function __construct() {
		$this->db = DB::getDB();
	}

	public function renderHome() {
		

	}


}
