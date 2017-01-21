<?php

class PDF_Model {

	public function __construct($html_content) {
		$this->html_input = $html_content
	}

	public function get_PDF() {
		$url = "http://test.ohmgeek.co.uk/PDFGenerator/generate_pdf.php";
		$encoded_html = urlencode($this->html_input);
		$pdf = file_get_contents($encoded_html);
		return $pdf;
	}

}
