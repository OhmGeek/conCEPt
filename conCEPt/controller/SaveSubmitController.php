<?php
//include model here
namespace Concept\Controller;

use Concept\Model\SaveSubmitModel;

class SaveSubmitController
{
    //Requires post variables to work
    function __construct($postVariables)
    {
        $this->retrieveInformation($postVariables);
    }

    //Returns the ID of the current logged in user

    function retrieveInformation($postVariables)
    {
        //Initialise Model to interact with database
        $Model = new SaveSubmitModel();

        //Get the formID and the storeType (Save, Submit, Reject, or Confirm)
        $formID = $postVariables["documentID"];
        $storeType = $postVariables["action"];

        //If storeType is Confirm or Reject, form is completed merged form, only need to change a flag
        if ($storeType == "Confirm") {
            //If Confirm, change isSubmitted flag to 1
            $result = $Model->updateSubmitFlag($formID, 1);

            //Check it worked
            if ($result) {
                echo($this->sendSuccessMessage("Confirm successful"));
            } else {
                echo($this->sendErrorMessage("Confirm failed, try again"));
            }
            exit;
        } elseif ($storeType == "Reject") {
            //If Reject, change edited flag to 0 (Supervisor must edit the comments again)
            $result = $Model->changeEditedFlag($formID, 0);

            //Check it worked
            if ($result) {
                echo($this->sendSuccessMessage("Successfully rejected"));
            } else {
                echo($this->sendErrorMessage("Failed to reject, try again"));
            }
            exit;
        }
        //Array of sections to send to the database
        $sections = array();

        // The number of sections in the form
        $numberOfSections = $postVariables["numberOfSections"];

        // Iterate through all sections in the form (except comments section)
        for ($n = 1; $n < $numberOfSections; $n++) {
            $sectionNumber = $n; //Section number in ordering on form
            //Get mark for this secion if it exists
            if (!(empty($postVariables["mark-" . $n]))) {
                $mark = $postVariables["mark-" . $n]; //Mark for this section
            } else {
                $mark = 0;
            }
            //Get rationale for this section if it exists
            if (!(empty($postVariables["rationale-" . $n]))) {
                $rationale = trim($postVariables["rationale-" . $n]);
                $rationale = stripslashes($rationale);
            } else {
                $rationale = "";
            }
            //Add mark and rationale to sections array
            $section = array("sectionNumber" => $n, "mark" => $mark,
                "rationale" => $rationale);
            array_push($sections, $section);
        }

        // Add comments section to sections array (treated as a normal section with a mark of 0)
        if (isset($postVariables["comments"])) {         
            $comments = stripslashes(trim($postVariables["comments"]));
            $section = array("sectionNumber" => ($numberOfSections), "mark" => 0, "rationale" => $comments);
            array_push($sections, $section);
        }

        //If submission comment is set, get the text, otherwise set it to "Initital Submission"
        if (isset($postVariables["submitComment"])) {
            $submissionComment = stripslashes(trim($postVariables["submitComment"]));
        } else {
            $submissionComment = "Initial submission";
        }

        //Save each section
        foreach ($sections as $section) {
            $result = $Model->sendSection($formID, $section["sectionNumber"], $section["mark"], $section["rationale"]);

            //Check it worked
            if (!($result)) {
                echo($this->sendErrorMessage("Couldn't send a section, please try again"));
                exit;
            }
        }

        //If store type is submit, need to do more work
        if ($storeType == "Submit") {
            //Check if it is a merged form
            $isMerged = $Model->isMergedForm($formID);

            //If it is, supervisor has edited comments, set edited flag to 1
            if ($isMerged) {
                $result = $Model->changeEditedFlag($formID, 1);
                if ($result) {
                    echo($this->sendSuccessMessage("Edit successful"));
                } else {
                    echo($this->sendErrorMessage("Edit failed, try again"));
                }
                exit;
            } //Else, not a merged form, update the submitted flag to 1 and update submission comment in Form table
            else {
                $result = $Model->updateSubmitFlag($formID, 1);
                $result2 = $Model->addSubmitComment($formID, $submissionComment);
                if (!($result && $result2)) {
                    echo($this->sendErrorMessage("Submission failed"));
                }

                //Get details from this form
                $details = $Model->getGeneralDetails($formID);
                $details = $details[0];
                $BFormID = $details["BForm_ID"];
                $studentID = $details["Student_ID"];
                $isSupervisor = $details["IsSupervisor"];

                //Find other marker's form
                $otherForm = $Model->getOtherMarkerForm($studentID, $BFormID, $isSupervisor);
                if (count($otherForm) > 0) {

                    //If it exists, get the details (isSubmitted, isSupervisor)
                    $otherForm = $otherForm[0];
                    $otherFormSubmitted = $otherForm["IsSubmitted"];

                    //If other marker has submitted their form, figure out which is the examiner form and which is the supervisor form
                    if ($otherFormSubmitted) {
                        $otherForm = $otherForm["Form_ID"];
                        if ($isSupervisor) {
                            $SForm = $formID;
                            $EForm = $otherForm;
                        } else {
                            $SForm = $otherForm;
                            $EForm = $formID;
                        }
                        //Try to find the merged form that comes from these two forms
                        $mergedForm = $Model->getMergedForm($EForm);
                        //If it doesn't exist, merge these two forms
                        if (count($mergedForm) == 0) {
                            //$result = $Model->mergeForms($Eform, $SForm);
                            $result = $this->mergeForms($EForm, $SForm, $BFormID, $Model);
                            if (!($result)) {
                                echo($this->sendErrorMessage("Merge failed, please resubmit"));
                            }
                        } //Else, get the details of the form and update its sections
                        else {
                            $mergedForm = $mergedForm[0];
                            $mergedForm = $mergedForm["MForm_ID"];
                            $result = $Model->updateMergedForm($mergedForm, $EForm, $SForm);
                            if (!($result)) {
                                echo($this->sendErrorMessage("Merge failed, please resubmit"));
                                //Have to reopen form for resubmission
                                //$this->reset($EForm, $SForm, $mergedForm); //Don't remove the form, just open up this one again??
                            }
                        }

                        //Get the merged form ID
                        $mergedForm = $Model->getMergedForm($EForm);
                        if (count($mergedForm) > 0) {
                            $mergedForm = $mergedForm[0];
                            $mergedForm = $mergedForm["MForm_ID"];

                            //Remove conflicts first so they aren't re-found even after they've been dealt with
                            $Model->removeConflicts($mergedForm);

                            //Find conflicts and update in the DB
                            $Model->createConflicts($mergedForm, $EForm);

                            //Check if any conflicts were found
                            $conflicts = $Model->getConflicts($mergedForm);
                            //If there are conflicts, duplicate Examiner and Supervisor forms and open both originals to be re-edited
                            if (count($conflicts) > 0) {
                                $Model->duplicateForm($EForm);
                                $Model->duplicateForm($SForm);
                                $Model->openForm($EForm);
                                $Model->openForm($SForm);
                            }
                        }
                    }
                }
            }
        }


        //Assume it has been successful if it got this far
        return ($this->sendSuccessMessage($storeType . " successfull"));
        exit;

    }

