<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
include '../../model/pdf/pdf_model.php';


class PDFController
{
	/*store model globally*/
	private $model;

    function __construct()
    {
    	$this->model = new PDF_Model();
    	if (isset($_GET['form']))
    	{
            $this->createPDF($_GET['form']);
    	}
    	else
    	{
    		$this->displayCompletedForms();
    	}
        
    }
    function createPDF($formID)
    {
        $pdfContents = $this->model->getFormContentsByID($formID);

        $html = print_r($pdfContents, true);
        $pdf = $this->model->get_PDF("<html><head></head><body>".$html."</body><html>");
 
        header("Content-type:application/pdf");
        header("Content-Disposition:attachment;filename=downloaded.pdf");
        readfile("./temp.pdf");
        exit;
    }
    function displayCompletedForms()
    {
        $completedFormInformation = $this->model->getAllCompletedFormIDs();

        $formInformationByStudent = array();
        foreach($completedFormInformation as $row)
        {
            $id = $row['Student_ID'];
            $student = $row['Student_ID'] . " " . $row['Fname'] . " " . $row['Lname'];
            $formTitle = $row['Form_Title'] . " Marks";

            $formInformationByStudent[$id]['studentInfo'] = $student;
            $formInformationByStudent[$id]['forms'][$formTitle] = $row['Form_ID'];

        }


        $loader = new Twig_Loader_Filesystem('../../view/');
        $twig = new Twig_Environment($loader);

        $template = $twig->loadTemplate('completedFormList.twig');

        $output = $template->render(array('studentList' => $formInformationByStudent));

        print($output);

    }
}