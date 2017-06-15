<?php
header("Access-Control-Allow-Origin: *");
include_once 'vendor/autoload.php';

use mikehaertl\wkhtmlto\Pdf;

// get HTML data from the post variable
$html_data = $_POST['html'];

// specify options for the PDF converter interface
$options = array(
    'binary' => '/usr/bin/wkhtmltopdf',
    'load-error-handling' => 'ignore',
    'load-media-error-handling' => 'ignore',
);

// Write to a PDF
$pdf = new Pdf($html_data);
$pdf->setOptions($options);

// Try to send the PDF. If this fails, send back an error message instead
if(!$pdf->send()) {
	echo $pdf->getError();
}
