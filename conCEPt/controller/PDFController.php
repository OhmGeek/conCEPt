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
        
        /*Find marker names*/
        foreach ($markerDetails as $marker)
        {
            if($marker['IsSupervisor'] === "1")
            {
                $supervisorName = $marker['Fname'].' '.$marker['Lname'];
            }
            else
            {
                $examinerName = $marker['Fname'].' '.$marker['Lname'];
            }
        }
        
        /*Find student name and from title*/
        $studentName = $studentDetails[0]['Fname'].' '.$studentDetails[0]['Lname'];
        $title = $formDetails[0]['Form_title'];

        /*Find form contents (criteria, comments etc.)*/
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

        /* get all necissary CSS and JS and render page*/
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
        $this->model->getPDF($html);

        $filename = '"'.$studentDetails[0]['Student_ID'].' '.$formDetails[0]['Form_title'].'.pdf"';
 
        header("Content-type:application/pdf");
        header("Content-Disposition:attachment;filename=".$filename);
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
            $studentName = $row['Fname'] . " " . $row['Lname'];
            $studentID = $row['Student_ID'];
            $formTitle = $row['Form_Title'] . " Marks";

            $formInformationByStudent[$id]['studentName'] = $studentName;
            $formInformationByStudent[$id]['studentID'] = $studentID;
            $formInformationByStudent[$id]['forms'][$formTitle] = $row['Form_ID'];
        }
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

        $navbarTemplate = $twig->loadTemplate('navbarAdmin.twig');
        $navbar = $navbarTemplate->render(array());

        $template = $twig->loadTemplate('completedFormList.twig');

        $output = $template->render(array('navbar' => $navbar,
                                          'studentList' => $formInformationByStudent,
                                          'isEmpty' => empty($formInformationByStudent)));

        print($output);
    }
}