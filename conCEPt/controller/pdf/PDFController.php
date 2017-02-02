<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
include '../../model/pdf/pdf_model.php';


class PDFController
{
    function __construct()
    {
        $this->displayCompletedForms();
    }
    function displayCompletedForms()
    {

        $PDF_Model = new PDF_Model();

        $completedFormInformation = $PDF_Model->getAllCompletedFormIDs();

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