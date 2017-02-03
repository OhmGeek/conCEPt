<?php
	require_once '../vendor/autoload.php';
	include '../model/db.php';
	include '../controller/SaveSubmitController.php';
	include '../controller/FormSelectionController.php';
	include '../controller/FormDisplayController.php';
	include '../controller/HistoryController.php';
	include '../controller/NavbarController.php';
	
	$route = $_GET["route"];

	
	if ($route == "send"){
		$test = new SaveSubmitController($_POST);
	}elseif ($route == "receive"){
		$formID = $_GET["formid"];
		$test = new FormDisplayController($formID);
	}elseif ($route == "select"){
		$formTypeID = $_GET["typeId"];
		$test = new FormSelectionController();
		$test->generateSelectionPage($formTypeID);
	}elseif($route == "history"){
		$test = new HistoryController();
	}elseif($route == "navbar"){
		$test = new NavbarController();
		print_r($test->generateNavbarHtml());
	}
