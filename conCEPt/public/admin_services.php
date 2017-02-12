<?php
	
require_once(__DIR__ . '/../vendor/autoload.php');

	use Concept\Controller\AddingController;
	use Concept\Controller\LinkingController;
    use Concept\Controller\PDFController;

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
		/*File will print to screen of its own accord. Handles the Printing page and PDF generation*/
		$test = new PDFController();
	}
?>