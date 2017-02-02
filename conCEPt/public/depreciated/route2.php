<?php

require_once(__DIR__ . '/../controller/auth/Auth_Controller.php');
require_once(__DIR__ . '/../model/pdf/pdf_model.php');
// deal with the odd installation we have going on

$request_type = $_SERVER['REQUEST_METHOD'];
$parts = explode("&",$_SERVER['QUERY_STRING']);
// this gets only the path part, no get variables
$path = $parts[0];

// strip the base path


switch ($path) {
	case "/?":
	case "/":
	case "":
		echo Auth_Controller::auth_page($_SERVER['REMOTE_USER']);
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
	default:
		echo "404 Error\n";
		echo $path;
}
