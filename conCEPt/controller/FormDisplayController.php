<?php

namespace Concept\Controller;

use Concept\Model\FormDisplayModel;
use Twig_Environment;
use Twig_Loader_Filesystem;

Class FormDisplayController
{
    //TODO this week
    //	- Do something with conflict sections on merged form to show markers that this form has conflicts
    //  - Decide how admin staff will view the forms
    //	- Add some checks to make sure marker has access to the current form

    function __construct()
    {
        //$this->generateForm($formID);
        $this->model = new FormDisplayModel();
        $loader = new Twig_Loader_Filesystem('../view/formPage');
        $this->twig = new Twig_Environment($loader);
    }

    //Returns the current user that is logged in

    function generatePage($formID)
    {
        //Initialise the model and Twig objects to use


        //Get general form information (title, isSubmitted, isMerged)
        $formInformation = $this->model->getFormInformation($formID);
        $formInformation = $formInformation[0];
        $formTitle = $formInformation["Form_title"];

        //Get HTML for main form
        $form = $this->generateForm($formID);

        //Generat HTML for navbar
        $navbar = new NavbarController();
        $navbar = $navbar->generateNavbarHtml();

        //Generate main page with mainFormPage.twig
        $template = $this->twig->loadTemplate("mainFormPage.twig");
        print($template->render(array('title' => $formTitle, 'navbar' => $navbar, 'form' => $form)));
    }

    //Function to generate the whole page (Navbar and form)

    function generateForm($formID)
    {
        //Inititalise the Model and Twig objects to use
        $Model = new FormDisplayModel();
        $loader = new Twig_Loader_Filesystem('../view/formPage');
        $twig = new Twig_Environment($loader);

        //Get general form information (title, isSubmitted, isMerged)
        $formInformation = $Model->getFormInformation($formID);
        $formInformation = $formInformation[0];
        $formTitle = $formInformation["Form_title"];
        $isSubmitted = $formInformation["IsSubmitted"];
        $isMerged = $formInformation["IsMerged"]; //0 means not merged, -1 means merged, 1 means forms a merge??


        //Check if person is admin, they can view all forms in submitted form (non-editable)
        $admin = $Model->checkAdmin($this->getCurrentMarker());
        if ($admin) {
            return $this->displaySubmitted($formID, $twig, $Model, $formTitle);
            exit;
        }

        //Check if this person is a marker on this form or contributing to this merged form
        $result = $this->checkMarker($formID, $Model);
        if (!($result)) {
            //The user is not authorised to view this form:
            //Display mainFormPage but with html replaced with message indicating user should leave
            $navbar = new NavbarController();
            $navbar = $navbar->generateNavbarHtml();
            $template = $twig->loadTemplate("mainFormPage.twig");
            $invalid = "<h1>Not allowed to view that form, please go back to the main page</h1>";
            return ($template->render(array('title' => "Not Authorised", 'navbar' => $navbar, 'form' => $invalid)));
            exit;
        }


        //Checks if form is merged (-1), contributing to a merged form (1), or just an individual form (0)
        if ($isMerged == 0) {
            //Individual form
            //If not submitted, display as editable, else, display as non-editable
            if (!($isSubmitted)) {
                $this->displayEditableForm($formID, $twig, $Model, $formTitle);
            } else {
                $this->displaySubmitted($formID, $twig, $Model, $formTitle);
            }
        } elseif ($isMerged == 1) {
            //Individual form that contributes to a merged form
            //If submitted, display as non-editable
            if ($isSubmitted) {
                return $this->displaySubmitted($formID, $twig, $Model, $formTitle);
                return;
            } else {
                //Get formID of merged form that this form contributes to
                $mergedFormID = $Model->getMergedForm($formID);
                $mergedFormID = $mergedFormID[0];
                $mergedFormID = $mergedFormID["MForm_ID"];

                //Check if it the merged form has conflicts
                $results = $Model->getConflicts($mergedFormID);
                $conflictSections = array();
                foreach ($results as $conflict) {
                    //Extract section order information from each conflict
                    $sectionID = $conflict["Sec_ID"];
                    $sectionOrder = $Model->getSectionOrderFromID($sectionID);
                    array_push($conflictSections, $sectionOrder); // Check the Param name
                }
                //If there are conflicts, display editable form with only conflict sections editable
                if (count($conflictSections) > 0) {
                    return $this->displayEditableForm($formID, $twig, $Model, $formTitle, $conflictSections, 0, 0, 1);
                }
                //Else, display non-editable form
                //This should never get called as if there are no conflicts, the form should be marked as submitted and therefore already be displayed
                else {
                    return $this->displaySubmitted($formID, $twig, $Model, $formTitle);
                }
            }
        } else {
            //This is the merged form

            //If it is submitted, show non-editable version
            if ($isSubmitted) {
                return $this->displaySubmitted($formID, $twig, $Model, $formTitle, array(), 0, 1);
            } else {
                //Hasn't been submitted fully, matters who is trying to view it

                //Check if the form has conflicts
                $results = $Model->getConflicts($formID); //Check if this form has conflicts

                //If it has conflicts, display non-editable version of form
                if (count($results) > 0) {
                    $conflictSections = array();
                    foreach ($results as $conflict) {
                        $sectionID = $conflict["Sec_ID"];
                        $sectionOrder = $Model->getSectionOrderFromID($sectionID);
                        array_push($conflictSections, $sectionOrder); // Check the Param name
                    }
                    return $this->displaySubmitted($formID, $twig, $Model, $formTitle, $conflictSections);
                    return;
                }

                //Determine if the form has been edited and if the current marker is the examiner or supeervisor for this form
                $currentMarker = $this->getCurrentMarker();
                $isEdited = $Model->isEdited($formID);
                $isEdited = $isEdited[0];
                $isEdited = $isEdited["IsEdited"];
                $isSupervisor = $Model->isSupervisor($formID, $currentMarker);
                $isSupervisor = $isSupervisor[0];
                $isSupervisor = $isSupervisor["IsSupervisor"];

                //If the current marker is the supervisor
                if ($isSupervisor) {
                    //If the form hasn't been edited, show the editable version of the form with only the comment sections editable
                    //Else, display submitted version (supervisor has already edited it, waiting for confirmation from examiner)
                    if (!($isEdited)) {
                        //See form with editable rationales (could use displayIndividual??)
                        return $this->displayEditableForm($formID, $twig, $Model, $formTitle, array(), 1, 1);
                    } else {
                        return $this->displaySubmitted($formID, $twig, $Model, $formTitle, 0, 1);
                    }
                } //If the current marker is the examiner
                else {
                    //If the form has been edited, display submitted version of form with "Confirm" and "Reject" buttons
                    //Else display the submitted form without those buttons (waiting for supervisor to edit the comments)
                    if ($isEdited) {
                        return $this->displaySubmitted($formID, $twig, $Model, $formTitle, 1, 1);
                    } else {
                        return $this->displaySubmitted($formID, $twig, $Model, $formTitle, 0, 1);
                    }
                }
            }
        }
    }

    //Main function to decide how to display the form

    function getCurrentMarker()
    {
        return $_SERVER["REMOTE_USER"];
    }

    /** Function to display a non-editable form
     * Inputs:
     *    -formID - id of form to display
     *    -twig - the twig object used to display forms
     *    -Model - model used
     *    -formTitle - title of the document (e.g Design Report)
     *  -conflictSections - array of section conflicts in merged form (Currently not used but will be used to show where conflicts are)
     *  -confirmButton - use 1 if you want to display "Reject" and "Confirm" buttons (used when an examiner has to confirm edits to a merged form)
     *  -merged - use 1 if the form is merged, 0 if individual
     */
    function displaySubmitted($formID, $twig, $Model, $formTitle, $conflictSections = array(), $confirmButton = 0, $merged = 0)
    {
        //Get student's information
        if ($merged) {
            $studentInfo = $Model->getStudentInformationMerged($formID);
        } else {
            $studentInfo = $Model->getStudentInformation($formID);
        }

        $studentInfo = $studentInfo[0];
        $studentName = $studentInfo["Fname"] . " " . $studentInfo["Lname"];

        //Get marker's details
        $examinerName = "";
        $supervisorName = "";

        //If merged form, get information about both markers
        //Else, get information about individual marker
        if ($merged) {
            $markerInformation = $Model->getMarkerInformationMerged($formID);
        } else {
            $markerInformation = $Model->getMarkerInformation($formID);
        }
        foreach ($markerInformation as $marker) {
            $markerName = $marker["Fname"] . " " . $marker["Lname"];
            if ($marker["IsSupervisor"]) {
                $supervisorName = $markerName;
            } else {
                $examinerName = $markerName;
            }
        }

        //Get the form sections (section criteria, order, mark, rationale, weighting)
        $formDetails = $Model->getFormSections($formID);
        $sections = array(); //array to hold each the sections

        //Get data for mark and rationale section, store in Sections array
        for ($id = 0; $id < count($formDetails) - 1; $id++) {
            //Possible to get section conflicts and add them to the form display here

            $row = $formDetails[$id];
            $sectionName = $row["Sec_Name"];
            $sectionWeight = $row["Sec_Percent"];
            $sectionCriteria = $row["Sec_Criteria"];
            $sectionMark = $row["Mark"];
            $sectionRationale = $row["Comment"];
            $section = array();

            //Split criteria, generate html
            $criteria = explode("\n", $sectionCriteria);
            $template = $twig->loadTemplate("criteria.twig");
            $criteria = $template->render(array('criteriaName' => $sectionName,
                'criteriaWeighting' => $sectionWeight,
                'criteriaList' => $criteria));

            //Submitted form, so everything is non-editable, no need for IDs or readOnly variables
            $section["criteria"] = $criteria;
            $section["mark"] = $sectionMark;
            $section["rationale"] = $sectionRationale;
            array_push($sections, $section);
        }

        //Get comments section
        $commentsRow = $formDetails[count($formDetails) - 1];
        $comments = $commentsRow["Comment"];

        //Generate html table from nonEditableTable.twig (No textareas)
        $markingSections = array('formID' => $formID, 'rows' => $sections);
        $template = $twig->loadTemplate("nonEditableTable.twig");
        $table = $template->render($markingSections);

        //Get the total mark of the form
        $totalMark = $Model->getTotalMark($formID);
        $totalMark = $totalMark[0];
        $totalMark = $totalMark["Total"];
        $totalMark = round($totalMark, 2);

        //Determine the subtitle (based on the marker types)
        if ($examinerName == "") {
            $subtitle = "Individual Supervisor's Report";
        } elseif ($supervisorName == "") {
            $subtitle = "Individual Examiner's Report";
        } else {
            $subtitle = "Final Report";
        }

        //Generate the html of the table
        $template = $twig->loadTemplate("nonEditableForm.twig");
        $form = $template->render(array('table' => $table,            //HTML of the table, generated by nonEditableTable.twig
            'title' => $formTitle,                //Title of form (e.g Design Report)
            'subtitle' => $subtitle,             //Subtitle, (e.g Individual Examiner's Report)
            'examinerName' => $examinerName,     //Examiner Name (can be empty string)
            'supervisorName' => $supervisorName, //Supervisor Name (can be empty string)
            'studentName' => $studentName,       //Name of student
            'totalMark' => $totalMark,           //Total mark
            'comments' => $comments,               //Text in General comments section
            'confirmButton' => $confirmButton,     //Use 1 to display Confirm and Reject buttons
            'formID' => $formID));                 //ID of form from Form table in database

        return $form;
    }

    function checkMarker($formID, $Model)
    {
        $markerID = $this->getCurrentMarker();
        $result = $Model->checkMarkerIndividual($formID, $markerID);
        $result2 = $Model->checkMarkerMerged($formID, $markerID);
        return ($result || $result2);
    }

    //Returns 1 if the current user logged in is authorised to view this form

    /** Function to display an editable form
     * Inputs:
     *    -formID - id of form to display
     *    -twig - the twig object used to display the form
     *    -Model - model used for getting information
     *    -formTitle - title of the document (e.g Design Report)
     *    -conflictSections - array of section conflicts in merged form (can be empty array)
     *    -marksReadOnly - use 1 to set all marks to be read only (used when editing merged forms)
     *    -isMerged - use 1 if a merged form, 0 for individual forms
     *    -addSubmitComment - use 1 if you want user to have to add comment along with submission (usually only used if submitting for a second time due to conflict sections)
     */
    function displayEditableForm($formID, $twig, $Model, $formTitle, $conflictSections = array(), $marksReadOnly = 0, $isMerged = 0, $addSubmitComment = 0)
    {
        //Get student's information
        if ($isMerged) {
            $studentInfo = $Model->getStudentInformationMerged($formID);
        } else {
            $studentInfo = $Model->getStudentInformation($formID);
        }
        $studentInfo = $studentInfo[0];
        $studentName = $studentInfo["Fname"] . " " . $studentInfo["Lname"];

        //Get marker's details
        $examinerName = "";
        $supervisorName = "";

        //If merged form, get information about both markers
        //Else, get information about individual marker
        if ($isMerged) {
            $markerInformation = $Model->getMarkerInformationMerged($formID);
        } else {
            $markerInformation = $Model->getMarkerInformation($formID);
        }
        foreach ($markerInformation as $marker) {
            $markerName = $marker["Fname"] . " " . $marker["Lname"];
            if ($marker["IsSupervisor"]) {
                $supervisorName = $markerName;
            } else {
                $examinerName = $markerName;
            }
        }

        //Get the form sections (section criteria, order, mark, rationale, weighting)
        $formDetails = $Model->getFormSections($formID);
        $sections = array(); //array to hold each the sections

        //Get data for each mark and rationale section
        //Add each section to an array
        for ($id = 0; $id < count($formDetails) - 1; $id++) {
            $row = $formDetails[$id];
            $sectionName = $row["Sec_Name"];
            $sectionWeight = $row["Sec_Percent"];
            $sectionCriteria = $row["Sec_Criteria"];
            $sectionMark = $row["Mark"];
            $sectionRationale = $row["Comment"];
            $section = array();

            //Split criteria, generate html
            $criteria = explode("\n", $sectionCriteria);
            $template = $twig->loadTemplate("criteria.twig");
            $criteria = $template->render(array('criteriaName' => $sectionName,
                'criteriaWeighting' => $sectionWeight,
                'criteriaList' => $criteria));
            $section["criteria"] = $criteria;

            //ID+1 gives the section order in the form
            $section["markID"] = $id + 1;
            //If markReadOnly = 1, display non-editable mark cell in form table
            //markReadOnly = 1 if this section is not a conflict section (and there are some conflict sections), or marksReadOnly = 1 in the input to the function
            $section["markReadOnly"] = ((1 - (in_array(($id + 1), $conflictSections)) && count($conflictSections) > 0) || $marksReadOnly);
            $section["mark"] = $sectionMark;
            $section["rationaleID"] = $id + 1;
            $section["rationaleReadOnly"] = ((1 - (in_array(($id + 1), $conflictSections))) && count($conflictSections) > 0);
            $section["rationale"] = $sectionRationale;
            array_push($sections, $section);
        }

        //Get comments section
        $commentsRow = $formDetails[count($formDetails) - 1];
        $comments = $commentsRow["Comment"];
        $commentID = count($formDetails);
        $commentsReadOnly = ((1 - (in_array((count($formDetails)), $conflictSections))) && count($conflictSections) > 0);

        //Create array to pass to editableTable.twig file to generate the html of the table
        $markingSections = array('formID' => $formID, 'rows' => $sections);
        //Generate html table from allTables.twig
        $template = $twig->loadTemplate("editableTable.twig");
        $table = $template->render($markingSections);

        //Calculate the toal mark (if applicable)
        $totalMark = -1; //Will be -1 if not a submitted form
        if ($isMerged) {
            $totalMark = $Model->getTotalMark($formID);
            $totalMark = $totalMark[0];
            $totalMark = $totalMark["Total"];
            $totalMark = round($totalMark, 2);
        }

        //Determine the subtitle (based on the marker types and if the form is merged)
        $subtitle = "";
        if ($examinerName == "") {
            $subtitle = "Individual Supervisor's Report";
        } elseif ($supervisorName == "") {
            $subtitle = "Individual Examiner's Report";
        } else {
            $subtitle = "Final Report";
        }

        //Display the form submission buttons (Save and Submit) - this could be removed as it is always there
        $displayFormSubmission = 1;
        //Use editableForm.twig to generate the html for the form
        $template = $twig->loadTemplate("editableForm.twig");
        $form = $template->render(array('table' => $table,               //The HTML for the table (generated by editableTable.twig)
            'title' => $formTitle,                              //Main title (e.g Design Report)
            'subtitle' => $subtitle,                           //Subtitle (e.g Individual Examiner's Report)
            'examinerName' => $examinerName,                   //Name of the Examiner (can be empty string)
            'supervisorName' => $supervisorName,               //Name of Supervisor (can be empty string)
            'studentName' => $studentName,                     //Name of the student
            'totalMark' => $totalMark,                         //Total mark (-1 if not applicable)
            'formID' => $formID,                   //ID of the form in the Form table in the database
            'comments' => $comments,               //The text in the General comments section of form)
            'commentsReadOnly' => $commentsReadOnly,       //Set to 1 if the comments section should be readonly
            'displayFormSubmission' => $displayFormSubmission,   //Display the submission buttons
            'displaySubmissionComment' => $addSubmitComment,));  //Set to 1 if want user to add a submission comment (for dealing with conflicts)

        return $form;
    }
}
