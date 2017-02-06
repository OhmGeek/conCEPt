<?php

require_once(__DIR__ . '/../vendor/autoload.php');
use Concept\Model\PDFModel;

class PDFController
{
	/*store model globally*/
	private $model;

    function __construct()
    {
    	$this->model = new PDFModel();
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
        $baseUrl = 'http://community.dur.ac.uk/cs.seg04/password/conCEPt/conCEPt/public';
        
        $pdfContents = $this->model->getFormContentsByID($formID);
        $pdfContentsString = print_r($pdfContents,true);
        ##var_dump($pdfContentsString);

        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

        $tableData = array();
        foreach ($pdfContents as $each)
        {
            $tableData[] = array('criteria' => $each['Sec_Criteria'], 'mark' => $each['Mark'], 'rationale' => $each['Comment']);
        }
        ##var_dump($tableData);

        $template = $twig->loadTemplate('pdf.twig');
        $html = $template->render(array('rows' => $tableData, 'baseUrl' => $baseUrl));

        /*Writes to generated PDF from $html variable to temporaryFiles folder*/
        $this->model->getPDF($html);
 
        header("Content-type:application/pdf");
        header("Content-Disposition:attachment;filename=downloaded.pdf");
        readfile("../temporaryFiles/temp.pdf");
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


        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

        $template = $twig->loadTemplate('completedFormList.twig');

        $output = $template->render(array('studentList' => $formInformationByStudent));

        print($output);

    }
}