    //Retrieve data from the POST request variables from the form (sent in an array by forms.php)

    function sendSuccessMessage($e)
    {
        echo '{"success":"' . $e . '"}';

    }

    //Function to merge an examiner and supervisor form

    function sendErrorMessage($e)
    {
        echo '{"success":"' . $e . '"}';
    }

    //Function to reset the forms to originals (not implemented)

    function mergeForms($EForm, $SForm, $BFormType, $Model)
    {
        $result = true; //assume it worked

        //Create a blank form of the correct type
        $Model->createBlankForm($EForm);
        //Find the merged form that was just created
        $mergedForm = $Model->getBlankMergedForm($BFormType);
        //Return false if it can't be found
        if (count($mergedForm) == 0) {
            $result = false;
        } else {

            $mergedForm = $mergedForm[0];
            $mergedForm = $mergedForm["Form_ID"];

            //Link the Examiner and Supervisor to the blank form in the mergedForm table
            $result1 = $Model->updateMergeTable($mergedForm, $EForm, $SForm);

            //Update the sections in the merged form
            $result2 = $Model->updateMergedForm($mergedForm, $EForm, $SForm);

            //Update the isMerged flags of the contributing forms to 1
            $result3 = $Model->updateMergeFlag($EForm, 1);
            $result4 = $Model->updateMergeFlag($SForm, 1);

            //Return 1 if all succeeded
            $result = ($result1 && $result2 && $result3 && $result4);
        }

        return $result;
    }

    function getMarkerID()
    {
        //return "hkd4hdk";
        //return "knd6usj";
        return $_SERVER["REMOTE_USER"];
    }

    function reset($EForm, $SForm, $mergedForm)
    {
        //remove merged form from form table
        //change merge flags back to 0
        //Open up original so they can resubmit??
    }
}

?>
