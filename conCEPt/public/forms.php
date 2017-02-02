<?php
	require_once '../vendor/autoload.php';
	include '../model/db.php';
	include '../controller/form/saveSubmitController.php';
	include '../controller/form/formSelectionController.php';
	include '../controller/FormController.php';
	include '../controller/historyController.php';
	include '../controller/navbarController.php';
	
	$route = $_GET["route"];

	
	if ($route == "send"){
		$test = new SaveSubmitController($_POST);
	}elseif ($route == "receive"){
		$formID = $_GET["id"];
		$test = new FormController($formID);
	}elseif ($route == "select"){
		$formTypeID = $_GET["typeId"];
		$test = new formSelectionController();
		$test->generateSelectionPage($formTypeID);
	}elseif($route == "history"){
		$test = new HistoryController();
	}elseif($route == "navbar"){
		$test = new navbarController();
		print_r($test->generateNavbarHtml());
	}
