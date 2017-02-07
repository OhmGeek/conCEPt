<?php

namespace Concept\Controller;

use Concept\Model\HistoryModel;
use Twig_Loader_Filesystem;
use Twig_Environment;
class HistoryController
{

    function __construct()
    {
        $this->generatePage();
    }

    //Returns the id of the current user logged in

    function generatePage()
    {
        //Get current marker
        $markerID = $this->getCurrentMarker();

        //Create object to get information
        $Model = new HistoryModel();

        //Get all documents that the current marker has submitted
        $rows = $Model->getAllDocuments($markerID);

        $documents = array();

        //Add each document to the documents array
        foreach ($rows as $row) {
            //Get general detail (ID, form title, submission comment, student name, year level)
            $formID = $row["Form_ID"];
            $formName = $row["Form_Title"];
            $comment = $row["comment"];
            $studentName = $row["Fname"] . " " . $row["Lname"];
            $year = $row["Year_Level"];

            //Get timeStamp information, reformat date and time to be more user friendly
            $timeStamp = $row["Time_Stamp"];
            $details = split(" ", $timeStamp);
            $date = $details[0];
            $date = date('d-m-Y', strtotime($date));
            $time = $details[1];

            //Add relevant details to a document array
            $document = array();
            $document["name"] = $formName . "-" . $studentName . "- year " . $year;
            $document["comment"] = $comment;
            $document["link"] = "forms.php?route=receive&id=" . $formID; //Set link to form (will change if routing changes)
            $document["date"] = $date . " at " . $time;
            //Add document array to documents array
            array_push($documents, $document);
        }

        //Inititalise Twig object
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

        //Get HTML for navbar
        $navbar = new NavbarController();
        $navbar = $navbar->generateNavbarHtml();

        //Generate and print html for history page
        $template = $twig->loadTemplate("history.twig");
        print($template->render(array("navbar" => $navbar, "documents" => $documents)));
    }

    //Displays the History page for the current marker

    function getCurrentMarker()
    {
        return $_SERVER['REMOTE_USER'];
    }

}


?>
