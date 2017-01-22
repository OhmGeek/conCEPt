<?php

require_once(__DIR__ . '/../controller/auth/Auth_Controller.php');
require_once(__DIR__ . '/../model/pdf/pdf_model.php');
// deal with the odd installation we have going on

$base = __DIR__;
$path = preg_replace("/cs.seg04\/password\/conCEPt\/conCEPt\/public\//", "", $_SERVER['REQUEST_URI']);
$request_type = $_SERVER['REQUEST_METHOD'];


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
