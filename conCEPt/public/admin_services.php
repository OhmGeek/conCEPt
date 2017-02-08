<?php
	
require_once(__DIR__ . '/../vendor/autoload.php');

	use Concept\Controller\AddingController;
	use Concept\Controller\LinkingController;
	$route = $_GET["route"];

	if($route == "adding"){
		$test = new AddingController();
		print_r($test->generatePage());
	}
	elseif($route == "linking"){
		$test = new LinkingController();
		print_r($test->generatePage());
	}
	elseif($route == "printing"){
		$test = new PrintingController();
		print_r($test->generatePage());
		//this path will break if executed
	}
?>