<<<<<<< HEAD
<?php

require_once(__DIR__ . '/../controller/auth/Auth_Controller.php');
require_once(__DIR__ . '/../model/pdf/pdf_model.php');
require_once(__DIR__ . '/../controller/homepage/Home_Controller.php');
require_once(__DIR__ . '/../controller/form/formSelectionController.php');

// deal with the odd installation we have going on

$base = __DIR__;
$path = preg_replace("/cs.seg04\/password\/conCEPt\/conCEPt\/public\//", "", $_SERVER['REQUEST_URI']);
$request_type = $_SERVER['REQUEST_METHOD'];


// strip the base path


switch ($path) {
	case "/?":
	case "/":
	case "":
		//echo Auth_Controller::auth_page($_SERVER['REMOTE_USER']);
		$cont = new MainPageController();
		echo $cont->generatePage();
		break;

	case "/?admin/":
		echo "Admin";
		break;

	case "/?marker/":
		echo "Marker";
		break;
	case "/?pdf_test/":
		$html = "<!DOCTYPE html><html><body><h1>This is a test PDF</h1></body></html>";
		$pdf = new PDF_Model($html);
		header("Content-type:application/pdf");
		header("Content-Disposition:attachment;filename='downloaded.pdf'");
		echo $pdf->get_PDF();
		break;
	case "/?form/":
		$formController = new formSelectionController();
		echo $formController->generateSelectionPage($_GET['form_id']);
		break;

	case "/?save_form/":
		$controller = new SaveSubmitController($_POST);
		
		break;
	default:
		echo "404 Error\n";
		echo $path;
}

=======
<?php
	require_once '../vendor/autoload.php';
	
	include '../model/db.php';
	include '../control/saveSubmitController.php';
	include '../control/formSelectionController.php';
	include '../control/FormController.php';
	include '../control/historyController.php';
	include '../control/navbarController.php';
	
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
	
?>
>>>>>>> a14d206ebbdbfc6871135bd41a5a4c12d4fad09a
