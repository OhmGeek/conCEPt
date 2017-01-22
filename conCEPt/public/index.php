<?php
	require_once '../vendor/autoload.php';
	
	include '../model/db.php';
	include '../control/saveSubmitController.php';
	include '../control/formSelectionController.php';
	include '../control/FormController.php';
	
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
	}
	
?>