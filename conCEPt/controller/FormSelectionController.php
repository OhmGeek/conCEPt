<?php

namespace Concept\Controller;

use Concept\Model\FormSelectionModel;
use Twig_Environment;
use Twig_Loader_Filesystem;

class FormSelectionController
{

    //Returns the ID of the current logged in user
    function generateSelectionPage($formTypeID)
    {
        //The model used to get information
        $Model = new FormSelectionModel();

        //Generate navbar from the NavbarController class
        $navbar = new NavbarController();
        $navbar = $navbar->generateNavbarHtml();

        //Get the current marker
        $markerID = $this->getCurrentMarker();

        //Get name of form from form type (e.g formTypeID = 1, form name = Design Report)
        $formDetails = $Model->getFormName($formTypeID);
        $documentName = $formDetails[0]["Form_Title"];

        //Get list of students that marker marks for the given form type
        $results = $Model->getStudentOptions($formTypeID, $markerID);


        $students = array();
        //Add each student to students array
        foreach ($results as $row) {

            $studentFName = $row["Fname"];
            $studentLName = $row["Lname"];
            $studentLevel = $row["Year_Level"];
            $formID = $row["Form_ID"];

            $student = array();
            //Add student name, year level, and formID of the associated form for this student and form type
            $student["name"] = $studentFName . " " . $studentLName;
            $student["level"] = $studentLevel;
            $student["formID"] = $formID;

            array_push($students, $student);
        }

        //Inititalise twig object
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

        //Generate and print html for the page, using the Navbar, Document name, and students array
        $template = $twig->loadTemplate("formSelection.twig");
        print($template->render(array("navbar" => $navbar, "documentName" => $documentName, "students" => $students)));
    }

    //Main function to generate the page

    function getCurrentMarker()
    {
        return $_SERVER["REMOTE_USER"];
        //return something hardcoded this time
        //return "hkd4hdk";
        //return "knd6usj";
    }
}
