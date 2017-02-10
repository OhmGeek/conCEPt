<?php

namespace Concept\Controller;

use Concept\Model\PDFModel;
use Twig_Environment;
use Twig_Loader_Filesystem;

class PDFController
{
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
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);
        
        $formContents = $this->model->getFormContentsByID($formID);
        $studentDetails = $this->model->getStudentFromID($formID);
        $markerDetails = $this->model->getMarkersFromID($formID);
        $formDetails = $this->model->getFormTitleFromID($formID);
        $totalMark = $this->model->getTotalFormMarkByID($formID);
        
        $studentName = $studentDetails[0]['Fname'].' '.$studentDetails[0]['Lname'];
        $supervisorName = print_r($markerDetails, true);
        $examinerName = "TODO, determine which marker is examiner and supervisor";
        $title = $formDetails[0]['Form_title'];

        $generalComments = "";
        $tableData = array();
        foreach ($formContents as $each)
        {
            if ($each["Sec_Name"] === "General Comments")
            {
               $generalComments = $each['Comment'];

            }
            else
            {
                $sectionName = $each["Sec_Name"];
                $sectionWeight = $each["Sec_Percent"];
                $sectionCriteria = $each["Sec_Criteria"];

                $criteriaList = explode("\n", $sectionCriteria); //WHY??
                $template = $twig->loadTemplate("criteria.twig");
                $criteria = $template->render(array('criteriaName' => $sectionName,
                    'criteriaWeighting' => $sectionWeight,
                    'criteriaList' => $criteriaList));


                $tableData[] = array('criteria' => $criteria,
                                     'mark' => $each['Mark'],
                                     'rationale' => $each['Comment']);
            } 
        }


        $printCSS = file_get_contents('../public/css/print.css');
        $jqueryNotebookCSS = file_get_contents('../public/css/jquery.notebook.css');
        $formsCSS = file_get_contents('../public/css/forms.css');
        $formsJS = file_get_contents('../public/js/forms.js');
        $jqueryNotebookJS = file_get_contents('../public/js/jquery.notebook.js');

        $template = $twig->loadTemplate('pdf.twig');
        $html = $template->render(array(/*Form Contents*/
                                        'rows' => $tableData,
                                        'generalComments' => $generalComments,
                                        /*Form Information*/
                                        'studentName' => $studentName,
                                        'examinerName' => $examinerName,
                                        'supervisorName' => $supervisorName,
                                        'title' => $title,
                                        'totalMark' => $totalMark,
                                        /*CSS and JS*/
                                        'printCSS' => $printCSS,
                                        'jqueryNotebookCSS' => $jqueryNotebookCSS,
                                        'formsCSS' => $formsCSS,
                                        'formsJS' => $formsJS,
                                        'jqueryNotebookJS' => $jqueryNotebookJS));
        
        /*Writes to generated PDF from $html variable to temporaryFiles folder*/
        print($html);
        //$this->model->getPDF($html);
 
        #header("Content-type:application/pdf");
        #header("Content-Disposition:attachment;filename=downloaded.pdf");
        //readfile("../temporaryFiles/temp.pdf");
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
